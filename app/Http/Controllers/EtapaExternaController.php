<?php

namespace App\Http\Controllers;

use App\Models\EtapaExtPlanilla;
use App\Models\EtapaExtEtiqueta;
use App\Models\OpDetRegProd;
use App\Models\OpDetRegProdMaq;
use App\Models\Maquina;
use App\Models\Operario;
use App\Models\Empresa;
use App\Models\Seguridad\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * ETAPA EXTERNA — planilla y etiquetas de un proceso que se ejecuta fuera del
 * sistema, entre una etapa de produccion y la siguiente.
 *
 * Contexto: el operario que hace ese trabajo (hoy, entre extrusion/impresion y
 * sellado) no tiene computador donde registrar nada. El supervisor le entrega
 * una planilla impresa con los datos de lo que va a procesar -con la etiqueta
 * del lote de origen incluida, para que no se equivoque de rollo- y las
 * etiquetas que le pegara a cada paquete que arme. Hoy eso se hace en Excel y
 * sin ningun control.
 *
 * El modulo se ancla UNICAMENTE en el lote (opdetregprod). No toca el registro
 * de produccion ni ninguna de sus pantallas, y no depende de que la etapa sea
 * sellado: la etapa en la que se procesa se deduce de la OP del lote. El dia
 * que este proceso se convierta en una etapa real del sistema, basta con
 * quitar el menu.
 */
class EtapaExternaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Pantalla principal: el supervisor escribe el numero de lote y desde ahi
     * imprime la planilla y las etiquetas.
     */
    public function index()
    {
        can('listar-etapa-externa');
        return view('etapaexterna.index');
    }

    /* ===================================================================
       DATOS DEL LOTE
       =================================================================== */

    /**
     * Arma todos los datos de un lote: cabecera, etapa en la que se procesa,
     * maquinas y operarios disponibles, forma de embalar e historial impreso.
     * Es el nucleo del modulo; lo usan la busqueda AJAX y las dos impresiones.
     *
     * Lanza excepcion con mensaje legible si el lote no sirve, para que el
     * supervisor sepa por que y no quede una pantalla en blanco.
     */
    private function datosLote($opdetregprod_id)
    {
        $lote = OpDetRegProd::find($opdetregprod_id);
        if (!$lote) {
            throw new \Exception('No existe el lote ' . $opdetregprod_id . '.');
        }
        /* Solo lotes aprobados: la planilla manda a procesar material real, y un
           registro pendiente o rechazado todavia puede cambiar de cantidad o
           desaparecer. aprobstatus 2 = aprobado. */
        if ($lote->aprobstatus != 2) {
            throw new \Exception('El lote ' . $lote->id . ' no esta aprobado. Solo se puede procesar material de lotes aprobados.');
        }

        $opdet = $lote->opdet;
        $op    = $opdet->op;
        $otdet = $op->otdet;
        $ot    = $otdet->ot;

        /* Etapa en la que se va a procesar: la SIGUIENTE a la del lote dentro de
           la misma OP. Mismo criterio que usa la etiqueta de etapa en
           OpDetRegProdTempAprobSupController: se ordena por el campo 'orden' de
           areaproduccionsucetapaprod y se toma la primera mayor. */
        $aux_ordenactual = $opdet->areaproduccionsucetapaprod->orden;
        $aux_siguiente = null;
        $aux_ordensig  = null;
        foreach ($op->opdets as $od) {
            $aux_orden = $od->areaproduccionsucetapaprod->orden;
            if ($aux_orden > $aux_ordenactual && ($aux_ordensig === null || $aux_orden < $aux_ordensig)) {
                $aux_ordensig  = $aux_orden;
                $aux_siguiente = $od;
            }
        }

        /* Si el lote es de la ultima etapa no hay siguiente que deducir. No se
           bloquea: se devuelve etapa nula y la pantalla pide elegirla a mano,
           asi un caso de borde no deja el modulo inutilizable. */
        $etapaprod_id     = null;
        $etapaprod_nombre = null;
        if ($aux_siguiente) {
            $etapaprod_id     = $aux_siguiente->areaproduccionsucetapaprod->etapaprod_id;
            $etapaprod_nombre = $aux_siguiente->areaproduccionsucetapaprod->etapaprod->nombre;
        }

        // Etapa de la que SALIO el lote (solo informativa, para la cabecera)
        $etapaorigen_nombre = $opdet->areaproduccionsucetapaprod->etapaprod->nombre ?? '—';

        // Maquina en la que se produjo el lote (va en la etiqueta del lote)
        $maquinaorigen_nombre = '—';
        $aux_maqorigen = OpDetRegProdMaq::where('opdetregprod_id', $lote->id)->first();
        if ($aux_maqorigen) {
            $aux_maq = Maquina::find($aux_maqorigen->maquina_id);
            if ($aux_maq) {
                $maquinaorigen_nombre = $aux_maq->nombre;
            }
        }

        // Operario que registro el lote (va en la etiqueta del lote)
        $aux_operorigen = Operario::find($lote->operario_id);
        $operarioorigen_nombre = $aux_operorigen ? $aux_operorigen->nombre : '—';

        /* Materia prima del producto, via acuerdo tecnico. Mismo camino que usa
           MateriaPrima::textoAptoAlimentoPorProducto. */
        $aux_mp = DB::selectOne("
            SELECT mp.nombre
            FROM   acuerdotecnico at
            INNER  JOIN materiaprima mp ON mp.id = at.at_materiaprima_id
                                       AND ISNULL(mp.deleted_at)
            WHERE  at.producto_id = ?
              AND  ISNULL(at.deleted_at)
            ORDER  BY at.id DESC
            LIMIT  1
        ", [$otdet->producto_id]);
        $materiaprima_nombre = $aux_mp ? $aux_mp->nombre : '—';

        return [
            'lote'                  => $lote,
            'opdet'                 => $opdet,
            'op'                    => $op,
            'otdet'                 => $otdet,
            'ot'                    => $ot,
            'etapaprod_id'          => $etapaprod_id,
            'etapaprod_nombre'      => $etapaprod_nombre,
            'etapaorigen_nombre'    => $etapaorigen_nombre,
            'maquinaorigen_nombre'  => $maquinaorigen_nombre,
            'operarioorigen_nombre' => $operarioorigen_nombre,
            'materiaprima_nombre'   => $materiaprima_nombre,
            'cliente_nombre'        => $ot->cliente->razonsocial ?? null,
            'producto_glosa'        => $otdet->producto->glosa ?? ($otdet->producto->nombre ?? '—'),
            'formaembalar'          => $this->formaEmbalar($otdet, $ot),
        ];
    }

    /**
     * Forma de embalar para la planilla.
     *
     * Primero lo congelado en el detalle de la OT; si esta vacio, lo acordado
     * con el cliente para ese producto. Una OT libre (produccion para stock) no
     * tiene cliente, asi que solo puede venir de otdet.
     */
    private function formaEmbalar($otdet, $ot)
    {
        if (!empty($otdet->formaembalar)) {
            return $otdet->formaembalar;
        }
        if ($ot && $ot->cliente_id) {
            $aux_cp = DB::table('cliente_producto')
                ->where('cliente_id', $ot->cliente_id)
                ->where('producto_id', $otdet->producto_id)
                ->first();
            if ($aux_cp && !empty($aux_cp->formaembalar)) {
                return $aux_cp->formaembalar;
            }
        }
        return null;
    }

    /**
     * Guarda la forma de embalar que el supervisor escribio en el modal.
     *
     * Se graba en los dos lados a proposito: en otdet queda congelada para esta
     * OT, y en cliente_producto queda como acuerdo para las proximas (que es
     * donde la capturaran los formularios de OT y OTNV mas adelante). Asi el
     * dato se empieza a cargar desde el primer uso del modulo y la segunda OT
     * del mismo cliente y producto ya lo trae solo.
     */
    private function guardarFormaEmbalar($otdet, $ot, $formaembalar)
    {
        if ($formaembalar === null || trim($formaembalar) === '') {
            return;
        }
        $aux_valor = trim($formaembalar);

        DB::table('otdet')->where('id', $otdet->id)->update(['formaembalar' => $aux_valor]);

        // Sin cliente (OT libre) no hay fila en cliente_producto que actualizar
        if ($ot && $ot->cliente_id) {
            DB::table('cliente_producto')
                ->where('cliente_id', $ot->cliente_id)
                ->where('producto_id', $otdet->producto_id)
                ->update(['formaembalar' => $aux_valor, 'updated_at' => now()]);
        }
    }

    /**
     * Maquinas de una etapa, limitadas a las sucursales del usuario.
     * Mismo criterio que el select de maquina del registro de produccion.
     */
    private function maquinasEtapa($etapaprod_id)
    {
        if (!$etapaprod_id) {
            return collect();
        }
        $aux_sucursales = Usuario::findOrFail(auth()->id())->sucursales->pluck('id')->toArray();
        if (count($aux_sucursales) == 0) {
            $aux_sucursales = [0];   // sin sucursales asignadas no debe ver ninguna maquina
        }
        return DB::table('maquina as m')
            ->join('maquinaetapaprod as mep', 'mep.maquina_id', '=', 'm.id')
            ->where('mep.etapaprod_id', $etapaprod_id)
            ->whereIn('m.sucursal_id', $aux_sucursales)
            ->whereNull('m.deleted_at')
            ->select('m.id', 'm.nombre')
            ->orderBy('m.nombre')
            ->get();
    }

    /**
     * Operarios de una etapa, limitados a las sucursales del usuario.
     *
     * Mismo criterio que la funcion global consultaOperarios() que usa el
     * registro de produccion, pero implementado aqui a proposito: aquella es
     * una funcion suelta declarada al final de OpDetRegProdTempController, asi
     * que solo existe si ese archivo ya fue cargado. Depender de eso ataria
     * este modulo -que es temporal y debe poder quitarse- a un archivo del
     * flujo principal de produccion.
     */
    private function operariosEtapa($etapaprod_id)
    {
        if (!$etapaprod_id) {
            return collect();
        }
        $aux_sucursales = Usuario::findOrFail(auth()->id())->sucursales->pluck('id')->toArray();
        if (count($aux_sucursales) == 0) {
            $aux_sucursales = [0];
        }
        return DB::table('operario as op')
            ->join('operario_areaproduccionsucep as oa', 'oa.operario_id', '=', 'op.id')
            ->join('areaproduccionsucetapaprod as ae', 'ae.id', '=', 'oa.areaproduccionsucep_id')
            ->join('areaproduccionsuc as a', 'a.id', '=', 'ae.areaproduccionsuc_id')
            ->where('ae.etapaprod_id', $etapaprod_id)
            ->whereIn('a.sucursal_id', $aux_sucursales)
            ->where('op.activo', 1)
            ->whereNull('op.deleted_at')
            ->select('op.id', 'op.nombre')
            ->groupBy('op.id', 'op.nombre')   // un operario puede estar en varias areas
            ->orderBy('op.nombre')
            ->get();
    }

    /**
     * Busqueda del lote desde la pantalla (AJAX).
     * Devuelve la ficha, los selects y lo ya impreso para ese lote.
     */
    public function buscarLote($id)
    {
        can('listar-etapa-externa');
        try {
            $aux_datos = $this->datosLote($id);
        } catch (\Exception $e) {
            return response()->json(['ok' => false, 'mensaje' => $e->getMessage()]);
        }

        $lote  = $aux_datos['lote'];
        $otdet = $aux_datos['otdet'];

        return response()->json([
            'ok'    => true,
            'lote'  => [
                'id'            => $lote->id,
                'ot_id'         => $aux_datos['ot']->id,
                'op_id'         => $aux_datos['op']->id,
                'opdet_id'      => $aux_datos['opdet']->id,
                'cliente'       => $aux_datos['cliente_nombre'] ?: '(sin cliente — OT libre)',
                'producto'      => $aux_datos['producto_glosa'],
                'producto_id'   => $otdet->producto_id,
                'materiaprima'  => $aux_datos['materiaprima_nombre'],
                'kgprod'        => number_format($lote->kgprod, 2, ',', '.'),
                'cantprod'      => number_format($lote->cantprod, 0, ',', '.'),
                'etapaorigen'   => $aux_datos['etapaorigen_nombre'],
                'maquinaorigen' => $aux_datos['maquinaorigen_nombre'],
                'formaembalar'  => $aux_datos['formaembalar'],
            ],
            'etapaprod_id'     => $aux_datos['etapaprod_id'],
            'etapaprod_nombre' => $aux_datos['etapaprod_nombre'],
            'maquinas'         => $this->maquinasEtapa($aux_datos['etapaprod_id']),
            'operarios'        => $this->operariosEtapa($aux_datos['etapaprod_id']),
            'planillas'        => $this->historialPlanillas($id),
            'etiquetas'        => $this->historialEtiquetas($id),
            'destinos'         => $this->destinosLote($id),
        ]);
    }

    /* ===================================================================
       HISTORIAL
       =================================================================== */

    /**
     * Registros de produccion que consumieron este lote: cierra el circulo
     * entre lo que se imprimio (planilla y etiquetas) y lo que realmente se
     * produjo con ese material.
     *
     * No hace falta guardar el destino en etapaextplanilla/etapaextetiqueta:
     * la relacion ya la lleva el FIFO entre etapas (opdetregprod_origen para
     * los lotes aprobados, opdetregprodtemp_origen para los que aun estan en
     * curso). Ademas asi queda bien representado que un mismo lote puede
     * alimentar a VARIOS registros -dos turnos, entregas parciales-, cosa que
     * un unico campo destino no podria.
     *
     * Los temps ya aprobados se excluyen porque vuelven a aparecer, esta vez
     * como definitivos, por la otra consulta.
     */
    private function destinosLote($opdetregprod_id)
    {
        $aux_definitivos = DB::select("
            SELECT o.opdetregprod_id AS destino_id, o.kg, o.cant,
                   r.aprobstatus, r.aprobfechahora, r.created_at,
                   e.nombre AS etapa, op.nombre AS operario
            FROM   opdetregprod_origen o
            INNER  JOIN opdetregprod r ON r.id = o.opdetregprod_id
                                      AND ISNULL(r.deleted_at)
            LEFT   JOIN etapaprod e  ON e.id  = r.etapaprod_id
            LEFT   JOIN operario  op ON op.id = r.operario_id
            WHERE  o.opdetregprod_origen_id = ?
            ORDER  BY o.opdetregprod_id
        ", [$opdetregprod_id]);

        $aux_encurso = DB::select("
            SELECT t.opdetregprodtemp_id AS destino_id, t.kg, t.cant,
                   tmp.aprobstatus, tmp.created_at,
                   e.nombre AS etapa, op.nombre AS operario
            FROM   opdetregprodtemp_origen t
            INNER  JOIN opdetregprodtemp tmp ON tmp.id = t.opdetregprodtemp_id
                                            AND ISNULL(tmp.deleted_at)
            LEFT   JOIN etapaprod e  ON e.id  = tmp.etapaprod_id
            LEFT   JOIN operario  op ON op.id = tmp.operario_id
            WHERE  t.opdetregprod_id = ?
              AND  (tmp.aprobstatus IS NULL OR tmp.aprobstatus <> 2)
            ORDER  BY t.opdetregprodtemp_id
        ", [$opdetregprod_id]);

        $aux_salida = [];
        foreach ($aux_definitivos as $d) {
            $aux_salida[] = [
                'id'       => $d->destino_id,
                'tipo'     => 'lote',          // registro ya aprobado
                'etapa'    => $d->etapa,
                'operario' => $d->operario,
                'kg'       => $d->kg,
                'fecha'    => $d->aprobfechahora ?: $d->created_at,
                'estado'   => 'Aprobado',
            ];
        }
        foreach ($aux_encurso as $t) {
            $aux_salida[] = [
                'id'       => $t->destino_id,
                'tipo'     => 'temp',          // registro todavia sin aprobar
                'etapa'    => $t->etapa,
                'operario' => $t->operario,
                'kg'       => $t->kg,
                'fecha'    => $t->created_at,
                'estado'   => ($t->aprobstatus == 3 ? 'Rechazado' : 'Pendiente'),
            ];
        }
        return $aux_salida;
    }

    private function historialPlanillas($opdetregprod_id)
    {
        return DB::table('etapaextplanilla as p')
            ->leftJoin('maquina as m', 'm.id', '=', 'p.maquina_id')
            ->leftJoin('operario as o', 'o.id', '=', 'p.operario_id')
            ->leftJoin('usuario as u', 'u.id', '=', 'p.usuario_id')
            ->where('p.opdetregprod_id', $opdetregprod_id)
            ->whereNull('p.deleted_at')
            ->select('p.id', 'p.created_at', 'p.nrobolsas', 'p.obs',
                     'm.nombre as maquina', 'o.nombre as operario', 'u.nombre as usuario')
            ->orderBy('p.id', 'desc')
            ->get();
    }

    private function historialEtiquetas($opdetregprod_id)
    {
        return DB::table('etapaextetiqueta as e')
            ->leftJoin('maquina as m', 'm.id', '=', 'e.maquina_id')
            ->leftJoin('operario as o', 'o.id', '=', 'e.operario_id')
            ->leftJoin('usuario as u', 'u.id', '=', 'e.usuario_id')
            ->where('e.opdetregprod_id', $opdetregprod_id)
            ->whereNull('e.deleted_at')
            ->select('e.id', 'e.created_at', 'e.etapaextplanilla_id', 'e.cantidad',
                     'e.kilos', 'e.unidades', 'e.turno', 'e.fecha', 'e.obs',
                     'm.nombre as maquina', 'o.nombre as operario', 'u.nombre as usuario')
            ->orderBy('e.id', 'desc')
            ->get();
    }

    /* ===================================================================
       GUARDAR
       =================================================================== */

    /**
     * Guarda la planilla y devuelve su id para abrir la impresion.
     * La forma de embalar que venga en el formulario se graba en otdet y
     * cliente_producto (ver guardarFormaEmbalar).
     */
    public function guardarPlanilla(Request $request)
    {
        can('crear-etapa-externa');

        $request->validate([
            'opdetregprod_id' => 'required|numeric',
            'etapaprod_id'    => 'required|numeric',
            'maquina_id'      => 'required|numeric',
            'operario_id'     => 'required|numeric',
        ], [
            'etapaprod_id.required' => 'Debe indicar la etapa en la que se procesa.',
            'maquina_id.required'   => 'Debe seleccionar la maquina.',
            'operario_id.required'  => 'Debe seleccionar el responsable.',
        ]);

        try {
            $aux_datos = $this->datosLote($request->opdetregprod_id);
        } catch (\Exception $e) {
            return response()->json(['ok' => false, 'mensaje' => $e->getMessage()]);
        }

        DB::beginTransaction();
        try {
            $this->guardarFormaEmbalar($aux_datos['otdet'], $aux_datos['ot'], $request->formaembalar);

            $planilla = EtapaExtPlanilla::create([
                'opdetregprod_id' => $request->opdetregprod_id,
                'etapaprod_id'    => $request->etapaprod_id,
                'maquina_id'      => $request->maquina_id,
                'operario_id'     => $request->operario_id,
                'nrobolsas'       => $request->nrobolsas !== '' ? $request->nrobolsas : null,
                'obs'             => $request->obs,
                'usuario_id'      => auth()->id(),
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['ok' => false, 'mensaje' => 'No se pudo guardar la planilla: ' . $e->getMessage()]);
        }

        return response()->json(['ok' => true, 'id' => $planilla->id]);
    }

    /**
     * Guarda una tirada de etiquetas y devuelve su id para abrir la impresion.
     * Un registro por tirada, con la cantidad de copias.
     */
    public function guardarEtiqueta(Request $request)
    {
        can('crear-etapa-externa');

        $request->validate([
            'opdetregprod_id' => 'required|numeric',
            'etapaprod_id'    => 'required|numeric',
            'maquina_id'      => 'required|numeric',
            'operario_id'     => 'required|numeric',
            'fecha'           => 'required',
            'cantidad'        => 'required|numeric|min:1',
        ], [
            'maquina_id.required'  => 'Debe seleccionar la maquina.',
            'operario_id.required' => 'Debe seleccionar el operario.',
            'fecha.required'       => 'Debe indicar la fecha de produccion.',
            'cantidad.required'    => 'Debe indicar cuantas etiquetas imprimir.',
            'cantidad.min'         => 'La cantidad de etiquetas debe ser al menos 1.',
        ]);

        try {
            $this->datosLote($request->opdetregprod_id);   // valida el lote
        } catch (\Exception $e) {
            return response()->json(['ok' => false, 'mensaje' => $e->getMessage()]);
        }

        /* La fecha llega en formato chileno (DD/MM/AAAA) desde el datepicker.
           Se normaliza aqui porque la columna es date. */
        $aux_fecha = $request->fecha;
        if (strpos($aux_fecha, '/') !== false) {
            $aux_part = explode('/', $aux_fecha);
            if (count($aux_part) == 3) {
                $aux_fecha = $aux_part[2] . '-' . $aux_part[1] . '-' . $aux_part[0];
            }
        }

        DB::beginTransaction();
        try {
            $etiqueta = EtapaExtEtiqueta::create([
                'etapaextplanilla_id' => $request->etapaextplanilla_id ?: null,
                'opdetregprod_id'     => $request->opdetregprod_id,
                'etapaprod_id'        => $request->etapaprod_id,
                'maquina_id'          => $request->maquina_id,
                'operario_id'         => $request->operario_id,
                'fecha'               => $aux_fecha,
                'turno'               => $request->turno,
                'kilos'               => $request->kilos !== '' ? $request->kilos : null,
                'unidades'            => $request->unidades !== '' ? $request->unidades : null,
                'obs'                 => $request->obs,
                'cantidad'            => $request->cantidad,
                'usuario_id'          => auth()->id(),
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['ok' => false, 'mensaje' => 'No se pudo guardar la tirada: ' . $e->getMessage()]);
        }

        return response()->json(['ok' => true, 'id' => $etiqueta->id]);
    }

    /* ===================================================================
       IMPRESION
       =================================================================== */

    /**
     * Planilla imprimible (formato DP REG.06), con la etiqueta del lote de
     * origen incluida en la misma hoja: el operario recibe un solo papel y sabe
     * exactamente que rollo tomar. Se imprime por id, asi reimprimir es volver
     * a abrir la misma URL.
     */
    public function imprimirPlanilla($id)
    {
        can('listar-etapa-externa');
        $planilla  = EtapaExtPlanilla::findOrFail($id);
        $aux_datos = $this->datosLote($planilla->opdetregprod_id);

        $maquina  = Maquina::find($planilla->maquina_id);
        $operario = Operario::find($planilla->operario_id);

        return view('etapaexterna.planilla', [
            'planilla'      => $planilla,
            'datos'         => $aux_datos,
            'maquinaNombre' => $maquina ? $maquina->nombre : '—',
            'operarioNombre' => $operario ? $operario->nombre : '—',
            'empresaNombre' => optional(Empresa::first())->nombre ?? '',
            /* La forma de embalar se vuelve a resolver al imprimir y no se guarda
               en la planilla: si la corrigen despues, la reimpresion sale con el
               dato corregido. */
            'formaembalar'  => $aux_datos['formaembalar'],
        ]);
    }

    /**
     * Etiquetas de paquete: la misma etiqueta repetida tantas veces como diga
     * la tirada. Lleva el lote de origen y un QR con el id de esta tirada.
     */
    public function imprimirEtiqueta($id)
    {
        can('listar-etapa-externa');
        $etiqueta  = EtapaExtEtiqueta::findOrFail($id);
        $aux_datos = $this->datosLote($etiqueta->opdetregprod_id);

        $maquina  = Maquina::find($etiqueta->maquina_id);
        $operario = Operario::find($etiqueta->operario_id);

        return view('etapaexterna.etiqueta', [
            'etiqueta'       => $etiqueta,
            'datos'          => $aux_datos,
            'maquinaNombre'  => $maquina ? $maquina->nombre : '—',
            'operarioNombre' => $operario ? $operario->nombre : '—',
            'empresaNombre'  => optional(Empresa::first())->nombre ?? '',
        ]);
    }

    /* ===================================================================
       LISTADO GENERAL (trazabilidad)
       =================================================================== */

    public function listado()
    {
        can('listar-etapa-externa');
        return view('etapaexterna.listado');
    }

    /**
     * Datos del listado general. Muestra las tiradas de etiquetas, que son el
     * registro mas fino del proceso, con la planilla de la que salieron.
     */
    public function listadopage(Request $request)
    {
        can('listar-etapa-externa');

        $query = DB::table('etapaextetiqueta as e')
            ->leftJoin('etapaextplanilla as p', 'p.id', '=', 'e.etapaextplanilla_id')
            ->leftJoin('maquina as m', 'm.id', '=', 'e.maquina_id')
            ->leftJoin('operario as o', 'o.id', '=', 'e.operario_id')
            ->leftJoin('usuario as u', 'u.id', '=', 'e.usuario_id')
            ->leftJoin('etapaprod as ep', 'ep.id', '=', 'e.etapaprod_id')
            ->whereNull('e.deleted_at')
            ->select('e.id', 'e.opdetregprod_id', 'e.etapaextplanilla_id', 'e.fecha',
                     'e.turno', 'e.kilos', 'e.unidades', 'e.cantidad', 'e.obs',
                     'e.created_at', 'm.nombre as maquina', 'o.nombre as operario',
                     'u.nombre as usuario', 'ep.nombre as etapa',
                     /* Registros de produccion que consumieron el lote. Son
                        varios cuando el lote se proceso en mas de una vez, por
                        eso van concatenados. Derivado del FIFO entre etapas:
                        el modulo no guarda este dato (ver destinosLote). */
                     DB::raw("(SELECT GROUP_CONCAT(oo.opdetregprod_id ORDER BY oo.opdetregprod_id SEPARATOR ', ')
                               FROM   opdetregprod_origen oo
                               INNER  JOIN opdetregprod rr ON rr.id = oo.opdetregprod_id
                                                          AND ISNULL(rr.deleted_at)
                               WHERE  oo.opdetregprod_origen_id = e.opdetregprod_id) as destinos"));

        // Filtros opcionales de la pantalla
        if (!empty($request->opdetregprod_id)) {
            $query->where('e.opdetregprod_id', intval($request->opdetregprod_id));
        }
        /* Las fechas llegan en formato chileno desde el datepicker. Mismo
           tratamiento que usa consultaopdet en el registro de produccion. */
        if (!empty($request->fechad)) {
            $aux_f = date_create_from_format('d/m/Y', $request->fechad);
            if ($aux_f) {
                $query->where('e.created_at', '>=', date_format($aux_f, 'Y-m-d') . ' 00:00:00');
            }
        }
        if (!empty($request->fechah)) {
            $aux_f = date_create_from_format('d/m/Y', $request->fechah);
            if ($aux_f) {
                $query->where('e.created_at', '<=', date_format($aux_f, 'Y-m-d') . ' 23:59:59');
            }
        }

        // datatables() global, el patron que usa el resto del modulo
        return datatables($query->get())->toJson();
    }
}
