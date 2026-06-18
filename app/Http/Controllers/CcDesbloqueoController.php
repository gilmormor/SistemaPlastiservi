<?php

namespace App\Http\Controllers;

use App\Models\CcRegistMuestra;
use App\Models\CcRegistMuestraDesbloqueo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CcDesbloqueoController extends Controller
{
    /**
     * Listado de muestras CC rechazadas — gestión de desbloqueos.
     */
    public function index()
    {
        can('listar-cc-muestra-desbloqueo');
        return view('ccdesbloqueo.index');
    }

    /**
     * DataTable AJAX: muestras status=3 aprobadas por supervisor (sta_env=2).
     * Incluye tanto pendientes de desbloqueo como ya desbloqueadas (historial completo).
     */
    public function page()
    {
        can('listar-cc-muestra-desbloqueo');

        $datas = DB::select("
            SELECT
                ccm.id,
                ccm.created_at,
                ccm.updated_at,
                ccm.fechahora,
                ccm.status,
                ccm.sta_env,
                ccm.observacion,
                odrp.id          AS opdetregprod_id,
                odrp.kgprod,
                odrp.cantprod,
                um.nombre        AS unidadmedidasal_nombre,
                ep.nombre        AS etapaprod_nombre,
                prod.nombre      AS producto_nombre,
                prod.id          AS producto_id,
                op.id            AS op_id,
                ot.id            AS ot_id,
                CONCAT('OP ', op.id, ' / OT ', ot.id) AS op_ot,
                nv.id                    AS notaventa_id,
                at.id                    AS acuerdotecnico_id,
                at.at_impresofoto,
                IF(desb.id IS NOT NULL, 1, 0)  AS desbloqueado,
                desb.observacion               AS desbloqueo_obs,
                usuariodesb.nombre             AS desbloqueo_usuario,
                desb.created_at                AS desbloqueo_fecha
            FROM  ccregistmuestra ccm
            INNER JOIN opdetregprod   odrp  ON odrp.id  = ccm.opdetregprod_id
            INNER JOIN etapaprod      ep    ON ep.id    = odrp.etapaprod_id
            INNER JOIN producto       prod  ON prod.id  = odrp.producto_id
            INNER JOIN opdet                ON opdet.id = odrp.opdet_id
            INNER JOIN op                   ON op.id    = opdet.op_id
            INNER JOIN otdet                ON otdet.id = op.otdet_id
            INNER JOIN ot                   ON ot.id    = otdet.ot_id
            LEFT  JOIN unidadmedida um      ON um.id    = odrp.unidadmedidasal_id
            LEFT  JOIN otnotaventa otnv     ON otnv.ot_id = ot.id AND otnv.deleted_at IS NULL
            LEFT  JOIN notaventa   nv       ON nv.id = otnv.notaventa_id AND nv.deleted_at IS NULL
            LEFT  JOIN acuerdotecnico at    ON at.producto_id = odrp.producto_id
            LEFT  JOIN ccregistmuestra_desbloqueo desb        ON desb.ccregistmuestra_id = ccm.id
            LEFT  JOIN usuario                   usuariodesb  ON usuariodesb.id = desb.usuario_id
            LEFT  JOIN ccregistmuestraanul       anul         ON anul.ccregistmuestra_id = ccm.id
            WHERE  ccm.deleted_at IS NULL
              AND  ccm.status   = 3
              AND  ccm.sta_env  = 2
              AND  anul.id IS NULL
            ORDER  BY desb.id IS NOT NULL ASC, ccm.id DESC
        ");

        return datatables()->collection(collect($datas))->toJson();
    }

    /**
     * Desbloquea una muestra CC rechazada (status=3) para permitir su despacho.
     * Inserta registro en ccregistmuestra_desbloqueo. Responde JSON (AJAX).
     */
    public function desbloquear(Request $request, $id)
    {
        can('guardar-cc-muestra-desbloqueo');

        $muestra = CcRegistMuestra::findOrFail($id);

        if ($muestra->status != 3) {
            return response()->json(['ok' => false, 'msg' => 'Solo se pueden desbloquear muestras con status Rechazado.']);
        }
        if ($muestra->desbloqueo) {
            return response()->json(['ok' => false, 'msg' => 'La muestra #' . $id . ' ya fue desbloqueada.']);
        }

        $request->validate(['observacion' => 'required|string|max:500']);

        CcRegistMuestraDesbloqueo::create([
            'ccregistmuestra_id' => $id,
            'observacion'        => $request->observacion,
            'usuario_id'         => auth()->id(),
        ]);

        return response()->json(['ok' => true, 'msg' => 'Muestra #' . $id . ' desbloqueada correctamente.']);
    }
}
