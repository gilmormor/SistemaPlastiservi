<?php

namespace App\Console\Commands;

use App\Models\CcTolerancia;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Corrige las tolerancias de ancho y largo guardadas en los acuerdos técnicos
 * cuando no corresponden a la medida que tienen al lado.
 *
 * Motivo: la tolerancia (at_anchodesv / at_largodesv) se graba como texto cuando
 * se crea o edita el acuerdo, calculada en el navegador. Durante mucho tiempo la
 * función solo tuvo tres tramos y cortaba en 150 cm, por lo que todo lo mayor
 * quedó grabado como '±3 CM'; el tramo '301 a ∞ → ±4 CM' de la tabla de Control
 * de Calidad nunca se aplicó. También hay acuerdos donde alguien cambió la medida
 * sin que se recalculara la tolerancia.
 *
 * Esto pasó a importar cuando CC empezó a usar esa tolerancia para aprobar o
 * rechazar muestras: antes era un dato informativo que solo se imprimía.
 *
 * Uso:
 *   php artisan acuerdotecnico:corregir-tolerancias              (solo informa)
 *   php artisan acuerdotecnico:corregir-tolerancias --aplicar    (respalda y corrige)
 *
 * Sin --aplicar no modifica nada. Con --aplicar escribe primero un archivo SQL de
 * reversión en storage/app/ y solo entonces actualiza.
 *
 * No se toca updated_at: la tolerancia es un dato derivado del propio acuerdo, y
 * cambiar la marca de tiempo invalidaría con "modificado por otro usuario" los
 * formularios que alguien tuviera abiertos.
 */
class CorregirToleranciasAcuerdoTecnico extends Command
{
    protected $signature = 'acuerdotecnico:corregir-tolerancias
                            {--aplicar : Aplica los cambios. Sin esta opción solo informa}';

    protected $description = 'Corrige at_anchodesv / at_largodesv de los acuerdos técnicos cuya tolerancia no corresponde a su medida. Genera respaldo antes de aplicar.';

    public function handle()
    {
        $campos = [
            ['medida' => 'at_ancho', 'desv' => 'at_anchodesv'],
            ['medida' => 'at_largo', 'desv' => 'at_largodesv'],
        ];

        $this->line('Revisando acuerdos técnicos vigentes...');

        $acuerdos = DB::select("
            SELECT id, producto_id, at_ancho, at_anchodesv, at_largo, at_largodesv
            FROM   acuerdotecnico
            WHERE  ISNULL(deleted_at)
        ");

        $pendientes = [];
        foreach ($acuerdos as $at) {
            foreach ($campos as $c) {
                $correcto = CcTolerancia::tramoAnchoLargo($at->{$c['medida']});
                if ($correcto === null) {
                    continue; // sin medida cargada, no hay tolerancia que calcular
                }
                if ($correcto !== $at->{$c['desv']}) {
                    $pendientes[] = [
                        'id'       => $at->id,
                        'producto' => $at->producto_id,
                        'campo'    => $c['desv'],
                        'medida'   => $at->{$c['medida']},
                        'viejo'    => $at->{$c['desv']},
                        'nuevo'    => $correcto,
                    ];
                }
            }
        }

        $this->line('Acuerdos vigentes revisados: ' . count($acuerdos));

        if (empty($pendientes)) {
            $this->info('No hay tolerancias desactualizadas. No hay nada que corregir.');
            return 0;
        }

        $this->warn('Tolerancias desactualizadas: ' . count($pendientes));
        $this->line('');

        // Resumen por tipo de cambio, más útil que listar cientos de filas.
        $resumen = [];
        foreach ($pendientes as $p) {
            $clave = $p['campo'] . ': ' . ($p['viejo'] === null || $p['viejo'] === '' ? '(vacío)' : $p['viejo']) . ' -> ' . $p['nuevo'];
            $resumen[$clave] = ($resumen[$clave] ?? 0) + 1;
        }
        ksort($resumen);
        $filas = [];
        foreach ($resumen as $clave => $n) {
            $filas[] = [$clave, $n];
        }
        $this->table(['Cambio', 'Acuerdos'], $filas);

        // Muestra algunos ejemplos concretos para poder verificarlos a mano.
        $this->line('Ejemplos:');
        foreach (array_slice($pendientes, 0, 5) as $p) {
            $this->line(sprintf("   AT %-6s producto %-7s %-13s medida=%-8s '%s' -> '%s'",
                $p['id'], $p['producto'], $p['campo'], $p['medida'],
                $p['viejo'], $p['nuevo']));
        }
        $this->line('');

        if (!$this->option('aplicar')) {
            $this->comment('Modo consulta: no se modificó nada.');
            $this->comment('Para aplicar:  php artisan acuerdotecnico:corregir-tolerancias --aplicar');
            return 0;
        }

        // ── Respaldo antes de tocar nada ──────────────────────────────────────
        $nombre = 'respaldo_tolerancias_' . date('Ymd_His') . '.sql';
        $ruta   = storage_path('app/' . $nombre);

        $sql  = "-- Respaldo de tolerancias de ancho/largo de acuerdotecnico\n";
        $sql .= "-- Generado el " . date('d/m/Y H:i:s') . " antes de corregir " . count($pendientes) . " valores.\n";
        $sql .= "-- Ejecutar este archivo devuelve cada acuerdo a su tolerancia anterior.\n\n";
        foreach ($pendientes as $p) {
            $viejo = ($p['viejo'] === null) ? 'NULL' : "'" . addslashes($p['viejo']) . "'";
            $sql  .= "UPDATE acuerdotecnico SET {$p['campo']} = {$viejo} WHERE id = {$p['id']};\n";
        }

        if (file_put_contents($ruta, $sql) === false) {
            $this->error('No se pudo escribir el respaldo en ' . $ruta . '. No se aplicó ningún cambio.');
            return 1;
        }
        $this->info('Respaldo escrito: ' . $ruta);

        // ── Aplicar ───────────────────────────────────────────────────────────
        DB::beginTransaction();
        try {
            foreach ($pendientes as $p) {
                DB::update("UPDATE acuerdotecnico SET {$p['campo']} = ? WHERE id = ?", [$p['nuevo'], $p['id']]);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Error al aplicar, se revirtió todo: ' . $e->getMessage());
            return 1;
        }

        $this->info('Acuerdos corregidos: ' . count($pendientes));

        // ── Verificación posterior ────────────────────────────────────────────
        $restantes = 0;
        foreach (DB::select("SELECT at_ancho, at_anchodesv, at_largo, at_largodesv
                             FROM acuerdotecnico WHERE ISNULL(deleted_at)") as $at) {
            foreach ($campos as $c) {
                $correcto = CcTolerancia::tramoAnchoLargo($at->{$c['medida']});
                if ($correcto !== null && $correcto !== $at->{$c['desv']}) {
                    $restantes++;
                }
            }
        }

        if ($restantes === 0) {
            $this->info('Verificación: no quedan tolerancias desactualizadas.');
            return 0;
        }

        $this->error('Verificación: quedan ' . $restantes . ' sin corregir. Revisar.');
        return 1;
    }
}
