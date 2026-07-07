<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\EtapaProd;
use App\Models\Seguridad\Usuario;
use App\Models\Sucursal;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportccMuestraController extends Controller
{
    public function index()
    {
        can('listar-reporte-cc-muestra');

        $etapaprods = EtapaProd::orderBy('nombre')->get();

        $operarios = DB::select("
            SELECT DISTINCT op.id, op.nombre
            FROM   operario op
            INNER JOIN opdetregprod odrp ON odrp.operario_id = op.id
            WHERE  ISNULL(op.deleted_at)
            ORDER  BY op.nombre
        ");

        $sucursales = Sucursal::orderBy('id')->get();

        // Solo máquinas que tienen al menos una muestra CC registrada
        $maquinas = DB::select("
            SELECT DISTINCT maq.id, maq.nombre
            FROM   maquina maq
            INNER JOIN opdetmaquina odm  ON odm.maquina_id = maq.id
            INNER JOIN opdet             ON opdet.id = odm.opdet_id
            INNER JOIN opdetregprod odrp ON odrp.opdet_id = opdet.id
            INNER JOIN ccregistmuestra ccm ON ccm.opdetregprod_id = odrp.id
            WHERE  ISNULL(maq.deleted_at)
            ORDER  BY maq.nombre
        ");

        $selecmultprod = true; // Habilita la selección múltiple de productos en el modal
        return view('reportccmuestra.index', compact('etapaprods', 'operarios', 'sucursales', 'maquinas', 'selecmultprod'));
    }

    /**
     * DataTable AJAX — consulta principal de muestras CC con todos los filtros.
     */
    public function page(Request $request)
    {
        can('listar-reporte-cc-muestra');
        $datas = $this->consultaSql($request);
        return datatables()->collection(collect($datas))->toJson();
    }

    public function exportPdf(Request $request)
    {
        can('listar-reporte-cc-muestra');
        $datas   = $this->consultaSql($request);
        $empresa = Empresa::orderBy('id')->get();
        $usuario = Usuario::findOrFail(auth()->id());

        if (env('APP_DEBUG')) {
            return view('reportccmuestra.listado', compact('datas', 'empresa', 'usuario', 'request'));
        }

        $pdf = PDF::loadView('reportccmuestra.listado', compact('datas', 'empresa', 'usuario', 'request'))
                  ->setPaper('a4', 'landscape');
        return $pdf->stream('ReporteCC_Muestras.pdf');
    }

    public function totalizarindex(Request $request)
    {
        $datas         = $this->consultaSql($request);
        $total         = count($datas);
        $aprobados     = 0;
        $conObs        = 0;
        $rechazados    = 0;
        $anulados      = 0;
        $desbloqueados = 0;

        foreach ($datas as $d) {
            if ($d->anulado)          $anulados++;
            elseif ($d->status == 1)  $aprobados++;
            elseif ($d->status == 2)  $conObs++;
            elseif ($d->status == 3)  $rechazados++;
            if ($d->desbloqueado)     $desbloqueados++;
        }

        return response()->json(compact('total', 'aprobados', 'conObs', 'rechazados', 'anulados', 'desbloqueados'));
    }

    /**
     * Query SQL centralizada usada por page(), exportPdf() y totalizarindex().
     */
    private function consultaSql(Request $request)
    {
        $where  = ["ccm.deleted_at IS NULL"];
        $params = [];

        if ($request->fecha_desde) {
            $where[]  = "ccm.fechahora >= ?";
            $params[] = $request->fecha_desde . ' 00:00:00';
        }
        if ($request->fecha_hasta) {
            $where[]  = "ccm.fechahora <= ?";
            $params[] = $request->fecha_hasta . ' 23:59:59';
        }
        if ($request->sucursal_id) {
            $where[]  = "odrp.sucursal_id = ?";
            $params[] = $request->sucursal_id;
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
        if ($request->status) {
            $where[]  = "ccm.status = ?";
            $params[] = $request->status;
        }
        if ($request->sta_env !== null && $request->sta_env !== '') {
            $where[]  = "ccm.sta_env = ?";
            $params[] = $request->sta_env;
        }
        if ($request->cliente_rut) {
            // Comparar sin guion para que funcione independiente del formato almacenado
            $rutSinGuion = str_replace('-', '', $request->cliente_rut);
            $where[]  = "REPLACE(cli.rut, '-', '') LIKE ?";
            $params[] = '%' . $rutSinGuion . '%';
        }
        if (!empty($request->producto_idPxP)) {
            // Acepta uno o varios IDs separados por coma (igual que reportinvmov)
            $codprods = implode(',', array_map('intval', explode(',', $request->producto_idPxP)));
            $where[]  = "odrp.producto_id IN ($codprods)";
        }
        // Filtro anulado: 1=solo anulados, 0=solo no anulados, vacío=todos
        if ($request->anulado === '1') {
            $where[] = "anul.id IS NOT NULL";
        } elseif ($request->anulado === '0') {
            $where[] = "anul.id IS NULL";
        }
        // Filtro desbloqueado: 1=solo desbloqueados, 0=solo no desbloqueados, vacío=todos
        if ($request->desbloqueado === '1') {
            $where[] = "desb.id IS NOT NULL";
        } elseif ($request->desbloqueado === '0') {
            $where[] = "desb.id IS NULL";
        }

        $whereStr = implode(' AND ', $where);

        return DB::select("
            SELECT
                ccm.id,
                ccm.fechahora,
                ccm.status,
                ccm.sta_env,
                ccm.sta_env_obs,
                ccm.observacion,
                odrp.id          AS opdetregprod_id,
                odrp.kgprod,
                odrp.cantprod,
                odrp.sucursal_id,
                ep.nombre        AS etapaprod_nombre,
                prod.id          AS producto_id,
                prod.nombre      AS producto_nombre,
                at.id            AS acuerdotecnico_id,
                at.at_impresofoto,
                op.id            AS op_id,
                ot.id            AS ot_id,
                um.nombre        AS unidadmedidasal_nombre,
                sucur.nombre     AS sucursal_nombre,
                nv.id            AS notaventa_id,
                cli.rut          AS cliente_rut,
                cli.razonsocial  AS cliente_razonsocial,
                oper.nombre      AS operario_nombre,
                maq.nombre       AS maquina_nombre,
                usuarioreg.nombre    AS usuario_nombre,
                usuariostaenv.nombre AS usuariostaenv_nombre,
                IF(anul.id IS NOT NULL, 1, 0)  AS anulado,
                anul.motivo                    AS anulacion_motivo,
                usuarioanul.nombre             AS anulacion_usuario,
                anul.created_at                AS anulacion_fecha,
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
            LEFT  JOIN sucursal sucur       ON sucur.id = odrp.sucursal_id
            LEFT  JOIN unidadmedida um      ON um.id    = odrp.unidadmedidasal_id
            LEFT  JOIN operario oper        ON oper.id  = odrp.operario_id
            LEFT  JOIN acuerdotecnico at    ON at.producto_id = odrp.producto_id
            LEFT  JOIN opdetmaquina odm     ON odm.opdet_id = odrp.opdet_id
            LEFT  JOIN maquina maq          ON maq.id = odm.maquina_id
            LEFT  JOIN usuario usuarioreg   ON usuarioreg.id = ccm.usuario_id
            LEFT  JOIN usuario usuariostaenv ON usuariostaenv.id = ccm.usuariostaenv_id
            LEFT  JOIN otnotaventa otnv     ON otnv.ot_id = ot.id AND otnv.deleted_at IS NULL
            LEFT  JOIN notaventa   nv       ON nv.id = otnv.notaventa_id AND nv.deleted_at IS NULL
            LEFT  JOIN cliente     cli      ON cli.id = nv.cliente_id
            LEFT  JOIN ccregistmuestraanul       anul        ON anul.ccregistmuestra_id = ccm.id
            LEFT  JOIN usuario               usuarioanul     ON usuarioanul.id = anul.usuario_id
            LEFT  JOIN ccregistmuestra_desbloqueo desb       ON desb.ccregistmuestra_id = ccm.id
            LEFT  JOIN usuario               usuariodesb     ON usuariodesb.id = desb.usuario_id
            WHERE $whereStr
            ORDER BY ccm.id DESC
        ", $params);
    }
}
