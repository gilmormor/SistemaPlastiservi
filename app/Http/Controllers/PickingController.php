<?php

namespace App\Http\Controllers;

use App\Models\AreaProduccion;
use App\Models\Cliente;
use App\Models\ClienteBloqueado;
use App\Models\ClienteVendedor;
use App\Models\Comuna;
use App\Models\DespachoObs;
use App\Models\DespachoSol;
use App\Models\DespachoSol_InvMov;
use App\Models\DespachoSolDet;
use App\Models\DespachoSolDet_InvBodegaProducto;
use App\Models\DespachoSolEnvOrdDesp;
use App\Models\Empresa;
use App\Models\FormaPago;
use App\Models\Giro;
use App\Models\InvBodega;
use App\Models\InvBodegaProducto;
use App\Models\InvMov;
use App\Models\InvMovDet;
use App\Models\InvMovDet_BodSolDesp;
use App\Models\InvMovModulo;
use App\Models\PlazoPago;
use App\Models\Producto;
use App\Models\Seguridad\Usuario;
use App\Models\Sucursal;
use App\Models\TipoEntrega;
use App\Models\Vendedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PickingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-picking');
        $giros = Giro::orderBy('id')->get();
        $areaproduccions =  AreaProduccion::areaproduccionxusuario();
        $tipoentregas = TipoEntrega::orderBy('id')->get();
        $fechaAct = date("d/m/Y");
        $tablashtml['comunas'] = Comuna::selectcomunas();
        $tablashtml['vendedores'] = Vendedor::selectvendedores();
        $user = Usuario::findOrFail(auth()->id());
        $tablashtml['sucurArray'] = $user->sucursales->pluck('id')->toArray(); //$clientesArray['sucurArray'];
        $tablashtml['sucursales'] = Sucursal::orderBy('id')->whereIn('sucursal.id', $tablashtml['sucurArray'])->get();
        return view('picking.index', compact('giros','areaproduccions','tipoentregas','fechaAct','tablashtml'));
    }

    public function pickingpage(Request $request){
        $datas = consultasoldesp($request);
        //dd($datas);
        foreach ($datas as &$data) {
            $despachosol = DespachoSol::findOrFail($data->id);
            $aux_despachosolenvorddesp_obs = "";
            if($despachosol->despachosolenvorddesp){
                if($despachosol->despachosolenvorddesp->staenvdesp == 0){
                    $aux_despachosolenvorddesp_obs = $despachosol->despachosolenvorddesp->obs;
                }
            }
            $data->despachosolenvorddesp_obs = $aux_despachosolenvorddesp_obs;
            $aux_totalkilospicking = 0;
            $aux_totalcantpicking = 0;
            $despachosoldets = $despachosol->despachosoldets;
            //dd($despachosoldets);
            $arrayBodegasPickings = InvBodega::llenarArrayBodegasPickingSolDesp($despachosoldets);
            foreach ($arrayBodegasPickings as $arrayBodegasPicking) {
                $producto = Producto::findOrFail($arrayBodegasPicking["producto_id"]);
                $aux_totalkilospicking += $arrayBodegasPicking["stock"] * $producto->peso;
                $aux_totalcantpicking += $arrayBodegasPicking["stock"];
            }
            $data->totalkilospicking = $aux_totalkilospicking;
            $data->totalcantpicking = $aux_totalcantpicking;
        }
        //dd($datas);
        return datatables($datas)->toJson();
    }

    public function reportesoldesp(Request $request){
        //$respuesta = app(DespachoSolController::class)->reportesoldesp($request);
        $respuesta = reportesoldesp1($request);
        return $respuesta;
    }

    public function crearord($id)
    {
        can('editar-picking');
        $data = DespachoSol::findOrFail($id);
        if(isset($data->notaventa->cliente->clientebloqueado->descripcion)){
            return redirect('/')->with([
                'mensaje'=>'Condición financiera en revisión: ' . $data->notaventa->cliente->clientebloqueado->descripcion . " Razon Social: " . $data->notaventa->cliente->razonsocial,
                'tipo_alert' => 'alert-error'
            ]);    
        }
        /* if($data->despachosoldev){
            return redirect('/')->with([
                'mensaje'=>'Picking: Solicitud de despacho Nro: ' . $data->id . ' fue cerrada el ' . date("d/m/Y h:i:s A", strtotime($data->despachosoldev->created_at)),
                'tipo_alert' => 'alert-error'
            ]);    
        }; */
        if($data->despachosolanul){
            return redirect('/')->with([
                'mensaje'=>'Picking: Solicitud de despacho Nro: ' . $data->id . ' fue anulada el ' . date("d/m/Y h:i:s A", strtotime($data->despachosolanul->created_at)),
                'tipo_alert' => 'alert-error'
            ]);    
        };

        $data->plazoentrega = $newDate = date("d/m/Y", strtotime($data->plazoentrega));
        $data->fechaestdesp = $newDate = date("d/m/Y", strtotime($data->fechaestdesp));
        $detalles = $data->despachosoldets()->get();
        $arrayBodegasPicking = InvBodega::llenarArrayBodegasPickingSolDesp($detalles);
        //dd($arrayBodegasPicking);

        /*
        foreach($detalles as $detalle){
            dd($detalle);
            $sql = "SELECT cantsoldesp
                    FROM vista_sumsoldespdet
                    WHERE notaventadetalle_id=$detalle->id";
            $datasuma = DB::select($sql);
            if(empty($datasuma)){
                $sumacantsoldesp= 0;
            }else{
                $sumacantsoldesp= $datasuma[0]->cantsoldesp;
            }
            //if($detalle->cant > $sumacantsoldesp);
            
        } */
        //dd($detalles);
        $vendedor_id=$data->notaventa->vendedor_id;
        $clienteselec = $data->notaventa->cliente()->get();
        //session(['aux_aprocot' => '0']);
        //dd($clienteselec[0]->rut);

        $clientesArray = Cliente::clientesxUsuario($vendedor_id);
        $clientes = $clientesArray['clientes'];
        //$vendedor_id = $clientesArray['vendedor_id'];
        $sucurArray = $clientesArray['sucurArray'];

        //dd($sucurArray);
        //Aqui si estoy filtrando solo las categorias de asignadas al usuario logueado
        //******************* */
        $clientedirecs = Cliente::where('rut', $clienteselec[0]->rut)
        ->join('clientedirec', 'cliente.id', '=', 'clientedirec.cliente_id')
        ->join('cliente_sucursal', 'cliente.id', '=', 'cliente_sucursal.cliente_id')
        ->whereIn('cliente_sucursal.sucursal_id', $sucurArray)
        ->select([
                    'cliente.id as cliente_id',
                    'cliente.razonsocial',
                    'cliente.telefono',
                    'cliente.email',
                    'cliente.regionp_id',
                    'cliente.provinciap_id',
                    'cliente.comunap_id',
                    'cliente.contactonombre',
                    'cliente.direccion',
                    'clientedirec.id',
                    'clientedirec.direcciondetalle'
                ])->get();
        //dd($clientedirecs);
        $clienteDirec = $data->notaventa->clientedirec()->get();
        $fecha = date("d/m/Y", strtotime($data->fechahora));
        $formapagos = FormaPago::orderBy('id')->get();
        $plazopagos = PlazoPago::orderBy('id')->get();
        $vendedores = Vendedor::orderBy('id')->get();
        $comunas = Comuna::orderBy('id')->get();
        $productos = Producto::productosxUsuario();

        $vendedores1 = Usuario::join('sucursal_usuario', function ($join) {
            $user = Usuario::findOrFail(auth()->id());
            $sucurArray = $user->sucursales->pluck('id')->toArray();
            $join->on('usuario.id', '=', 'sucursal_usuario.usuario_id')
            ->whereIn('sucursal_usuario.sucursal_id', $sucurArray);
                    })
            ->join('persona', 'usuario.id', '=', 'persona.usuario_id')
            ->join('vendedor', function ($join) {
                $join->on('persona.id', '=', 'vendedor.persona_id')
                    ->where('vendedor.sta_activo', '=', 1);
            })
            ->select([
                'vendedor.id',
                'persona.nombre',
                'persona.apellido'
            ])
            ->get();

        $empresa = Empresa::findOrFail(1);
        $tipoentregas = TipoEntrega::orderBy('id')->get();
        $giros = Giro::orderBy('id')->get();
        $despachoobss = DespachoObs::orderBy('id')->get();
        $aux_sta=2;
        $aux_statusPant = 0;
        session(['aux_fecinicreOD' => date("Y-m-d H:i:s")]); //Fecha inicio de creacion Orden de despacho
        $invmovmodulo = InvMovModulo::where("cod","=","PICKING")->get();
        $array_bodegasmodulo = $invmovmodulo[0]->invmovmodulobodsals->pluck('id')->toArray();


        //dd($clientedirecs);
        return view('picking.crear', compact('data','clienteselec','clientes','clienteDirec','clientedirecs','detalles','comunas','formapagos','plazopagos','vendedores','vendedores1','productos','fecha','empresa','tipoentregas','giros','despachoobss','sucurArray','aux_sta','aux_cont','aux_statusPant','vendedor_id','array_bodegasmodulo','arrayBodegasPicking'));
        
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function guardar(Request $request)
    {
        can('guardar-picking');
        $despachosol = DespachoSol::findOrFail($request->despachosol_id);
        if($request->updated_at != $despachosol->updated_at){
            return redirect('/')->with([
                'mensaje'=>'Picking: Registro no fue editado. Modificado por otro usuario. Fecha Hora: '.$despachosol->updated_at,
                'tipo_alert' => 'alert-error'
            ]);    
        }

        if($despachosol->despachosolanul){
            return redirect('/')->with([
                'mensaje'=>'Picking: Solicitud de despacho Nro: ' . $despachosol->id . ' fue anulada el ' . date("d/m/Y h:i:s A", strtotime($despachosol->despachosolanul->created_at)),
                'tipo_alert' => 'alert-error'
            ]);    
        };


        $despachosol->updated_at = date("Y-m-d H:i:s"); //ACTUALIZO DESDE EL PRINCIPIO EL REGISTRO PARA EVITAR QUE SEA MODIFICADO MIENTRAS EL PROCESO SE EJECUTA
        if (!$despachosol->save()) {
           return response()->json(['mensaje' => 'ng']);
        }

        $invmodulo = InvMovModulo::where("cod","PICKING")->get();
        $invmoduloBod = InvMovModulo::findOrFail($invmodulo[0]->id);
        //dd($invmoduloBod->invmovmodulobodents[0]->id);
        if(count($invmodulo) == 0){
            return response()->json([
                'mensaje' => 'No existe modulo PICKING'
            ]);
        }

        $invmoduloPesaje = InvMovModulo::where("cod","PESAJE")->get();
        $invmoduloBodPesaje = InvMovModulo::findOrFail($invmoduloPesaje[0]->id);
        
        if(count($invmoduloPesaje) == 0){
            return response()->json([
                'mensaje' => 'No existe modulo SOLDESP'
            ]);
        }
        $statusSalPicking = false;
        $statusEntPicking = false;
        //VALIDAR EN EL CASO QUE EXISTAN 2 PRODUCTOS IGUALES EN LA NV 
        //NO PUEDO PASARME DEL STOCK POR PRODUCTO
        $array_exisInvbodegaproducto = [];
        for ($i=0; $i < count($request->invbodegaproducto_id); $i++){
            $statusSalPicking = true; //por lo menos hay un movimiento para salida de Picking
            $invbodegaproducto = InvBodegaProducto::findOrFail($request->invbodegaproducto_id[$i]);
            $requestProd = new Request();
            $requestProd["invbodegaproducto_id"] = $invbodegaproducto->id;
            $requestProd["producto_id"] = $invbodegaproducto->producto_id;
            $requestProd["invbodega_id"] = $invbodegaproducto->invbodega_id;
            $arrayExistencia = InvBodegaProducto::existencia($requestProd);
            $aux_existencia = $arrayExistencia["stock"]["cant"];
            if(!isset($array_exisInvbodegaproducto[$request->invbodegaproducto_id[$i]])){
                $array_exisInvbodegaproducto[$request->invbodegaproducto_id[$i]] = [
                    "stock" => $aux_existencia,
                    "stockresto" => $aux_existencia,
                    "cant" => 0
                ];                    
            }
        }
        for ($i=0; $i < count($request->invbodegaproducto_id); $i++){
            $invbodegaproducto = InvBodegaProducto::findOrFail($request->invbodegaproducto_id[$i]);
            $array_exisInvbodegaproducto[$request->invbodegaproducto_id[$i]]["stockresto"] -= $request->invcant[$i];
            $array_exisInvbodegaproducto[$request->invbodegaproducto_id[$i]]["cant"] += $request->invcant[$i];
            $aux_stock = $array_exisInvbodegaproducto[$request->invbodegaproducto_id[$i]]["stock"];
            $aux_cant = $array_exisInvbodegaproducto[$request->invbodegaproducto_id[$i]]["cant"];
            $aux_saldostock = $array_exisInvbodegaproducto[$request->invbodegaproducto_id[$i]]["stockresto"];
            if($aux_saldostock < 0){
                return redirect('/')->with([
                    'mensaje'=> "Picking: Sin Stock Sucursal: " . $despachosol->notaventa->sucursal->nombre . ", Bodega: " . $invbodegaproducto->invbodega->nombre . ". Id: " . $invbodegaproducto->producto_id . ", Nombre: " . $invbodegaproducto->producto->nombre . ", Stock: " . $aux_stock . "  Estas tratando de hacer picking por: " . $aux_cant,
                    'tipo_alert' => 'alert-error'
                ]);    
            }
        }
        //HASTA AQUI LA VALIDACION: EN EL CASO QUE EXISTAN 2 PRODUCTOS IGUALES EN LA NV 

        for ($i=0; $i < count($request->invbodegaproducto_id); $i++) {
            //PROCESO PARA MODIFICAR PICKING DE SOLICITUD DESPACHO EXISTENTE
            if($request->pickingPrevio[$i] > 0 and $request->pickingPrevio[$i] > $request->invcant[$i]){
                //VALIDACION SALIDA DE BODEGA PICKING
                $statusSalPicking = true; //por lo menos hay un movimiento para salida de Picking
                $invbodegaproducto = InvBodegaProducto::findOrFail($request->invbodegaproducto_id[$i]);
                $requestProd = new Request();
                $requestProd["invbodegaproducto_id"] = $invbodegaproducto->id;
                $requestProd["producto_id"] = $invbodegaproducto->producto_id;
                $requestProd["invbodega_id"] = $invbodegaproducto->invbodega_id;
                $arrayExistencia = InvBodegaProducto::existencia($requestProd);
                $aux_cant = ($request->pickingPrevio[$i] - $request->invcant[$i]);
                $aux_existencia = $arrayExistencia["stock"]["cant"];
                if(($aux_existencia - $aux_cant) < 0){                
                    return redirect('/')->with([
                        'mensaje'=> "Picking: Sin Stock Sucursal: " . $despachosol->notaventa->sucursal->nombre . ", Bodega: " . $invbodegaproducto->invbodega->nombre . ". Id: " . $invbodegaproducto->producto_id . ", Nombre: " . $invbodegaproducto->producto->nombre . ", Stock: " . $arrayExistencia["stock"]["cant"] . " Cant: " . $aux_cant,
                        'tipo_alert' => 'alert-error'
                    ]);    
                }
            }
            //PROCESO PARA AGREGAR PICKING A SOLICITUD DESPACHO EXISTENTE
            if(is_null($request->pickingPrevio[$i]) and $request->invcant[$i] > 0){
                //VALIDACION SALIDA DE BODEGA PESAJE O BODEGAS DE PRODUCTO TERMINADO
                $statusEntPicking = true; //por lo menos hay un movimiento para salida de Bodegas
                $invbodegaproducto = InvBodegaProducto::findOrFail($request->invbodegaproducto_id[$i]);
                $requestProd = new Request();
                $requestProd["invbodegaproducto_id"] = $invbodegaproducto->id;
                $requestProd["producto_id"] = $invbodegaproducto->producto_id;
                $requestProd["invbodega_id"] = $invbodegaproducto->invbodega_id;
                $arrayExistencia = InvBodegaProducto::existencia($requestProd);
                $aux_cant = $request->invcant[$i];
                $aux_existencia = $arrayExistencia["stock"]["cant"];
                if(($aux_existencia - $aux_cant) < 0){                
                    return redirect('/')->with([
                        'mensaje'=> "Picking: Sin Stock Sucursal: " . $despachosol->notaventa->sucursal->nombre . ", Bodega: " . $invbodegaproducto->invbodega->nombre . ". Id: " . $invbodegaproducto->producto_id . ", Nombre: " . $invbodegaproducto->producto->nombre . ", Stock: " . $arrayExistencia["stock"]["cant"] . " Cant: " . $aux_cant,
                        'tipo_alert' => 'alert-error'
                    ]);    
                }
            }
        }
        $annomes = date("Ym");
        if($statusSalPicking){
            $invmov_array = array();
            $invmov_array["fechahora"] = date("Y-m-d H:i:s");
            $invmov_array["annomes"] = $annomes;
            $invmov_array["desc"] = "Salida de Picking entrada Bodega Pesaje NV:" . $despachosol->notaventa_id . " SD:" . $despachosol->id;
            $invmov_array["obs"] = "Salida de Picking entrada Bodega Pesaje NV:" . $despachosol->notaventa_id . " SD:" . $despachosol->id;
            $invmov_array["invmovmodulo_id"] = $invmodulo[0]->id; //Modulo PICKING
            $invmov_array["idmovmod"] = $despachosol->id;
            $invmov_array["invmovtipo_id"] = 2;
            $invmov_array["sucursal_id"] = $despachosol->notaventa->sucursal_id;
            $invmov_array["usuario_id"] = auth()->id();
            $invmov = InvMov::create($invmov_array);
            $invmov_idSalPicking = $invmov->id;
            $despachosol_invmov = DespachoSol_InvMov::create(
                [
                'despachosol_id' => $despachosol->id,
                'invmov_id' => $invmov->id
            ]);
        }
        if($statusEntPicking){
            $invmov_array = array();
            $invmov_array["fechahora"] = date("Y-m-d H:i:s");
            $invmov_array["annomes"] = $annomes;
            $invmov_array["desc"] = "Entrada a Picking salida Bodeba NV:" . $despachosol->notaventa_id . " SD:" . $despachosol->id;
            $invmov_array["obs"] = "Entrada a Picking salida Bodeba NV:" . $despachosol->notaventa_id . " SD:" . $despachosol->id;
            $invmov_array["invmovmodulo_id"] = $invmodulo[0]->id; //Modulo PICKING
            $invmov_array["idmovmod"] = $despachosol->id;
            $invmov_array["invmovtipo_id"] = 2;
            $invmov_array["sucursal_id"] = $despachosol->notaventa->sucursal_id;
            $invmov_array["usuario_id"] = auth()->id();
            
            $invmov = InvMov::create($invmov_array);
            $invmov_idEntPicking = $invmov->id;
            $despachosol_invmov = DespachoSol_InvMov::create(
                [
                'despachosol_id' => $despachosol->id,
                'invmov_id' => $invmov->id
            ]);
        }
        for ($j=0; $j < count($request->invbodegaproducto_id); $j++) {
            //PROCESO PARA MODIFICAR PICKING DE SOLICITUD DESPACHO EXISTENTE
            $despachosoldet = DespachoSolDet::findOrFail($request->invbodegaproductoNVdet_id[$j]);
            $invbodegaproducto = InvBodegaProducto::findOrFail($request->invbodegaproducto_id[$j]);
            if($request->pickingPrevio[$j] > 0 and $request->pickingPrevio[$j] > $request->invcant[$j]){ //SI EL PICKING PREVIO ES MAYOT A CERO Y ES MAYOR A LA CANTIDAD INTRODUCIDA
                $aux_cant = ($request->pickingPrevio[$j] - $request->invcant[$j]) * -1;
                $array_invmovdet = [
                    "invbodegaproducto_id" => $request->invbodegaproducto_id[$j],
                    "producto_id" => $invbodegaproducto->producto_id,
                    "invbodega_id" => $invbodegaproducto->invbodega_id,
                    "sucursal_id" => $invbodegaproducto->invbodega->sucursal_id,
                    "unidadmedida_id" => $despachosoldet->notaventadetalle->unidadmedida_id,
                    "invmovtipo_id" => 2,
                    "cant" => $aux_cant,
                    "cantgrupo" => $aux_cant,
                    "cantxgrupo" => 1,
                    "peso" => $despachosoldet->notaventadetalle->producto->peso,
                    "cantkg" => ($despachosoldet->notaventadetalle->totalkilos / $despachosoldet->notaventadetalle->cant) * $aux_cant,
                    "invmov_id" => $invmov_idSalPicking,
                ];
                $invmovdet = InvMovDet::create($array_invmovdet);
                $invmovdet_bodsoldesp = InvMovDet_BodSolDesp::create([
                    'invmovdet_id' => $invmovdet->id,
                    'despachosoldet_invbodegaproducto_id' => $request->despachosoldet_invbodegaproducto_id[$j]
                    ]);

                
                $aux_sucursal_id_producto = $invbodegaproducto->invbodega->sucursal_id; 
                foreach($invmoduloBodPesaje->invmovmodulobodents as $invmovmodulobodent){
                    //BUSCAR BODEGA PESAJE CORRESPONDIENTE AL PRODUCTO QUE SE ESTA PROCESANDO DEPENDIENDO DE LA SUCURSAL QUE CORRESPONDE EL PRODUCTO
                    if($invmovmodulobodent->sucursal_id == $aux_sucursal_id_producto){
                        $aux_bodega_idPesaje = $invmovmodulobodent->id;
                    }
                }            
                $invbodegaproducto = InvBodegaProducto::updateOrCreate(
                    ['producto_id' => $request->invbodegaproducto_producto_id[$j],'invbodega_id' => $aux_bodega_idPesaje],
                    [
                        'producto_id' => $request->invbodegaproducto_producto_id[$j],
                        'invbodega_id' => $aux_bodega_idPesaje
                    ]
                );
                $aux_cant = ($request->pickingPrevio[$j] - $request->invcant[$j]);
                $array_invmovdet = [
                    "invbodegaproducto_id" => $invbodegaproducto->id,
                    "producto_id" => $invbodegaproducto->producto_id,
                    "invbodega_id" => $aux_bodega_idPesaje,
                    "sucursal_id" => $invbodegaproducto->invbodega->sucursal_id,
                    "unidadmedida_id" => $despachosoldet->notaventadetalle->unidadmedida_id,
                    "invmovtipo_id" => 1,
                    "cant" => $aux_cant,
                    "cantgrupo" => $aux_cant,
                    "cantxgrupo" => 1,
                    "peso" => $despachosoldet->notaventadetalle->producto->peso,
                    "cantkg" => ($despachosoldet->notaventadetalle->totalkilos / $despachosoldet->notaventadetalle->cant) * $aux_cant,
                    "invmov_id" => $invmov_idSalPicking,
                ];
                $invmovdet = InvMovDet::create($array_invmovdet);
                //BUSCO EL REGISTRO Y ACTUALIZO LOS VALORES DE PICKING Y EXESO QUE SE VA A LA SOLOCITUD Y LUEGO A LA ORDEN DE DESPACHO
                $despachosoldet_invbodegaproducto = DespachoSolDet_InvBodegaProducto::findOrFail($request->despachosoldet_invbodegaproducto_id[$j]);
                $despachosoldet_invbodegaproducto->cant = $request->invcant[$j] * -1;
                $despachosoldet_invbodegaproducto->cantex = ($aux_cant * -1) + $despachosoldet_invbodegaproducto->cantex;
                $despachosoldet_invbodegaproducto->save();

            }
            //FIN PROCESO PARA MODIFICAR PICKING DE SOLICITUD DESPACHO EXISTENTE

            //PROCESO PARA AGREGAR PICKING A SOLICITUD DESPACHO EXISTENTE
            if(is_null($request->pickingPrevio[$j]) and $request->invcant[$j] > 0){ //SI EL PICKING PREVIO ES NULL Y LA CANTIDAD ES MAYOR A CERO
                $aux_cant = $request->invcant[$j] * -1;
                $array_invmovdet = [
                    "invbodegaproducto_id" => $invbodegaproducto->id,
                    "producto_id" => $invbodegaproducto->producto_id,
                    "invbodega_id" => $invbodegaproducto->invbodega_id,
                    "sucursal_id" => $invbodegaproducto->invbodega->sucursal_id,
                    "unidadmedida_id" => $despachosoldet->notaventadetalle->unidadmedida_id,
                    "invmovtipo_id" => 2,
                    "cant" => $aux_cant,
                    "cantgrupo" => $aux_cant,
                    "cantxgrupo" => 1,
                    "peso" => $despachosoldet->notaventadetalle->producto->peso,
                    "cantkg" => ($despachosoldet->notaventadetalle->totalkilos / $despachosoldet->notaventadetalle->cant) * $aux_cant,
                    "invmov_id" => $invmov_idEntPicking,
                ];
                $invmovdet = InvMovDet::create($array_invmovdet);

                $aux_sucursal_id_producto = $invbodegaproducto->invbodega->sucursal_id; 
                foreach($invmoduloBod->invmovmodulobodents as $invmovmodulobodent){
                    //BUSCAR BODEGA PICKING CORRESPONDIENTE AL PRODUCTO QUE SE ESTA PROCESANDO DEPENDIENDO DE LA SUCURSAL QUE CORRESPONDE EL PRODUCTO
                    if($invmovmodulobodent->sucursal_id == $aux_sucursal_id_producto){
                        $aux_bodega_idPicking = $invmovmodulobodent->id;
                    }
                }            
                $invbodegaproducto = InvBodegaProducto::updateOrCreate(
                    ['producto_id' => $request->invbodegaproducto_producto_id[$j],'invbodega_id' => $aux_bodega_idPicking],
                    [
                        'producto_id' => $request->invbodegaproducto_producto_id[$j],
                        'invbodega_id' => $aux_bodega_idPicking
                    ]
                );
                $aux_cant = $request->invcant[$j];
                $array_invmovdet = [
                    "invbodegaproducto_id" => $invbodegaproducto->id,
                    "producto_id" => $invbodegaproducto->producto_id,
                    "invbodega_id" => $invbodegaproducto->invbodega_id,
                    "sucursal_id" => $invbodegaproducto->invbodega->sucursal_id,
                    "unidadmedida_id" => $despachosoldet->notaventadetalle->unidadmedida_id,
                    "invmovtipo_id" => 1,
                    "cant" => $aux_cant,
                    "cantgrupo" => $aux_cant,
                    "cantxgrupo" => 1,
                    "peso" => $despachosoldet->notaventadetalle->producto->peso,
                    "cantkg" => ($despachosoldet->notaventadetalle->totalkilos / $despachosoldet->notaventadetalle->cant) * $aux_cant,
                    "invmov_id" => $invmov_idEntPicking,
                ];
                $invmovdet = InvMovDet::create($array_invmovdet);

                $despachosoldet_invbodegaproducto = DespachoSolDet_InvBodegaProducto::findOrFail($request->despachosoldet_invbodegaproducto_id[$j]);
                $despachosoldet_invbodegaproducto->cant = $despachosoldet_invbodegaproducto->cant - $request->invcant[$j];
                $despachosoldet_invbodegaproducto->cantex = $despachosoldet_invbodegaproducto->cantex + $aux_cant;
                $despachosoldet_invbodegaproducto->save();
                $invmovdet_bodsoldesp = InvMovDet_BodSolDesp::create([
                    'invmovdet_id' => $invmovdet->id,
                    'despachosoldet_invbodegaproducto_id' => $request->despachosoldet_invbodegaproducto_id[$j]
                    ]);
            }
            //FIN PROCESO PARA AGREGAR PICKING A SOLICITUD DESPACHO EXISTENTE
        }
        $despachosol->updated_at = date("Y-m-d H:i:s");

        if ($despachosol->save()) {
            if($statusSalPicking == false and $statusEntPicking == false){
                return redirect('/')->with([
                    'mensaje'=> "Picking: no se realizaron modificaciones.",
                    'tipo_alert' => 'alert-error'
                ]);    
            }else{
                return redirect('/')->with([
                    'mensaje'=> "Picking generado con exito.",
                ]);
            }

        } else {
            return response()->json(['mensaje' => 'ng']);
        }

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
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function enviardespord(Request $request)
    {
        $despachosol = DespachoSol::findOrFail($request->despachosol_id);
        
        if($despachosol->updated_at != $request->updated_at){
            return [
                'error' => 1,
                'mensaje' => 'No se pudo modificar. Registro Editado por otro usuario. Fecha Hora: '.$despachosol->updated_at,
                'tipo_alert' => 'error'
            ];
        }
        //dd($despachosol->notaventa->cliente->clientebloqueado);
        if(isset($despachosol->notaventa->cliente->clientebloqueado)){
            return [
                'error' => 1,
                'mensaje' => 'Condición financiera en revisión: ' . $despachosol->notaventa->cliente->clientebloqueado->descripcion,
                'tipo_alert' => 'error'
            ];
        }

        //BLOQUEO POR DEUDA: DESHABILITADO POR AUTORIZACION DE JEANNETTE MARTINEZ 14/08/2024
        /* $request1 = new Request();
        $request1->merge(['modulo_id' => 6]);
        $request1->request->set('modulo_id', 6);
        $request1->merge(['notaventa_id' => $despachosol->notaventa_id]);
        $request1->request->set('notaventa_id', $despachosol->notaventa_id);
        $request1->merge(['deldesbloqueo' => 1]);
        $request1->request->set('deldesbloqueo', 1);
        $clibloq = clienteBloqueado($despachosol->notaventa->cliente_id,0,$request1);
        if(!is_null($clibloq["bloqueo"])){   
            return [
                "error" => 1,
                "mensaje" => "Condición financiera en revisión: \n" . $clibloq["bloqueo"],
                "tipo_alert" => isset($bloqcli["tipo_alert"]) ? $bloqcli["tipo_alert"] : "error"
            ];
        } */


        $despachosol->updated_at = date("Y-m-d H:i:s");
        if($despachosol->despachosolenvorddesp){
            $despachosol->despachosolenvorddesp->staenvdesp = 1;
            $despachosol->despachosolenvorddesp->save();
        }else{
            $request->merge([
                'staenvdesp' => 1,
                'usuario_id' => auth()->id()
            ]);
            DespachoSolEnvOrdDesp::create($request->all());
        }
        $despachosol->save();
        return [
            'error' => 0,
            'mensaje' => 'Procesado con exito.',
            'tipo_alert' => 'success'
        ];

        /* if($request->estado == 1){
            $request->merge(['usuario_id' => auth()->id()]);
            DespachoSolEnvOrdDesp::create($request->all());
            $despachosol->save();
            return [
                'error' => 0,
                'mensaje' => 'Procesado con exito.',
                'tipo_alert' => 'success'
            ];
        }else{
            if (DespachoSolEnvOrdDesp::destroy($despachosol->despachosolenvorddesp->id)) {
                //Despues de eliminar actualizo el campo usuariodel_id=usuario que elimino el registro
                $despachosolenvorddesp = DespachoSolEnvOrdDesp::withTrashed()->findOrFail($despachosol->despachosolenvorddesp->id);
                $despachosolenvorddesp->usuariodel_id = auth()->id();
                $despachosolenvorddesp->save();
                $despachosol->save();
                return [
                    'error' => 0,
                    'mensaje' => 'Procesado con exito.',
                    'tipo_alert' => 'success'
                ];
            } else {
                return [
                    'error' => 1,
                    'mensaje' => 'Ocurrio un error.',
                    'tipo_alert' => 'error'
                ];
            }    
        } */

        //dd($request);
    }

    public function validareditarpicking(Request $request)
    {
        $despachosol = DespachoSol::findOrFail($request->despachosol_id);
        if($despachosol->updated_at != $request->updated_at){
            return [
                'error' => 1,
                'mensaje' => 'No se pudo modificar. Registro Editado por otro usuario. Fecha Hora: '.$despachosol->updated_at,
                'tipo_alert' => 'error'
            ];
        }
        //BLOQUEO POR DEUDA: DESHABILITADO POR AUTORIZACION DE JEANNETTE MARTINEZ 14/08/2024
        /* if(isset($despachosol->notaventa->cliente->clientebloqueado)){
            return [
                'error' => 1,
                'mensaje' => 'Condición financiera en revisión: ' . $despachosol->notaventa->cliente->clientebloqueado->descripcion,
                'tipo_alert' => 'error'
            ];
        } */
        /* $despachosol->updated_at = date("Y-m-d H:i:s");
        $despachosol->save(); */
        return [
            'error' => 0,
            'mensaje' => 'Procesado con exito.',
            'tipo_alert' => 'success'
        ];
    }
}


function reportesoldesp1($request){
    $respuesta = array();
    $respuesta['exito'] = false;
    $respuesta['mensaje'] = "Código no Existe";
    $respuesta['tabla'] = "";

    if($request->ajax()){
        $datas = consultasoldesp($request);

        $respuesta['tabla'] .= "<table id='pendientesoldesp' name='pendientesoldesp' class='table display AllDataTables table-hover table-condensed tablascons' data-page-length='50'>
        <thead>
            <tr>
                <th class='tooltipsC' title='Solicitud de Despacho'>SD</th>
                <th>Fecha</th>
                <th class='tooltipsC' title='Fecha Estimada de Despacho'>Fecha ED</th>
                <th>Razón Social</th>
                <th>Sucursal</th>
                <th class='tooltipsC' title='Orden de Compra'>OC</th>
                <th class='tooltipsC' title='Nota de Venta'>NV</th>
                <th>Comuna</th>
                <th class='tooltipsC' title='Total Kg Pendientes'>Total Kg</th>
                <th class='tooltipsC' title='Total $'>$</th>
                <th class='tooltipsC' title='Vista Previa Orden Despacho'>VP</th>
                <th class='tooltipsC' title='Acción'>Acción</th>
            </tr>
        </thead>
        <tbody>";

        $i = 0;
        $aux_Ttotalkilos = 0;
        $aux_Tsubtotal = 0;
        foreach ($datas as $data) {
            $rut = number_format( substr ( $data->rut, 0 , -1 ) , 0, "", ".") . '-' . substr ( $data->rut, strlen($data->rut) -1 , 1 );
            if(empty($data->oc_file)){
                $aux_enlaceoc = $data->oc_id;
            }else{
                $aux_enlaceoc = "<a onclick='verpdf2(\"$data->oc_file\",2)' class='tooltipsC' title='Orden de Compra'>$data->oc_id</a>";
            }
            $ruta_nuevoOrdDesp = route('crearord_picking', ['id' => $data->id]);
            //dd($ruta_nuevoSolDesp);

            $clibloq = ClienteBloqueado::where("cliente_id" , "=" ,$data->cliente_id)->get();
            if(count($clibloq) > 0){
                $aux_descbloq = $clibloq[0]->descripcion;
                $nuevoOrdDesp = "<a class='btn-accion-tabla tooltipsC' title='Condición financiera en revisión: $aux_descbloq'>
                                    <button type='button' class='btn btn-default btn-xs' disabled>
                                        <i class='fa fa-fw fa-lock text-danger'></i>
                                    </button>
                                </a>";
            }else{
                $ruta_nuevoSolDesp = route('crearsol_despachosol', ['id' => $data->id]);
                $nuevoOrdDesp = "<a href='$ruta_nuevoOrdDesp' target='_blank' class='btn-accion-tabla tooltipsC' title='Hacer orden despacho: $data->tipentnombre'>
                                    <button type='button' class='btn btn-default btn-xs'>
                                        <i class='fa fa-fw $data->icono'></i>
                                    </button>
                                </a>";    
            }

            $sql = "SELECT COUNT(*) as cont
            FROM despachoord
            WHERE despachoord.despachosol_id=$data->id
            AND despachoord.id 
            NOT IN (SELECT despachoordanul.despachoord_id FROM despachoordanul WHERE ISNULL(despachoordanul.deleted_at));";

            $contorddesp = DB::select($sql);
            if($contorddesp[0]->cont == 0){
                /*****PARA DEVOLVER SOLDESP: EL DETALLE DEBE SER IGUAL A LA SUMA POR ITEM EN LA TABLA $despachosoldet_invbodegaproducto*/
                $despachosol = DespachoSol::findOrFail($data->id);
                foreach ($despachosol->despachosoldets as $despachosoldet) {
                    $aux_cant = 0;
                    foreach ($despachosoldet->despachosoldet_invbodegaproductos as $despachosoldet_invbodegaproducto) {
                        $aux_cant += $despachosoldet_invbodegaproducto->cant + $despachosoldet_invbodegaproducto->cantex;
                    }
                }

            }

            $aux_totalkilos = $data->totalkilos - $data->totalkilosdesp;
            $aux_subtotal = $data->subtotalsoldesp - $data->subtotaldesp;
            $aux_Ttotalkilos += $aux_totalkilos;
            $aux_Tsubtotal += $aux_subtotal;

            $respuesta['tabla'] .= "
            <tr id='fila$i' name='fila$i' class='btn-accion-tabla tooltipsC'>
                <td id='id$i' name='id$i'>
                    <a class='btn-accion-tabla btn-sm tooltipsC' title='Solicitud de Despacho' onclick='genpdfSD($data->id,1)'>
                        $data->id
                    </a>
                </td>
                <td id='fechahora$i' name='fechahora$i'>" . date('d/m/Y', strtotime($data->fechahora)) . "</td>
                <td>" . 
                    "<a id='fechaestdesp$i' name='fechaestdesp$i' class='editfed'>" .
                        date('d/m/Y', strtotime($data->fechaestdesp)) . 
                    "</a>
                    <input type='text' class='form-control datepickerfed savefed' name='fechaed$i' id='fechaed$i' value='" . date('d/m/Y', strtotime($data->fechaestdesp)) . "' style='display:none; width: 70px; height: 21.6px;padding-left: 0px;padding-right: 0px;' readonly>" .

                    "<a name='editfed$i' id='editfed$i' class='tooltipsC editfed' title='Editar Fecha ED' onclick='editfeced($data->id,$i)'>
                        <i class='fa fa-fw fa-pencil-square-o'></i>
                    </a>" .
                    "<a name='savefed$i' id='savefed$i' class='tooltipsC savefed' title='Guardar Fecha ED' onclick='savefeced($data->id,$i)' style='display:none' updated_at='$data->updated_at'>
                        <i class='fa fa-fw fa-save text-red'></i>
                    </a>" .
                "</td>
                <td id='razonsocial$i' name='razonsocial$i'>$data->razonsocial</td>
                <td id='sucursal_nombre$i' name='sucursal_nombre$i'>$data->sucursal_nombre</td>
                <td id='oc_id$i' name='oc_id$i'>$aux_enlaceoc</td>
                <td>
                    <a class='btn-accion-tabla btn-sm tooltipsC' title='Nota de Venta' onclick='genpdfNV($data->notaventa_id,1)'>
                        $data->notaventa_id
                    </a>
                </td>
                <td id='comuna$i' name='comuna$i'>$data->comunanombre</td>
                <td class='kgpend' style='text-align:right' data-order='$aux_totalkilos' data-search='$aux_totalkilos'>".
                    number_format($aux_totalkilos, 2, ",", ".") .
                "</td>
                <td class='dinpend' style='text-align:right' data-order='$aux_subtotal' data-search='$aux_subtotal'>".
                    number_format($aux_subtotal, 0, ",", ".") .
                "</td>
                <td>
                    <a class='btn-accion-tabla btn-sm tooltipsC' title='Vista Previa' onclick='genpdfVPOD($data->id,1)'>
                        <i class='fa fa-fw fa-file-pdf-o'></i>
                    </a>
                </td>
                <td>
                    $nuevoOrdDesp
                </td>
            </tr>";
            $i++;
            //dd($data->contacto);
        }

        $respuesta['tabla'] .= "
        </tbody>
            <tfoot>
                <tr>
                    <th colspan='7' style='text-align:right'>Total página</th>
                    <th id='totalkg' name='totalkg' style='text-align:right'>0,00</th>
                    <th id='totaldinero' name='totaldinero' style='text-align:right'>0,00</th>
                    <th></th>
                    <th></th>
                </tr>
                <tr>
                    <th colspan='7'  style='text-align:right'>TOTAL GENERAL</th>
                    <th style='text-align:right'>". number_format($aux_Ttotalkilos, 2, ",", ".") ."</th>
                    <th style='text-align:right'>". number_format($aux_Tsubtotal, 0, ",", ".") ."</th>
                    <th></th>
                    <th></th>
                </tr>
            </tfoot>
        </table>";
        return $respuesta;
    }

}

function consultasoldesp($request){
    //dd($request->vendedor_id);
    $user = Usuario::findOrFail(auth()->id());
    if(empty($request->vendedor_id)){
        $sql= 'SELECT COUNT(*) AS contador
            FROM vendedor INNER JOIN persona
            ON vendedor.persona_id=persona.id
            INNER JOIN usuario 
            ON persona.usuario_id=usuario.id
            WHERE usuario.id=' . auth()->id();
        $counts = DB::select($sql);
        if($counts[0]->contador>0){
            $vendedor_id=$user->persona->vendedor->id;
            $vendedorcond = "notaventa.vendedor_id=" . $vendedor_id ;
            $clientevendedorArray = ClienteVendedor::where('vendedor_id',$vendedor_id)->pluck('cliente_id')->toArray();
            $sucurArray = $user->sucursales->pluck('id')->toArray();
        }else{
            $vendedorcond = " true ";
            $clientevendedorArray = ClienteVendedor::pluck('cliente_id')->toArray();
        }
    }else{
        //dd("Entro aqui");
        if(is_array($request->vendedor_id)){
            $aux_vendedorid = implode ( ',' , $request->vendedor_id);
        }else{
            $aux_vendedorid = $request->vendedor_id;
        }
        $vendedorcond = " notaventa.vendedor_id in ($aux_vendedorid) ";

        //$vendedorcond = "notaventa.vendedor_id='$request->vendedor_id'";
    }
    //dd($vendedorcond);
    $sucurArray = implode ( ',' , $user->sucursales->pluck('id')->toArray());
    if(!isset($request->sucursal_id) or empty($request->sucursal_id)){
        $aux_condsucursal_id = " notaventa.sucursal_id in ($sucurArray) ";
    }else{
        if(is_array($request->sucursal_id)){
            $aux_sucursal = implode ( ',' , $request->sucursal_id);
        }else{
            $aux_sucursal = $request->sucursal_id;
        }
        $aux_condsucursal_id = " (notaventa.sucursal_id in ($aux_sucursal) and notaventa.sucursal_id in ($sucurArray))";
    }
    if(empty($request->fechad) or empty($request->fechah)){
        $aux_condFecha = " true";
    }else{
        $fecha = date_create_from_format('d/m/Y', $request->fechad);
        $fechad = date_format($fecha, 'Y-m-d')." 00:00:00";
        $fecha = date_create_from_format('d/m/Y', $request->fechah);
        $fechah = date_format($fecha, 'Y-m-d')." 23:59:59";
        $aux_condFecha = "despachosol.fechahora>='$fechad' and despachosol.fechahora<='$fechah'";
    }
    if(empty($request->rut)){
        $aux_condrut = " true";
    }else{
        $aux_condrut = "cliente.rut='$request->rut'";
    }
    if(empty($request->oc_id)){
        $aux_condoc_id = " true";
    }else{
        $aux_condoc_id = "notaventa.oc_id='$request->oc_id'";
    }
    if(empty($request->giro_id)){
        $aux_condgiro_id = " true";
    }else{
        $aux_condgiro_id = "notaventa.giro_id='$request->giro_id'";
    }
    if(empty($request->areaproduccion_id)){
        $aux_condareaproduccion_id = " true";
    }else{
        $aux_condareaproduccion_id = "categoriaprod.areaproduccion_id='$request->areaproduccion_id'";
    }
    if(empty($request->tipoentrega_id)){
        $aux_condtipoentrega_id = " true";
    }else{
        $aux_condtipoentrega_id = "despachosol.tipoentrega_id='$request->tipoentrega_id'";
    }
    if(empty($request->notaventa_id)){
        $aux_condnotaventa_id = " true";
    }else{
        $aux_condnotaventa_id = "notaventa.id='$request->notaventa_id'";
    }

    if(empty($request->aprobstatus)){
        $aux_aprobstatus = " true";
    }else{
        switch ($request->aprobstatus) {
            case 1:
                $aux_aprobstatus = "notaventa.aprobstatus='0'";
                break;
            case 2:
                $aux_aprobstatus = "notaventa.aprobstatus='$request->aprobstatus'";
                break;    
            case 3:
                $aux_aprobstatus = "(notaventa.aprobstatus='1' or notaventa.aprobstatus='3')";
                break;
            case 4:
                $aux_aprobstatus = "notaventa.aprobstatus='$request->aprobstatus'";
                break;
        }
        
    }
/*
    if(empty($request->comuna_id)){
        $aux_condcomuna_id = " true";
    }else{
        $aux_condcomuna_id = "notaventa.comunaentrega_id='$request->comuna_id'";
    }
*/
    if(empty($request->comuna_id)){
        $aux_condcomuna_id = " true ";
    }else{
        if(is_array($request->comuna_id)){
            $aux_comuna = implode ( ',' , $request->comuna_id);
        }else{
            $aux_comuna = $request->comuna_id;
        }
        $aux_condcomuna_id = " despachosol.comunaentrega_id in ($aux_comuna) ";
    }


    $aux_condaprobord = "true";
    switch ($request->filtro) {
        case 1:
            //Filtra solo las aprobadas. Esto es para la consulta para crear ordenes de Despacho
            $aux_condaprobord = "despachosol.aprorddesp = 1";
            break;
        case 2:
            //Muestra todo sin importar si fue aprobadada o no. Esto es para el reporte
            $aux_condaprobord = "true";
            break;
    }
    if(empty($request->fechaestdesp)){
        $aux_condfechaestdesp = " true";
    }else{
        $fecha = date_create_from_format('d/m/Y', $request->fechaestdesp);
        $fechad = date_format($fecha, 'Y-m-d');
        $aux_condfechaestdesp = "despachosol.fechaestdesp='$fechad'";
    }

    if(empty($request->id)){
        $aux_condid = " true";
    }else{
        $aux_condid = "despachosol.id='$request->id'";
    }

    $aux_condproducto_id = " true";
    if(!empty($request->producto_id)){
        $aux_codprod = explode(",", $request->producto_id);
        $aux_codprod = implode ( ',' , $aux_codprod);
        $aux_condproducto_id = "notaventadetalle.producto_id in ($aux_codprod)";
    }

    if(empty($request->sta_picking)){
        $aux_condsta_picking = " true";
    }else{
        switch ($request->sta_picking) {
            case 0:
                $aux_condsta_picking = " true";
                break;
            case 1:
                $aux_condsta_picking = "despachosoldet_invbodegaproducto.cant != 0";
                break;
            case 2:
                $aux_condsta_picking = "despachosoldet_invbodegaproducto.cant = 0";
                break;    
        }

    }

    //$suma = DespachoSol::findOrFail(2)->despachosoldets->where('notaventadetalle_id',1);

    $aux_notinNullSoldesp = "despachosol.id NOT IN (SELECT despachosolanul.despachosol_id FROM despachosolanul WHERE isnull(despachosolanul.deleted_at))";
    $aux_sqlsumdesp = "SELECT cantdesp
                        FROM vista_sumorddespdet
                        WHERE despachosoldet_id=despachosoldet.id";
    $aux_condactivas = "if((if(isnull(($aux_sqlsumdesp)),0,($aux_sqlsumdesp))
                    ) >= despachosoldet.cantsoldesp,FALSE,TRUE)
                    AND $aux_notinNullSoldesp";
    //$aux_condactivas = "true";

    $sql = "SELECT despachosol.id,despachosol.fechahora,notaventa.cliente_id,cliente.rut,cliente.razonsocial,notaventa.oc_id,
            notaventa.oc_file,
            comuna.nombre as comunanombre,sucursal.nombre as sucursal_nombre,
            despachosol.notaventa_id,despachosol.fechaestdesp,tipoentrega.nombre as tipentnombre,tipoentrega.icono,
            IFNULL(vista_despordxdespsoltotales.totalkilos,0) as totalkilosdesp,
            IFNULL(vista_despordxdespsoltotales.subtotal,0) as subtotaldesp,
            vista_despsoltotales.totalkilos,
            vista_despsoltotales.subtotalsoldesp,despachosol.updated_at,
            clientebloqueado.descripcion as clientebloqueado_descripcion,
            despachosolenvorddesp.despachosol_id as despachosolenvorddesp_despachosol_id,
            despachosolenvorddesp.despachosol_id as despachosolenvorddesp_updated_at,
            cliente.limitecredito,
            IFNULL(vista_datacobranza.tfac,0) AS datacobranza_tfac,
            IFNULL(vista_datacobranza.tdeuda,0) AS datacobranza_tdeuda,
            IFNULL(vista_datacobranza.tdeudafec,0) AS datacobranza_tdeudafec,
            IFNULL(vista_datacobranza.nrofacdeu,'') AS datacobranza_nrofacdeu,
            clientedesbloqueado.obs as clientedesbloqueado_obs,
            modulo.stamodapl as modulo_stamodapl,clientedesbloqueadomodulo.modulo_id,
            clientedesbloqueadomodulo_orddesp.modulo_id as modulo_id_orddesp,
            if(cliente.plazopago_id = 1,'Condición pago: Contado',clientebloqueado.descripcion) as clientebloqueado_desc,
            IFNULL(clientedesbloqueadopro.obs,'') AS clientedesbloqueadopro_obs
            FROM despachosol INNER JOIN despachosoldet
            ON despachosol.id=despachosoldet.despachosol_id
            AND $aux_condactivas
            INNER JOIN notaventa
            ON notaventa.id=despachosol.notaventa_id
            INNER JOIN notaventadetalle
            ON despachosoldet.notaventadetalle_id=notaventadetalle.id
            INNER JOIN producto
            ON notaventadetalle.producto_id=producto.id
            INNER JOIN categoriaprod
            ON categoriaprod.id=producto.categoriaprod_id
            INNER JOIN areaproduccion
            ON areaproduccion.id=categoriaprod.areaproduccion_id
            INNER JOIN cliente
            ON cliente.id=notaventa.cliente_id
            INNER JOIN comuna
            ON comuna.id=despachosol.comunaentrega_id
            INNER JOIN tipoentrega
            ON tipoentrega.id=despachosol.tipoentrega_id
            INNER JOIN vista_despsoltotales
            ON despachosol.id = vista_despsoltotales.id
            LEFT JOIN vista_despordxdespsoltotales
            ON despachosol.id = vista_despordxdespsoltotales.despachosol_id
            INNER JOIN sucursal
            ON notaventa.sucursal_id = sucursal.id AND ISNULL(sucursal.deleted_at)
            INNER JOIN despachosoldet_invbodegaproducto
            ON despachosoldet.id = despachosoldet_invbodegaproducto.despachosoldet_id AND ISNULL(despachosoldet_invbodegaproducto.deleted_at)
            LEFT JOIN clientebloqueado
            ON clientebloqueado.cliente_id = notaventa.cliente_id AND ISNULL(clientebloqueado.deleted_at)
            LEFT JOIN despachosolenvorddesp
            ON despachosolenvorddesp.despachosol_id = despachosol.id AND ISNULL(despachosolenvorddesp.deleted_at)
            LEFT JOIN vista_datacobranza
            ON vista_datacobranza.cliente_id = notaventa.cliente_id
            LEFT JOIN clientedesbloqueado
            ON clientedesbloqueado.cliente_id = notaventa.cliente_id and clientedesbloqueado.notaventa_id = notaventa.id and not isnull(clientedesbloqueado.notaventa_id) and isnull(clientedesbloqueado.deleted_at)
            LEFT JOIN clientedesbloqueadomodulo
            ON clientedesbloqueadomodulo.clientedesbloqueado_id = clientedesbloqueado.id and clientedesbloqueadomodulo.modulo_id = 6
            LEFT JOIN modulo
            ON modulo.id = clientedesbloqueadomodulo.modulo_id
            LEFT JOIN clientedesbloqueadopro
            ON clientedesbloqueadopro.cliente_id = notaventa.cliente_id  and isnull(clientedesbloqueadopro.deleted_at)

            LEFT JOIN clientedesbloqueado as clientedesbloqueado_orddesp
            ON clientedesbloqueado_orddesp.cliente_id = notaventa.cliente_id and clientedesbloqueado_orddesp.notaventa_id = notaventa.id and not isnull(clientedesbloqueado_orddesp.notaventa_id) and isnull(clientedesbloqueado_orddesp.deleted_at)
            LEFT JOIN clientedesbloqueadomodulo as clientedesbloqueadomodulo_orddesp
            ON clientedesbloqueadomodulo_orddesp.clientedesbloqueado_id = clientedesbloqueado_orddesp.id and clientedesbloqueadomodulo_orddesp.modulo_id = 7

            WHERE $vendedorcond
            and $aux_condFecha
            and $aux_condrut
            and $aux_condoc_id
            and $aux_condgiro_id
            and $aux_condareaproduccion_id
            and $aux_condtipoentrega_id
            and $aux_condnotaventa_id
            and $aux_aprobstatus
            and $aux_condcomuna_id
            and $aux_condaprobord
            and $aux_condfechaestdesp
            and $aux_condid
            and $aux_condproducto_id
            and $aux_condsucursal_id
            and $aux_condsta_picking
            and notaventa.id not in (select notaventa_id from notaventacerrada where isnull(notaventacerrada.deleted_at))
            and isnull(despachosol.deleted_at) AND isnull(notaventa.deleted_at) AND isnull(notaventadetalle.deleted_at)
            and isnull(despachosoldet.deleted_at)
            and despachosol.id not in (SELECT despachosol_id FROM despachosolenvorddesp where despachosolenvorddesp.despachosol_id = despachosol.id AND despachosolenvorddesp.staenvdesp = 1 AND ISNULL(despachosolenvorddesp.deleted_at))
            AND despachosol.id NOT IN (SELECT despachoord.despachosol_id 
                                            from despachoord 
                                            WHERE despachoord.id NOT IN 
                                                (SELECT despachoordanul.despachoord_id from despachoordanul 
                                                    WHERE despachoordanul.despachoord_id = despachoord.id 
                                                    AND ISNULL(despachoordanul.deleted_at))
                                            AND isnull(despachoord.aprguiadesp)
                                            AND ISNULL(despachoord.deleted_at))
            GROUP BY despachosol.id
            ORDER BY despachosol.id ASC;";
/*
(select sum(cantsoldesp) as cantsoldesp
                    from despachosol inner join despachosoldet
                    on despachosol.id=despachosoldet.despachosol_id
                    where despachosol.id not in (select despachosol_id from despachosolanul)
                    and despachosoldet.notaventadetalle_id=notaventadetalle.id
                    despachosol.deleted_at is null
                    group by notaventadetalle_id)
*/
    //dd($sql);
    $datas = DB::select($sql);
    filtrarclientesbloqueados($request,$datas);
    //dd($datas);
    return $datas;
}