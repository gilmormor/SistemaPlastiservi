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
        'scrap',
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

    /**
     * Encuentra la etapa (opdet) anterior a la del opdet dado, dentro del mismo OP.
     * Misma lógica que usa asignarFifo() para ubicar la etapa de origen.
     */
    public static function opdetAnterior(OpDet $opdet)
    {
        if (!$opdet->areaproduccionsucetapaprod) return null;
        $ordenActual = $opdet->areaproduccionsucetapaprod->orden;

        $opdetAnterior = null;
        $ordenAnterior = null;
        foreach ($opdet->op->opdets as $od) {
            $orden = $od->areaproduccionsucetapaprod ? $od->areaproduccionsucetapaprod->orden : null;
            if ($orden !== null && $orden < $ordenActual
                && ($ordenAnterior === null || $orden > $ordenAnterior)) {
                $ordenAnterior = $orden;
                $opdetAnterior = $od;
            }
        }
        return $opdetAnterior;
    }

    /**
     * Lista los lotes (opdetregprod) de la etapa anterior con saldo disponible,
     * para que el operario elija manualmente de cuál(es) viene su registro.
     * Mismo cálculo de "disponible" que asignarFifo(), pero sin asignar nada.
     *
     * $excludeTempId: al editar un registro existente, se excluye su propia
     * reserva vigente del cálculo de "reservado", para que sus lotes ya
     * asignados vuelvan a aparecer con su saldo completo (se re-crean al guardar).
     */
    public static function lotesDisponibles(OpDet $opdet, $excludeTempId = null)
    {
        $opdetAnterior = self::opdetAnterior($opdet);
        if (!$opdetAnterior) return collect();

        $lotes = OpDetRegProd::where('opdet_id', $opdetAnterior->id)
            ->where('es_muestra', 0)
            ->whereNull('deleted_at')
            ->orderBy('id', 'asc')
            ->get();

        return $lotes->map(function ($lote) use ($excludeTempId) {
            $consumidoDef = (float) OpDetRegProdOrigen::where('opdetregprod_origen_id', $lote->id)->sum('kg');

            $reservadoQuery = self::where('opdetregprod_id', $lote->id)
                ->whereHas('opdetregprodtemp', function ($q) {
                    $q->whereNull('deleted_at')->where('aprobstatus', '!=', 2);
                });
            if ($excludeTempId) {
                $reservadoQuery->where('opdetregprodtemp_id', '!=', $excludeTempId);
            }
            $reservadoTmp = (float) $reservadoQuery->sum('kg');

            $disponible = max((float) $lote->kgprod - $consumidoDef - $reservadoTmp, 0);

            return [
                'id'             => $lote->id,
                'disponible_kg'  => round($disponible, 2),
                'registrado_por' => optional($lote->usuario)->nombre,
                'fecha_hora'     => $lote->aprobfechahora ? date('d/m/Y H:i', strtotime($lote->aprobfechahora)) : ($lote->created_at ? $lote->created_at->format('d/m/Y H:i') : null),
            ];
        })->filter(function ($l) {
            return $l['disponible_kg'] > 0;
        })->values();
    }

    /**
     * Asigna manualmente los lotes de origen elegidos por el operario, con el
     * detalle de cuánto de cada lote fue a producción buena y cuánto a scrap.
     * $selecciones: [opdetregprod_id => ['kg' => produccion, 'scrap' => scrap], ...]
     * 'kg' se guarda como el TOTAL consumido del lote (produccion + scrap), igual
     * semántica que ya usaba asignarFifo(); 'scrap' es la porción declarada scrap.
     */
    public static function asignarManual(OpDetRegProdTemp $temp, array $selecciones)
    {
        self::where('opdetregprodtemp_id', $temp->id)->delete();

        $kgentTotal = (float) $temp->kgent;
        foreach ($selecciones as $loteId => $datos) {
            $prod  = (float) ($datos['kg'] ?? 0);
            $scrap = (float) ($datos['scrap'] ?? 0);
            $total = $prod + $scrap;
            if ($total <= 0) continue;
            self::create([
                'opdetregprodtemp_id' => $temp->id,
                'opdetregprod_id'     => $loteId,
                'kg'                  => round($total, 2),
                'scrap'               => round($scrap, 2),
                'cant'                => $kgentTotal > 0
                    ? round((float) $temp->cantent * $total / $kgentTotal, 2)
                    : 0,
            ]);
        }
    }
}
