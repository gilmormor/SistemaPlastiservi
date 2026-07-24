<?php

namespace App\Http\Controllers;

use App\Models\CcRegistMuestra;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CcRegistMuestraSupervisarController extends Controller
{
    /**
     * Pantalla del supervisor: lista muestras con sta_env=1 pendientes de revisión.
     */
    public function index()
    {
        can('supervisar-ccregistmuestra');
        return view('ccregistmuestra.supervisar');
    }

    /**
     * DataTable server-side para la pantalla de supervisión (sta_env=1).
     */
    public function page()
    {
        can('supervisar-ccregistmuestra');
        $datas = DB::select("
            SELECT
                ccm.id,
                ccm.updated_at,
                ccm.fechahora,
                ccm.status,
                ccm.sta_env,
                ccm.observacion,
                odrp.id          AS opdetregprod_id,
                odrp.kgprod,
                ep.nombre        AS etapaprod_nombre,
                prod.nombre      AS producto_nombre,
                op.id            AS op_id,
                ot.id            AS ot_id,
                CONCAT('OP ', op.id, ' / OT ', ot.id) AS op_ot,
                usuarioreg.nombre        AS usuario_nombre,
                nv.id                    AS notaventa_id,
                odrp.producto_id,
                at.id                    AS acuerdotecnico_id,
                at.at_impresofoto
            FROM  ccregistmuestra ccm
            INNER JOIN opdetregprod   odrp  ON odrp.id  = ccm.opdetregprod_id
            INNER JOIN etapaprod      ep    ON ep.id    = odrp.etapaprod_id
            INNER JOIN producto       prod  ON prod.id  = odrp.producto_id
            INNER JOIN opdet                ON opdet.id = odrp.opdet_id
            INNER JOIN op                   ON op.id    = opdet.op_id
            INNER JOIN otdet                ON otdet.id = op.otdet_id
            INNER JOIN ot                   ON ot.id    = otdet.ot_id
            INNER JOIN usuario usuarioreg   ON usuarioreg.id = ccm.usuario_id
            LEFT  JOIN otnotaventa otnv     ON otnv.ot_id = ot.id AND otnv.deleted_at IS NULL
            LEFT  JOIN notaventa   nv       ON nv.id = otnv.notaventa_id AND nv.deleted_at IS NULL
            LEFT  JOIN acuerdotecnico at    ON at.producto_id = odrp.producto_id
            WHERE  ccm.deleted_at IS NULL
              AND  ccm.sta_env = 1
            ORDER  BY ccm.id ASC
        ");
        return datatables()->collection(collect($datas))->toJson();
    }

    /**
     * Acción del supervisor: aprobar (sta_env=2) o rechazar (sta_env=3) una muestra.
     */
    public function actuar(Request $request, $id)
    {
        can('supervisar-ccregistmuestra');
        $muestra = CcRegistMuestra::whereNull('deleted_at')->findOrFail($id);

        if ($muestra->sta_env != 1) {
            return response()->json(['ok' => false, 'msg' => 'La muestra #' . $id . ' no está pendiente de supervisión.']);
        }
        // Optimistic locking: evitar sobreescritura si otro usuario modificó el registro
        if ($request->has('updated_at') && $muestra->updated_at->timestamp != (int) $request->updated_at) {
            return response()->json(['ok' => false, 'msg' => 'La muestra #' . $id . ' fue modificada por otro usuario. Recargue el listado.']);
        }

        $accion = $request->accion; // 'aprobar' | 'rechazar'

        if ($accion === 'rechazar') {
            if (!$request->obs || trim($request->obs) === '') {
                return response()->json(['ok' => false, 'msg' => 'Debe ingresar la observación del rechazo.']);
            }
            $muestra->sta_env     = 3;
            $muestra->sta_env_obs = trim($request->obs);
        } elseif ($accion === 'aprobar') {
            $muestra->sta_env     = 2;
            $muestra->sta_env_obs = null;
        } else {
            return response()->json(['ok' => false, 'msg' => 'Acción no válida.']);
        }

        $muestra->fechahora_env    = now()->format('Y-m-d H:i:s');
        $muestra->usuariostaenv_id = auth()->id();
        $muestra->save();

        $msg = $accion === 'aprobar'
            ? 'Muestra #' . $id . ' aprobada por supervisor.'
            : 'Muestra #' . $id . ' rechazada por supervisor.';

        return response()->json(['ok' => true, 'msg' => $msg]);
    }
}
