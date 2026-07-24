<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;

/**
 * Lógica centralizada de validación CC para despacho.
 * Recorre toda la cadena de producción (BFS bidireccional por opdetregprod_origen)
 * y verifica que cada etapa con requiere_cc=1 tenga al menos 1 muestra válida.
 *
 * Los resultados se cachean por lote dentro del mismo request para no repetir
 * queries cuando varios lotes comparten cadena.
 */
class CcValidacion
{
    private static $bfsCache        = [];
    private static $validacionCache = [];

    /**
     * BFS bidireccional desde un lote: devuelve todos los IDs de lotes conectados.
     * Sube solo por padres directos (sin expandir hermanos) y baja solo por hijos directos.
     */
    public static function bfsLotesCadena(int $loteId)
    {
        if (isset(self::$bfsCache[$loteId])) {
            return self::$bfsCache[$loteId];
        }

        $todos = [$loteId];

        // Hacia arriba: padres directos (etapas anteriores en la cadena)
        $pendientes = [$loteId];
        for ($d = 0; $d < 15 && !empty($pendientes); $d++) {
            $ph = implode(',', $pendientes);
            $pendientes = [];
            foreach (DB::select("SELECT DISTINCT opdetregprod_origen_id AS id FROM opdetregprod_origen WHERE opdetregprod_id IN ($ph)") as $r) {
                $id = (int)$r->id;
                if (!in_array($id, $todos)) {
                    $todos[]    = $id;
                    $pendientes[] = $id;
                }
            }
        }

        // Hacia abajo: hijos directos (etapas posteriores en la cadena)
        $pendientes = [$loteId];
        for ($d = 0; $d < 15 && !empty($pendientes); $d++) {
            $ph = implode(',', $pendientes);
            $pendientes = [];
            foreach (DB::select("SELECT DISTINCT opdetregprod_id AS id FROM opdetregprod_origen WHERE opdetregprod_origen_id IN ($ph)") as $r) {
                $id = (int)$r->id;
                if (!in_array($id, $todos)) {
                    $todos[]    = $id;
                    $pendientes[] = $id;
                }
            }
        }

        self::$bfsCache[$loteId] = $todos;
        return $todos;
    }

