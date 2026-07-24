<?php

namespace App\Http\Controllers;

use App\Models\CcRegistMuestra;
use App\Models\CcRegistMuestraDesbloqueo;
use App\Models\EtapaProd;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CcDesbloqueoController extends Controller
{
    /**
     * Listado de muestras CC rechazadas — gestión de desbloqueos.
     * R5: pasa máquinas y etapaprods para los selects de filtro.
     */
    public function index()
    {
        can('listar-cc-muestra-desbloqueo');

        $etapaprods = EtapaProd::orderBy('nombre')->get();

        // R5: máquinas que participan en registros de producción con muestras rechazadas
        $maquinas = DB::select("
            SELECT DISTINCT maq.id, maq.nombre
            FROM   maquina maq
            INNER JOIN opdetmaquina odm  ON odm.maquina_id = maq.id
            INNER JOIN opdet             ON opdet.id = odm.opdet_id
            INNER JOIN opdetregprod odrp ON odrp.opdet_id = opdet.id AND odrp.deleted_at IS NULL
            INNER JOIN ccregistmuestra ccm ON ccm.opdetregprod_id = odrp.id
                AND ccm.status = 3 AND ccm.sta_env = 2 AND ccm.deleted_at IS NULL
            WHERE maq.deleted_at IS NULL
            ORDER BY maq.nombre
        ");

        return view('ccdesbloqueo.index', compact('etapaprods', 'maquinas'));
    }

    /**
     * DataTable AJAX: muestras status=3 aprobadas por supervisor (sta_env=2).
     * R5: acepta filtros dinámicos vía Request.
     */
    public function page(Request $request)
    {
        can('listar-cc-muestra-desbloqueo');

        $params = [];
        $where  = "
            WHERE  ccm.deleted_at IS NULL
              AND  ccm.status   = 3
              AND  ccm.sta_env  = 2
              AND  anul.id IS NULL
        ";

        // Filtro: RUT cliente (strip puntos/guión, búsqueda parcial)
        if ($request->filled('rut')) {
            $rutLimpio = preg_replace('/[^a-zA-Z0-9]/', '', $request->rut);
            $where .= " AND REPLACE(REPLACE(cl.rut, '.', ''), '-', '') LIKE ?";
            $params[] = '%' . $rutLimpio . '%';
        }

        // Filtro: ID lote / reg. prod.
        if ($request->filled('opdetregprod_id')) {
            $where .= " AND odrp.id = ?";
            $params[] = intval($request->opdetregprod_id);
        }

        // Filtro: Fecha desde
        if ($request->filled('fecha_desde')) {
            $fd = Carbon::createFromFormat('d/m/Y', $request->fecha_desde)->startOfDay();
            $where .= " AND ccm.fechahora >= ?";
            $params[] = $fd->format('Y-m-d H:i:s');
        }

        // Filtro: Fecha hasta
        if ($request->filled('fecha_hasta')) {
            $fh = Carbon::createFromFormat('d/m/Y', $request->fecha_hasta)->endOfDay();
            $where .= " AND ccm.fechahora <= ?";
            $params[] = $fh->format('Y-m-d H:i:s');
        }

        // Filtro: N° OT
        if ($request->filled('ot_id')) {
            $where .= " AND ot.id = ?";
            $params[] = intval($request->ot_id);
        }

        // Filtro: N° OP
        if ($request->filled('op_id')) {
            $where .= " AND op.id = ?";
            $params[] = intval($request->op_id);
        }

        // Filtro: Máquina (vía opdetmaquina — tabla sin deleted_at)
        if ($request->filled('maquina_id')) {
            $where .= " AND EXISTS (
                SELECT 1 FROM opdetmaquina odm2
                WHERE odm2.opdet_id = odrp.opdet_id
                  AND odm2.maquina_id = ?
            )";
            $params[] = intval($request->maquina_id);
        }

        // Filtro: N° Nota de Venta
        if ($request->filled('notaventa_id')) {
            $where .= " AND nv.id = ?";
            $params[] = intval($request->notaventa_id);
        }

        // Filtro: Producto (puede ser lista separada por coma)
        if ($request->filled('producto_idPxP')) {
            $ids = array_values(array_filter(array_map('intval', explode(',', $request->producto_idPxP))));
            if (!empty($ids)) {
                $placeholders = implode(',', array_fill(0, count($ids), '?'));
                $where .= " AND odrp.producto_id IN ($placeholders)";
                $params = array_merge($params, $ids);
            }
        }

        // Filtro: Etapa producción
        if ($request->filled('etapaprod_id')) {
            $where .= " AND ep.id = ?";
            $params[] = intval($request->etapaprod_id);
        }

        // Filtro: Status desbloqueo (1=desbloqueado, 0=bloqueado)
        if ($request->filled('sta_desbloqueo')) {
            if ($request->sta_desbloqueo == '1') {
                $where .= " AND desb.id IS NOT NULL";
            } elseif ($request->sta_desbloqueo == '0') {
                $where .= " AND desb.id IS NULL";
            }
        }

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
                cl.razonsocial           AS cliente_nombre,
                cl.rut                   AS cliente_rut,
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
            LEFT  JOIN cliente     cl       ON cl.id = nv.cliente_id AND cl.deleted_at IS NULL
            LEFT  JOIN acuerdotecnico at    ON at.producto_id = odrp.producto_id
            LEFT  JOIN ccregistmuestra_desbloqueo desb        ON desb.ccregistmuestra_id = ccm.id
            LEFT  JOIN usuario                   usuariodesb  ON usuariodesb.id = desb.usuario_id
            LEFT  JOIN ccregistmuestraanul       anul         ON anul.ccregistmuestra_id = ccm.id
            $where
            ORDER BY desb.id IS NOT NULL ASC, ccm.id DESC
        ", $params);

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
