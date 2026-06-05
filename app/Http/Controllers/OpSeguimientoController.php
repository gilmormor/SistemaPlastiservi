<?php

namespace App\Http\Controllers;

use App\Models\Seguridad\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Pantalla de seguimiento de Órdenes de Producción.
 * Permite al programador ver las OPs generadas, su progreso por etapa
 * y el estado de los registros de producción (temp + aprobados).
 */
class OpSeguimientoController extends Controller
{
    public function index()
    {
        can('listar-registro-produccion');
        return view('op.seguimiento');
    }

    public function page(Request $request)
    {
        can('listar-registro-produccion');

        $user = Usuario::findOrFail(auth()->id());
        $sucurArray = $user->sucursales->pluck('id')->toArray();
        $sucurcadena = implode(',', $sucurArray);

        // Filtros
        $condFecha = 'true';
        if (!empty($request->fechad) && !empty($request->fechah)) {
            $fd = date_format(date_create_from_format('d/m/Y', $request->fechad), 'Y-m-d') . ' 00:00:00';
            $fh = date_format(date_create_from_format('d/m/Y', $request->fechah), 'Y-m-d') . ' 23:59:59';
            $condFecha = "op.created_at >= '$fd' AND op.created_at <= '$fh'";
        }

        $condOp     = !empty($request->op_id)      ? "op.id = " . intval($request->op_id)                    : 'true';
        $condOt     = !empty($request->ot_id)      ? "ot.id = " . intval($request->ot_id)                    : 'true';
        $condNv     = !empty($request->nv_id)      ? "otnotaventa.notaventa_id = " . intval($request->nv_id) : 'true';
        $condProd   = !empty($request->producto_id) ? "otdet.producto_id = " . intval($request->producto_id)  : 'true';

        $sql = "
            SELECT
                op.id                           AS op_id,
                op.created_at                   AS op_fecha,
                op.kgprod                       AS op_kgprod,
                op.cantprod                     AS op_cantprod,
                op.prioridad,
                ot.id                           AS ot_id,
                otdet.id                        AS otdet_id,
                otdet.producto_id,
                -- Usa producto.glosa (campo pre-calculado del merge con rama producción).
                -- IFNULL garantiza compatibilidad si glosa aún está vacío en este entorno.
                IFNULL(producto.glosa, CONCAT('Prod. ', otdet.producto_id))
                                                AS producto_nombre,
                cliente.razonsocial,
                IFNULL(otnotaventa.notaventa_id, '') AS notaventa_id,

                -- Totales de producción aprobada (opdetregprod)
                IFNULL(SUM_PROD.total_kgprod,   0) AS total_kgprod_aprobado,
                IFNULL(SUM_PROD.total_cantprod,  0) AS total_cantprod_aprobado,

                -- Registros pendientes en temp (no enviados a aprobación: aprobstatus 0 o NULL)
                IFNULL(PEND_OP.cnt_noenviados,  0) AS pend_no_enviados,
                -- Registros en espera de aprobación supervisor (aprobstatus = 1)
                IFNULL(PEND_OP.cnt_esperando,   0) AS pend_esperando_sup,
                -- Registros rechazados (aprobstatus = 3)
                IFNULL(PEND_OP.cnt_rechazados,  0) AS pend_rechazados,

                -- Número de etapas de la OP
                COUNT(DISTINCT opdet.id) AS num_etapas,
                -- Etapa completa: (opdet.kgprod + opdet.kgscrap) >= op.kgprod
                -- kgprod + kgscrap = kgent total procesado (material bueno + scrap)
                COUNT(DISTINCT CASE
                    WHEN (opdet.kgprod + opdet.kgscrap) >= op.kgprod AND op.kgprod > 0
                    THEN opdet.id ELSE NULL END)     AS etapas_completadas,
                -- Etapa parcial: procesó algo pero aún no alcanza op.kgprod
                COUNT(DISTINCT CASE
                    WHEN (opdet.kgprod + opdet.kgscrap) > 0
                     AND (opdet.kgprod + opdet.kgscrap) < op.kgprod
                    THEN opdet.id ELSE NULL END)     AS etapas_parciales,

                UNIX_TIMESTAMP(op.updated_at)       AS updatednum_at

            FROM op

            INNER JOIN otdet          ON otdet.id         = op.otdet_id
            INNER JOIN ot             ON ot.id            = otdet.ot_id
            INNER JOIN cliente        ON cliente.id        = ot.cliente_id
            INNER JOIN opdet          ON opdet.op_id       = op.id
            LEFT  JOIN otnotaventa    ON otnotaventa.ot_id = ot.id
                                     AND ISNULL(otnotaventa.deleted_at)
            -- producto.glosa: nombre pre-calculado (rama producción master_20260513-01)
            LEFT  JOIN producto       ON producto.id = otdet.producto_id

            -- Suma de producción aprobada para esta OP
            LEFT JOIN (
                SELECT odrp.opdet_id,
                       SUM(odrp.kgprod)   AS total_kgprod,
                       SUM(odrp.cantprod) AS total_cantprod
                FROM   opdetregprod odrp
                WHERE  ISNULL(odrp.deleted_at)
                GROUP  BY odrp.opdet_id
            ) SUM_PROD ON SUM_PROD.opdet_id = opdet.id

            -- Pendientes en temp para esta OP
            LEFT JOIN (
                SELECT opd2.op_id,
                       SUM(CASE WHEN ISNULL(t.aprobstatus) OR t.aprobstatus = 0 THEN 1 ELSE 0 END) AS cnt_noenviados,
                       SUM(CASE WHEN t.aprobstatus = 1 THEN 1 ELSE 0 END)                          AS cnt_esperando,
                       SUM(CASE WHEN t.aprobstatus = 3 THEN 1 ELSE 0 END)                          AS cnt_rechazados
                FROM   opdetregprodtemp t
                INNER  JOIN opdet opd2 ON opd2.id = t.opdet_id
                WHERE  ISNULL(t.deleted_at)
                  AND  t.aprobstatus != 2
                GROUP  BY opd2.op_id
            ) PEND_OP ON PEND_OP.op_id = op.id

            WHERE ot.sucursal_id IN ($sucurcadena)
              AND ISNULL(op.deleted_at)
              AND ISNULL(otdet.deleted_at)
              AND ISNULL(ot.deleted_at)
              AND ISNULL(opdet.deleted_at)
              AND $condFecha
              AND $condOp
              AND $condOt
              AND $condNv
              AND $condProd

            GROUP BY op.id, otdet.id, cliente.id, otnotaventa.notaventa_id,
                     producto.glosa
            ORDER BY op.id DESC
        ";

        $datas = DB::select($sql);
        return datatables($datas)->toJson();
    }

