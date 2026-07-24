<?php

namespace App\Console\Commands;

use App\Models\OpDetRegProd;
use App\Models\OpDetRegProdOrigen;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Backfill de trazabilidad entre etapas (opdetregprod_origen) para registros
 * históricos aprobados ANTES de implementar el FIFO automático (2026-06-11).
 *
 * Reproduce el mismo FIFO en orden cronológico de aprobación (id ASC):
 * cada registro consume kgent de los lotes aprobados de la etapa anterior,
 * lote más antiguo primero. Solo procesa registros SIN origen ya asignado
 * (no duplica). Si los lotes no alcanzan, asigna lo que haya (nunca inventa).
 *
 * Uso:
 *   php artisan produccion:backfill-trazabilidad --dry        (simula todo, no guarda)
 *   php artisan produccion:backfill-trazabilidad --op=86      (solo la OP 86)
 *   php artisan produccion:backfill-trazabilidad              (todas las OPs)
 */
class BackfillTrazabilidadEtapas extends Command
{
    protected $signature = 'produccion:backfill-trazabilidad
                            {--op= : Procesar solo esta OP}
                            {--dry : Simular sin guardar (rollback al final)}';

    protected $description = 'Reconstruye la trazabilidad entre etapas (opdetregprod_origen) de registros históricos por FIFO';

    public function handle()
    {
        $opFiltro = $this->option('op');
        $dry      = $this->option('dry');

        // OPs que tienen registros aprobados
        $sqlOps = "
            SELECT DISTINCT od.op_id
            FROM   opdetregprod r
            INNER  JOIN opdet od ON od.id = r.opdet_id
            WHERE  ISNULL(r.deleted_at) AND ISNULL(od.deleted_at)
            " . ($opFiltro ? " AND od.op_id = " . intval($opFiltro) : "") . "
            ORDER  BY od.op_id
        ";
        $ops = DB::select($sqlOps);
        if (empty($ops)) {
            $this->info('No hay OPs con registros aprobados para procesar.');
            return 0;
        }

        $totalFilas = 0;
        DB::beginTransaction();
        try {
            foreach ($ops as $opRow) {
                $op_id = $opRow->op_id;

                // Etapas de la OP en orden de producción
                $opdets = DB::select("
                    SELECT opdet.id
                    FROM   opdet
                    INNER  JOIN areaproduccionsucetapaprod ap ON ap.id = opdet.apsucetapaprod_id
                    WHERE  opdet.op_id = ? AND ISNULL(opdet.deleted_at)
                    ORDER  BY ap.orden ASC
                ", [$op_id]);

                $opdetAnteriorId = null;
                foreach ($opdets as $od) {
                    if ($opdetAnteriorId !== null) {
                        $totalFilas += $this->procesarEtapa($op_id, $od->id, $opdetAnteriorId);
                    }
                    $opdetAnteriorId = $od->id;
                }
            }

            if ($dry) {
                DB::rollBack();
                $this->warn("DRY RUN: se habrían creado {$totalFilas} filas de origen. Nada guardado.");
            } else {
                DB::commit();
                $this->info("Backfill completado: {$totalFilas} filas de origen creadas.");
            }
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('ERROR (rollback): ' . $e->getMessage());
            return 1;
        }
        return 0;
    }

    /**
     * Procesa los registros aprobados de una etapa contra los lotes de la anterior.
     * Devuelve cuántas filas de origen creó.
     */
    private function procesarEtapa($op_id, $opdet_id, $opdetAnterior_id)
    {
        $creadas = 0;

        // Registros aprobados de esta etapa SIN origen ya asignado, en orden cronológico
        $registros = OpDetRegProd::where('opdet_id', $opdet_id)
            ->whereNull('deleted_at')
            ->whereNotExists(function ($q) {
                $q->select(DB::raw(1))
                  ->from('opdetregprod_origen')
                  ->whereRaw('opdetregprod_origen.opdetregprod_id = opdetregprod.id');
            })
            ->orderBy('id', 'asc')
            ->get();

        foreach ($registros as $reg) {
            $restanteKg = (float) $reg->kgent;
            $kgentTotal = (float) $reg->kgent;
            if ($restanteKg <= 0) continue;

            // Lotes de la etapa anterior FIFO, con saldo recalculado en vivo
            // (incluye las filas que este mismo backfill va creando en la transacción)
            $lotes = OpDetRegProd::where('opdet_id', $opdetAnterior_id)
                ->whereNull('deleted_at')
                ->orderBy('id', 'asc')
                ->get();

            $detalle = [];
            foreach ($lotes as $lote) {
                if ($restanteKg <= 0) break;
                $consumido  = (float) OpDetRegProdOrigen::where('opdetregprod_origen_id', $lote->id)->sum('kg');
                $disponible = max((float) $lote->kgprod - $consumido, 0);
                if ($disponible <= 0) continue;

                $tomarKg = min($disponible, $restanteKg);
                OpDetRegProdOrigen::create([
                    'opdetregprod_id'        => $reg->id,
                    'opdetregprod_origen_id' => $lote->id,
                    'kg'                     => round($tomarKg, 2),
                    'cant'                   => $kgentTotal > 0
                        ? round((float) $reg->cantent * $tomarKg / $kgentTotal, 2)
                        : 0,
                ]);
                $detalle[] = 'apr-' . $lote->id . '=' . round($tomarKg, 2) . 'kg';
                $restanteKg -= $tomarKg;
                $creadas++;
            }

            $sinOrigen = $restanteKg > 0 ? ' [SIN ORIGEN: ' . round($restanteKg, 2) . ' kg]' : '';
            $this->line("OP {$op_id} | apr-{$reg->id} (kgent=" . round($kgentTotal, 2) . ') ← '
                . (empty($detalle) ? 'sin lotes disponibles' : implode(', ', $detalle)) . $sinOrigen);
        }

        return $creadas;
    }
}
