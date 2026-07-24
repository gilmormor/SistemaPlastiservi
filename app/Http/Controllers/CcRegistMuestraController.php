<?php

namespace App\Http\Controllers;

use App\Models\CcParamApsucetapaprod;
use App\Models\CcRegistMuestra;
use App\Models\CcRegistMuestraAnul;
use App\Models\CcRegistMuestraDet;
use App\Models\CcRegistMuestraDesbloqueo;
use App\Models\Empresa;
use App\Models\EtapaProd;
use App\Models\OpDetRegProd;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CcRegistMuestraController extends Controller
{
    /**
     * Listado principal de muestras CC registradas.
     */
    public function index()
    {
        can('listar-ccregistmuestra');
        return view('ccregistmuestra.index');
    }

    /**
     * DataTable AJAX del listado de muestras CC.
     */
    public function ccregistmuestrapage()
    {
        can('listar-ccregistmuestra');
        $datas = DB::select("
            SELECT
                ccm.id,
                ccm.created_at,
                ccm.updated_at,
                ccm.fechahora,
                ccm.status,
                ccm.sta_env,
                ccm.sta_env_obs,
                ccm.observacion,
                odrp.id          AS opdetregprod_id,
                odrp.kgprod,
                odrp.cantprod,
                um.nombre        AS unidadmedidasal_nombre,
                ep.nombre        AS etapaprod_nombre,
                prod.nombre      AS producto_nombre,
                op.id            AS op_id,
                ot.id            AS ot_id,
                CONCAT('OP ', op.id, ' / OT ', ot.id) AS op_ot,
                IF(anul.id IS NOT NULL, 1, 0)          AS anulado,
                anul.motivo                            AS motivo_anulacion,
                usuarioanul.nombre                     AS usuario_anulacion,
                anul.created_at                        AS fechahora_anulacion,
                IF(desb.id IS NOT NULL, 1, 0)          AS desbloqueado,
                desb.observacion                       AS desbloqueo_obs,
                usuariodesb.nombre                     AS desbloqueo_usuario,
                desb.created_at                        AS desbloqueo_fecha,
                usuarioreg.nombre        AS usuario_nombre,
                usuariolid.nombre        AS usuario_env_nombre,
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
            LEFT  JOIN unidadmedida um      ON um.id    = odrp.unidadmedidasal_id
            LEFT  JOIN usuario usuariolid   ON usuariolid.id = ccm.usuariostaenv_id
            LEFT  JOIN otnotaventa otnv     ON otnv.ot_id = ot.id AND otnv.deleted_at IS NULL
            LEFT  JOIN notaventa   nv       ON nv.id = otnv.notaventa_id AND nv.deleted_at IS NULL
            LEFT  JOIN acuerdotecnico at    ON at.producto_id = odrp.producto_id
            LEFT  JOIN ccregistmuestraanul       anul        ON anul.ccregistmuestra_id = ccm.id
                                                           AND  anul.deleted_at IS NULL
            LEFT  JOIN usuario                   usuarioanul ON usuarioanul.id = anul.usuario_id
            LEFT  JOIN ccregistmuestra_desbloqueo desb        ON desb.ccregistmuestra_id = ccm.id
            LEFT  JOIN usuario                   usuariodesb ON usuariodesb.id = desb.usuario_id
            WHERE  ccm.deleted_at IS NULL
              AND  ccm.sta_env IN (0, 3)
              AND  anul.id IS NULL
            ORDER  BY ccm.id DESC
        ");
        return datatables()->collection(collect($datas))->toJson();
    }

    /**
     * Pantalla de búsqueda de registros opdetregprod para tomar una muestra CC.
     * Muestra tabla vacía hasta que el usuario consulte.
     */
    public function listaropdetregprod()
    {
        can('listar-ccregistmuestra');
        $etapaprods = EtapaProd::orderBy('nombre')->get();
        // Máquinas con su etapa (via maquinaetapaprod) para el dropdown de filtro
        $maquinas = DB::select("
            SELECT m.id, m.nombre, ep.nombre AS etapaprod_nombre
            FROM   maquina m
            LEFT   JOIN maquinaetapaprod mep ON mep.maquina_id = m.id
            LEFT   JOIN etapaprod ep          ON ep.id = mep.etapaprod_id
            WHERE  ISNULL(m.deleted_at)
            ORDER  BY ep.nombre, m.nombre
        ");
        return view('ccregistmuestra.listaropdetregprod', compact('etapaprods', 'maquinas'));
    }

    /**
     * Formulario para crear una nueva muestra CC a partir de un opdetregprod.
     */
    public function crear($opdetregprod_id)
    {
        can('crear-ccregistmuestra');

        $reg = DB::select("
            SELECT
                odrp.id, odrp.kgprod, odrp.cantprod, odrp.kgscrap, odrp.created_at,
                ep.nombre        AS etapaprod_nombre,
                prod.nombre      AS producto_nombre,
                op.id            AS op_id,
                ot.id            AS ot_id,
                opdet.apsucetapaprod_id,
                um.nombre        AS unidadmedidasal_nombre
            FROM  opdetregprod odrp
            INNER JOIN etapaprod  ep    ON ep.id   = odrp.etapaprod_id
            INNER JOIN producto   prod  ON prod.id = odrp.producto_id
            INNER JOIN opdet            ON opdet.id = odrp.opdet_id
            INNER JOIN op               ON op.id    = opdet.op_id
            INNER JOIN otdet            ON otdet.id = op.otdet_id
            INNER JOIN ot               ON ot.id    = otdet.ot_id
            LEFT  JOIN unidadmedida um  ON um.id    = odrp.unidadmedidasal_id
            WHERE odrp.id = ? AND odrp.aprobstatus = 2 AND ISNULL(odrp.deleted_at)
            LIMIT 1
        ", [$opdetregprod_id]);

        if (empty($reg)) {
            return redirect()->route('listaropdetregprod_ccregistmuestra')
                ->with('mensaje', 'Registro de producción no encontrado o no aprobado.');
        }
        $reg = $reg[0];

        // Parámetros CC configurados para la etapa
        $params = CcParamApsucetapaprod::where('apsucetapaprod_id', $reg->apsucetapaprod_id)
            ->whereNull('deleted_at')
            ->with('ccparam')
            ->orderBy('orden')
            ->get();

        // Prepara el JSON de params para el JS (evita closure multilínea en @json Blade L6)
        $ccParamsJson = json_encode($params->map(function ($p) {
            return [
                'id'        => $p->id,
                'tipo'      => $p->ccparam->tipo,
                'valor_min' => $p->valor_min,
                'valor_max' => $p->valor_max,
            ];
        })->values()->all());

        return view('ccregistmuestra.crear', compact('reg', 'params', 'ccParamsJson'));
    }

    /**
     * Guarda la muestra CC y sus detalles.
     */
    public function guardar(Request $request)
    {
        can('crear-ccregistmuestra');

        // R2: Muestra sin parámetros — se registra que no se pudo tomar la muestra
        if ($request->input('sin_parametros') == '1') {
            $request->validate([
                'opdetregprod_id' => 'required|integer|exists:opdetregprod,id',
                'observacion'     => 'required|string|min:1|max:500',
            ], [
                'observacion.required' => 'El motivo es obligatorio al registrar sin parámetros.',
            ]);

            DB::beginTransaction();
            try {
                $muestra = CcRegistMuestra::create([
                    'opdetregprod_id'  => $request->opdetregprod_id,
                    'fechahora'        => now()->format('Y-m-d H:i:s'),
                    'usuario_id'       => auth()->id(),
                    'status'           => 5,   // Sin parámetros
                    'observacion'      => $request->observacion,
                    'sta_env'          => 2,   // Auto-aprobado: sin medición no requiere revisión
                    'fechahora_env'    => now()->format('Y-m-d H:i:s'),
                    'usuariostaenv_id' => auth()->id(),
                ]);
                DB::commit();
                return redirect()->route('ver_ccregistmuestra', ['id' => $muestra->id])
                    ->with('mensaje', 'Registrada como "Sin parámetros". No bloqueará el despacho.');
            } catch (\Exception $e) {
                DB::rollBack();
                return back()->withInput()->with('mensaje', 'Error al guardar: ' . $e->getMessage());
            }
        }

        $forzarStatus2 = $request->input('forzar_status_2') == '1';

        $request->validate([
            'opdetregprod_id' => 'required|integer|exists:opdetregprod,id',
            'observacion'     => $forzarStatus2 ? 'required|string|min:1|max:500' : 'nullable|string|max:500',
        ], [
            'observacion.required' => 'La observación es obligatoria al aprobar con observaciones.',
        ]);

        DB::beginTransaction();
        try {
            $muestra = CcRegistMuestra::create([
                'opdetregprod_id' => $request->opdetregprod_id,
                'fechahora'       => now()->format('Y-m-d H:i:s'),
                'usuario_id'      => auth()->id(),
                'status'          => 1,
                'observacion'     => $request->observacion,
                'sta_env'         => 0,
            ]);

            $peorResultado = 1;
            $valores = $request->input('valor_param', []);

            foreach ($valores as $ccparam_ap_id => $valor) {
                $param = CcParamApsucetapaprod::with('ccparam')->find($ccparam_ap_id);
                if (!$param) continue;

                $resultado = CcRegistMuestraDet::calcularResultado($valor, $param);
                if ($resultado > $peorResultado) $peorResultado = $resultado;

                CcRegistMuestraDet::create([
                    'ccregistmuestra_id'        => $muestra->id,
                    'ccparam_apsucetapaprod_id' => $ccparam_ap_id,
                    'valor'                     => $valor,
                    'resultado'                 => $resultado,
                ]);
            }

            // Si el operario marcó "Aprobado c/obs", forzar status=2 independiente del cálculo
            $muestra->status = $forzarStatus2 ? 2 : $peorResultado;
            $muestra->save();

            DB::commit();
            return redirect()->route('ccregistmuestra', ['id' => $muestra->id])
                ->with('mensaje', 'Muestra CC registrada con éxito.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('mensaje', 'Error al guardar: ' . $e->getMessage());
        }
    }

    /**
     * Ver el detalle de una muestra CC.
     */
    public function ver($id)
    {
        can('ver-ccregistmuestra');
        $muestra = CcRegistMuestra::with(['dets.ccparamApsucetapaprod.ccparam', 'usuario', 'usuarioStaenv', 'anulacion'])->findOrFail($id);
        $opdetregprod = DB::select("
            SELECT odrp.id, odrp.kgprod, odrp.cantprod, odrp.created_at,
                   ep.nombre AS etapaprod_nombre, prod.nombre AS producto_nombre,
                   op.id AS op_id, ot.id AS ot_id,
                   um.nombre AS unidadmedidasal_nombre
            FROM opdetregprod odrp
            INNER JOIN etapaprod ep   ON ep.id   = odrp.etapaprod_id
            INNER JOIN producto prod  ON prod.id = odrp.producto_id
            INNER JOIN opdet          ON opdet.id = odrp.opdet_id
            INNER JOIN op             ON op.id    = opdet.op_id
            INNER JOIN otdet          ON otdet.id = op.otdet_id
            INNER JOIN ot             ON ot.id    = otdet.ot_id
            LEFT  JOIN unidadmedida um ON um.id   = odrp.unidadmedidasal_id
            WHERE odrp.id = ? LIMIT 1
        ", [$muestra->opdetregprod_id]);
        $reg = !empty($opdetregprod) ? $opdetregprod[0] : null;
        return view('ccregistmuestra.ver', compact('muestra', 'reg'));
    }

    /**
     * Etiqueta compacta de muestra CC (térmica 10.4 x 5.08 cm).
     * Sin tabla de parámetros — QR apunta a la muestra CC en el sistema.
     */
    public function etiquetaCompacta($id)
    {
        can('ver-ccregistmuestra');
        $muestra = CcRegistMuestra::with(['usuario'])->findOrFail($id);
        $reg = $this->_getDatosReg($muestra->opdetregprod_id);

        $productoNombre = $reg ? $reg->producto_nombre : '—';
        $etapaNombre    = $reg ? $reg->etapaprod_nombre : '—';
        $usuarioCC      = $muestra->usuario->nombre ?? '—';
        $op_id          = $reg ? $reg->op_id : '—';
        $ot_id          = $reg ? $reg->ot_id : '—';

        return view('ccregistmuestra.etiqueta-compacta',
            compact('muestra', 'productoNombre', 'etapaNombre', 'usuarioCC', 'op_id', 'ot_id'));
    }

    /**
     * Etiqueta completa de muestra CC (papel, incluye tabla de parámetros medidos).
     */
    public function etiquetaCompleta($id)
    {
        can('ver-ccregistmuestra');
        $muestra = CcRegistMuestra::with(['dets.ccparamApsucetapaprod.ccparam', 'usuario'])->findOrFail($id);
        $reg = $this->_getDatosReg($muestra->opdetregprod_id);

        $productoNombre = $reg ? $reg->producto_nombre : '—';
        $etapaNombre    = $reg ? $reg->etapaprod_nombre : '—';
        $usuarioCC      = $muestra->usuario->nombre ?? '—';
        $op_id          = $reg ? $reg->op_id : '—';
        $ot_id          = $reg ? $reg->ot_id : '—';
        $kgprod         = $reg ? $reg->kgprod : 0;
        $cantprod       = $reg ? $reg->cantprod : null;
        $unidadmedida   = $reg ? ($reg->unidadmedidasal_nombre ?? '') : '';

        return view('ccregistmuestra.etiqueta-completa',
            compact('muestra', 'productoNombre', 'etapaNombre', 'usuarioCC',
                    'op_id', 'ot_id', 'kgprod', 'cantprod', 'unidadmedida'));
    }

    /**
     * Helper privado: obtiene datos del registro de producción asociado.
     */
    private function _getDatosReg($opdetregprod_id)
    {
        $rows = DB::select("
            SELECT odrp.id, odrp.kgprod, odrp.cantprod,
                   ep.nombre   AS etapaprod_nombre,
                   prod.nombre AS producto_nombre,
                   op.id       AS op_id,
                   ot.id       AS ot_id,
                   um.nombre   AS unidadmedidasal_nombre
            FROM opdetregprod odrp
            INNER JOIN etapaprod ep   ON ep.id   = odrp.etapaprod_id
            INNER JOIN producto prod  ON prod.id = odrp.producto_id
            INNER JOIN opdet          ON opdet.id = odrp.opdet_id
            INNER JOIN op             ON op.id    = opdet.op_id
            INNER JOIN otdet          ON otdet.id = op.otdet_id
            INNER JOIN ot             ON ot.id    = otdet.ot_id
            LEFT  JOIN unidadmedida um ON um.id   = odrp.unidadmedidasal_id
            WHERE odrp.id = ? LIMIT 1
        ", [$opdetregprod_id]);
        return !empty($rows) ? $rows[0] : null;
    }

    /**
     * Aprueba una muestra CC (sta_env=1). Responde JSON para llamada AJAX desde el listado.
     */
    public function aprobar(Request $request, $id)
    {
        can('ver-ccregistmuestra');
        $muestra = CcRegistMuestra::whereNull('deleted_at')->findOrFail($id);
        // Bloquear solo si ya fue aprobada por el supervisor (sta_env=2) o está en tránsito (sta_env=1)
        if ($muestra->sta_env == 2 || $muestra->sta_env == 1) {
            return response()->json(['ok' => false, 'msg' => 'La muestra ya fue enviada al supervisor o ya fue procesada.']);
        }
        // Optimistic locking: evitar sobreescritura si otro usuario modificó el registro
        if ($request->has('updated_at') && $muestra->updated_at->timestamp != (int) $request->updated_at) {
            return response()->json(['ok' => false, 'msg' => 'La muestra #' . $id . ' fue modificada por otro usuario. Recargue el listado.']);
        }
        $muestra->sta_env          = 1;
        $muestra->fechahora_env    = now()->format('Y-m-d H:i:s');
        $muestra->usuariostaenv_id = auth()->id();
        $muestra->save();
        return response()->json(['ok' => true, 'msg' => 'Muestra #' . $id . ' enviada al supervisor correctamente.']);
    }

    /**
     * Formulario de edición de una muestra CC (solo si sta_env=0 y no anulada).
     */
    public function editar(Request $request, $id)
    {
        can('ver-ccregistmuestra');
        $muestra = CcRegistMuestra::with(['dets.ccparamApsucetapaprod.ccparam'])
            ->whereNull('deleted_at')->findOrFail($id);
        // Solo bloquear edición cuando ya fue aprobada por el supervisor (sta_env=2)
        if ($muestra->sta_env == 2) {
            return redirect()->route('ccregistmuestra')
                ->with('mensaje', 'No se puede editar una muestra aprobada por el supervisor.');
        }
        // Optimistic locking: detectar si otro usuario modificó el registro antes de abrir el form
        if ($request->has('updated_at') && $muestra->updated_at->timestamp != (int) $request->updated_at) {
            return redirect()->route('ccregistmuestra')->with([
                'mensaje'    => 'La muestra #' . $id . ' fue modificada por otro usuario. Vuelva a recargar el listado.',
                'tipo_alert' => 'alert-error',
            ]);
        }
        // JSON de params para recálculo en tiempo real en el JS
        $ccParamsJson = json_encode($muestra->dets->map(function ($det) {
            $cap = $det->ccparamApsucetapaprod;
            $cp  = $cap ? $cap->ccparam : null;
            return [
                'det_id'    => $det->id,
                'tipo'      => $cp ? $cp->tipo : 'text',
                'valor_min' => $cap ? $cap->valor_min : null,
                'valor_max' => $cap ? $cap->valor_max : null,
            ];
        })->values()->all());
        return view('ccregistmuestra.editar', compact('muestra', 'ccParamsJson'));
    }

    /**
     * Guarda los cambios de una muestra CC editada.
     */
    public function actualizar(Request $request, $id)
    {
        can('ver-ccregistmuestra');
        $muestra = CcRegistMuestra::with(['dets.ccparamApsucetapaprod.ccparam'])
            ->whereNull('deleted_at')->findOrFail($id);
        // Solo bloquear actualización cuando ya fue aprobada por el supervisor (sta_env=2)
        if ($muestra->sta_env == 2) {
            return back()->with('mensaje', 'No se puede editar una muestra aprobada por el supervisor.');
        }
        // Optimistic locking: detectar modificación concurrente comparando timestamps numéricos
        if ($muestra->updated_at->timestamp != (int) $request->updated_at) {
            return redirect()->route('ccregistmuestra')->with([
                'mensaje'    => 'La muestra #' . $id . ' fue modificada por otro usuario. Vuelva a cargar el formulario.',
                'tipo_alert' => 'alert-error',
            ]);
        }
        $request->validate(['observacion' => 'nullable|string|max:500']);
        DB::beginTransaction();
        try {
            $muestra->observacion = $request->observacion;
            $peorResultado = 1;
            $valores = $request->input('valor_param', []);
            foreach ($valores as $det_id => $valor) {
                $det = CcRegistMuestraDet::with(['ccparamApsucetapaprod.ccparam'])->find($det_id);
                if (!$det || $det->ccregistmuestra_id != $id) continue;
                $resultado = CcRegistMuestraDet::calcularResultado($valor, $det->ccparamApsucetapaprod);
                if ($resultado > $peorResultado) $peorResultado = $resultado;
                $det->valor     = $valor;
                $det->resultado = $resultado;
                $det->save();
            }
            $muestra->status     = $peorResultado;
            $muestra->updated_at = now(); // fuerza actualización aunque no haya otros cambios
            $muestra->save();
            DB::commit();
            return redirect()->route('ccregistmuestra')
                ->with('mensaje', 'Muestra actualizada correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('mensaje', 'Error al actualizar: ' . $e->getMessage());
        }
    }

    /**
     * Anula una muestra CC. Responde JSON (AJAX desde listado) o redirect (desde ver.blade).
     */
    public function anular(Request $request, $id)
    {
        can('ver-ccregistmuestra');
        $muestra = CcRegistMuestra::with('anulacion')->findOrFail($id);
        if ($muestra->anulacion) {
            $msg = 'La muestra ya fue anulada.';
            return $request->ajax()
                ? response()->json(['ok' => false, 'msg' => $msg])
                : back()->with('mensaje', $msg);
        }
        $request->validate(['motivo' => 'required|string|max:500']);
        DB::beginTransaction();
        try {
            CcRegistMuestraAnul::create([
                'ccregistmuestra_id' => $id,
                'motivo'             => $request->motivo,
                'usuario_id'         => auth()->id(),
            ]);
            // Registrar quién anuló sin eliminar el registro (anulación ≠ borrado)
            $muestra->usuariodel_id = auth()->id();
            $muestra->save();
            DB::commit();
            $msg = 'Muestra #' . $id . ' anulada correctamente.';
            return $request->ajax()
                ? response()->json(['ok' => true, 'msg' => $msg])
                : redirect()->route('ccregistmuestra')->with('mensaje', $msg);
        } catch (\Exception $e) {
            DB::rollBack();
            $msg = 'Error al anular: ' . $e->getMessage();
            return $request->ajax()
                ? response()->json(['ok' => false, 'msg' => $msg])
                : back()->with('mensaje', $msg);
        }
    }

    /**
     * Ver todas las muestras CC de un opdetregprod.
     */
    public function verxopdetregprod($opdetregprod_id)
    {
        can('ver-ccregistmuestra');
        $muestras = CcRegistMuestra::where('opdetregprod_id', $opdetregprod_id)
            ->whereNull('deleted_at')
            ->with(['dets', 'usuario'])
            ->orderBy('id', 'desc')
            ->get();
        $reg_arr = DB::select("
            SELECT odrp.id, odrp.kgprod, odrp.cantprod,
                   ep.nombre AS etapaprod_nombre, prod.nombre AS producto_nombre,
                   op.id AS op_id, ot.id AS ot_id,
                   um.nombre AS unidadmedidasal_nombre
            FROM opdetregprod odrp
            INNER JOIN etapaprod ep   ON ep.id   = odrp.etapaprod_id
            INNER JOIN producto prod  ON prod.id = odrp.producto_id
            INNER JOIN opdet          ON opdet.id = odrp.opdet_id
            INNER JOIN op             ON op.id    = opdet.op_id
            INNER JOIN otdet          ON otdet.id = op.otdet_id
            INNER JOIN ot             ON ot.id    = otdet.ot_id
            LEFT  JOIN unidadmedida um ON um.id   = odrp.unidadmedidasal_id
            WHERE odrp.id = ? LIMIT 1
        ", [$opdetregprod_id]);
        $reg = !empty($reg_arr) ? $reg_arr[0] : null;
        return view('ccregistmuestra.verxopdetregprod', compact('muestras', 'reg'));
    }

    /**
     * DataTable AJAX de registros opdetregprod filtrados.
     * Se llama solo cuando el usuario hace clic en "Consultar".
     */
    public function listaropdetregprodpage(Request $request)
    {
        can('listar-ccregistmuestra');

        $fechad           = $request->input('fechad', '');
        $fechah           = $request->input('fechah', '');
        $etapaprod_id     = $request->input('etapaprod_id', '');
        $op_id            = $request->input('op_id', '');
        $ot_id            = $request->input('ot_id', '');
        $con_muestra      = $request->input('con_muestra', ''); // '0'=sin muestra, '1'=con muestra, ''=todos
        $opdetregprod_id  = $request->input('opdetregprod_id', '');
        $maquina_id       = $request->input('maquina_id', '');
        $rut              = $request->input('rut', '');

        $where = "WHERE ISNULL(odrp.deleted_at) AND odrp.aprobstatus = 2";
        $params = [];

        if ($opdetregprod_id) {
            $where .= " AND odrp.id = ?";
            $params[] = $opdetregprod_id;
        }
        if ($fechad) {
            $where .= " AND DATE(odrp.created_at) >= ?";
            $params[] = fecha_db($fechad);
        }
        if ($fechah) {
            $where .= " AND DATE(odrp.created_at) <= ?";
            $params[] = fecha_db($fechah);
        }
        if ($etapaprod_id) {
            $where .= " AND odrp.etapaprod_id = ?";
            $params[] = $etapaprod_id;
        }
        if ($op_id) {
            $where .= " AND op.id = ?";
            $params[] = $op_id;
        }
        if ($ot_id) {
            $where .= " AND ot.id = ?";
            $params[] = $ot_id;
        }
        if ($maquina_id) {
            $where .= " AND odm.maquina_id = ?";
            $params[] = $maquina_id;
        }
        if ($rut) {
            $where .= " AND cli.rut = ?";
            $params[] = $rut;
        }

        $havingClause = '';
        if ($con_muestra === '0') {
            $havingClause = 'HAVING cant_muestras = 0';
        } elseif ($con_muestra === '1') {
            $havingClause = 'HAVING cant_muestras > 0';
        }

        $sql = "
            SELECT
                odrp.id,
                odrp.es_muestra,
                odrp.created_at,
                odrp.kgprod,
                odrp.cantprod,
                odrp.kgscrap,
                um.nombre        AS unidadmedidasal_nombre,
                ep.nombre        AS etapaprod_nombre,
                prod.nombre      AS producto_nombre,
                op.id            AS op_id,
                ot.id            AS ot_id,
                GROUP_CONCAT(DISTINCT maq.nombre ORDER BY maq.nombre SEPARATOR ', ') AS maquina_nombre,
                COUNT(ccm.id)    AS cant_muestras,
                MAX(ccm.status)  AS peor_status_cc
            FROM  opdetregprod odrp
            INNER JOIN etapaprod  ep    ON ep.id   = odrp.etapaprod_id
            INNER JOIN producto   prod  ON prod.id = odrp.producto_id
            INNER JOIN opdet            ON opdet.id = odrp.opdet_id
            INNER JOIN op               ON op.id    = opdet.op_id
            INNER JOIN otdet            ON otdet.id = op.otdet_id
            INNER JOIN ot               ON ot.id    = otdet.ot_id
            LEFT  JOIN unidadmedida um  ON um.id    = odrp.unidadmedidasal_id
            LEFT  JOIN otnotaventa otnv ON otnv.ot_id = ot.id
            LEFT  JOIN notaventa  nv   ON nv.id     = otnv.notaventa_id
            LEFT  JOIN cliente    cli  ON cli.id    = nv.cliente_id
            LEFT  JOIN opdetmaquina odm ON odm.opdet_id = opdet.id
            LEFT  JOIN maquina    maq  ON maq.id = odm.maquina_id AND ISNULL(maq.deleted_at)
            LEFT  JOIN ccregistmuestra ccm
                   ON ccm.opdetregprod_id = odrp.id AND ISNULL(ccm.deleted_at)
            {$where}
            GROUP  BY odrp.id, odrp.es_muestra, odrp.created_at, odrp.kgprod, odrp.cantprod,
                      odrp.kgscrap, um.nombre, ep.nombre, prod.nombre, op.id, ot.id
            {$havingClause}
            ORDER  BY odrp.id DESC
            LIMIT 500
        ";

        $datas = DB::select($sql, $params);
        return datatables($datas)->toJson();
    }

    /**
     * Desbloquea una muestra CC rechazada (status=3) para permitir su despacho.
     * Guarda quién desbloqueó, cuándo y por qué en ccregistmuestra_desbloqueo.
     * Responde JSON para llamada AJAX desde el listado.
     */
    public function desbloquear(Request $request, $id)
    {
        can('ver-ccregistmuestra');
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


    /**
     * Genera PDF de la muestra CC. Slug de permiso: ver-pdf-muestra-cc.
     */
    public function exportPdf($id)
    {
        if (can('ver-pdf-muestra-cc', false)) {
            $muestra = CcRegistMuestra::with([
                'dets.ccparamApsucetapaprod.ccparam',
                'usuario',
                'usuarioStaenv',
                'anulacion.usuario',
                'desbloqueo.usuario',
            ])->findOrFail($id);

            $opdetregprod = DB::select("
                SELECT odrp.id, odrp.kgprod, odrp.cantprod, odrp.created_at,
                       ep.nombre AS etapaprod_nombre, prod.nombre AS producto_nombre,
                       op.id AS op_id, ot.id AS ot_id,
                       um.nombre AS unidadmedidasal_nombre,
                       cli.rut AS cliente_rut, cli.razonsocial AS cliente_razonsocial
                FROM opdetregprod odrp
                INNER JOIN etapaprod ep   ON ep.id   = odrp.etapaprod_id
                INNER JOIN producto prod  ON prod.id = odrp.producto_id
                INNER JOIN opdet          ON opdet.id = odrp.opdet_id
                INNER JOIN op             ON op.id    = opdet.op_id
                INNER JOIN otdet          ON otdet.id = op.otdet_id
                INNER JOIN ot             ON ot.id    = otdet.ot_id
                LEFT  JOIN unidadmedida um  ON um.id  = odrp.unidadmedidasal_id
                LEFT  JOIN otnotaventa otnv ON otnv.ot_id = ot.id
                LEFT  JOIN notaventa nv     ON nv.id  = otnv.notaventa_id
                LEFT  JOIN cliente cli      ON cli.id = nv.cliente_id
                WHERE odrp.id = ? LIMIT 1
            ", [$muestra->opdetregprod_id]);
            $reg     = !empty($opdetregprod) ? $opdetregprod[0] : null;
            $empresa = Empresa::orderBy('id')->get();

            $pdf = PDF::loadView('ccregistmuestra.reporte', compact('muestra', 'reg', 'empresa'));
            return $pdf->stream('muestra-cc-' . str_pad($id, 6, '0', STR_PAD_LEFT) . '.pdf');
        } else {
            $pdf = PDF::loadView('generales.pdfmensajesinacceso');
            return $pdf->stream('sinacceso.pdf');
        }
    }
}
