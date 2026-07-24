<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Trazabilidad entre etapas — RESERVA (lado temp).
 * Cada fila indica de qué lote aprobado (opdetregprod de la etapa anterior)
 * tomó material un registro temporal de producción, y cuánto (kg/cant).
 * Asignación automática por FIFO al guardar el registro del operario.
 */
class OpDetRegProdTempOrigen extends Model
{
    protected $table = 'opdetregprodtemp_origen';

    protected $fillable = [
        'opdetregprodtemp_id',
        'opdetregprod_id',
        'kg',
        'cant',
    ];

    // Registro temp de la etapa actual (hijo)
    public function opdetregprodtemp()
    {
        return $this->belongsTo(OpDetRegProdTemp::class, 'opdetregprodtemp_id');
    }

    // Lote aprobado de la etapa anterior (padre/origen)
    public function lote()
    {
        return $this->belongsTo(OpDetRegProd::class, 'opdetregprod_id');
    }

    /**
     * Asigna automáticamente (FIFO) los lotes de la etapa anterior que consume
     * un registro temporal de producción. Borra las asignaciones previas del temp
     * y las recrea según los kg de entrada actuales (kgent).
     *
     * Reglas:
     *  - FIFO: lote aprobado más antiguo primero (id ASC; la regla de aprobación
     *    ASC garantiza que id menor = aprobado antes).
     *  - saldo lote = kgprod − consumos definitivos − reservas de temps vigentes
     *    (no eliminados y aprobstatus != 2; los status 2 ya tienen fila definitiva).
     *  - Primera etapa (sin etapa anterior): no asigna nada.
     *  - Si los lotes no cubren kgent (ej. datos históricos sin detalle), asigna
     *    lo que alcance y el resto queda sin origen. NUNCA lanza excepción:
     *    la trazabilidad es contabilidad paralela, no debe bloquear al operario.
     *
     * Debe llamarse DENTRO de la transacción de guardar/actualizar.
     */
    public static function asignarFifo(OpDetRegProdTemp $temp)
    {
        // R1: las muestras físicas no consumen lotes de la etapa anterior
        if ($temp->es_muestra) {
            self::where('opdetregprodtemp_id', $temp->id)->delete();
            return;
        }

        // Reasignación limpia: borrar reservas previas de este temp (si las hay)
        self::where('opdetregprodtemp_id', $temp->id)->delete();

        // Ubicar la etapa anterior del mismo op (misma lógica de "orden" que usa
        // OpDetRegProdTempAprobSupController para hallar la etapa siguiente)
        $opdet = $temp->opdet;
        if (!$opdet || !$opdet->areaproduccionsucetapaprod) return;
        $ordenActual = $opdet->areaproduccionsucetapaprod->orden;

        $opdetAnterior = null;
        $ordenAnterior = null;
        foreach ($opdet->op->opdets as $od) {
            $orden = $od->areaproduccionsucetapaprod ? $od->areaproduccionsucetapaprod->orden : null;
            // La etapa anterior es la de mayor orden entre las menores al actual
            if ($orden !== null && $orden < $ordenActual
                && ($ordenAnterior === null || $orden > $ordenAnterior)) {
                $ordenAnterior = $orden;
                $opdetAnterior = $od;
            }
        }
        if (!$opdetAnterior) return; // primera etapa: el origen vendrá del módulo MP (futuro)

        // Lotes aprobados de la etapa anterior, FIFO (más antiguo primero).
        // Se excluyen muestras físicas (es_muestra=1): no son producción disponible.
        $lotes = OpDetRegProd::where('opdet_id', $opdetAnterior->id)
            ->where('es_muestra', 0)
            ->whereNull('deleted_at')
            ->orderBy('id', 'asc')
            ->get();

        $restanteKg = (float) $temp->kgent;   // lo que esta etapa consume de la anterior
        $kgentTotal = (float) $temp->kgent;   // base para prorratear cantent

        foreach ($lotes as $lote) {
            if ($restanteKg <= 0) break;

            // Consumos definitivos del lote (hijos ya aprobados)
            $consumidoDef = (float) OpDetRegProdOrigen::where('opdetregprod_origen_id', $lote->id)
                ->sum('kg');

            // Reservas de otros temps vigentes (no eliminados, no aprobados aún)
            $reservadoTmp = (float) self::where('opdetregprod_id', $lote->id)
                ->whereHas('opdetregprodtemp', function ($q) {
                    $q->whereNull('deleted_at')->where('aprobstatus', '!=', 2);
                })
                ->sum('kg');

            $disponible = max((float) $lote->kgprod - $consumidoDef - $reservadoTmp, 0);
            if ($disponible <= 0) continue;

            $tomarKg = min($disponible, $restanteKg);
            self::create([
                'opdetregprodtemp_id' => $temp->id,
                'opdetregprod_id'     => $lote->id,
                'kg'                  => round($tomarKg, 2),
                // cantent prorrateada según los kg tomados de este lote
                'cant'                => $kgentTotal > 0
                    ? round((float) $temp->cantent * $tomarKg / $kgentTotal, 2)
                    : 0,
            ]);
            $restanteKg -= $tomarKg;
        }
        // Si $restanteKg > 0: lotes sin detalle suficiente (histórico) — queda sin origen.
    }
}