    /**
     * Detalle por etapa de una OP (para el child row del DataTable).
     * Devuelve cada opdet con sus registros individuales de producción:
     *   - opdetregprodtemp: pendientes (aprobstatus 0/1/3)
     *   - opdetregprod: aprobados (aprobstatus 2)
     * GET /op/{op_id}/etapas-detalle
     */
    public function etapasDetalle($op_id)
    {
        can('listar-registro-produccion');
        $op_id = intval($op_id);

        // 1. Resumen por etapa (opdet)
        $etapas = DB::select("
            SELECT
                opdet.id                                AS opdet_id,
                etapaprod.nombre                        AS etapa_nombre,
                areaproduccionsucetapaprod.orden        AS etapa_orden,
                IFNULL(maquina.nombre, '—')             AS maquina_nombre,
                opdet.kgrec                             AS opdet_kgrec,
                opdet.kgprod                            AS opdet_kgprod,
                opdet.kgscrap                           AS opdet_kgscrap,
                opdet.saldokg                           AS opdet_saldokg,
                op.kgprod                               AS op_kgprod,
                IFNULL(PROD.total_kgprod,  0)           AS kgprod_aprobado,
                IFNULL(PROD.total_kgscrap, 0)           AS kgscrap_aprobado,
                IFNULL(PROD.cnt,           0)           AS cnt_aprobados,
                IFNULL(TEMP.cnt_noenviados,0)           AS temp_no_enviados,
                IFNULL(TEMP.cnt_esperando, 0)           AS temp_esperando_sup,
                IFNULL(TEMP.cnt_rechazados,0)           AS temp_rechazados
            FROM opdet
            INNER JOIN op ON op.id = opdet.op_id
            INNER JOIN areaproduccionsucetapaprod
                   ON areaproduccionsucetapaprod.id = opdet.apsucetapaprod_id
            INNER JOIN etapaprod
                   ON etapaprod.id = areaproduccionsucetapaprod.etapaprod_id
            LEFT  JOIN opdetmaquina ON opdetmaquina.opdet_id = opdet.id
            LEFT  JOIN maquina      ON maquina.id = opdetmaquina.maquina_id
            LEFT  JOIN (
                SELECT opdet_id,
                       SUM(kgprod)  AS total_kgprod,
                       SUM(kgscrap) AS total_kgscrap,
                       COUNT(*)     AS cnt
                FROM   opdetregprod WHERE ISNULL(deleted_at) GROUP BY opdet_id
            ) PROD ON PROD.opdet_id = opdet.id
            LEFT  JOIN (
                SELECT opdet_id,
                       SUM(CASE WHEN ISNULL(aprobstatus) OR aprobstatus=0 THEN 1 ELSE 0 END) AS cnt_noenviados,
                       SUM(CASE WHEN aprobstatus=1 THEN 1 ELSE 0 END)                        AS cnt_esperando,
                       SUM(CASE WHEN aprobstatus=3 THEN 1 ELSE 0 END)                        AS cnt_rechazados
                FROM   opdetregprodtemp
                WHERE  ISNULL(deleted_at) AND (aprobstatus IS NULL OR aprobstatus != 2)
                GROUP  BY opdet_id
            ) TEMP ON TEMP.opdet_id = opdet.id
            WHERE opdet.op_id = $op_id AND ISNULL(opdet.deleted_at)
            ORDER BY areaproduccionsucetapaprod.orden ASC
        ");

        // 2. Registros individuales en temp (pendientes: aprobstatus 0/NULL/1/3)
        $temps = DB::select("
            SELECT
                t.id,
                t.opdet_id,
                t.aprobstatus,
                t.kgprod,
                t.kgscrap,
                t.kgent,
                t.cantprod,
                t.aprobobs,
                t.created_at,
                IFNULL(operario.nombre, '—') AS operario_nombre,
                IFNULL(usuario.nombre, '—')  AS usuario_nombre
            FROM opdetregprodtemp t
            INNER JOIN opdet od ON od.id = t.opdet_id
            LEFT  JOIN operario ON operario.id = t.operario_id
            LEFT  JOIN usuario  ON usuario.id  = t.usuario_id
            WHERE od.op_id = $op_id
              AND ISNULL(t.deleted_at)
              AND (t.aprobstatus IS NULL OR t.aprobstatus != 2)
            ORDER BY t.opdet_id ASC, t.id ASC
        ");

        // 3. Registros aprobados en opdetregprod
        $aprobados = DB::select("
            SELECT
                r.id,
                r.opdet_id,
                r.kgprod,
                r.kgscrap,
                r.kgent,
                r.cantprod,
                r.created_at,
                IFNULL(operario.nombre, '—') AS operario_nombre,
                IFNULL(usuario.nombre, '—')  AS usuario_nombre
            FROM opdetregprod r
            INNER JOIN opdet od ON od.id = r.opdet_id
            LEFT  JOIN operario ON operario.id = r.operario_id
            LEFT  JOIN usuario  ON usuario.id  = r.usuario_id
            WHERE od.op_id = $op_id
              AND ISNULL(r.deleted_at)
            ORDER BY r.opdet_id ASC, r.id ASC
        ");

        // Indexar registros por opdet_id para adjuntarlos en el response
        $tempsXOpdet = [];
        foreach ($temps as $t) {
            $tempsXOpdet[$t->opdet_id][] = $t;
        }
        $aprobXOpdet = [];
        foreach ($aprobados as $a) {
            $aprobXOpdet[$a->opdet_id][] = $a;
        }

        // Adjuntar registros a cada etapa
        foreach ($etapas as &$etapa) {
            $etapa->registros_temp    = $tempsXOpdet[$etapa->opdet_id]   ?? [];
            $etapa->registros_aprobados = $aprobXOpdet[$etapa->opdet_id] ?? [];
        }

        return response()->json($etapas);
    }
}