    /**
     * Verifica si un lote puede despacharse desde el punto de vista CC.
     * Recorre TODA la cadena de producción (BFS) y valida cada etapa con requiere_cc=1.
     *
     * Regla principal: cada etapa con requiere_cc=1 debe tener al menos 1 muestra
     * con status IN (1=Aprobado, 2=Aprobado c/obs, 5=Sin parámetros) y sta_env=2.
     *
     * @return array {
     *   bloqueado: bool,
     *   sin_muestra: bool,        — bloqueado por falta de muestra en alguna etapa
     *   rechazado: bool,          — bloqueado por rechazo CC sin desbloqueo
     *   desbloqueado: bool,       — tiene rechazos pero todos desbloqueados
     *   con_obs: bool,            — tiene muestras aprobadas con obs (no bloquea)
     *   ids_bloqueados: string,   — IDs de muestras rechazadas sin desbloqueo
     *   ids_desbloqueados: string,
     *   ids_con_obs: string,
     *   desbloqueo_info: string,
     *   etapas_problema: array,   — nombres de etapas con problema
     * }
     */
    public static function verificarCadenaDespacho(int $loteId)
    {
        if (isset(self::$validacionCache[$loteId])) {
            return self::$validacionCache[$loteId];
        }

        $resultado = [
            'bloqueado'         => false,
            'sin_muestra'       => false,
            'rechazado'         => false,
            'desbloqueado'      => false,
            'con_obs'           => false,
            'ids_bloqueados'    => '',
            'ids_desbloqueados' => '',
            'ids_con_obs'       => '',
            'desbloqueo_info'   => '',
            'etapas_problema'   => [],
        ];

        $loteIds = self::bfsLotesCadena($loteId);
        $ph      = implode(',', $loteIds);

        // Datos de cada lote en la cadena: etapa y si requiere CC
        $lotes = DB::select("
            SELECT
                odrp.id,
                odrp.sucursal_id,
                ep.nombre    AS etapa_nombre,
                apse.requiere_cc
            FROM opdetregprod odrp
            INNER JOIN etapaprod                  ep   ON ep.id             = odrp.etapaprod_id
            LEFT  JOIN areaproduccionsucetapaprod apse ON apse.etapaprod_id = odrp.etapaprod_id
            LEFT  JOIN areaproduccionsuc          apsu ON apsu.id           = apse.areaproduccionsuc_id
                                                      AND apsu.sucursal_id  = odrp.sucursal_id
            WHERE odrp.id IN ($ph)
              AND odrp.deleted_at IS NULL
        ");

        if (empty($lotes)) {
            self::$validacionCache[$loteId] = $resultado;
            return $resultado;
        }

        // Muestras CC de todos los lotes de la cadena (una sola query)
        $ccRows = DB::select("
            SELECT
                ccm.opdetregprod_id,
                ccm.id      AS muestra_id,
                ccm.status,
                ccm.sta_env,
                IF(anul.id IS NOT NULL, 1, 0) AS anulado,
                IF(desb.id IS NOT NULL, 1, 0) AS desbloqueado,
                CONCAT(udesb.nombre, ' — ', DATE_FORMAT(desb.created_at, '%d/%m/%Y %H:%i'),
                       IF(desb.observacion IS NOT NULL AND desb.observacion != '',
                          CONCAT(': ', desb.observacion), '')) AS desbloqueo_detalle
            FROM ccregistmuestra ccm
            LEFT JOIN ccregistmuestraanul        anul  ON anul.ccregistmuestra_id = ccm.id
                                                      AND anul.deleted_at IS NULL
            LEFT JOIN ccregistmuestra_desbloqueo desb  ON desb.ccregistmuestra_id = ccm.id
            LEFT JOIN usuario                    udesb ON udesb.id = desb.usuario_id
            WHERE ccm.opdetregprod_id IN ($ph)
              AND ccm.deleted_at IS NULL
        ");

        $muestrasPorLote = [];
        foreach ($ccRows as $cc) {
            $muestrasPorLote[$cc->opdetregprod_id][] = $cc;
        }

        $idsBloquedosArr     = [];
        $idsDesbloqueadosArr = [];
        $idsConObsArr        = [];
        $desbloqueoInfoArr   = [];

        foreach ($lotes as $lote) {
            if (!$lote->requiere_cc) continue; // etapa sin requisito CC, se omite

            $muestras          = $muestrasPorLote[$lote->id] ?? [];
            $tieneValida       = false;
            $tieneRechazado    = false;
            $tieneDesbloqueado = false;
            $tieneConObs       = false;

            foreach ($muestras as $m) {
                if ($m->anulado) continue;

                // Válida: aprobada (1), aprobada c/obs (2) o sin parámetros (5), y revisada por supervisor
                if (in_array($m->status, [1, 2, 5]) && $m->sta_env == 2) {
                    $tieneValida = true;
                    if ($m->status == 2) {
                        $tieneConObs    = true;
                        $idsConObsArr[] = $m->muestra_id;
                    }
                }
                if ($m->status == 3) {
                    if ($m->desbloqueado) {
                        $tieneDesbloqueado       = true;
                        $idsDesbloqueadosArr[]   = $m->muestra_id;
                        if ($m->desbloqueo_detalle) {
                            $desbloqueoInfoArr[] = $m->desbloqueo_detalle;
                        }
                    } else {
                        $tieneRechazado    = true;
                        $idsBloquedosArr[] = $m->muestra_id;
                    }
                }
            }

            if ($tieneRechazado) {
                $resultado['bloqueado']         = true;
                $resultado['rechazado']         = true;
                $resultado['etapas_problema'][] = $lote->etapa_nombre;
            } elseif (!$tieneValida) {
                // No hay ninguna muestra válida (puede que no haya ninguna muestra)
                $resultado['bloqueado']         = true;
                $resultado['sin_muestra']       = true;
                $resultado['etapas_problema'][] = $lote->etapa_nombre;
            } elseif ($tieneDesbloqueado) {
                $resultado['desbloqueado'] = true;
            }

            if ($tieneConObs) {
                $resultado['con_obs'] = true;
            }
        }

        $resultado['ids_bloqueados']    = implode(', ', $idsBloquedosArr);
        $resultado['ids_desbloqueados'] = implode(', ', $idsDesbloqueadosArr);
        $resultado['ids_con_obs']       = implode(', ', $idsConObsArr);
        $resultado['desbloqueo_info']   = implode(' | ', $desbloqueoInfoArr);

        self::$validacionCache[$loteId] = $resultado;
        return $resultado;
    }
}
