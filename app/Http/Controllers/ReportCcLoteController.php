<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\Seguridad\Usuario;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Http\Request;
use App\Models\CcValidacion;
use Illuminate\Support\Facades\DB;

class ReportCcLoteController extends Controller
{
    public function index()
    {
        can('reporte-cc-lote');
        return view('reportcclote.index');
    }

    // Endpoint usado tanto por el modal AJAX de despachosol como por la página standalone.
    // Sin permiso propio: cualquier usuario autenticado puede consultarlo (ya tiene acceso al despacho).
    public function consultar(Request $request)
    {
        $loteId = (int)$request->opdetregprod_id;
        if (!$loteId) {
            return response('<div class="alert alert-warning">Ingrese un número de lote válido.</div>', 422);
        }

        $data = $this->obtenerDatos($loteId);

        if (isset($data['error'])) {
            return response('<div class="alert alert-danger">' . e($data['error']) . '</div>', 404);
        }

        return view('reportcclote.detalle', $data);
    }

    public function exportPdf(Request $request)
    {
        can('reporte-cc-lote');
        $loteId = (int)$request->opdetregprod_id;
        if (!$loteId) {
            abort(422, 'ID de lote requerido.');
        }

        $data    = $this->obtenerDatos($loteId);
        if (isset($data['error'])) {
            abort(404, $data['error']);
        }

        $empresa = Empresa::orderBy('id')->get();
        $usuario = Usuario::findOrFail(auth()->id());

        if (env('APP_DEBUG') && request()->has('html')) {
            return view('reportcclote.listado', array_merge($data, compact('empresa', 'usuario')));
        }

        $pdf = PDF::loadView('reportcclote.listado', array_merge($data, compact('empresa', 'usuario')))
                  ->setPaper('a4', 'portrait');
        return $pdf->stream('CC_Lote_' . $loteId . '.pdf');
    }

