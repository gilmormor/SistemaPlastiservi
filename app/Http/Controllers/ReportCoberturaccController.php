<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\EtapaProd;
use App\Models\Seguridad\Usuario;
use App\Models\Sucursal;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportCoberturaccController extends Controller
{
    public function index()
    {
        can('reporte-cobertura-cc');

        $etapaprods = EtapaProd::orderBy('nombre')->get();

        $operarios = DB::select("
            SELECT DISTINCT op.id, op.nombre
            FROM   operario op
            INNER JOIN opdetregprod odrp ON odrp.operario_id = op.id
            WHERE  ISNULL(op.deleted_at)
              AND  odrp.aprobstatus = 2
              AND  ISNULL(odrp.deleted_at)
            ORDER  BY op.nombre
        ");

        $maquinas = DB::select("
            SELECT DISTINCT maq.id, maq.nombre
            FROM   maquina maq
            INNER JOIN opdetmaquina odm  ON odm.maquina_id = maq.id
            INNER JOIN opdetregprod odrp ON odrp.opdet_id = odm.opdet_id
            WHERE  ISNULL(maq.deleted_at)
              AND  odrp.aprobstatus = 2
              AND  ISNULL(odrp.deleted_at)
            ORDER  BY maq.nombre
        ");

        return view('reportcoberturacc.index', compact('etapaprods', 'operarios', 'maquinas'));
    }

    public function page(Request $request)
    {
        can('reporte-cobertura-cc');
        $datas = $this->consultaSql($request);
        return datatables()->collection(collect($datas))->toJson();
    }

    public function exportPdf(Request $request)
    {
        can('reporte-cobertura-cc');
        $datas   = $this->consultaSql($request);
        $empresa = Empresa::orderBy('id')->get();
        $usuario = Usuario::findOrFail(auth()->id());

        if (env('APP_DEBUG')) {
            return view('reportcoberturacc.listado', compact('datas', 'empresa', 'usuario', 'request'));
        }

        $pdf = PDF::loadView('reportcoberturacc.listado', compact('datas', 'empresa', 'usuario', 'request'))
                  ->setPaper('a4', 'landscape');
        return $pdf->stream('ReporteCC_Cobertura.pdf');
    }

    public function totalizarindex(Request $request)
    {
        $datas        = $this->consultaSql($request);
        $total        = count($datas);
        $conCobertura = 0;
        $sinCobertura = 0;

        foreach ($datas as $d) {
            if ($d->con_muestra) $conCobertura++;
            else                  $sinCobertura++;
        }

        $pct = $total > 0 ? round($conCobertura / $total * 100, 1) : 0;

        return response()->json(compact('total', 'conCobertura', 'sinCobertura', 'pct'));
    }

    /**
     * Consulta base: lotes aprobados (aprobstatus=2) con conteo de muestras CC válidas (no anuladas).
     * Un lote está "cubierto" si tiene al menos 1 muestra CC no anulada (status 1, 2, 3 o 5).
     */
    private function consultaSql(Request $request)
    {
        $where  = ["odrp.aprobstatus = 2", "odrp.deleted_at IS NULL"];
        $params = [];
        $having = [];

        if ($request->fecha_desde) {
            $where[]  = "odrp.aprobfechahora >= ?";
            $params[] = $request->fecha_desde . ' 00:00:00';
        }
        if ($request->fecha_hasta) {
            $where[]  = "odrp.aprobfechahora <= ?";
            $params[] = $request->fecha_hasta . ' 23:59:59';
        }
        if ($request->etapaprod_id) {
            $where[]  = "odrp.etapaprod_id = ?";
            $params[] = $request->etapaprod_id;
        }
        if ($request->operario_id) {
            $where[]  = "odrp.operario_id = ?";
            $params[] = $request->operario_id;
        }
        if ($request->maquina_id) {
            $where[]  = "odm.maquina_id = ?";
            $params[] = $request->maquina_id;
        }

        // Filtro cobertura: 1=solo con muestra, 0=solo sin muestra
        if ($request->cobertura === '1') {
            $having[] = "total_muestras > 0";
        } elseif ($request->cobertura === '0') {
            $having[] = "total_muestras = 0";
        }

        $whereStr  = implode(' AND ', $where);
        $havingStr = !empty($having) ? 'HAVING ' . implode(' AND ', $having) : '';

        return DB::select("
            SELECT
                odrp.id,
                odrp.kgprod,
                odrp.cantprod,
                odrp.aprobfechahora,
                ep.id            AS etapaprod_id,
                ep.nombre        AS etapaprod_nombre,
                prod.id          AS producto_id,
                prod.nombre      AS producto_nombre,
                op.id            AS op_id,
                ot.id            AS ot_id,
                um.nombre        AS unidadmedidasal_nombre,
                oper.nombre      AS operario_nombre,
                maq.nombre       AS maquina_nombre,
                COUNT(ccmv.id)   AS total_muestras,
                IF(COUNT(ccmv.id) > 0, 1, 0) AS con_muestra
            FROM  opdetregprod odrp
            INNER JOIN etapaprod ep     ON ep.id    = odrp.etapaprod_id
            INNER JOIN producto prod    ON prod.id  = odrp.producto_id
            INNER JOIN opdet            ON opdet.id = odrp.opdet_id
            INNER JOIN op               ON op.id    = opdet.op_id
            INNER JOIN otdet            ON otdet.id = op.otdet_id
            INNER JOIN ot               ON ot.id    = otdet.ot_id
            LEFT  JOIN unidadmedida um  ON um.id    = odrp.unidadmedidasal_id
            LEFT  JOIN operario oper    ON oper.id  = odrp.operario_id
            LEFT  JOIN opdetmaquina odm ON odm.opdet_id = odrp.opdet_id
            LEFT  JOIN maquina maq      ON maq.id   = odm.maquina_id
            LEFT  JOIN (
                SELECT ccm.id, ccm.opdetregprod_id
                FROM   ccregistmuestra ccm
                LEFT   JOIN ccregistmuestraanul ccma ON ccma.ccregistmuestra_id = ccm.id
                WHERE  ccm.deleted_at IS NULL
                  AND  ccma.id IS NULL
            ) ccmv ON ccmv.opdetregprod_id = odrp.id
            WHERE  $whereStr
            GROUP  BY odrp.id
            $havingStr
            ORDER  BY odrp.id DESC
        ", $params);
    }
}
