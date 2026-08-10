<?php

namespace App\Http\Controllers;

use App\Models\ApsucEtapaProdBodega;
use App\Models\CategoriaGrupoValMes;
use App\Models\EtapaProd;
use App\Models\InvBodega;
use App\Models\Maquina;
use App\Models\InvBodegaProducto;
use App\Models\InvControl;
use App\Models\InvMov;
use App\Models\InvMovDet;
use App\Models\InvMovDetNVDet;
use App\Models\InvMovDetOpDetRegProd;
use App\Models\OpDet;
use App\Models\OpDetRegProd;
use App\Models\OpDetRegProdCampoVal;
use App\Models\OpDetRegProdMaq;
use App\Models\OpDetRegProdOrigen;
use App\Models\OpDetRegProdTemp;
use App\Models\OpDetRegProdTempMaq;
use App\Models\OtDetNVDet;
use App\Models\Produccion;
use App\Models\Producto;
use App\Models\Seguridad\Usuario;
use App\Models\Sucursal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OpDetRegProdTempAprobSupController extends Controller
{
    public function etapaprod()
    {
        can('listar-registro-produccion-aprobar-supervidor');
        $data = EtapaProd::etapasProdxPersona();
        $aux_contEtapasProd = count($data);
        if($aux_contEtapasProd==0){
            return redirect()->route('inicio')->with('mensaje','No tiene etapas de produccion asignadas, consulte con el administrador del sistema.')->send();
        }
        if($aux_contEtapasProd==1){
            session(['etapaprod_id' => $data[0]->etapaprod_id]);
            return redirect()->route('opdetregprodtempaprobsup');
        }else{
            return redirect()->route('opdetregprodtempaprobsup_selecetapaprod');
        }
    }

    public function selecetapaprod(){
        can('listar-registro-produccion-aprobar-supervidor');
        return view('opdetregprodtempaprobsup.selecetapaprod');
    }
    public function selecetapaprodpage(){
        $datas = EtapaProd::etapasProdxPersona();
        return datatables($datas)->toJson();
    }

    public function setId($id)
    {
        session(['etapaprod_id' => $id]);
        return redirect()->route('opdetregprodtempaprobsup_index01');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-registro-produccion-aprobar-supervidor');
        $data = EtapaProd::etapasProdxPersona();
        $aux_contEtapasProd = count($data);
        if($aux_contEtapasProd==0){
            return redirect()->route('inicio')->with('mensaje','No tiene etapas de produccion asignadas, consulte con el administrador del sistema.')->send();
        }
        if($aux_contEtapasProd==1){
            session(['etapaprod_id' => $data[0]->etapaprod_id]);
            return redirect()->route('opdetregprodtempaprobsup_index01');
        }else{
            return redirect()->route('opdetregprodtempaprobsup_selecetapaprod');
        }

        //$datas = FormaPago::orderBy('id')->get();
        //return view('opdetregprodtempaprobsup.index');
    }

    public function index01()
    {
        can('listar-registro-produccion-aprobar-supervidor');
        //dd(session('etapaprod_id'));
        //$datas = FormaPago::orderBy('id')->get();
        $tablas["etapaprod"] = EtapaProd::findOrFail(session('etapaprod_id'));
        return view('opdetregprodtempaprobsup.index', compact('tablas'));
    }

    public function opdetregprodtempaprobsuppage(){
        $user = Usuario::findOrFail(auth()->id());
        $sucurArray = $user->sucursales->pluck('id')->toArray();
        $sucurcadena = implode(",", $sucurArray);
        $aux_etapaprod_id = session('etapaprod_id');

        $aux_statusaprob = "opdetregprodtemp.aprobstatus = 1";
        
        $sql = "SELECT opdetregprodtemp.id,opdetregprodtemp.es_muestra,otdet.id as otdet_id,opdet.op_id,opdetregprodtemp.opdet_id,
                sucursal.nombre AS sucursal_nombre,
                cliente.razonsocial,ot.id as ot_id,producto.id as producto_id,
                opdet.kg as opdet_kg,opdet.cant as opdet_cant,opdet.cantrec as opdet_cantrec,
                opdet.kgrec as opdet_kgrec,opdet.cantprod as opdet_cantprod,
                opdet.kgprod as opdet_kgprod,opdet.kgscrap as opdet_kgscrap,
                opdetregprodtemp.kgent,opdetregprodtemp.kgprod,opdetregprodtemp.kgscrap,
                opdetregprodtemp.cantent,opdetregprodtemp.cantprod,
                opdetregprodtemp.unidadmedidaent_id,opdetregprodtemp.unidadmedidasal_id,
                (opdet.kgrec - (opdet.kgprod + opdetregprodtemp.kgprod + opdetregprodtemp.kgscrap)) as kgsaldo,
                opdetregprodtemp.updated_at,
                UNIX_TIMESTAMP(opdetregprodtemp.updated_at) as updatednum_at,
                (CASE WHEN IFNULL(opdetregprodtemp.cantprod,0) > 0 THEN 1 ELSE 0 END) AS rollo_cerrado,
                operario.nombre AS operario_nombre,
                /* puede_aprobar: no existe anterior pendiente (0 o 1) DENTRO DEL MISMO ROLLO
                   (mismo opdet_id y sin registro cerrador entre medio). */
                (SELECT COUNT(*) FROM opdetregprodtemp t1
                    WHERE t1.opdet_id = opdetregprodtemp.opdet_id
                      AND t1.id < opdetregprodtemp.id
                      AND t1.aprobstatus IN (0,1)
                      AND t1.deleted_at IS NULL
                      AND NOT EXISTS (
                          SELECT 1 FROM opdetregprodtemp c1
                          WHERE c1.opdet_id = opdetregprodtemp.opdet_id
                            AND c1.id >= t1.id
                            AND c1.id < opdetregprodtemp.id
                            AND IFNULL(c1.cantprod,0) > 0
                            AND c1.deleted_at IS NULL
                      )) = 0 AS puede_aprobar,
                /* puede_rechazar: no existe posterior no-rechazado DENTRO DEL MISMO ROLLO. */
                (SELECT COUNT(*) FROM opdetregprodtemp t2
                    WHERE t2.opdet_id = opdetregprodtemp.opdet_id
                      AND t2.id > opdetregprodtemp.id
                      AND (t2.aprobstatus IS NULL OR t2.aprobstatus NOT IN (3,4))
                      AND t2.deleted_at IS NULL
                      AND NOT EXISTS (
                          SELECT 1 FROM opdetregprodtemp c2
                          WHERE c2.opdet_id = opdetregprodtemp.opdet_id
                            AND c2.id >= opdetregprodtemp.id
                            AND c2.id < t2.id
                            AND IFNULL(c2.cantprod,0) > 0
                            AND c2.deleted_at IS NULL
                      )) = 0 AS puede_rechazar,
                maquina.nombre AS maquina_nombre,
                unidadmedidasal.nombre AS unidadmedidasal_nombre,
                acuerdotecnico.id AS acuerdotecnico_id,acuerdotecnico.at_impresofoto
                from opdetregprodtemp INNER JOIN sucursal
                ON opdetregprodtemp.sucursal_id=sucursal.id
                INNER JOIN producto
                ON opdetregprodtemp.producto_id=producto.id
                INNER JOIN etapaprod
                ON opdetregprodtemp.etapaprod_id=etapaprod.id
                INNER JOIN opdet
                ON opdetregprodtemp.opdet_id=opdet.id
                LEFT JOIN opdetmaquina
                ON opdetmaquina.opdet_id=opdet.id
                LEFT JOIN maquina
                ON maquina.id=opdetmaquina.maquina_id
                LEFT JOIN unidadmedida AS unidadmedidasal
                ON unidadmedidasal.id=opdetregprodtemp.unidadmedidasal_id
                INNER JOIN op
                ON opdet.op_id=op.id
                INNER JOIN otdet
                ON op.otdet_id=otdet.id
                INNER JOIN ot
                ON otdet.ot_id=ot.id
                INNER JOIN cliente
                ON ot.cliente_id=cliente.id
                INNER JOIN operario
                ON opdetregprodtemp.operario_id=operario.id
                LEFT JOIN acuerdotecnico
                ON acuerdotecnico.producto_id=producto.id
                where opdetregprodtemp.sucursal_id IN ($sucurcadena)
                AND etapaprod.id=$aux_etapaprod_id
                AND $aux_statusaprob
                AND isnull(opdetregprodtemp.deleted_at);";
        //dd($sql);
        $datas = DB::select($sql);
        foreach ($datas as &$data) {
            $data->producto_nombre = Producto::atributosProducto($data->producto_id)["nombre"];
        }
        return datatables($datas)->toJson();
    }

    public function listaropdet()
    {
        $fechaAct = date("d/m/Y");
        $user = Usuario::findOrFail(auth()->id());
        $tablashtml['sucurArray'] = $user->sucursales->pluck('id')->toArray(); //$clientesArray['sucurArray'];
        $tablashtml['sucursales'] = Sucursal::orderBy('id')->whereIn('sucursal.id', $tablashtml['sucurArray'])->get();
        $tablashtml["etapaprod"] = EtapaProd::findOrFail(session('etapaprod_id'));
        return view('opdetregprodtempaprobsup.listaropdet', compact('fechaAct','tablashtml'));
    }
    public function listaropdetpage(Request $request){
        $datas = consultaopdet($request);
        return datatables($datas)->toJson();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function editar($cadena)
    {
        can('editar-registro-produccion');
        // Separamos por el carácter "&"
        list($id, $updatednum_at) = explode("&", $cadena);
        $data = OpDetRegProdTemp::findOrFail($id);
        if(strtotime($data->updated_at) != $updatednum_at){
            return redirect('opdetregprodtempaprobsup/index01')->with([
                'mensaje'=>'Registro Editado por otro usuario. Fecha Hora: '.$data->updated_at,
                'tipo_alert' => 'alert-error'
            ]);    
        }
        //dd($id);
        /* $opdet_id = session('opdet_id');
        $updatednum_at = session('opdet_updatednum_at'); */
        $opdet = OpDet::findOrFail($data->opdet_id);
        $tablas["operarios"] = consultaOperarios($data->etapaprod_id);
        return view('opdetregprodtempaprobsup.editar', compact('data','opdet','tablas'));
    }

    public function aprob(Request $request)
    {
        if ($request->ajax()) {
            //dd($request);
            // 1️⃣ Obtener el registro base
            $opdetregprodtemp = OpDetRegProdTemp::findOrFail($request->id);
            $aux_etapaprod_orden = $opdetregprodtemp->opdet->areaproduccionsucetapaprod->orden;
            $op = $opdetregprodtemp->opdet->op;
            foreach ($op->opdets as &$opdet) {
                $opdet->orden = $opdet->areaproduccionsucetapaprod->orden;
            }

            if($opdetregprodtemp == null){
                return response()->json([
                    'resp' => 0,
                    'tipmen' => 'error',
                    'mensaje' => 'Registro eliminado por otro usuario.'
                ]);
            }
            if(strtotime($opdetregprodtemp->updated_at) != $request->updated_at){
                return response()->json([
                    'resp' => 0,
                    'tipmen' => 'error',
                    'mensaje'=>'Registro fué modificado por otro usuario.'
                ]);
            }

            // ──────────────────────────────────────────────────────────────────
            // Validacion de orden ASC/DESC dentro del mismo opdet_id
            // Aprobar  (staaprob=2)   : no puede haber registros anteriores (id<N)
            //                           con aprobstatus IN (0,1) pendientes.
            // Rechazar (staaprob=3/4) : no puede haber registros posteriores (id>N)
            //                           con aprobstatus distinto de 3/4 (es decir,
            //                           pendientes o aprobados). Si hay aprobados
            //                           posteriores, se debera usar la funcionalidad
            //                           futura "Anular registro aprobado".
            // ──────────────────────────────────────────────────────────────────
            if ($request->staaprob == 2) { // APROBAR
                $previoBloqueante = OpDetRegProdTemp::buscarEnMismoRollo(
                    $opdetregprodtemp->opdet_id,
                    $opdetregprodtemp->id,
                    'anterior',
                    function ($q) { $q->whereIn('aprobstatus', [0, 1]); }
                );
                if ($previoBloqueante) {
                    return response()->json([
                        'resp' => 0,
                        'tipmen' => 'error',
                        'mensaje' => 'No se puede aprobar este registro. Debe aprobar primero el registro anterior id='
                            . $previoBloqueante->id . ' (del mismo OpDet y rollo abierto). La aprobacion debe hacerse en orden ascendente.'
                    ]);
                }
            } elseif (in_array($request->staaprob, [3, 4])) { // RECHAZAR
                $posteriorBloqueante = OpDetRegProdTemp::buscarEnMismoRollo(
                    $opdetregprodtemp->opdet_id,
                    $opdetregprodtemp->id,
                    'posterior',
                    function ($q) {
                        $q->where(function ($q2) {
                            $q2->whereNull('aprobstatus')->orWhereNotIn('aprobstatus', [3, 4]);
                        });
                    }
                );
                if ($posteriorBloqueante) {
                    $msgExtra = ($posteriorBloqueante->aprobstatus == 2)
                        ? ' Como el posterior ya fue aprobado, primero se debe anularlo (funcionalidad en desarrollo).'
                        : '';
                    return response()->json([
                        'resp' => 0,
                        'tipmen' => 'error',
                        'mensaje' => 'No se puede rechazar este registro. Debe rechazar primero el registro posterior id='
                            . $posteriorBloqueante->id . ' (del mismo OpDet y rollo abierto). El rechazo debe hacerse en orden descendente.'
                            . $msgExtra
                    ]);
                }
            }

            DB::beginTransaction();
            try {
                $usuario = Usuario::findOrFail(auth()->id());
                $opdet = OpDet::findOrFail($opdetregprodtemp->opdet_id);
                $opdet->updated_at = date("Y-m-d H:i:s");
                $opdetregprodtemp->updated_at = date("Y-m-d H:i:s");
                $opdetregprodtemp->aprobstatus = $request->staaprob;
                if($request->obsaprob){
                    //$opdetregprodtemp->aprobobs = ($request->obsaprob ?? "") . $request->obsaprob ?? " / Usuario: " . auth()->id() . " " . $usuario->nombre . " " . date("d-m-Y H:i:s");
                    $opdetregprodtemp->aprobobs = $request->obsaprob;
                    if($opdetregprodtemp->aprobstatus == 3){
                        $opdetregprodtemp->aprobobs .= " / Usuario: " . auth()->id() . " " . $usuario->nombre . " " . date("d-m-Y H:i:s");
                    }
                }
                $esUltimaEtapa = false;
                if($opdetregprodtemp->aprobstatus == 2){
                    // 1️⃣ Copiar todos los atributos del temp al registro definitivo
                    // Muestras físicas (es_muestra=1) TAMBIÉN crean opdetregprod para
                    // que CC pueda vincular su registro. El trigger de opdetregprod
                    // respeta es_muestra=1 y no acumula kg en opdet.
                    $data = $opdetregprodtemp->toArray();
                    unset($data['id'], $data['created_at'], $data['updated_at'], $data['deleted_at']);
                    $data['opdetregprodtemp_id'] = $opdetregprodtemp->id;
                    $data['usuario_id'] = auth()->id();

                    // 2️⃣ Crear el registro definitivo en opdetregprod (necesario antes del else
                    //    para poder usar $produccion->id en la trazabilidad de inventario)
                    //$produccion = OpDetRegProd::create($data);
                    $opdetregprod = OpDetRegProd::create($data);

                    // Copiar campos adicionales de temp → aprobado.
                    // Si la etapa no tiene campos configurados, campovalues estará vacío
                    // y este bloque no hace nada. No afecta el flujo existente.
                    foreach ($opdetregprodtemp->campovalues as $tempVal) {
                        OpDetRegProdCampoVal::create([
                            'opdetregprod_id'    => $opdetregprod->id,
                            'etapaprod_campo_id' => $tempVal->etapaprod_campo_id,
                            // Normalizar decimal a "." al copiar a registro aprobado
                            'valor'              => str_replace(',', '.', $tempVal->valor),
                        ]);
                    }

                    // Trazabilidad entre etapas: consolidar la reserva FIFO del temp
                    // (opdetregprodtemp_origen) como consumo definitivo del registro
                    // aprobado (opdetregprod_origen). Al quedar el temp en status 2,
                    // su reserva deja de contar en el cálculo de saldo (sin doble conteo).
                    foreach ($opdetregprodtemp->origenes as $tempOrigen) {
                        OpDetRegProdOrigen::create([
                            'opdetregprod_id'        => $opdetregprod->id,
                            'opdetregprod_origen_id' => $tempOrigen->opdetregprod_id,
                            'kg'                     => $tempOrigen->kg,
                            'cant'                   => $tempOrigen->cant,
                        ]);
                    }

                    // Copiar máquina de temp → aprobado (si la etapa usa máquina).
                    $tempMaq = OpDetRegProdTempMaq::where('opdetregprodtemp_id', $opdetregprodtemp->id)->first();
                    if ($tempMaq) {
                        OpDetRegProdMaq::create([
                            'opdetregprod_id' => $opdetregprod->id,
                            'maquina_id'      => $tempMaq->maquina_id,
                        ]);
                    }

                    // R1: las muestras físicas no transfieren kg ni generan inventario
                    if ($opdetregprodtemp->es_muestra) {
                        $opdet->save();
                        $opdetregprodtemp->save();
                        DB::commit();
                        return response()->json([
                            'resp'              => 1,
                            'tipmen'            => 'success',
                            'mensaje'           => 'Muestra física aprobada.',
                            'opdetregprod_id'   => $opdetregprod->id,
                            'es_ultima_etapa'   => 0,
                        ]);
                    }

                    // 3️⃣ Obtener la siguiente etapa de producción (la de mayor orden inmediato)
                    $siguienteEtapa = collect($op->opdets)
                        ->where('orden', '>', $aux_etapaprod_orden)
                        ->sortBy('orden')
                        ->first();

                    if ($siguienteEtapa) {
                        // Hay siguiente etapa: transferir kg producidos / cant producida a la siguiente etapa.
                        // Se transfiere kgprod (sin scrap) y cantprod (unidades cerradas en UM salida
                        // de la etapa actual = UM entrada de la siguiente).
                        $opdetSig = OpDet::findOrFail($siguienteEtapa->id);
                        $kgTransfer   = (float) ($opdetregprodtemp->kgprod ?? 0);
                        $cantTransfer = (float) ($opdetregprodtemp->cantprod ?? 0);

                        if($opdetSig->kgrec == 0){
                            $opdetSig->saldokg = $kgTransfer;
                        }else{
                            $opdetSig->saldokg += $kgTransfer;
                        }
                        $opdetSig->cantrec += $cantTransfer;
                        $opdetSig->kgrec   += $kgTransfer;
                        $opdetSig->save();

                        // ──────────────────────────────────────────────────────────
                        // Movimiento de inventario para etapa INTERMEDIA.
                        // Solo se ejecuta si la etapa tiene bodegas configuradas en
                        // apsucetapaprod_bodega. Sin bodegas: no genera movimiento
                        // y el flujo de producción sigue igual que antes.
                        // ──────────────────────────────────────────────────────────
                        $apsucActual   = $opdetregprodtemp->opdet->areaproduccionsucetapaprod;
                        $bodegasEtapa  = ApsucEtapaProdBodega::where('apsucetapaprod_id', $apsucActual->id)
                            ->with('invbodega')
                            ->get();

                        if ($bodegasEtapa->isNotEmpty()) {
                            // Validar período de inventario no cerrado (igual que última etapa)
                            $annomesInv = date('Ym');
                            $periodoInv = InvControl::where('annomes', $annomesInv)
                                ->where('sucursal_id', $opdetregprodtemp->sucursal_id)
                                ->where('status', 1)
                                ->count();
                            if ($periodoInv > 0) {
                                throw new \Exception('El período ' . $annomesInv . ' está cerrado para esta sucursal.');
                            }

                            // Si hay más de 1 bodega y el usuario no eligió, pedir selección
                            if ($bodegasEtapa->count() > 1 && !$request->invbodega_id) {
                                DB::rollBack();
                                return response()->json([
                                    'resp'    => 2,
                                    'tipmen'  => 'info',
                                    'mensaje' => 'Seleccione la bodega de producción destino.',
                                    'bodegas' => $bodegasEtapa->map(function ($b) {
                                        return [
                                            'id'     => $b->invbodega_id,
                                            'nombre' => $b->invbodega->nombre ?? '—',
                                        ];
                                    }),
                                ]);
                            }

                            $invbodega_id_actual = $request->invbodega_id
                                ? (int) $request->invbodega_id
                                : $bodegasEtapa->first()->invbodega_id;

                            // ENTRADA: producción de la etapa actual entra a su bodega
                            $invmovEntrada = InvMov::create([
                                'fechahora'       => date('Y-m-d H:i:s'),
                                'annomes'         => $annomesInv,
                                'desc'            => 'Prod.Etapa OT:'
                                                     . $opdetregprod->opdet->op->otdet->ot_id
                                                     . ' OP:' . $opdetregprod->opdet->op_id
                                                     . ' Reg:' . $opdetregprod->id,
                                'obs'             => 'Etapa: ' . ($apsucActual->etapaprod->nombre ?? ''),
                                'invmovmodulo_id' => 9,   // Producción
                                'idmovmod'        => $opdetregprod->id,
                                'invmovtipo_id'   => 1,   // Entrada
                                'sucursal_id'     => $opdetregprodtemp->sucursal_id,
                                'usuario_id'      => auth()->id(),
                            ]);

                            $productoEnt = Producto::findOrFail($opdetregprodtemp->producto_id);
                            $ibpEntrada  = InvBodegaProducto::updateOrCreate(
                                ['producto_id' => $opdetregprodtemp->producto_id, 'invbodega_id' => $invbodega_id_actual],
                                ['producto_id' => $opdetregprodtemp->producto_id, 'invbodega_id' => $invbodega_id_actual]
                            );
                            $invmovdetEntrada = InvMovDet::create([
                                'invmov_id'            => $invmovEntrada->id,
                                'invbodegaproducto_id' => $ibpEntrada->id,
                                'producto_id'          => $opdetregprodtemp->producto_id,
                                'invbodega_id'         => $invbodega_id_actual,
                                'sucursal_id'          => $opdetregprodtemp->sucursal_id,
                                'unidadmedida_id'      => $opdetregprodtemp->unidadmedidasal_id,
                                'invmovtipo_id'        => 1,
                                'cant'                 => $opdetregprodtemp->cantprod,
                                'cantgrupo'            => $opdetregprodtemp->cantprod,
                                'cantxgrupo'           => 1,
                                'peso'                 => $productoEnt->peso ?? 0,
                                'cantkg'               => $opdetregprodtemp->kgprod,
                            ]);
                            InvMovDetOpDetRegProd::create([
                                'invmovdet_id'    => $invmovdetEntrada->id,
                                'opdetregprod_id' => $opdetregprod->id,
                            ]);

                            // SALIDA: descontar de la bodega donde estaba el lote origen.
                            // Se basa en la trazabilidad FIFO ($opdetregprodtemp->origenes).
                            // Si el lote anterior no tiene invmovdet (histórico pre-implementación)
                            // se omite la salida para no romper lotes existentes.
                            foreach ($opdetregprodtemp->origenes as $tempOrigen) {
                                // Buscar el invmovdet de ENTRADA asociado al lote origen
                                $prevInvMovDet = InvMovDet::join(
                                        'invmovdet_opdetregprod as lnk_sal',
                                        'lnk_sal.invmovdet_id', '=', 'invmovdet.id'
                                    )
                                    ->where('lnk_sal.opdetregprod_id', $tempOrigen->opdetregprod_id)
                                    ->where('invmovdet.invmovtipo_id', 1)
                                    ->whereNull('invmovdet.deleted_at')
                                    ->select('invmovdet.*')
                                    ->first();

                                if (!$prevInvMovDet) {
                                    // Lote histórico sin movimiento de inventario: omitir salida
                                    continue;
                                }

                                // Cantidad proporcional en la UM del lote origen
                                $prevReg    = OpDetRegProd::find($tempOrigen->opdetregprod_id);
                                $cantSalida = 0;
                                if ($prevReg && $prevReg->kgprod > 0) {
                                    $cantSalida = round(
                                        ($tempOrigen->kg / $prevReg->kgprod) * ($prevReg->cantprod ?? 0),
                                        4
                                    );
                                }

                                $invmovSalida = InvMov::create([
                                    'fechahora'       => date('Y-m-d H:i:s'),
                                    'annomes'         => $annomesInv,
                                    'desc'            => 'Salida a etapa siguiente - Reg:' . $opdetregprod->id,
                                    'obs'             => 'Consumido por: ' . ($apsucActual->etapaprod->nombre ?? ''),
                                    'invmovmodulo_id' => 9,
                                    'idmovmod'        => $opdetregprod->id,
                                    'invmovtipo_id'   => 2,   // Salida
                                    'sucursal_id'     => $opdetregprodtemp->sucursal_id,
                                    'usuario_id'      => auth()->id(),
                                ]);

                                $ibpSalida = InvBodegaProducto::updateOrCreate(
                                    ['producto_id' => $opdetregprodtemp->producto_id, 'invbodega_id' => $prevInvMovDet->invbodega_id],
                                    ['producto_id' => $opdetregprodtemp->producto_id, 'invbodega_id' => $prevInvMovDet->invbodega_id]
                                );
                                $invmovdetSalida = InvMovDet::create([
                                    'invmov_id'            => $invmovSalida->id,
                                    'invbodegaproducto_id' => $ibpSalida->id,
                                    'producto_id'          => $opdetregprodtemp->producto_id,
                                    'invbodega_id'         => $prevInvMovDet->invbodega_id,
                                    'sucursal_id'          => $opdetregprodtemp->sucursal_id,
                                    'unidadmedida_id'      => $prevInvMovDet->unidadmedida_id,
                                    'invmovtipo_id'        => 2,
                                    // cant negativo porque tipomov de Salida = -1 y el sistema
                                    // almacena cant ya aplicado el signo (igual que inventsal)
                                    'cant'                 => -$cantSalida,
                                    'cantgrupo'            => -$cantSalida,
                                    'cantxgrupo'           => 1,
                                    'peso'                 => $prevInvMovDet->peso,
                                    'cantkg'               => -$tempOrigen->kg,
                                ]);
                                // Enlazar la salida con el lote origen (del que se descontó)
                                InvMovDetOpDetRegProd::create([
                                    'invmovdet_id'    => $invmovdetSalida->id,
                                    'opdetregprod_id' => $tempOrigen->opdetregprod_id,
                                ]);
                            }
                        }
                        // ── fin movimiento inventario etapa intermedia ─────────────

                    } else {
                        // 4️⃣ Última etapa: ingresar producto a bodega de Producción (tipo=5)
                        $esUltimaEtapa = true;

                        // Verificar período de inventario no cerrado
                        // date('Ym') ya devuelve 'yyyymm'; CategoriaGrupoValMes::annomes()
                        // convierte "Mes Año" textual a yyyymm y no aplica aquí.
                        $annomes = date('Ym');
                        $periodoVigente = InvControl::where('annomes', $annomes)
                            ->where('sucursal_id', $opdetregprodtemp->sucursal_id)
                            ->where('status', 1)
                            ->count();
                        if ($periodoVigente > 0) {
                            throw new \Exception('El período ' . $annomes . ' está cerrado. No se puede ingresar a bodega de producción.');
                        }

                        // Si la ÚLTIMA etapa tiene bodegas configuradas explícitamente en
                        // apsucetapaprod_bodega (igual que cualquier etapa intermedia), usar esas.
                        // Antes esta rama excluía TODA bodega que apareciera en apsucetapaprod_bodega
                        // asumiendo que esa tabla solo se usaba para etapas intermedias — pero si la
                        // última etapa también tiene bodegas configuradas ahí, quedaban excluidas por
                        // error y el sistema caía en la bodega "Producción" general en su lugar
                        // (bug detectado 2026-08-07, RegProdId 14). Si la etapa no tiene ninguna
                        // bodega configurada, se mantiene el fallback histórico: bodega tipo=5 que no
                        // esté asignada a NINGUNA etapa (para no romper sucursales/etapas que nunca
                        // configuraron esto).
                        $apsucActualFinal  = $opdetregprodtemp->opdet->areaproduccionsucetapaprod;
                        $bodegasEtapaFinal = ApsucEtapaProdBodega::where('apsucetapaprod_id', $apsucActualFinal->id)
                            ->with('invbodega')
                            ->get();

                        if ($bodegasEtapaFinal->isNotEmpty()) {
                            $bodegasProduccion = $bodegasEtapaFinal->map(function ($b) {
                                return (object) ['id' => $b->invbodega_id, 'nombre' => $b->invbodega->nombre ?? '—'];
                            });
                        } else {
                            $bodegasProduccion = InvBodega::where('tipo', 5)
                                ->where('sucursal_id', $opdetregprodtemp->sucursal_id)
                                ->whereNull('deleted_at')
                                ->whereNotIn('id', function($q) {
                                    $q->select('invbodega_id')->from('apsucetapaprod_bodega');
                                })
                                ->get();
                        }

                        if ($bodegasProduccion->isEmpty()) {
                            throw new \Exception('No existe bodega de producción final configurada para esta etapa/sucursal.');
                        }

                        // Si hay más de 1 bodega general y el usuario aún no eligió, solicitar selección
                        if ($bodegasProduccion->count() > 1 && !$request->invbodega_id) {
                            DB::rollBack();
                            return response()->json([
                                'resp'    => 2,
                                'tipmen'  => 'info',
                                'mensaje' => 'Seleccione la bodega de producción destino.',
                                'bodegas' => $bodegasProduccion->map(function($b){
                                    return ['id' => $b->id, 'nombre' => $b->nombre];
                                }),
                            ]);
                        }

                        // Usar bodega del request solo si pertenece a las bodegas finales válidas
                        // (previene que el JS envíe una bodega de etapa intermedia por error)
                        $idsBodsValidas = $bodegasProduccion->pluck('id')->toArray();
                        $invbodega_id = ($request->invbodega_id && in_array((int)$request->invbodega_id, $idsBodsValidas))
                            ? (int)$request->invbodega_id
                            : $bodegasProduccion->first()->id;

                        // Crear el movimiento de inventario de entrada
                        $invmov = InvMov::create([
                            'fechahora'       => date('Y-m-d H:i:s'),
                            'annomes'         => $annomes,
                            'desc'            => 'Ingreso produccion - OT: ' . $opdetregprod->opdet->op->otdet->ot_id . ' OP: ' . $opdetregprod->opdet->op_id . ' RegProdId: ' . $opdetregprod->id,
                            'obs'             => 'OP: ' . $opdetregprod->opdet->op_id,
                            'invmovmodulo_id' => 9,               // Producción
                            'idmovmod'        => $opdetregprod->id, // Trazabilidad → opdetregprod
                            'invmovtipo_id'   => 1,               // Entrada
                            'sucursal_id'     => $opdetregprodtemp->sucursal_id,
                            'usuario_id'      => auth()->id(),
                        ]);

                        // Buscar o crear el registro InvBodegaProducto
                        $invbodegaproducto = InvBodegaProducto::updateOrCreate(
                            [
                                'producto_id'  => $opdetregprodtemp->producto_id,
                                'invbodega_id' => $invbodega_id,
                            ],
                            [
                                'producto_id'  => $opdetregprodtemp->producto_id,
                                'invbodega_id' => $invbodega_id,
                            ]
                        );

                        // Crear el detalle del movimiento
                        $producto = Producto::findOrFail($opdetregprodtemp->producto_id);
                        $invmovdet = InvMovDet::create([
                            'invmov_id'            => $invmov->id,
                            'invbodegaproducto_id' => $invbodegaproducto->id,
                            'producto_id'          => $opdetregprodtemp->producto_id,
                            'invbodega_id'         => $invbodega_id,
                            'sucursal_id'          => $opdetregprodtemp->sucursal_id,
                            'unidadmedida_id'      => $producto->categoriaprod->unidadmedida_id,
                            'invmovtipo_id'        => 1,
                            'cant'                 => $opdetregprodtemp->cantprod,
                            'cantgrupo'            => $opdetregprodtemp->cantprod,
                            'cantxgrupo'           => 1,
                            'peso'                 => $producto->peso,
                            'cantkg'               => $opdetregprodtemp->kgprod,
                        ]);

                        // Resolver notaventadetalle_id para trazabilidad NV → bodega producción.
                        // Cadena: opdetregprodtemp → opdet → op.otdet_id → otdetnvdet → notaventadetalle_id
                        // (otdet_id está en op, no en opdet)
                        // NULL cuando la OT no proviene de ninguna NV (producción para stock).
                        $nvdetIdBodega = null;
                        $otdetnvdetBodega = OtDetNVDet::where(
                            'otdet_id', $opdetregprodtemp->opdet->op->otdet_id
                        )->first();
                        if ($otdetnvdetBodega) {
                            $nvdetIdBodega = $otdetnvdetBodega->notaventadetalle_id;
                        }

                        // Registrar trazabilidad: invmovdet ↔ opdetregprod ↔ notaventadetalle
                        InvMovDetOpDetRegProd::create([
                            'invmovdet_id'        => $invmovdet->id,
                            'opdetregprod_id'     => $opdetregprod->id,
                            'notaventadetalle_id' => $nvdetIdBodega,
                        ]);
                        InvMovDetNVDet::create([
                            'invmovdet_id'        => $invmovdet->id,
                            'notaventadetalle_id' => $nvdetIdBodega,
                        ]);

                        // SALIDA: descontar de la bodega donde estaba el lote origen.
                        // Se basa en la trazabilidad FIFO ($opdetregprodtemp->origenes).
                        // Si el lote anterior no tiene invmovdet (histórico pre-implementación)
                        // se omite la salida para no romper lotes existentes.
                        // (Antes este bloque solo existia en la rama de etapa intermedia; en la
                        // ultima etapa se creaba la entrada a la bodega final pero nunca se
                        // descontaba de la bodega de la etapa anterior — bug detectado 2026-08-07).
                        foreach ($opdetregprodtemp->origenes as $tempOrigen) {
                            // Buscar el invmovdet de ENTRADA asociado al lote origen
                            $prevInvMovDet = InvMovDet::join(
                                    'invmovdet_opdetregprod as lnk_sal',
                                    'lnk_sal.invmovdet_id', '=', 'invmovdet.id'
                                )
                                ->where('lnk_sal.opdetregprod_id', $tempOrigen->opdetregprod_id)
                                ->where('invmovdet.invmovtipo_id', 1)
                                ->whereNull('invmovdet.deleted_at')
                                ->select('invmovdet.*')
                                ->first();

                            if (!$prevInvMovDet) {
                                // Lote histórico sin movimiento de inventario: omitir salida
                                continue;
                            }

                            // Cantidad proporcional en la UM del lote origen
                            $prevReg    = OpDetRegProd::find($tempOrigen->opdetregprod_id);
                            $cantSalida = 0;
                            if ($prevReg && $prevReg->kgprod > 0) {
                                $cantSalida = round(
                                    ($tempOrigen->kg / $prevReg->kgprod) * ($prevReg->cantprod ?? 0),
                                    4
                                );
                            }

                            $invmovSalida = InvMov::create([
                                'fechahora'       => date('Y-m-d H:i:s'),
                                'annomes'         => $annomes,
                                'desc'            => 'Salida a bodega producción final - Reg:' . $opdetregprod->id,
                                'obs'             => 'Consumido por: ' . ($apsucActualFinal->etapaprod->nombre ?? ''),
                                'invmovmodulo_id' => 9,
                                'idmovmod'        => $opdetregprod->id,
                                'invmovtipo_id'   => 2,   // Salida
                                'sucursal_id'     => $opdetregprodtemp->sucursal_id,
                                'usuario_id'      => auth()->id(),
                            ]);

                            $ibpSalida = InvBodegaProducto::updateOrCreate(
                                ['producto_id' => $opdetregprodtemp->producto_id, 'invbodega_id' => $prevInvMovDet->invbodega_id],
                                ['producto_id' => $opdetregprodtemp->producto_id, 'invbodega_id' => $prevInvMovDet->invbodega_id]
                            );
                            $invmovdetSalida = InvMovDet::create([
                                'invmov_id'            => $invmovSalida->id,
                                'invbodegaproducto_id' => $ibpSalida->id,
                                'producto_id'          => $opdetregprodtemp->producto_id,
                                'invbodega_id'         => $prevInvMovDet->invbodega_id,
                                'sucursal_id'          => $opdetregprodtemp->sucursal_id,
                                'unidadmedida_id'      => $prevInvMovDet->unidadmedida_id,
                                'invmovtipo_id'        => 2,
                                // cant negativo porque tipomov de Salida = -1 y el sistema
                                // almacena cant ya aplicado el signo (igual que inventsal)
                                'cant'                 => -$cantSalida,
                                'cantgrupo'            => -$cantSalida,
                                'cantxgrupo'           => 1,
                                'peso'                 => $prevInvMovDet->peso,
                                'cantkg'               => -$tempOrigen->kg,
                            ]);
                            // Enlazar la salida con el lote origen (del que se descontó)
                            InvMovDetOpDetRegProd::create([
                                'invmovdet_id'    => $invmovdetSalida->id,
                                'opdetregprod_id' => $tempOrigen->opdetregprod_id,
                            ]);
                        }
                    }
                }
                $opdet->save();
                $opdetregprodtemp->save();

                DB::commit();

                $respData = [
                    'resp'    => 1,
                    'tipmen'  => 'success',
                    'mensaje' => 'Actualizado con exito.',
                ];
                // Si se aprobó (no rechazado), incluir id de producción para etiquetas
                if ($opdetregprodtemp->aprobstatus == 2 && isset($opdetregprod)) {
                    $respData['opdetregprod_id'] = $opdetregprod->id;
                    $respData['es_ultima_etapa'] = $esUltimaEtapa ? 1 : 0;
                }
                return response()->json($respData);

            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    'resp' => 0,
                    'tipmen' => 'error',
                    'mensaje'=> "Error: " . $e->getMessage(),
                    'tipo_alert' => 'alert-error'
                ]);
            }
        }
    }

    /**
     * Devuelve las bodegas configuradas para la etapa del registro temp dado.
     * Usado por el modal de aprobación del supervisor para pre-cargar el select de bodega.
     */
    public function getBodegasParaTemp($id)
    {
        $temp     = OpDetRegProdTemp::with('opdet.areaproduccionsucetapaprod')->findOrFail($id);
        $apsucActual = $temp->opdet->areaproduccionsucetapaprod ?? null;

        $bodegas = [];
        if ($apsucActual) {
            $bodegasEtapa = ApsucEtapaProdBodega::where('apsucetapaprod_id', $apsucActual->id)
                ->with('invbodega')
                ->get();
            $bodegas = $bodegasEtapa->map(function ($b) {
                return [
                    'id'     => $b->invbodega_id,
                    'nombre' => $b->invbodega->nombre ?? '—',
                ];
            })->values()->toArray();
        }

        return response()->json([
            'bodegas' => $bodegas,
            'count'   => count($bodegas),
        ]);
    }

    /**
     * Etiqueta de bodega (última etapa) — con QR apuntando a opdetregprod_id.
     * Sirve tanto para impresión inmediata como para reimpresión posterior.
     */
    public function etiquetaBodega($opdetregprod_id)
    {
        can('listar-registro-produccion-aprobar-supervidor');
        $produccion = OpDetRegProd::findOrFail($opdetregprod_id);
        $opdet = $produccion->opdet;
        $op    = $opdet->op;
        $otdet = $op->otdet;
        $ot    = $otdet->ot;

        // Nombre del producto usando la función global del proyecto
        $productoData = Producto::atributosProducto($produccion->producto_id);
        $productoNombre = $productoData['nombre'] ?? '—';
        $producto_id = $produccion->producto_id;

        // Nota de Venta (si el OT tiene relación con NV)
        $notaventa_id = null;
        if ($otdet->otdetnvdet && $otdet->otdetnvdet->notaventadetalle) {
            $notaventa_id = $otdet->otdetnvdet->notaventadetalle->notaventa_id;
        }

        // Bodega real a la que ingresó este registro (vía el movimiento de entrada
        // de inventario ya creado al aprobar), en vez del texto fijo "Bodega Producción".
        $bodegaEntrada = InvMovDet::join('invmovdet_opdetregprod as lnk_bod', 'lnk_bod.invmovdet_id', '=', 'invmovdet.id')
            ->where('lnk_bod.opdetregprod_id', $produccion->id)
            ->where('invmovdet.invmovtipo_id', 1)
            ->whereNull('invmovdet.deleted_at')
            ->select('invmovdet.invbodega_id')
            ->first();
        $bodegaNombre = $bodegaEntrada
            ? (InvBodega::find($bodegaEntrada->invbodega_id)->nombre ?? 'Bodega Producción')
            : 'Bodega Producción';

        return view('opdetregprodtempaprobsup.etiqueta-bodega', compact(
            'produccion', 'opdet', 'op', 'otdet', 'ot',
            'productoNombre', 'producto_id', 'notaventa_id', 'bodegaNombre'
        ));
    }

    /**
     * Etiqueta de etapa intermedia — muestra datos de la etapa actual y la próxima.
     * Sirve tanto para impresión inmediata como para reimpresión posterior.
     */
    public function etiquetaEtapa($opdetregprod_id)
    {
        can('listar-registro-produccion-aprobar-supervidor');
        $produccion = OpDetRegProd::findOrFail($opdetregprod_id);
        $opdet = $produccion->opdet;
        $op    = $opdet->op;
        $otdet = $op->otdet;
        $ot    = $otdet->ot;

        // Nombre del operario
        $operario = \App\Models\Operario::find($produccion->operario_id);
        $operarioNombre = $operario ? $operario->nombre : '—';

        // Máquina asociada al opdet (si existe)
        $maquina = null;
        $maquinaNombre = '—';
        if ($opdet->opdetmaquina && $opdet->opdetmaquina->maquina) {
            $maquina = $opdet->opdetmaquina->maquina;
            $maquinaNombre = $maquina->nombre;
        }

        // Próxima etapa de producción
        $etapaActualOrden = $opdet->areaproduccionsucetapaprod->orden;
        foreach ($op->opdets as &$od) {
            $od->orden = $od->areaproduccionsucetapaprod->orden;
        }
        $siguienteOpdet = collect($op->opdets)
            ->where('orden', '>', $etapaActualOrden)
            ->sortBy('orden')
            ->first();
        $proximaEtapaNombre = $siguienteOpdet
            ? $siguienteOpdet->areaproduccionsucetapaprod->etapaprod->nombre
            : 'Última etapa (Bodega)';

        // Nombre del producto
        $productoData   = Producto::atributosProducto($produccion->producto_id);
        $producto_id = $produccion->producto_id;
        $productoNombre = $productoData['nombre'] ?? '—';

        return view('opdetregprodtempaprobsup.etiqueta-etapa', compact(
            'produccion', 'opdet', 'op', 'otdet', 'ot',
            'productoNombre','producto_id', 'operarioNombre', 'maquina',
            'maquinaNombre', 'proximaEtapaNombre'
        ));
    }

}