    private function obtenerDatos(int $loteId): array
    {
        // Datos del lote ingresado
        $lote = DB::selectOne("
            SELECT
                odrp.id, odrp.opdet_id, odrp.producto_id, odrp.sucursal_id, odrp.etapaprod_id,
                prod.nombre      AS producto_nombre,
                prod.codintprod  AS producto_codigo,
                op.id            AS op_id,
                ot.id            AS ot_id
            FROM opdetregprod odrp
            INNER JOIN producto prod ON prod.id  = odrp.producto_id
            INNER JOIN opdet         ON opdet.id = odrp.opdet_id
            INNER JOIN op            ON op.id    = opdet.op_id
            INNER JOIN otdet         ON otdet.id = op.otdet_id
            INNER JOIN ot            ON ot.id    = otdet.ot_id
            WHERE odrp.id = ?
        ", [$loteId]);

        if (!$lote) {
            return ['error' => "No se encontró el lote #$loteId."];
        }

        // BFS bidireccional centralizado en CcValidacion::bfsLotesCadena()
        $todosLoteIds = CcValidacion::bfsLotesCadena($loteId);

        // Etapas configuradas para este producto y sucursal (vía acuerdo técnico)
        $etapasConfig = DB::select("
            SELECT
                apse.id             AS apsucetapaprod_id,
                apse.etapaprod_id,
                ep.nombre           AS etapaprod_nombre,
                apse.orden,
                apse.requiere_cc
            FROM acuerdotecnicoapsucetapaprod atap
            INNER JOIN acuerdotecnico            at2  ON at2.id   = atap.acuerdotecnico_id
            INNER JOIN areaproduccionsucetapaprod apse ON apse.id  = atap.apsucetapaprod_id
            INNER JOIN areaproduccionsuc          apsu ON apsu.id  = apse.areaproduccionsuc_id
            INNER JOIN etapaprod                  ep   ON ep.id    = apse.etapaprod_id
            WHERE at2.producto_id = ?
              AND apsu.sucursal_id = ?
            ORDER BY apse.orden
        ", [$lote->producto_id, $lote->sucursal_id]);

        // Lotes reales encontrados por BFS
        $ph          = implode(',', $todosLoteIds);
        $lotesReales = DB::select("
            SELECT
                odrp.id,
                odrp.etapaprod_id,
                odrp.cantprod,
                odrp.kgprod,
                odrp.aprobstatus,
                odrp.created_at,
                oper.nombre AS operario_nombre,
                maq.nombre  AS maquina_nombre,
                um.nombre   AS unidadmedida_nombre
            FROM opdetregprod odrp
            LEFT JOIN operario     oper ON oper.id  = odrp.operario_id
            LEFT JOIN unidadmedida um   ON um.id    = odrp.unidadmedidasal_id
            LEFT JOIN opdetmaquina odm  ON odm.opdet_id = odrp.opdet_id
            LEFT JOIN maquina      maq  ON maq.id   = odm.maquina_id
            WHERE odrp.id IN ($ph)
              AND odrp.deleted_at IS NULL
            ORDER BY odrp.etapaprod_id, odrp.id
        ");

        // Muestras CC de todos los lotes reales
        $ccPorLote = [];
        if (!empty($lotesReales)) {
            $loteIds = array_column($lotesReales, 'id');
            $ph      = implode(',', $loteIds);
            $ccRows  = DB::select("
                SELECT
                    ccm.opdetregprod_id,
                    ccm.id,
                    ccm.fechahora,
                    ccm.status,
                    ccm.sta_env,
                    ccm.observacion,
                    usuarioreg.nombre AS usuario_nombre,
                    IF(anul.id IS NOT NULL, 1, 0) AS anulado,
                    IF(desb.id IS NOT NULL, 1, 0) AS desbloqueado
                FROM ccregistmuestra ccm
                LEFT JOIN usuario                     usuarioreg ON usuarioreg.id = ccm.usuario_id
                LEFT JOIN ccregistmuestraanul         anul       ON anul.ccregistmuestra_id = ccm.id
                LEFT JOIN ccregistmuestra_desbloqueo  desb       ON desb.ccregistmuestra_id = ccm.id
                WHERE ccm.opdetregprod_id IN ($ph)
                  AND ccm.deleted_at IS NULL
                ORDER BY ccm.id
            ");
            foreach ($ccRows as $ccRow) {
                $ccPorLote[$ccRow->opdetregprod_id][] = $ccRow;
            }
        }

        // Agrupar lotes reales por etapa
        $lotesPorEtapa = [];
        foreach ($lotesReales as $lr) {
            $lotesPorEtapa[$lr->etapaprod_id][] = $lr;
        }

        // Construir filas: etapas configuradas + lotes reales + muestras CC
        $filas         = [];
        $sinAcuerdo    = empty($etapasConfig);

        if ($sinAcuerdo) {
            // Producto sin acuerdo técnico: mostrar solo los lotes reales existentes
            foreach ($lotesReales as $lr) {
                $etapaYaAgregada = false;
                foreach ($filas as &$f) {
                    if ($f['etapaprod_id'] == $lr->etapaprod_id) {
                        $f['lotes'][] = $this->buildLoteRow($lr, $ccPorLote, $loteId, false);
                        $etapaYaAgregada = true;
                        break;
                    }
                }
                unset($f);
                if (!$etapaYaAgregada) {
                    $filas[] = [
                        'etapaprod_id'     => $lr->etapaprod_id,
                        'etapaprod_nombre' => '—',
                        'orden'            => 0,
                        'requiere_cc'      => 0,
                        'lotes'            => [$this->buildLoteRow($lr, $ccPorLote, $loteId, false)],
                        'sin_registro'     => false,
                    ];
                }
            }
        } else {
            foreach ($etapasConfig as $etapa) {
                $lotesEtapa  = $lotesPorEtapa[$etapa->etapaprod_id] ?? [];
                $fila = [
                    'etapaprod_id'     => $etapa->etapaprod_id,
                    'etapaprod_nombre' => $etapa->etapaprod_nombre,
                    'orden'            => $etapa->orden,
                    'requiere_cc'      => $etapa->requiere_cc,
                    'lotes'            => [],
                    'sin_registro'     => empty($lotesEtapa),
                ];
                foreach ($lotesEtapa as $lr) {
                    $fila['lotes'][] = $this->buildLoteRow($lr, $ccPorLote, $loteId, (bool)$etapa->requiere_cc);
                }
                $filas[] = $fila;
            }
        }

        return compact('lote', 'filas', 'loteId', 'sinAcuerdo');
    }

    private function buildLoteRow($lr, array $ccPorLote, int $loteId, bool $requiereCc): array
    {
        $muestras       = $ccPorLote[$lr->id] ?? [];
        $tieneValida    = false;
        $tieneRechazado = false;
        foreach ($muestras as $m) {
            // status: 1=aprobado, 2=aprobado c/obs, 5=sin parámetros (auto-aprobado)
            if (!$m->anulado && in_array($m->status, [1, 2, 5]) && $m->sta_env == 2) $tieneValida    = true;
            if (!$m->anulado && $m->status == 3)                                      $tieneRechazado = true;
        }

        if (!$requiereCc)            $ccStatus = 'no_requiere';
        elseif ($tieneRechazado)     $ccStatus = 'rechazado';
        elseif ($tieneValida)        $ccStatus = 'ok';
        elseif (!empty($muestras))   $ccStatus = 'pendiente_sup';
        else                         $ccStatus = 'sin_muestra';

        return [
            'id'                  => $lr->id,
            'cantprod'            => $lr->cantprod,
            'kgprod'              => $lr->kgprod,
            'aprobstatus'         => $lr->aprobstatus,
            'created_at'          => $lr->created_at,
            'operario'            => $lr->operario_nombre,
            'maquina'             => $lr->maquina_nombre,
            'unidadmedida'        => $lr->unidadmedida_nombre,
            'muestras'            => $muestras,
            'cc_status'           => $ccStatus,
            'es_lote_consultado'  => ($lr->id == $loteId),
        ];
    }
}
