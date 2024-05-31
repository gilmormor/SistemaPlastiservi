<?php

namespace App\Http\Controllers;

use App\Events\GuardarFacturaDespacho;
use App\Events\GuardarGuiaDespacho;
use App\Events\Notificacion;
use App\Http\Requests\ValidarDespachoOrd;
use App\Models\AreaProduccion;
use App\Models\CategoriaProd;
use App\Models\Cliente;
use App\Models\ClienteBloqueado;
use App\Models\ClienteDesBloqueado;
use App\Models\ClienteSucursal;
use App\Models\ClienteVendedor;
use App\Models\Comuna;
use App\Models\DataCobranza;
use App\Models\DespachoObs;
use App\Models\DespachoOrd;
use App\Models\DespachoOrd_InvMov;
use App\Models\DespachoOrdAnul;
use App\Models\DespachoOrdDet;
use App\Models\DespachoOrdDet_InvBodegaProducto;
use App\Models\DespachoOrdRec;
use App\Models\DespachoSol;
use App\Models\DespachoSolDet;
use App\Models\DespachoSolEnvOrdDesp;
use App\Models\Dte;
use App\Models\Empresa;
use App\Models\FormaPago;
use App\Models\Giro;
use App\Models\InvBodega;
use App\Models\InvBodegaProducto;
use App\Models\InvMov;
use App\Models\InvMovDet;
use App\Models\InvMovDet_BodOrdDesp;
use App\Models\InvMovDet_BodSolDesp;
use App\Models\InvMovModulo;
use App\Models\NotaVenta;
use App\Models\NotaVentaCerrada;
use App\Models\NotaVentaDetalle;
use App\Models\PlazoPago;
use App\Models\Producto;
use App\Models\Seguridad\Usuario;
use App\Models\Sucursal;
use App\Models\TipoEntrega;
use App\Models\Vendedor;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DespachoOrdController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-orden-despacho');
        $empresa = Empresa::findOrFail(1);
        $tablashtml['stabloxdeusiscob'] = $empresa->stabloxdeusiscob;
        $solenvord = 0; //ESTATUS ENVIAR SOL DESP A ORDEN DESPACHO
        return view('despachoord.index',compact('solenvord','tablashtml'));
    }

    public function despachoordpage(Request $request){
        $datas = consultaindex($request);
        $i = 0;
        foreach ($datas as $data) {
            //dd($datas[$i]);
            $datas[$i]->obsdevolucion = ""; //Observacion devolucion
            $despachoord = DespachoOrd::findOrFail($data->id);
            if($despachoord->despachoordanulguiafacts->count()>0){
                $datas[$i]->obsdevolucion = $despachoord->despachoordanulguiafacts->last()->observacion;
            }
            $i++;
        }
        return datatables($datas)->toJson();
    }


    public function indexguia()
    {
        can('listar-guia-despacho');
        $despachoordanul = DespachoOrdAnul::select(['despachoord_id'])->get();
        $notaventacerradaArray = NotaVentaCerrada::pluck('notaventa_id')->toArray();
        $datas = DespachoOrd::orderBy('id')
                        ->where('aprguiadesp','1')
                        ->whereNull('guiadespacho')
                        ->whereNotIn('id',  $despachoordanul)
                        ->whereNotIn('notaventa_id', $notaventacerradaArray)
                        ->get();
        $aux_vista = 'G';
        $aux_titulo = "Asignar Guia Despacho";
        return view('despachoord.indexguiafact', compact('datas','aux_vista','aux_titulo'));
    }

    public function indexfact()
    {
        can('listar-factura-despacho');
        $despachoordanul = DespachoOrdAnul::select(['despachoord_id'])->get();
        $notaventacerradaArray = NotaVentaCerrada::pluck('notaventa_id')->toArray();
        $datas = DespachoOrd::orderBy('id')
                        ->whereNotNull('guiadespacho')
                        ->whereNull('numfactura')
                        ->whereNotIn('id',  $despachoordanul)
                        ->whereNotIn('notaventa_id', $notaventacerradaArray)
                        ->get();
        $aux_vista = 'F';
        $aux_titulo = "Asignar Número de Factura";
        return view('despachoord.indexguiafact', compact('datas','aux_vista','aux_titulo'));
    }

    public function indexcerrada()
    {
        can('listar-orden-despacho-cerrada');
        $despachoordanul = DespachoOrdAnul::select(['despachoord_id'])->get();
        $datas = DespachoOrd::orderBy('id')
                        ->whereNotNull('guiadespacho')
                        ->whereNotNull('numfactura')
                        ->whereNotIn('id',  $despachoordanul)
                        ->get();
        $aux_vista = 'C';
        $aux_titulo = "Ordenes de Despacho Cerradas";
        return view('despachoord.indexguiafact', compact('datas','aux_vista','aux_titulo'));
    }

    public function productobuscarpage(Request $request){
        $datas = Producto::productosxClienteTemp($request);
        return datatables($datas)->toJson();
    }

    public function clientebuscarpage(){
        $datas = Cliente::clientesxUsuarioSQLTemp();
        return datatables($datas)->toJson();
    }

    public function productobuscarpageid(Request $request){
        $datas = Producto::productosxClienteTemp($request);
        return datatables($datas)->toJson();
    }

    public function clientebuscarpageid($id){
        $datas = Cliente::clientesxUsuarioSQLTemp();
        return datatables($datas)->toJson();
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

    public function crearord($id)
    {
        can('crear-orden-despacho');
        $valores = explode('-', $id);
        $solenvord = $valores[0];
        $id = substr($id, strpos($id, '-') + 1);
        $data = DespachoSol::findOrFail($id);
        if(isset($data->notaventa->cliente->clientebloqueado->descripcion)){
            return redirect('despachoord')->with([
                'mensaje'=>'Cliente bloqueado: ' . $data->notaventa->cliente->clientebloqueado->descripcion . ". Razon Social: " . $data->notaventa->cliente->razonsocial,
                'tipo_alert' => 'alert-error'
            ]);    
        }

        $request = new Request();
        $request->merge(['modulo_id' => 7]);
        $request->request->set('modulo_id', 7);
        $request->merge(['notaventa_id' => $data->notaventa_id]);
        $request->request->set('notaventa_id', $data->notaventa_id);
        //$cliente = Cliente::findOrFail($request->cliente_id);
        $clibloq = clienteBloqueado($data->notaventa->cliente_id,0,$request);
        if(!is_null($clibloq["bloqueo"])){
            $request = new Request();
            $request->merge(['cliente_id' => $data->notaventa->cliente_id]);
            $request->request->set('cliente_id', $data->notaventa->cliente_id);
            $respuesta = DataCobranza::llenartabla($request);
    
            return redirect('despachoord')->with([
                "mensaje" => "Cliente Bloqueado por " . $clibloq["bloqueo"],
                "tipo_alert" => "alert-error"
            ]);
        }

        if(isset($data->despachosolanul)){
            return redirect('despachoord')->with([
                'mensaje'=>'Solicitud Despacho anulada: ' . $data->id,
                'tipo_alert' => 'alert-error'
            ]);
        }


        $data->plazoentrega = $newDate = date("d/m/Y", strtotime($data->plazoentrega));
        $data->fechaestdesp = $newDate = date("d/m/Y", strtotime($data->fechaestdesp));
        $detalles = $data->despachosoldets()->get();
        $arrayBodegasPicking = llenarArrayBodegasPickingSolDesp($detalles);
        //dd($arrayBodegasPicking);
        //dd($detalles);

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
        $invmovmodulo = InvMovModulo::where("cod","=","ORDDESP")->get();
        $array_bodegasmodulo = $invmovmodulo[0]->invmovmodulobodsals->pluck('id')->toArray();

        //dd($clientedirecs);
        return view('despachoord.crear', compact('data','clienteselec','clientes','clienteDirec','clientedirecs','detalles','comunas','formapagos','plazopagos','vendedores','vendedores1','productos','fecha','empresa','tipoentregas','giros','despachoobss','sucurArray','aux_sta','aux_cont','aux_statusPant','vendedor_id','array_bodegasmodulo','arrayBodegasPicking','solenvord'));
         
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function guardar(ValidarDespachoOrd $request)
    {
        can('guardar-orden-despacho');

        ////VALIDAR CUANDO EXSISTEN VARIOS ITEM DE UN MISMO PRODUCTO
        ////LA CANTIDAD DEL COD DE PRODUCTO REPETIDO LO AGRUPO EN UNO Y LO COMPARO CON EL STOCK DEL PRODUCTO
        ///INICIO DE VALIDACION DE STOCK
        $invbodegaproducto_arrays = [];
        $cont_invbodegaproducto = count($request->invbodegaproducto_id);
        if($cont_invbodegaproducto > 0){
            for ($i=0; $i < $cont_invbodegaproducto ; $i++){
                $invbodegaproducto = InvBodegaProducto::findOrFail($request->invbodegaproducto_id[$i]);
                //VALIDAR SI EL PRODUCTO PERMITE AVANZAR SIN IMPORTAR EL STOCK
                if($invbodegaproducto->producto->categoriaprod->stadespsinstock == 0){
                    //INICIO: VALIDANDO EL STOCK DE CADA ITEM 
                    $aux_Stock = 0;
                    if($invbodegaproducto->invbodega->tipo == 1){
                        $despachosoldet = DespachoSolDet::findOrFail($request->invbodegaproductoNVdet_id[$i]);
                        $notaventadetalle = NotaVentaDetalle::findOrFail($despachosoldet->notaventadetalle_id);
                        $detalles = $notaventadetalle->despachosoldets()->get();
                        $arrayBodegasPickings = InvBodega::llenarArrayBodegasPickingSolDesp($detalles);
                        $aux_Stock = 0;
                        foreach ($arrayBodegasPickings as $arrayBodegasPicking) {
                            $aux_Stock += $arrayBodegasPicking["stock"];
                        }   
                    }else{
                        $arrayStock = InvBodegaProducto::existencia([
                            "invbodegaproducto_id" => $request->invbodegaproducto_id[$i]
                        ]);
                        $aux_Stock = $arrayStock["stock"]["cant"];
                    }
                    if($request->invcant[$i]>0){
                        if($request->invcant[$i] > $aux_Stock){
                            return redirect('despachoord')->with([
                                'mensaje'=>'Producto ID:' . $invbodegaproducto->producto_id . ', Cantidad ' . $request->invcant[$i] . ' es mayor al Stock ' . $aux_Stock,
                                'tipo_alert' => 'alert-error'
                            ]); 
                        }
                    }
                    //FIN: VALIDANDO EL STOCK DE CADA ITEM 
                    $aux_cant = $request->invcant[$i] ? $request->invcant[$i] : 0;
                    if(isset($invbodegaproducto_arrays[$request->invbodegaproducto_id[$i]])){
                        $aux_cant += $invbodegaproducto_arrays[$request->invbodegaproducto_id[$i]]["cant"];
                    }
                    $invbodegaproducto_arrays[$request->invbodegaproducto_id[$i]] = [
                        "notaventadetalle_id" => $request->invbodegaproductoNVdet_id[$i],
                        "invbodegaproducto_id" => $request->invbodegaproducto_id[$i],
                        "producto_id" => $invbodegaproducto->producto_id,
                        "tipo" => $invbodegaproducto->invbodega->tipo,
                        "stock" => 0,
                        "cant" => $aux_cant,
                    ];    
                }    
            }
        }
        foreach ($invbodegaproducto_arrays as &$invbodegaproducto_array) {
            if($invbodegaproducto_array["tipo"] != 1) {
                $arrayStock = InvBodegaProducto::existencia([
                    "invbodegaproducto_id" => $invbodegaproducto_array["invbodegaproducto_id"]
                ]);
                $invbodegaproducto_array["stock"] = $arrayStock["stock"]["cant"];
                if($invbodegaproducto_array["cant"] > $invbodegaproducto_array["stock"]){
                    return redirect('despachoord')->with([
                        'mensaje'=>'Producto ID:' . $invbodegaproducto_array["producto_id"] . ', Cantidad ' . $invbodegaproducto_array["cant"] . ' es mayor al Stock ' . $invbodegaproducto_array["stock"],
                        'tipo_alert' => 'alert-error'
                    ]); 
                }    
            }else{
                //HICE ESTO PATRA VALIDAR TAMBIEN EL PIKING, PERO CON EL PICKING NO HAY PROBLEMA
                //YA QUE EL PICKING ESTA GUARDADO POR CLIENTE Y POR ITEM
                //ES DECIR QUE EL STOCK DEL PICKING ES INDIVIDUAL DE CADA ITEM, ASI QUE NO HAY PROBLEMA CON RESPECTO AL STOCK
                //IGUAL DEBO VALIDAR EL STOCK DEL LADO DEL SERVIDOR, ESO QUEDA PENDIENTE
                $despachosoldet = DespachoSolDet::findOrFail($invbodegaproducto_array["notaventadetalle_id"]);
                $notaventadetalle = NotaVentaDetalle::findOrFail($despachosoldet->notaventadetalle_id);
                $detalles = $notaventadetalle->despachosoldets()->get();
                $arrayBodegasPickings = InvBodega::llenarArrayBodegasPickingSolDesp($detalles);
                $aux_picking = 0;
                foreach ($arrayBodegasPickings as $arrayBodegasPicking) {
                    $aux_picking += $arrayBodegasPicking["stock"];
                }
                $invbodegaproducto_array["stock"] = $aux_picking;    
            }
            //$data->picking = $aux_picking;
        }
        ///FIN DE VALIDACION DE STOCK
        $notaventacerrada = NotaVentaCerrada::where('notaventa_id',$request->notaventa_id)->get();
        //dd($notaventacerrada);
        if(count($notaventacerrada) == 0){
            $despachosol = DespachoSol::findOrFail($request->despachosol_id);

            if($despachosol->updated_at != $request->updated_at){
                return redirect('despachoord')->with([
                    'mensaje'=>'Solicitud Despacho modificada por otro usuario:' . $despachosol->id,
                    'tipo_alert' => 'alert-error'
                ]);
            }
            if(isset($despachosol->despachosolanul)){
                return redirect('despachoord')->with([
                    'mensaje'=>'Solicitud Despacho anulada: ' . $despachosol->id,
                    'tipo_alert' => 'alert-error'
                ]);
            }
            $array_despachoord = DespachoOrd::where("despachosol_id",$request->despachosol_id)->pluck('id')->toArray();
            $despachoordrec = DespachoOrdRec::whereIn('despachoord_id',$array_despachoord)
                            ->where("updated_at",">",session('aux_fecinicreOD'))
                            ->get();
            if(count($despachoordrec) > 0){
                return redirect('despachoord')->with([
                    'mensaje'=>'Registro no fue creado. Motivo: Fue actualizado un Rechazo.',
                    'tipo_alert' => 'alert-error'
                ]);

            }
            foreach ($despachosol->notaventa->cliente->clientebloqueados as $clientebloqueado) {
                return redirect('despachoord')->with([
                    'id' => 0,
                    'mensaje'=>'Registro no fue guardado. Cliente Bloqueado: ' . $clientebloqueado->descripcion,
                    'tipo_alert' => 'alert-error'
                ]);
            }
            /*
            $clibloq = ClienteBloqueado::where("cliente_id" , "=" ,$despachosol->notaventa->cliente_id)->get();
            if(count($clibloq) > 0){
                return redirect('despachoord')->with([
                    'mensaje'=>'Registro no fue guardado. Cliente Bloqueado: ' . $clibloq[0]->descripcion ,
                    'tipo_alert' => 'alert-error'
                ]);
            }
            */

            $request1 = new Request();
            $request1->merge(['modulo_id' => 7]);
            $request1->request->set('modulo_id', 7);
            $request1->merge(['notaventa_id' => $despachosol->notaventa_id]);
            $request1->request->set('notaventa_id', $despachosol->notaventa_id);
            $request1->merge(['deldesbloqueo' => 1]);
            $request1->request->set('deldesbloqueo', 1);
            $clibloq = clienteBloqueado($despachosol->notaventa->cliente_id,0,$request1);
            if(!is_null($clibloq["bloqueo"])){
                $request1 = new Request();
                $request1->merge(['cliente_id' => $despachosol->notaventa->cliente_id]);
                $request1->request->set('cliente_id', $despachosol->notaventa->cliente_id);
                $respuesta = DataCobranza::llenartabla($request1);
    
                return redirect('despachoord')->with([
                    "mensaje" => "Cliente Bloqueado por " . $clibloq["bloqueo"],
                    "tipo_alert" => "alert-error"
                ]);
            }
    
            if($despachosol->updated_at == $request->updated_at){
                $despachosol->updated_at = date("Y-m-d H:i:s");
                $despachosol->save();
                $hoy = date("Y-m-d H:i:s");
                $request->request->add(['fechahora' => $hoy]);
                $request->request->add(['usuario_id' => auth()->id()]);
                $dateInput = explode('/',$request->plazoentrega);
                $request["plazoentrega"] = $dateInput[2].'-'.$dateInput[1].'-'.$dateInput[0];
                $dateInput = explode('/',$request->fechaestdesp);
                $request["fechaestdesp"] = $dateInput[2].'-'.$dateInput[1].'-'.$dateInput[0];
                $despachoord = DespachoOrd::create($request->all());
                $despachoord_id = $despachoord->id;
                $cont_producto = count($request->producto_id);
                if($cont_producto>0){
                    for ($i=0; $i < $cont_producto ; $i++){
                        $aux_cantord = $request->cantord[$i];
                        if(is_null($request->producto_id[$i])==false && is_null($aux_cantord)==false && $aux_cantord > 0){
                            $despachoorddet = new DespachoOrdDet();
                            $despachoorddet->despachoord_id = $despachoord_id;
                            $despachoorddet->despachosoldet_id = $request->despachosoldet_id[$i];
                            $despachoorddet->notaventadetalle_id = $request->notaventadetalle_id[$i];
                            $despachoorddet->cantdesp = $request->cantord[$i];
                            if($despachoorddet->save()){
                                $cont_bodegas = count($request->invcant);
                                if($cont_bodegas>0){
                                    for ($b=0; $b < $cont_bodegas ; $b++){
                                        if($request->invbodegaproducto_producto_id[$b] == $request->producto_id[$i] and $request->invbodegaproductoNVdet_id[$b] == $request->NVdet_id[$i] and ($request->invcant[$b] != 0)){
                                            $despachoorddet_invbodegaproducto = new DespachoOrdDet_InvBodegaProducto();
                                            $despachoorddet_invbodegaproducto->despachoorddet_id = $despachoorddet->id;
                                            $despachoorddet_invbodegaproducto->invbodegaproducto_id = $request->invbodegaproducto_id[$b];
                                            $despachoorddet_invbodegaproducto->cant = $request->invcant[$b] * -1;
                                            $despachoorddet_invbodegaproducto->save();
                                        }
                                    }
                                }
                                /*
                                $notaventadetalle = NotaVentaDetalle::findOrFail($request->NVdet_id[$i]);
                                $notaventadetalle->cantsoldesp = $request->cantsoldesp[$i];
                                $notaventadetalle->save();
                                */
                                //$despacho_id = $despachoord->id;
                            }
                        }
                    }
                }
                if($despachoord->notaventa->cliente->clientedesbloqueado){
                    $clientedesbloqueado_id = $despachoord->notaventa->cliente->clientedesbloqueado->id;
                    if (ClienteDesBloqueado::destroy($clientedesbloqueado_id)) {
                        //Despues de eliminar actualizo el campo usuariodel_id=usuario que elimino el registro
                        $clientedesbloqueado = ClienteDesBloqueado::withTrashed()->findOrFail($clientedesbloqueado_id);
                        $clientedesbloqueado->usuariodel_id = auth()->id();
                        $clientedesbloqueado->save();
                    }
                }
                return redirect('despachoord')->with([
                    'mensaje'=>'Registro creado con exito.',
                    'tipo_alert' => 'alert-success'
                ]);
            }else{
                return redirect('despachoord')->with([
                    'mensaje'=>'Registro no fue creado. Registro Editado por otro usuario. Fecha Hora: '.$despachosol->updated_at,
                    'tipo_alert' => 'alert-error'
                ]);
            }
        }else{
            return redirect('despachoord')->with([
                'mensaje'=>'Registro no fue creado. La nota de venta fue Cerrada. Observ: ' . $notaventacerrada[0]->observacion . ' Fecha: ' . date("d/m/Y h:i:s A", strtotime($notaventacerrada[0]->created_at)),
                'tipo_alert' => 'alert-error'
            ]);
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
    public function editar($id)
    {
        can('editar-orden-despacho');
        $data = DespachoOrd::findOrFail($id);

        $request = new Request();
        $request->merge(['stanv' => 0]);
        $request->request->set('stanv', 0);
        //$cliente = Cliente::findOrFail($request->cliente_id);
        $clibloq = clienteBloqueado($data->notaventa->cliente_id,0,$request);
        if(!is_null($clibloq["bloqueo"])){
            return redirect('despachoord')->with([
                "mensaje" => "Cliente Bloqueado por " . $clibloq["bloqueo"],
                "tipo_alert" => "alert-error"
            ]);
        }

        $data->plazoentrega = $newDate = date("d/m/Y", strtotime($data->plazoentrega));
        $data->fechaestdesp = $newDate = date("d/m/Y", strtotime($data->fechaestdesp));
        $detalles = $data->despachoorddets()->get();
        $arrayBodegasPicking = llenarArrayBodegasPickingOrdDesp($detalles);

        //dd($arrayBodegasPicking);
/*
        foreach($detalles as $detalle){
            dd($detalle);
            $sql = "SELECT cantsoldesp
                    FROM vista_sumsoldespdet
                    WHERE notaventadetalle_id=$detalle->notaventadetalle_id";
            $datasuma = DB::select($sql);
            if(empty($datasuma)){
                $sumacantsoldesp= 0;
            }else{
                $sumacantsoldesp= $datasuma[0]->cantsoldesp;
            }
            //if($detalle->cant > $sumacantsoldesp);
            
        }*/
        $vendedor_id=$data->notaventa->vendedor_id;
        $clienteselec = $data->notaventa->cliente()->get();
        //session(['aux_aprocot' => '0']);
        //dd($clienteselec[0]->rut);

        $user = Usuario::findOrFail(auth()->id());
        $sucurArray = $user->sucursales->pluck('id')->toArray();

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
        $invmovmodulo = InvMovModulo::where("cod","=","ORDDESP")->get();
        $array_bodegasmodulo = $invmovmodulo[0]->invmovmodulobodsals->pluck('id')->toArray();
        return view('despachoord.editar', compact('data','clienteselec','clientes','clienteDirec','clientedirecs','detalles','comunas','formapagos','plazopagos','vendedores','vendedores1','productos','fecha','empresa','tipoentregas','giros','despachoobss','sucurArray','aux_sta','aux_cont','aux_statusPant','vendedor_id','array_bodegasmodulo','arrayBodegasPicking'));
  
  
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function actualizar(ValidarDespachoOrd $request, $id)
    {
        can('guardar-orden-despacho');
        $request->merge(['stanv' => 0]);
        $clibloq = clienteBloqueado($request->cliente_id,0,$request);
        if(!is_null($clibloq["bloqueo"])){
            return redirect('despachoord')->with([
                "mensaje" => "Cliente Bloqueado por " . $clibloq["bloqueo"],
                "tipo_alert" => "alert-error"
            ]);
        }


        $notaventacerrada = NotaVentaCerrada::where('notaventa_id',$request->notaventa_id)->get();
        //dd($request);
        if(count($notaventacerrada) == 0){
            //dd($request);
            $dateInput = explode('/',$request->plazoentrega);
            $request["plazoentrega"] = $dateInput[2].'-'.$dateInput[1].'-'.$dateInput[0];
            $dateInput = explode('/',$request->fechaestdesp);
            $request["fechaestdesp"] = $dateInput[2].'-'.$dateInput[1].'-'.$dateInput[0];
            $despachoord = DespachoOrd::findOrFail($id);
            $clibloq = ClienteBloqueado::where("cliente_id" , "=" ,$despachoord->notaventa->cliente_id)->get();
            if(count($clibloq) > 0){
                return redirect('despachoord')->with([
                    'mensaje'=>'Registro no fue guardado. Cliente Bloqueado: ' . $clibloq[0]->descripcion ,
                    'tipo_alert' => 'alert-error'
                ]);
            }
            if($despachoord->updated_at == $request->updated_at){
                $despachoord->updated_at = date("Y-m-d H:i:s");
                $despachoord->comunaentrega_id = $request->comunaentrega_id;
                $despachoord->tipoentrega_id = $request->tipoentrega_id;
                $despachoord->plazoentrega = $request->plazoentrega;
                $despachoord->lugarentrega = $request->lugarentrega;
                $despachoord->contacto = $request->contacto;
                $despachoord->contactoemail = $request->contactoemail;
                $despachoord->contactotelf = $request->contactotelf;
                $despachoord->observacion = $request->observacion;
                $despachoord->fechaestdesp = $request->fechaestdesp;
                $despachoord->despachoobs_id = $request->despachoobs_id;
                //dd($request);
                if($despachoord->save()){
                    $cont_producto = count($request->producto_id);
                    if($cont_producto>0){
                        for ($i=0; $i < $cont_producto ; $i++){
                            if(is_null($request->producto_id[$i])==false && is_null($request->cant[$i])==false){
                                $despachoorddet = DespachoOrdDet::findOrFail($request->NVdet_id[$i]);
                                $despachoorddet->cantdesp = $request->cantord[$i];
                                if($despachoorddet->save()){
                                    if($request->cantord[$i]==0){
                                        $despachoorddet->usuariodel_id = auth()->id();
                                        $despachoorddet->save();
                                        DB::table('despachoorddet_invbodegaproducto')->where('despachoorddet_id', $despachoorddet->id)->delete();
                                        $despachoorddet->delete();
                                    }else{
                                        $cont_bodegas = count($request->invcant);
                                        if($cont_bodegas>0){
                                            for ($b=0; $b < $cont_bodegas ; $b++){
                                                if(($request->invbodegaproducto_producto_id[$b] == $request->producto_id[$i]) and $request->invbodegaproductoNVdet_id[$b] == $request->NVdet_id[$i]){
                                                    /*
                                                    DB::table('despachoorddet_invbodegaproducto')->updateOrInsert(
                                                        ['despachoorddet_id' => $request->NVdet_id[$i], 'invbodegaproducto_id' => $request->invbodegaproducto_id[$b]],
                                                        [
                                                            'cant' => $request->invcant[$b] * -1
                                                        ]
                                                    );
                                                    */
                                                    if($request->invcant[$b] > 0){
                                                        DespachoOrdDet_InvBodegaProducto::updateOrCreate(
                                                            ['despachoorddet_id' => $request->NVdet_id[$i], 'invbodegaproducto_id' => $request->invbodegaproducto_id[$b]],
                                                            [
                                                                'cant' => $request->invcant[$b] * -1
                                                            ]
                                                        );    
                                                    }else{
                                                        DespachoOrdDet_InvBodegaProducto::where('despachoorddet_id', $request->NVdet_id[$i])
                                                                    ->where('invbodegaproducto_id', $request->invbodegaproducto_id[$b])
                                                                    ->delete();
                                                    }
                        

                                                }
                                            }
                                        }
                                    }

                                    /*
                                    $notaventadetalle = NotaVentaDetalle::findOrFail($despachosoldet->notaventadetalle_id);
                                    $notaventadetalle->cantsoldesp = $request->cantsoldesp[$i];
                                    $notaventadetalle->save();
                                    */
                                    //$despacho_id = $despachosol->id;    
                                }
                            }
                        }
                    }
                }
                if($despachoord->notaventa->cliente->clientedesbloqueado){
                    $clientedesbloqueado_id = $despachoord->notaventa->cliente->clientedesbloqueado->id;
                    if (ClienteDesBloqueado::destroy($clientedesbloqueado_id)) {
                        //Despues de eliminar actualizo el campo usuariodel_id=usuario que elimino el registro
                        $clientedesbloqueado = ClienteDesBloqueado::withTrashed()->findOrFail($clientedesbloqueado_id);
                        $clientedesbloqueado->usuariodel_id = auth()->id();
                        $clientedesbloqueado->save();
                    }
                }

                return redirect('despachoord')->with([
                                                            'mensaje'=>'Registro actualizado con exito.',
                                                            'tipo_alert' => 'alert-success'
                                                        ]);
            }else{
                return redirect('despachoord')->with([
                    'mensaje'=>'Registro modificado por otro usuario. Fecha Hora: '.$despachoord->updated_at,
                    'tipo_alert' => 'alert-error'
                ]);
            }
        }else{
            return redirect('despachoord')->with([
                'mensaje'=>'Registro no fue Modificado. La nota de venta fue Cerrada. Observ: ' . $notaventacerrada[0]->observacion . ' Fecha: ' . date("d/m/Y h:i:s A", strtotime($notaventacerrada[0]->created_at)),
                'tipo_alert' => 'alert-error'
            ]);
        }


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

    public function anular(Request $request)
    {
        if ($request->ajax()) {
            $despachoord = DespachoOrd::findOrFail($request->id);
            if($request->updated_at != $despachoord->updated_at){
                return response()->json([
                    'id' => 0,
                    'error' => '0',
                    'mensaje' => 'Registro fué modificado por otro usuario.',
                    'tipo_alert' => 'error'
                ]);
            }
            if(empty($despachoord->guiadespacho) and empty($despachoord->numfactura)){
                $despachoordanul = new DespachoOrdAnul();
                $despachoordanul->despachoord_id = $request->id;
                $despachoordanul->usuario_id = auth()->id();
                if ($despachoordanul->save()) {
                    //return response()->json(['mensaje' => 'ok']);
                    return response()->json([
                        'error'=>'1',
                        'mensaje'=>'Registro anulado con exito.',
                        'tipo_alert' => 'success'
                    ]);
                } else {
                    //return response()->json(['mensaje' => 'ng']);
                    return response()->json([
                        'error'=>'0',
                        'mensaje'=>'Registro No fue anulado. Error al intentar modificar el registro.',
                        'tipo_alert' => 'error'
                    ]);
                }
            }else{
                //return response()->json(['mensaje' => 'guidesp_factura']);
                return response()->json([
                    'error'=>'0',
                    'mensaje'=>'Registro no fue anulado. Ya tiene asignado Guia despacho o Factura',
                    'tipo_alert' => 'error'
                ]);

            }
        } else {
            abort(404);
        }
    }

    public function guardarguiadesp(Request $request)
    {
        if ($request->ajax()) {
            $despachoord = DespachoOrd::where('guiadespacho','=',$request->guiadespacho)->get();
            $aux_contgdesp = count($despachoord);
            if($aux_contgdesp>0){
                return response()->json(['mensaje' => 'dup']);
            }else{
                $despachoord = DespachoOrd::findOrFail($request->id);
                if($request->updated_at != $despachoord->updated_at){
                    return response()->json([
                        'id' => 0,
                        'error' => '0',
                        'mensaje' => 'Registro fué modificado por otro usuario.',
                        'tipo_alert' => 'error'
                    ]);
                }
                $notaventacerrada = NotaVentaCerrada::where('notaventa_id',$despachoord->notaventa_id)->get();
                if(count($notaventacerrada) == 0){
                    if($request->updatesolonumguia){
                        return updatenumguia($despachoord,$request);
                    }else{
                        $aux_bandera = true;
                        $invmodulo = InvMovModulo::where("cod","ORDDESP")->get();
                        if(count($invmodulo) == 0){
                            return response()->json([
                                'mensaje' => 'MensajePersonalizado',
                                'menper' => "No existe modulo SOLDESP"    
                            ]);
                        }
                        $invmoduloBod = InvMovModulo::findOrFail($invmodulo[0]->id);

                        foreach ($despachoord->despachoorddets as $despachoorddet) {
                            //ESTO DEBE IR EN EL PROYECTO FINAL
                            foreach ($despachoorddet->despachoorddet_invbodegaproductos as $oddetbodprod) {
                                $aux_sucursal_id_producto = $oddetbodprod->invbodegaproducto->invbodega->sucursal_id; 
                                $aux_bodegadespacho_id = 0;
                                foreach($invmoduloBod->invmovmodulobodents as $invmovmodulobodent){
                                    //BUSCAR BODEGA DESPACHO CORRESPONDIENTE AL PRODUCTO QUE SE ESTA PROCESANDO DEPENDIENDO DE LA SUCURSAL QUE CORRESPONDE EL PRODUCTO
                                    if($invmovmodulobodent->sucursal_id == $aux_sucursal_id_producto){
                                        $aux_bodegadespacho_id = $invmovmodulobodent->id;
                                        $requestProd = new Request();
                                        $requestProd["producto_id"] = $oddetbodprod->invbodegaproducto->producto_id;
                                        $requestProd["invbodega_id"] = $aux_bodegadespacho_id;
                                        $requestProd["tipo"] = 2;
                                        $arrayExistencia = InvBodegaProducto::existencia($requestProd);
                                        $existencia = $arrayExistencia["stock"]["cant"];
                                        $existencia += $oddetbodprod->cant;
                                        //dd($oddetbodprod->invbodegaproducto->producto->categoriaprod->stadespsinstock);
                                        //$aux_respuesta = InvBodegaProducto::validarExistenciaStock($despachoorddet->despachoorddet_invbodegaproductos,$aux_bodegadespacho_id);
                                        if($existencia < 0){
                                            //$aux_bandera = $aux_respuesta["bandera"];
                                            return response()->json([
                                                'status' => "0",
                                                'title' => "Bodega sin stock!",
                                                //'mensaje1' => "Bodega: " . $oddetbodprod->invbodegaproducto->invbodega->nombre . ".\nSucursal: " . $oddetbodprod->invbodegaproducto->invbodega->sucursal->nombre . ".\nIdProd: " . $oddetbodprod->invbodegaproducto->producto_id . "\nNombre: " . $oddetbodprod->invbodegaproducto->producto->nombre. "\nCantidad movimiento: " . $oddetbodprod->cant . "\nStock actual: " . $arrayStock["stock"]["cant"],
                                                'mensaje' => "Bodega: " . $invmovmodulobodent->nombre . ".\nSucursal: " . $invmovmodulobodent->sucursal->nombre . ". IdProd: " . $requestProd["producto_id"] . ".\nNombre: " . $oddetbodprod->invbodegaproducto->producto->nombre . ".\nMov: " . $oddetbodprod->cant . ".\nStock: " . $arrayExistencia["stock"]["cant"],
                                                'tipo_alert' => 'error'
                                            ]);
                                        }    
                                    }
                                }
                            }
                            //ESTO DEBE IR EN EL PROYECTO FINAL
                        }
                        $annomes = date("Ym");
                        $aux_DespachoBodegaId = $invmoduloBod->invmovmodulobodents[0]->id; //Id Bodega Despacho (La bodega despacho debe ser unica)
                        validarSiExisteBodega($despachoord,$invmoduloBod);
                        $invmov_array = array();
                        $invmov_array["fechahora"] = date("Y-m-d H:i:s");
                        $invmov_array["annomes"] = $annomes;
                        $invmov_array["desc"] = "Salida de BD / NV:" . $despachoord->notaventa_id . " SD:" . $despachoord->despachosol_id . " OD:" . $request->id;
                        $invmov_array["obs"] = "Salida de BD / NV:" . $despachoord->notaventa_id . " SD:" . $despachoord->despachosol_id . " OD:" . $request->id;
                        $invmov_array["invmovmodulo_id"] = $invmoduloBod->id; //
                        $invmov_array["idmovmod"] = $request->id;
                        $invmov_array["invmovtipo_id"] = 2;
                        $invmov_array["sucursal_id"] = $despachoord->notaventa->sucursal_id;
                        $invmov_array["usuario_id"] = auth()->id();
                        $arrayinvmov_id = array();
                        
                        $invmov = InvMov::create($invmov_array);
                        array_push($arrayinvmov_id, $invmov->id);
                        foreach ($despachoord->despachoorddets as $despachoorddet) {
                            foreach ($despachoorddet->despachoorddet_invbodegaproductos as $oddetbodprod) {
                                //ESTO DEBE IR EN EL PROYECTO FINAL
                                $aux_sucursal_id_producto = $oddetbodprod->invbodegaproducto->invbodega->sucursal_id; 
                                foreach($invmoduloBod->invmovmodulobodents as $invmovmodulobodent){
                                    //BUSCAR BODEGA DESPACHO CORRESPONDIENTE AL PRODUCTO QUE SE ESTA PROCESANDO DEPENDIENDO DE LA SUCURSAL QUE CORRESPONDE EL PRODUCTO
                                    if($invmovmodulobodent->sucursal_id == $aux_sucursal_id_producto){
                                        $aux_bodegadespacho_id = $invmovmodulobodent->id;
                                    }
                                }
                                //ESTO DEBE IR EN EL PROYECTO FINAL

                                $invbodegaproducto = InvBodegaProducto::updateOrCreate(
                                    ['producto_id' => $oddetbodprod->invbodegaproducto->producto_id,'invbodega_id' => $aux_bodegadespacho_id],
                                    [
                                        'producto_id' => $oddetbodprod->invbodegaproducto->producto_id,
                                        'invbodega_id' => $aux_bodegadespacho_id
                                    ]
                                );
        
                                $array_invmovdet = $oddetbodprod->attributesToArray();
                                $array_invmovdet["invbodegaproducto_id"] = $invbodegaproducto->id;
                                $array_invmovdet["producto_id"] = $oddetbodprod->invbodegaproducto->producto_id;
                                $array_invmovdet["invbodega_id"] = $aux_bodegadespacho_id;
                                $array_invmovdet["sucursal_id"] = $invbodegaproducto->invbodega->sucursal_id;
                                $array_invmovdet["unidadmedida_id"] = $despachoorddet->notaventadetalle->unidadmedida_id;
                                $array_invmovdet["invmovtipo_id"] = 2;
                                $array_invmovdet["invmov_id"] = $invmov->id;
                                $array_invmovdet["cantgrupo"] = $array_invmovdet["cant"];
                                $array_invmovdet["cantxgrupo"] = 1;
                                $array_invmovdet["peso"] = $despachoorddet->notaventadetalle->producto->peso;
                                $array_invmovdet["cantkg"] = ($despachoorddet->notaventadetalle->totalkilos / $despachoorddet->notaventadetalle->cant) * $array_invmovdet["cant"];
                                $invmovdet = InvMovDet::create($array_invmovdet);
                                $invmovdet_bodorddesp = InvMovDet_BodOrdDesp ::create([
                                    'invmovdet_id' => $invmovdet->id,
                                    'despachoorddet_invbodegaproducto_id' => $oddetbodprod->id
                                ]);
        
                            }
                        }

                        return updatenumguia($despachoord,$request);
                        /*
                        $despachoord->guiadespacho = $request->guiadespacho;
                        $despachoord->guiadespachofec = date("Y-m-d H:i:s");
                        if ($despachoord->save()) {
                            Event(new GuardarGuiaDespacho($despachoord));
                            return response()->json([
                                                    'mensaje' => 'ok',
                                                    'despachoord' => $despachoord,
                                                    'guiadespachofec' => date("Y-m-d", strtotime($despachoord->guiadespachofec)),
                                                    'id' => $request->id,
                                                    'nfila' => $request->nfila,
                                                    ]);
                        } else {
                            return response()->json(['mensaje' => 'ng']);
                        }
                        */
                    }

                    /** ******** */
                    /*
                    $despachoord->guiadespacho = $request->guiadespacho;
                    $despachoord->guiadespachofec = date("Y-m-d H:i:s");
                    if ($despachoord->save()) {
                        Event(new GuardarGuiaDespacho($despachoord));
                        return response()->json([
                                                'mensaje' => 'ok',
                                                'despachoord' => $despachoord,
                                                'guiadespachofec' => date("Y-m-d", strtotime($despachoord->guiadespachofec))
                                                ]);
                    } else {
                        return response()->json(['mensaje' => 'ng']);
                    } 
                    */                       
                }else{
                    $mensaje = 'Nota Venta fue cerrada: Observ: ' . $notaventacerrada[0]->observacion . ' Fecha: ' . date("d/m/Y h:i:s A", strtotime($notaventacerrada[0]->created_at));
                    return response()->json(['mensaje' => $mensaje]);
                }
            }
        } else {
            abort(404);
        }    
    }

    public function guardarfactdesp(Request $request)
    {
        if ($request->ajax()) {
            //dd($request);
            $despachoord = DespachoOrd::findOrFail($request->id);
            if($request->updated_at != $despachoord->updated_at){
                return response()->json([
                    'status' => '0',
                    'id' => 0,
                    'error' => '0',
                    'title' => '',
                    'mensaje' => 'Registro fué modificado por otro usuario.',
                    'tipo_alert' => 'error'
                ]);
            }
            $notaventacerrada = NotaVentaCerrada::where('notaventa_id',$despachoord->notaventa_id)->get();
            if(count($notaventacerrada) == 0){
                $despachoord->numfactura = $request->numfactura;
                $dateInput = explode('/',$request->fechafactura);
                $despachoord->fechafactura = $dateInput[2].'-'.$dateInput[1].'-'.$dateInput[0];
                $despachoord->numfacturafec = date("Y-m-d H:i:s");
                if ($despachoord->save()) {
                    Event(new GuardarFacturaDespacho($despachoord));
                    return response()->json([
                                            'status' => '1',
                                            'title' => '',
                                            'mensaje' => 'ok',
                                            'despachoord' => $despachoord
                                            ]);
                } else {
                    return response()->json([
                        'status' => '0',
                        'title' => '',
                        'mensaje' => 'Error al guardar registro.'
                    ]);
                }    
            }else{
                $mensaje = 'Nota Venta fue cerrada: Observ: ' . $notaventacerrada[0]->observacion . ' Fecha: ' . date("d/m/Y h:i:s A", strtotime($notaventacerrada[0]->created_at));
                return response()->json(['mensaje' => $mensaje]);
            }
        } else {
            abort(404);
        }
    }
    
    public function consultarod(Request $request)
    {
        if ($request->ajax()) {
            $despachoord = DespachoOrd::findOrFail($request->id);
            if ($despachoord) {
                return response()->json([
                                        'mensaje' => 'ok',
                                        'despachoord' => $despachoord,
                                        'fechafactura' => date("d/m/Y", strtotime($despachoord->fechafactura))
                                        ]);
            } else {
                return response()->json(['mensaje' => 'ng']);
            }
        } else {
            abort(404);
        }
    }

    public function aproborddesp(Request $request)
    {
        if ($request->ajax()) {
            $despachoord = DespachoOrd::findOrFail($request->id);
            if($despachoord == null){
                return response()->json([
                    'id' => 0,
                    'mensaje' => 'Registro fue eliminado previamente.',
                    'tipo_alert' => 'error'
                ]);
            }
            if($request->updated_at != $despachoord->updated_at){
                return response()->json([
                    'id' => 0,
                    'mensaje'=>'Registro fué modificado por otro usuario.',
                    'tipo_alert' => 'error'
                ]);
            }
            //VALIDAR LOS ITEM DE BODEGAS INVOLUCRADAS EN EL DETALLE DE ORDEN DE DESPACHO
            //ENVIO EL DETALLE DE LA ORDEN DE DESPACHO
            $arrayExistencia = InvBodegaProducto::existenciaxSolDespOrdDesp($despachoord->despachoorddets);
            if($arrayExistencia["status"] == "0"){
                return response()->json($arrayExistencia);
            }
            $invmodulo = InvMovModulo::where("cod","ORDDESP")->get();
            if(count($invmodulo) == 0){
                return response()->json([
                    'status' => '0',
                    'title' => "",
                    'mensaje' => "No existe modulo SOLDESP",
                    'tipo_alert' => 'error'
                ]);
            }
            $request1 = new Request();
            $request1->merge(['modulo_id' => 8]);
            $request1->request->set('modulo_id', 8);
            $request1->merge(['notaventa_id' => $despachoord->notaventa_id]);
            $request1->request->set('notaventa_id', $despachoord->notaventa_id);
            $request1->merge(['deldesbloqueo' => 1]);
            $request1->request->set('deldesbloqueo', 1);
            $bloqcli = clienteBloqueado($despachoord->notaventa->cliente_id,0,$request1);
            if(!is_null($bloqcli["bloqueo"])){
                $request1 = new Request();
                $request1->merge(['cliente_id' => $despachoord->notaventa->cliente_id]);
                $request1->request->set('cliente_id', $despachoord->notaventa->cliente_id);
                $respuesta = DataCobranza::llenartabla($request1);

                return response()->json([
                    'error' => 1,
                    'mensaje' => "Cliente bloqueado \n" . $bloqcli["bloqueo"],
                    'tipo_alert' => "error"
                ]);
            }

            $invmoduloBod = InvMovModulo::findOrFail($invmodulo[0]->id);
            $aux_DespachoBodegaId = $invmoduloBod->invmovmodulobodents[0]->id; //Id Bodega Despacho (La bodega despacho debe ser unica)
            validarSiExisteBodega($despachoord,$invmoduloBod);   
            $annomes = date("Ym");
            $invmov_array = array();
            $invmov_array["fechahora"] = date("Y-m-d H:i:s");
            $invmov_array["annomes"] = $annomes;
            $invmov_array["desc"] = "Salida de Bodega / NV:" . $despachoord->notaventa_id . " SD:" . $despachoord->despachosol_id . " OD:" . $request->id;
            $invmov_array["obs"] = "Salida de Bodega / NV:" . $despachoord->notaventa_id . " SD:" . $despachoord->despachosol_id . " OD:" . $request->id;
            $invmov_array["invmovmodulo_id"] = $invmoduloBod->id; //Modulo Orden Despacho
            $invmov_array["idmovmod"] = $request->id;
            $invmov_array["invmovtipo_id"] = 2;
            $invmov_array["sucursal_id"] = $despachoord->notaventa->sucursal_id;
            $invmov_array["usuario_id"] = auth()->id();
            $arrayinvmov_id = array();
            
            $invmov = InvMov::create($invmov_array);
            array_push($arrayinvmov_id, $invmov->id);
            foreach ($despachoord->despachoorddets as $despachoorddet) {
                foreach ($despachoorddet->despachoorddet_invbodegaproductos as $oddetbodprod) {
                    $array_invmovdet = $oddetbodprod->attributesToArray();
                    $array_invmovdet["producto_id"] = $oddetbodprod->invbodegaproducto->producto_id;
                    $array_invmovdet["invbodega_id"] = $oddetbodprod->invbodegaproducto->invbodega_id;
                    $array_invmovdet["sucursal_id"] = $oddetbodprod->invbodegaproducto->invbodega->sucursal_id;
                    $array_invmovdet["unidadmedida_id"] = $despachoorddet->notaventadetalle->unidadmedida_id;
                    $array_invmovdet["invmovtipo_id"] = 2;
                    $array_invmovdet["cantgrupo"] = $array_invmovdet["cant"];
                    $array_invmovdet["cantxgrupo"] = 1;
                    $array_invmovdet["peso"] = $despachoorddet->notaventadetalle->producto->peso;
                    $array_invmovdet["cantkg"] = ($despachoorddet->notaventadetalle->totalkilos / $despachoorddet->notaventadetalle->cant) * $array_invmovdet["cant"];
                    $array_invmovdet["invmov_id"] = $invmov->id;
                    $invmovdet = InvMovDet::create($array_invmovdet);                                
                    if ($oddetbodprod->invbodegaproducto->invbodega->tipo == 1){ //Si = 1 Bodega de Picking
                        /***BUSCO LA BODEGA QUE TIENE PICKING */
                        foreach($oddetbodprod->despachoorddet->despachosoldet->despachosoldet_invbodegaproductos as $despachosoldet_invbodegaproducto){
                            if(($despachosoldet_invbodegaproducto->cant * -1) > 0){
                                $invmovdet_bodsoldesp = InvMovDet_BodSolDesp ::create([
                                    'invmovdet_id' => $invmovdet->id,
                                    'despachosoldet_invbodegaproducto_id' => $despachosoldet_invbodegaproducto->id
                                ]);
                                break;
                            }
                        }
                    }
                }
            }
            $invmov_array = array();
            $invmov_array["fechahora"] = date("Y-m-d H:i:s");
            $invmov_array["annomes"] = $annomes;
            $invmov_array["desc"] = "Entrada a BD / NV:" . $despachoord->notaventa_id . " SD:" . $despachoord->despachosol_id . " OD:" . $request->id;
            $invmov_array["obs"] = "Entrada a BD / NV:" . $despachoord->notaventa_id . " SD:" . $despachoord->despachosol_id . " OD:" . $request->id;
            $invmov_array["invmovmodulo_id"] = $invmoduloBod->id; //Modulo Orden Despacho
            $invmov_array["idmovmod"] = $request->id;
            $invmov_array["invmovtipo_id"] = 1;
            $invmov_array["sucursal_id"] = $despachoord->notaventa->sucursal_id;
            $invmov_array["usuario_id"] = auth()->id();
            
            $invmov = InvMov::create($invmov_array);
            array_push($arrayinvmov_id, $invmov->id);
            foreach ($despachoord->despachoorddets as $despachoorddet) {
                foreach ($despachoorddet->despachoorddet_invbodegaproductos as $oddetbodprod) {
                    $aux_sucursal_id_producto = $oddetbodprod->invbodegaproducto->invbodega->sucursal_id; 
                    //ESTO DEBE IR EN EL PROYECTO FINAL
                    foreach($invmoduloBod->invmovmodulobodents as $invmovmodulobodent){
                        //BUSCAR BODEGA DESPACHO CORRESPONDIENTE AL PRODUCTO QUE SE ESTA PROCESANDO DEPENDIENDO DE LA SUCURSAL QUE CORRESPONDE EL PRODUCTO
                        if($invmovmodulobodent->sucursal_id == $aux_sucursal_id_producto){
                            $aux_bodegadespacho_id = $invmovmodulobodent->id;
                        }
                    }      
                    //ESTO DEBE IR EN EL PROYECTO FINAL
                    $invbodegaproducto = InvBodegaProducto::updateOrCreate(
                        ['producto_id' => $oddetbodprod->invbodegaproducto->producto_id,'invbodega_id' => $aux_bodegadespacho_id],
                        [
                            'producto_id' => $oddetbodprod->invbodegaproducto->producto_id,
                            'invbodega_id' => $aux_bodegadespacho_id
                        ]
                    );
                    $array_invmovdet = $oddetbodprod->attributesToArray();
                    $array_invmovdet["invbodegaproducto_id"] = $invbodegaproducto->id;
                    $array_invmovdet["producto_id"] = $oddetbodprod->invbodegaproducto->producto_id;
                    $array_invmovdet["invbodega_id"] = $aux_bodegadespacho_id;
                    $array_invmovdet["sucursal_id"] = $invbodegaproducto->invbodega->sucursal_id;
                    $array_invmovdet["unidadmedida_id"] = $despachoorddet->notaventadetalle->unidadmedida_id;
                    $array_invmovdet["invmovtipo_id"] = 1;
                    $array_invmovdet["cant"] = $array_invmovdet["cant"] * -1;
                    $array_invmovdet["cantgrupo"] = $array_invmovdet["cant"];
                    $array_invmovdet["cantxgrupo"] = 1;
                    $array_invmovdet["peso"] = $despachoorddet->notaventadetalle->producto->peso;
                    $array_invmovdet["cantkg"] = ($despachoorddet->notaventadetalle->totalkilos / $despachoorddet->notaventadetalle->cant) * $array_invmovdet["cant"] *-1;
                    $array_invmovdet["invmov_id"] = $invmov->id;
                    $invmovdet = InvMovDet::create($array_invmovdet);
                    $invmovdet_bodorddesp = InvMovDet_BodOrdDesp ::create([
                        'invmovdet_id' => $invmovdet->id,
                        'despachoorddet_invbodegaproducto_id' => $oddetbodprod->id
                    ]);
                }
            }
            $despachoord->aprguiadesp = 1;
            $despachoord->aprguiadespfh = date("Y-m-d H:i:s");
            if ($despachoord->save()) {
                //$despachoord->invmovs()->sync($arrayinvmov_id);
                $despachoord_invmov = DespachoOrd_InvMov::create([
                        'despachoord_id' => $despachoord->id,
                        'invmov_id' => $arrayinvmov_id[0]
                    ]);
                $despachoord_invmov = DespachoOrd_InvMov::create(
                    [
                        'despachoord_id' => $despachoord->id,
                        'invmov_id' => $arrayinvmov_id[1]
                    ]);
                $aux_usuariodestino_id = NULL;
                if($despachoord->notaventa->vendedor->persona->usuario){
                    $aux_usuariodestino_id = $despachoord->notaventa->vendedor->persona->usuario->id;
                }
                /* NOTIFICACION EN COMENTARIO POR DECISION EN REUNION LOS PINOS 07/11/2023
                // SE DEFINIO QUE NO ES NECESARIO ENVIAR CORREO AL HACER ORDEN DE DESPACHO
                $aux_rut = number_format( substr ( $despachoord->notaventa->cliente->rut, 0 , -1 ) , 0, "", ".") . '-' . substr ( $despachoord->notaventa->cliente->rut, strlen($despachoord->notaventa->cliente->rut) -1 , 1 );
                $aux_telefono = $despachoord->notaventa->cliente->telefono;
                $aux_razonSocial = $despachoord->notaventa->cliente->razonsocial;
                $aux_direccion = $despachoord->notaventa->cliente->direccion;
                $aux_contactonombre = $despachoord->notaventa->cliente->contactonombre;
                $aux_comunaNombre = $despachoord->notaventa->cliente->comuna->nombre;
                $aux_sucursalNombre = $despachoord->notaventa->sucursal->nombre;
                $aux_orddespnro = str_pad($despachoord->id, 10, "0", STR_PAD_LEFT);
                $datehoy = date('d/m/Y h:i:s A');
                $dateOrdDesp = date('d/m/Y h:i:s A', strtotime($despachoord->fechahora));
                $aux_vendedorNombre = $despachoord->notaventa->vendedor->persona->nombre . " " . $despachoord->notaventa->vendedor->persona->apellido;
                $aux_notaventa_id = str_pad($despachoord->notaventa_id, 10, "0", STR_PAD_LEFT);
                $aux_cotizacion_id = str_pad($despachoord->notaventa->cotizacion_id, 10, "0", STR_PAD_LEFT);
                $aux_oc_id = $despachoord->notaventa->oc_id;
                $aux_despachosol_id = str_pad($despachoord->despachosol_id, 10, "0", STR_PAD_LEFT);
                $aux_logo = asset("assets/lte/dist/img/LOGO-PLASTISERVI.png");
                $aux_despOrddet = 
                "
                <br>
                <span class='h3'>PEDIDO EN PREPARACION DE DESPACHO</span>
                <br>
                <div id='page_pdf'>
                    <table id='factura_head'>
                        <tr>
                            <td class='logo_factura'>
                                <div>
                                    <img src='$aux_logo' style='max-width:100%;width:auto;height:auto;'>
                                </div>
                            </td>
                            <td class='info_empresa'>
                            </td>
                            <td class='info_factura'>
                                <div>
                                    <span class='h3'>Orden de Despacho / $aux_sucursalNombre</span>
                                    <p><strong>Orden Despacho Nro:</strong>$aux_orddespnro</p>
                                    <p><strong>Fecha Actual:</strong> $datehoy</p>
                                    <p><strong>Fecha OrdDesp:</strong> $dateOrdDesp</p>
                                    <p><strong>Vendedor:</strong> $aux_vendedorNombre</p>
                                </div>
                            </td>
                        </tr>
                    </table>
                    <div style='width:100% !important;'>
                        <span class='h3'>Cliente</span>
                        <table class='info_cliente'>
                            <tr>
                                <td><label><strong>Rut:</strong></label></td><td><p id='rutform' name='rutform'><p>$aux_rut</p></td>
                                <td><label><strong>Teléfono:</strong></label></td><td><p>$aux_telefono</p></td>
                            </tr>
                            <tr>
                                <td><label><strong>Nombre:</strong></label></td><td><p>$aux_razonSocial</p></td>
                                <td><label><strong>Dirección:</strong></label></td><td><p>$aux_direccion</p></td>
                            </tr>
                            <tr>
                                <td><label><strong>Contacto:</strong></label></td><td><p>$aux_contactonombre</p></td>
                                <td><label><strong>Comuna:</strong></label></td><td>$aux_comunaNombre<p></p></td>
                            </tr>
                        </table>
                    </div>
                    <table id='factura_detalle'>
                        <thead>
                            <tr>
                                <th width='50px' style='text-align: center;'>Cod</th>
                                <th width='300px' style='text-align: left;'>Descripción</th>
                                <th width='50px' style='text-align: center;'>UN</th>
                                <th width='50px' style='text-align: center;' title='Cant. Nota de Venta: $aux_notaventa_id'>NV</th>
                                <!--<th width='50px' style='text-align: center;' title='Cant. Solicitud Despacho: $aux_despachosol_id'>SolDesp</th>
                                <th width='50px' style='text-align: center;' title='Cant. Orden Despacho Previo'>OrdDespPrev</th>-->
                                <th width='50px' style='text-align: center;' title='Cant. Orden Despacho: $aux_orddespnro'>OrdDesp</th>
                                <!--<th width='50px' style='text-align: center;' title='Cant. Saldo Nota Venta: $aux_notaventa_id'>Saldo NV</th>-->
                            </tr>
                        </thead>
                        <tbody id='detalle_productos'>";

                        foreach ($despachoord->despachoorddets as $despachoorddet) {

                            $aux_producto_id = $despachoorddet->notaventadetalle->producto_id;
                            $atributoProd = Producto::atributosProducto($aux_producto_id);
                            $aux_producto_nombre = $atributoProd["nombre"];
                            $aux_cantNV = number_format($despachoorddet->notaventadetalle->cant, 0, ",", ".");
                            $aux_cantSD = number_format($despachoorddet->despachosoldet->cantsoldesp, 0, ",", ".");
                            $aux_cantOD = number_format($despachoorddet->cantdesp, 0, ",", ".");
                            $aux_unimed = $despachoorddet->notaventadetalle->unidadmedida->nombre;

                            $aux_despOrddet .=
                                "<tr class='headt' style='height:150%;'>
                                    <td style='text-align: center;'>$aux_producto_id</td>
                                    <td class='textleft'>$aux_producto_nombre</td>
                                    <td style='text-align: center;'>$aux_unimed</td>
                                    <td style='text-align: center;'>$aux_cantNV</td>
                                    <td style='text-align: center;'>$aux_cantOD</td>
                                </tr>";
                        }
                        $aux_obs = "";
                        if (!is_null($despachoord->observacion)){
                            $aux_obs = $despachoord->observacion;
                        }
                        $aux_despOrddet .=
                        "</tbody>
                    </table>
                    <div class='round2'>
                        <p class='nota'><strong> <H3>Observaciones: $aux_obs</H3></strong></p>
                    </div>
                    <br>
                    <div style='width:40% !important;'>
                        <span class='h3'>Informacion</span>
                        <table id='info_factura'>
                            <tr>
                                <td colspan='7' class='textleft' width='40%'><span><strong>Orden de Compra: </strong></span></td>
                                <td class='textleft' width='50%'><span>$aux_oc_id</span></td>
                            </tr>
                            <tr>
                                <td colspan='7' class='textleft' width='40%'><span><strong>Cotizacion Nro: </strong></span></td>
                                <td class='textleft' width='50%'><span>$aux_cotizacion_id</span></td>
                            </tr>
                            <tr>
                                <td colspan='7' class='textleft' width='40%'><span><strong>Nota de Venta Nro: </strong></span></td>
                                <td class='textleft' width='50%'><span>$aux_notaventa_id</span></td>
                            </tr>
                            <tr>
                                <td colspan='7' class='textleft' width='40%'><span><strong>Solicitud Despacho: </strong></span></td>
                                <td class='textleft' width='50%'><span>$aux_despachosol_id</span></td>
                            </tr>
                        </table>
                    </div>
                </div>";


                Event(new Notificacion( //ENVIO ARRAY CON LOS DATOS PARA CREAR LA NOTIFICACION
                    [
                        'usuarioorigen_id' => auth()->id(),
                        'usuariodestino_id' => $aux_usuariodestino_id,
                        'vendedor_id' => $despachoord->notaventa->vendedor_id,
                        'status' => 1,
                        'nombretabla' => 'despachoord',
                        'mensaje' => 'Nueva Orden Despacho Nro:'.$despachoord->id,
                        'rutadestino' => 'notaventaconsulta',
                        'tabla_id' => $despachoord->id,
                        'accion' => 'Nueva Orden Despacho',
                        'mensajetitle' => 'OD:'.$despachoord->id.' NV:'.$despachoord->notaventa_id,
                        'icono' => 'fa fa-fw fa-male text-primary',
                        'detalle' => $aux_despOrddet
                    ]
                ));
                */
                return response()->json([
                                        'status' => '1',
                                        'title' => "",
                                        'mensaje' => "No existe modulo SOLDESP",
                                        'id' => $request->id,
                                        'nfila' => $request->nfila,
                                        'tipo_alert' => 'success'
                                    ]);
            } else {
                return response()->json([
                    'status' => '0',
                    'title' => "",
                    'mensaje' => "Error al guardar despachoord",
                    'id' => $request->id,
                    'nfila' => $request->nfila,
                    'tipo_alert' => 'error'
                ]);
            }
        } else {
            abort(404);
        }
    }

    public function reporte(Request $request){
        //$respuesta = reportesol($request);
        //return $respuesta;
    }

    public function exportPdf($id,$stareport = '1')
    {
        if(can('ver-pdf-orden-de-despacho',false)){
            $despachoord = DespachoOrd::findOrFail($id);
            //dd($despachoord);
            $despachoorddets = $despachoord->despachoorddets()->get();
            //dd($despachoorddets);
            $empresa = Empresa::orderBy('id')->get();
            $rut = number_format( substr ( $despachoord->notaventa->cliente->rut, 0 , -1 ) , 0, "", ".") . '-' . substr ( $despachoord->notaventa->cliente->rut, strlen($despachoord->notaventa->cliente->rut) -1 , 1 );
            $aux_staacutec = false;
            foreach ($despachoord->despachoorddets as $detalle) {
                if(isset($detalle->notaventadetalle->producto->acuerdotecnico)){
                    $aux_staacutec = true;
                    break;
                }
            }
            $aux_monedaLocal = true;
            $aux_modena_nombre = "";
            $aux_modena_desc = "";
            $aux_modena_simb = "";
            if($empresa[0]['moneda_id'] != $despachoord->notaventa->moneda_id){
                $aux_monedaLocal = false;
                $aux_modena_nombre = $despachoord->notaventa->moneda->nombre;
                $aux_modena_desc = $despachoord->notaventa->moneda->desc;
                $aux_modena_simb = $despachoord->notaventa->moneda->simbolo;
            }
            $datosArray = [
                "monedaLocal" => $aux_monedaLocal,
                "modena_nombre" => $aux_modena_nombre,
                "modena_desc" => $aux_modena_desc,
                "modena_simb" => $aux_modena_simb
            ];    
            //dd($empresa[0]['iva']);
            if($stareport == '1'){
                if(env('APP_DEBUG')){
                    if($aux_staacutec == false){
                        return view('despachoord.reporte', compact('despachoord','despachoorddets','empresa','datosArray'));
                    }else{
                        return view('despachoord.reporteat', compact('despachoord','despachoorddets','empresa','datosArray'));
                    }
                }
                if($aux_staacutec == false){
                    $pdf = PDF::loadView('despachoord.reporte', compact('despachoord','despachoorddets','empresa','datosArray'));
                }else{
                    $pdf = PDF::loadView('despachoord.reporteat', compact('despachoord','despachoorddets','empresa','datosArray'));
                }

                //return $pdf->download('cotizacion.pdf');
                return $pdf->stream(str_pad($despachoord->notaventa->id, 5, "0", STR_PAD_LEFT) .' - '. $despachoord->notaventa->cliente->razonsocial . '.pdf');
            }else{
                if($stareport == '2'){
                    return view('despachoord.listado1', compact('despachoord','despachoorddets','empresa','datosArray'));        
                    $pdf = PDF::loadView('despachoord.listado1', compact('despachoord','despachoorddets','empresa','datosArray'));
                    //return $pdf->download('cotizacion.pdf');
                    return $pdf->stream(str_pad($despachoord->notaventa->id, 5, "0", STR_PAD_LEFT) .' - '. $despachoord->notaventa->cliente->razonsocial . '.pdf');
                }
            }
        }else{
            //return false;            
            $pdf = PDF::loadView('generales.pdfmensajesinacceso');
            return $pdf->stream("mensajesinacceso.pdf");
        }
    }

    public function listarorddespxnv(Request $request){
        $respuesta = array();
		$respuesta['exito'] = false;
		$respuesta['mensaje'] = "Código no Existe";
		$respuesta['tabla'] = "";
        if($request->ajax()){
            $notaventa = NotaVenta::findOrFail($request->id);
            //dd($notaventa->despachoords);
            $tab1 = "
            <table id='tabladespachoord' name='tabladespachoord' class='table display AllDataTables table-hover table-condensed' data-page-length='10'>
            <thead>
                <tr>
                    <th>ID OD</th>
                    <th>Fecha</th>
                    <th style='text-align:left'>Razon Social</th>
                    <th class='textcenter'>Guia</th>
                    <th class='textcenter'>FecFact</th>
                    <th class='textcenter'>Nfact</th>
                    <th style='text-align:right'>Total</th>
                </tr>
            </thead>
            <tbody>";
            $i=0;

            foreach ($notaventa->despachoords as $despachoord) {
                //VALIDAR QUE NO ESTE ANULADA LA OD
                if(!isset($despachoord->despachoordanul)){
                    $aux_enlaceguia = "";
                    if(!is_null($despachoord->guiadespacho) and !empty($despachoord->guiadespacho)){
                        $tipotrasladoDesc = "";
                        $tipotrasladoLetra = "";
                        $enlaceDteOrigen = "";
                        $aux_numguia = $despachoord->guiadespacho;
                        //SI LA GUIA ES ORIGINADA DE OTRA GUIA
                        //ES DECIR QUE SE HIZO UNA GUIA PREVIA DE VENTA QUE SE ORIGINO DESDE UNA NOTA DE VENTA Y LUEGO SE DESPACHO CON UNA GUIA DE TRASLADO
                        //SE HACE UNA GUIA DE TRASLADO PORQUE SE SUPONE QUE YA SE HIZO PREVIAMENTE UNA GUIA DE VENTA
                        //$enlaceDteOrigen EN ESTA VARIABLE ALMACENO LA GUIA PREVIA
                        $sql = "SELECT dte.id,dte_rel_guia.nrodocto as guiaorigenprecio_nrodocto,dte.indtraslado,
                            dte_rel_guia.indtraslado as indtrasladoorigen
                            FROM dte 
                            LEFT JOIN dtedte as dtedte_rel_guia
                            ON dtedte_rel_guia.dte_id = dte.id AND isnull(dtedte_rel_guia.deleted_at)
                            LEFT JOIN dte as dte_rel_guia
                            ON dtedte_rel_guia.dter_id = dte_rel_guia.id AND (dte_rel_guia.foliocontrol_id = 2)  AND isnull(dte_rel_guia.deleted_at)
                            WHERE dte.nrodocto = $aux_numguia 
                            AND isnull(dte.deleted_at)
                            AND dte.id not in (SELECT dteanul.dte_id from dteanul WHERE  isnull(dteanul.deleted_at));";
                        $dte = DB::select($sql);
                        if(count($dte) > 0 and $dte[0]->guiaorigenprecio_nrodocto != null){
                            $guiaorigenprecio_nrodocto = $dte[0]->guiaorigenprecio_nrodocto;
                            $arrayDTEtipotraslado = dtetipotraslado($dte[0]->indtrasladoorigen);
                            $tipotrasladoDesc = $arrayDTEtipotraslado["desc"];
                            $enlaceDteOrigen = "<a class='btn-accion-tabla btn-sm tooltipsC' title='' data-original-title='Guia Despacho origen: $guiaorigenprecio_nrodocto $tipotrasladoDesc' onclick='genpdfGD($guiaorigenprecio_nrodocto,\"\",\"myModalTablaOD\")'>
                                <i class='fa fa-fw fa-question-circle text-aqua'></i>
                            </a>";
                        }
                        if(count($dte) > 0){
                            $arrayDTEtipotraslado = dtetipotraslado($dte[0]->indtraslado);
                            $tipotrasladoDesc = $arrayDTEtipotraslado["desc"];
                            $tipotrasladoLetra = $arrayDTEtipotraslado["letra"];
                            $aux_enlaceguia = "<a style='padding-left: 0px;' class='btn-accion-tabla btn-sm tooltipsC' title='' onclick='genpdfGD($aux_numguia,\"\",\"myModalTablaOD\")' data-original-title='Guia Despacho $tipotrasladoDesc'>
                                                $aux_numguia $tipotrasladoLetra $enlaceDteOrigen
                                                </a>";

                        }
                    }
                    $aux_enlacefactura = "";
                    $aux_totalFact = "";
                    if(!is_null($despachoord->numfactura) and !empty($despachoord->numfactura)){
                        $dte = Dte::where("nrodocto",$despachoord->numfactura)->get();
                        if(count($dte) > 0){
                            $dte = Dte::findOrFail($dte[0]->id);
                            $aux_numfac = $despachoord->numfactura;
                            $nrodocto_str = $dte->foliocontrol->nombrepdf . str_pad($dte->nrodocto, 8, "0", STR_PAD_LEFT);
                            $aux_enlacefactura = "<a style='padding-left: 0px;' class='btn-accion-tabla btn-sm tooltipsC' title='' onclick='genpdfFAC(\"$nrodocto_str\",\"\",\"myModalTablaOD\")' data-original-title='Factura'>
                                $dte->nrodocto
                            </a>";
                            $aux_totalFact = number_format($dte->mnttotal, 0, ",", ".");    
                        }
                    }
                    $aux_fechaFact = $despachoord->fechafactura;
                    $aux_fechaFact = $aux_fechaFact ? date('d/m/Y', strtotime($despachoord->fechafactura)) : "";

                    //**** */
                    //MOSTRAR LOS DATOS DE GUIA DE DESPACHO Y FACTURA ANTES DEL 01/09/2023 
                    //PORQUE NO ESTABA EL MODULO GUIAS DE DESPACHO Y FACTURACION
                    if($despachoord->guiadespachofec < "2023-09-01 00:00:00"){
                        if($despachoord->guiadespacho != "" and $despachoord->guiadespacho != null){
                            $aux_enlaceguia = $despachoord->guiadespacho;
                        }
                    }
                    if($despachoord->numfacturafec < "2023-09-01 00:00:00"){
                        if($despachoord->numfactura != "" and $despachoord->numfactura != null){
                            $aux_fechaFact = date('d/m/Y', strtotime($despachoord->fechafactura));
                            $aux_enlacefactura = $despachoord->numfactura;
                            $aux_totalFact = "";
                        }

                    }
                    //**** */

                    $tab1 .=
                        "<tr id='filaord$i' name='filaord$i'>
                        <td id='id$i' name='id$i'>
                            <a class='btn-accion-tabla btn-sm tooltipsC' title='Ver Orden de Despacho' onclick='genpdfOD($despachoord->id,1,\"myModalTablaOD\")'>
                                $despachoord->id
                            </a>                            
                        </td>
                        <td id='fechahoraord$i' name='fechahoraord$i'>" . date('d/m/Y', strtotime($despachoord->created_at)) . "</td>
                        <td style='text-align:left'>". $despachoord->notaventa->cliente->razonsocial ."</td>
                        <td class='textcenter'>" . $aux_enlaceguia ." </td>
                        <td class='textcenter'>" . $aux_fechaFact . "</td>
                        <td class='textcenter'>" . $aux_enlacefactura . "</td>
                        <td style='text-align:right'>". $aux_totalFact ."</td>
                    </tr>";
                }
            }

            $tab1 .= "
                </tbody>
            </table>";

            $despachoordanul = DespachoOrdAnul::select(['despachoord_id'])->get();
            $despchoords = DespachoOrd::orderBy('id')
                ->where('notaventa_id','=',$request->id)
                ->whereNotIn('id',  $despachoordanul)
                ->get();
/*
            $despchoords = DespachoOrd::orderBy('id')
                    ->where('notaventa_id','=',$request->id)
                    ->whereNotNull('numfactura')
                    ->whereNotIn('id',  $despachoordanul)
                    ->get();
*/
            $tab2 = "
            <table id='tabladespachoorddet' name='tabladespachoorddet' class='table display AllDataTables table-hover table-condensed' data-page-length='10'>
            <thead>
                <tr>
                    <th>ID OD</th>
                    <th>Fecha</th>
                    <th>CodProd</th>
                    <th style='text-align:right'>Solic</th>
                    <th style='text-align:right'>Entregado</th>
                    <th class='textcenter'>Unidad</th>
					<th class='textleft'>Descripción</th>
					<th class='textleft'>Diametro</th>
					<th class='textleft'>Clase</th>
					<th class='textright'>Largo</th>
                    <th class='textcenter'>TU</th>
                    <th class='textcenter'>Peso</th>
                    <th class='textcenter'>Guia</th>
                    <th class='textcenter'>FecFact</th>
                    <th class='textcenter'>Nfact</th>
                </tr>
            </thead>
            <tbody>";
            $i=0;
            $aux_totalcantsoldesp = 0;
            $aux_totalcantdesp = 0;
            foreach ($despchoords as $despchoord) {
                foreach ($despchoord->despachoorddets as $despachoorddet) {
                    //dd($despachoorddet);
                    //Si codigo producto es nulo muestra todo, de lo contrario solo muestra el producto igual a producto_id
                    if($request->producto_id != null){
                        if($request->producto_id != $despachoorddet->notaventadetalle->producto_id){
                            continue;
                        }
                    }
                    $i++;
                    $unidades = $despachoorddet->notaventadetalle->producto->categoriaprod->unidadmedidafact->nombre;
                    $nombreproduc = $despachoorddet->notaventadetalle->producto->nombre;
                    $diametro = $despachoorddet->notaventadetalle->producto->diametro;
                    /*
                    $diametro = $despachoorddet->notaventadetalle->producto->diamextpg;
                    if ($despachoorddet->notaventadetalle->producto->categoriaprod->unidadmedida_id != 3){
                        $diametro = $despachoorddet->notaventadetalle->producto->diamextmm . 'mm';
                    }
                    */
                    $cla_nombre = $despachoorddet->notaventadetalle->producto->claseprod->cla_nombre;
                    $long = $despachoorddet->notaventadetalle->producto->long;
                    $tipounion = $despachoorddet->notaventadetalle->producto->tipounion;
                    $cantsoldesp = $despachoorddet->despachosoldet->cantsoldesp;
                    $peso = $despachoorddet->notaventadetalle->peso;
                    $tablaOrdTrab= "";
                    $aux_botonMostrar = "";
                    if(count($despachoorddet->despachoordrecdets) > 0){
                        //dd($despachoorddet->despachoordrecdets);
                        $tablaOrdTrab .= "
                            <table class='table display AllDataTables table-hover table-condensed' data-page-length='10'>
                            <thead>
                                <tr>
                                    <th style='text-align:right' class='btn-accion-tabla btn-sm tooltipsC' title='Id Rechazo'>ID</th>
                                    <th style='text-align:right' class='btn-accion-tabla btn-sm tooltipsC' title='Cant Rechazada'>Cant</th>
                                </tr>
                            </thead>
                            <tbody>";
                            $aux_saldoentregado = $despachoorddet->cantdesp;
                            foreach ($despachoorddet->despachoordrecdets as $despachoordrecdet){
                                //dd($despachoordrecdet->cantrec);
                                $despachoordrec = $despachoordrecdet->despachoordrec;
                                $tablaOrdTrab .= "
                                    <tr>
                                        <td>
                                            <a class='btn-accion-tabla btn-sm tooltipsC' title='Ver Rechazo OD' onclick='genpdfODRec($despachoordrec->id,1,\"myModalTablaOD\")'>
                                                $despachoordrec->id
                                            </a>
                                        </td>
                                        <td style='text-align:right'>$despachoordrecdet->cantrec</td>
                                    </tr>";
                                $aux_saldoentregado -= $despachoordrecdet->cantrec;
                            }
                            $tablaOrdTrab .= "
                            </tbody>
                                <tfoot>
                                    <tr>
                                        <th style='text-align:right'></th>
                                        <th style='text-align:right' class='btn-accion-tabla btn-sm tooltipsC' title='Entregado' >". number_format($aux_saldoentregado, 0, ",", ".") ."</th>
                                    </tr>
                                </tfoot>        
                            </table>";
                            $aux_botonMostrar = "
                                <a class='btn-accion-tabla btn-sm tooltipsC' title='Rechazo' onclick='mostrarH($i,\"botonD\",\"divTabOT\")'>
                                    <i name='botonD$i' id='botonD$i' class='fa fa-fw fa-caret-down'></i>
                                </a>";
                    }
//                    $tablaOrdTrab= "";
                    $tab2 .= "
                    <tr id='fila$i' name='fila$i'>
                        <td id='id$i' name='id$i'>
                            <a class='btn-accion-tabla btn-sm tooltipsC' title='Ver Orden de Despacho' onclick='genpdfOD($despachoorddet->despachoord_id,1,\"myModalTablaOD\")'>
                                $despachoorddet->despachoord_id
                            </a>                            
                        </td>
                        <td id='fechahora$i' name='fechahora$i'>" . date('d/m/Y', strtotime($despachoorddet->created_at)) . "</td>
                        <td style='text-align:center'>". $despachoorddet->notaventadetalle->producto_id ."</td>
                        <td style='text-align:right'>". number_format($cantsoldesp, 0, ",", ".") ."</td>
                        <td style='text-align:right'>" . $aux_botonMostrar . number_format($despachoorddet->cantdesp, 0, ",", ".") .
                            "<div id='divTabOT$i' style='display:none;'>
                                    $tablaOrdTrab
                            </div>
                        </td>
                        <td class='textcenter'>$unidades</td>
						<td class='textleft'>$nombreproduc</td>
                        <td class='textleft'>$diametro</td>
                        <td class='textleft'>$cla_nombre</td>
						<td class='textright'>$long mts</td>
                        <td class='textcenter'>$tipounion</td>
                        <td class='textcenter'>$peso</td>
                        <td class='textcenter'>" . $despachoorddet->despachoord->guiadespacho ." </td>
                        <td class='textcenter'>" . date('d/m/Y', strtotime($despachoorddet->despachoord->fechafactura)) . "</td>
                        <td class='textcenter'>" . $despachoorddet->despachoord->numfactura . "</td>    
                    </tr>";

                    $respuesta['exito'] = true;
                    $aux_totalcantsoldesp += $cantsoldesp;
                    $aux_totalcantdesp += $despachoorddet->cantdesp;
                }
            }
            $tab2 .= "
            </tbody>
            <tfoot>
                <tr>
                    <th colspan='2' style='text-align:left'>TOTALES</th>
                    <th style='text-align:right'></th>
                    <th style='text-align:right'></th>
                    <th style='text-align:right'>". number_format($aux_totalcantdesp, 0, ",", ".") ."</th>
                    <th colspan='10' style='text-align:right'></th>
                </tr>
            </tfoot>
            </table>";

            $respuesta['tabla'] .= "
            <div class='nav-tabs-custom' id='tabs'>
                <ul class='nav nav-tabs'>
                    <li class='active'><a href='#tab_1' data-toggle='tab'  id='tab1' name='tab1'>Orden de despacho</a></li>
                    <li><a href='#tab_2' data-toggle='tab' id='tab2' name='tab2'>Detalle Orden Despacho</a></li>
                </ul>
                <div class='tab-content'>
                <div class='tab-pane active' id='tab_1'>
                    $tab1
                </div>
                <div class='tab-pane' id='tab_2'>
                    $tab2
                </div>
            </div>";

        }
        return $respuesta;
    }

    public function buscarguiadesp(Request $request)
    {
        if ($request->ajax()) {
            $despachoord = DespachoOrd::where('guiadespacho' ,'=',$request->guiadespacho)->get();
            if(count($despachoord) > 0){
                return response()->json(['mensaje' => 'ok',
                'Mensaje' => 'Encontrado'
               ]);
            }else{
                return response()->json(['mensaje' => 'no',
                'Mensaje' => 'No existe.'
               ]);
            }
        }
    }

    public function totalizarindex(){
        $respuesta = array();
        $datas = consultaindex();
        $aux_totalkg = 0;
        //$aux_totaldinero = 0;
        foreach ($datas as $data) {
            $aux_totalkg += $data->aux_totalkg;
            //$aux_totaldinero += $data->subtotal;
        }
        $respuesta['aux_totalkg'] = $aux_totalkg;
        //$respuesta['aux_totaldinero'] = $aux_totaldinero;
        return $respuesta;
    }

    public function listarsoldesp() //Listar solicitudes de despacho
    {
        can('listar-orden-despacho');

        $giros = Giro::orderBy('id')->get();
        $areaproduccions = AreaProduccion::orderBy('id')->get();
        $tipoentregas = TipoEntrega::orderBy('id')->get();
        $fechaAct = date("d/m/Y");
        $tablashtml['comunas'] = Comuna::selectcomunas();
        $tablashtml['vendedores'] = Vendedor::selectvendedores();
        $tablashtml['vendedores'] = Vendedor::selectvendedores();
        $user = Usuario::findOrFail(auth()->id());
        $tablashtml['sucurArray'] = $user->sucursales->pluck('id')->toArray(); //$clientesArray['sucurArray'];
        $tablashtml['sucursales'] = Sucursal::orderBy('id')->whereIn('sucursal.id', $tablashtml['sucurArray'])->get();
        $tablashtml['sololectura'] = 0;
        $tablashtml['solenvord'] = 0; //ESTATUS SOLICITUD DESPACHO ENVIADA A ORD DESPACHO 
        $empresa = Empresa::findOrFail(1);
        $tablashtml['stabloxdeusiscob'] = $empresa->stabloxdeusiscob;
        return view('despachoord.listardespachosol', compact('giros','areaproduccions','tipoentregas','fechaAct','tablashtml'));
    }

    public function listarsoldespsolenvord() //Listar solicitudes de despacho
    {
        can('listar-orden-despacho');

        $giros = Giro::orderBy('id')->get();
        $areaproduccions = AreaProduccion::orderBy('id')->get();
        $tipoentregas = TipoEntrega::orderBy('id')->get();
        $fechaAct = date("d/m/Y");
        $tablashtml['comunas'] = Comuna::selectcomunas();
        $tablashtml['vendedores'] = Vendedor::selectvendedores();
        $tablashtml['vendedores'] = Vendedor::selectvendedores();
        $user = Usuario::findOrFail(auth()->id());
        $tablashtml['sucurArray'] = $user->sucursales->pluck('id')->toArray(); //$clientesArray['sucurArray'];
        $tablashtml['sucursales'] = Sucursal::orderBy('id')->whereIn('sucursal.id', $tablashtml['sucurArray'])->get();
        $tablashtml['sololectura'] = 0;
        $tablashtml['solenvord'] = 1; //ESTATUS SOLICITUD DESPACHO ENVIADA A ORD DESPACHO 
        $empresa = Empresa::findOrFail(1);
        $tablashtml['stabloxdeusiscob'] = $empresa->stabloxdeusiscob;
        return view('despachoord.listardespachosol', compact('giros','areaproduccions','tipoentregas','fechaAct','tablashtml'));
    }

    public function delsolenvord(Request $request)
    {
        //dd($request);
        $despachosol = DespachoSol::findOrFail($request->despachosol_id);
        if($despachosol->updated_at != $request->updated_at){
            return [
                'error' => 1,
                'mensaje' => 'No se pudo procesar. Registro Editado por otro usuario. Fecha Hora: '.$despachosol->updated_at,
                'tipo_alert' => 'error'
            ];
        }

        $error = 1;
        $mensaje = 'Error al procesar registro.';
        $tipo_alert = 'error';

        $despachosol->updated_at = date("Y-m-d H:i:s");
        if(isset($despachosol->despachosolenvorddesp)){
            $despachosol->despachosolenvorddesp->staenvdesp = 0;
            $despachosol->despachosolenvorddesp->obs = $request->obs;
            if($despachosol->despachosolenvorddesp->save()){
                $error = 0;
                $mensaje = 'Procesado con exito.';
                $tipo_alert = 'success';
            }
        }
        /* if (DespachoSolEnvOrdDesp::delsolenvord($despachosol)) {
            $error = 0;
            $mensaje = 'Procesado con exito.';
            $tipo_alert = 'success';
        }else{
            $error = 1;
            $mensaje = 'Error al procesar registro.';
            $tipo_alert = 'error';
        } */

        return [
            'error' => $error,
            'mensaje' => $mensaje,
            'tipo_alert' => $tipo_alert
        ];
    }

}


function consulta($request){
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
        $vendedorcond = "notaventa.vendedor_id='$request->vendedor_id'";
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
        $aux_condtipoentrega_id = "notaventa.tipoentrega_id='$request->tipoentrega_id'";
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

    if(empty($request->comuna_id)){
        $aux_condcomuna_id = " true";
    }else{
        $aux_condcomuna_id = "notaventa.comunaentrega_id='$request->comuna_id'";
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

    //$suma = DespachoSol::findOrFail(2)->despachosoldets->where('notaventadetalle_id',1);

    $sql = "SELECT despachosol.id,despachosol.fechahora,cliente.rut,cliente.razonsocial,notaventa.oc_id,notaventa.oc_file,
            comuna.nombre as comunanombre,
            despachosol.notaventa_id,despachosol.fechaestdesp,
            sum(despachosoldet.cantsoldesp * (notaventadetalle.totalkilos / notaventadetalle.cant)) AS totalkilos
            FROM despachosol INNER JOIN despachosoldet
            ON despachosol.id=despachosoldet.despachosol_id
            AND if((SELECT cantdesp
                    FROM vista_sumorddespdet
                    WHERE despachosoldet_id=despachosoldet.id
                    ) >= despachosoldet.cantsoldesp,FALSE,TRUE)
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
            and despachosol.deleted_at is null AND notaventa.deleted_at is null AND notaventadetalle.deleted_at is null
            GROUP BY despachosol.id;";
/*
(select sum(cantsoldesp) as cantsoldesp
                    from despachosol inner join despachosoldet
                    on despachosol.id=despachosoldet.despachosol_id
                    where despachosol.id not in (select despachosol_id from despachosolanul)
                    and despachosoldet.notaventadetalle_id=notaventadetalle.id
                    despachosol.deleted_at is null
                    group by notaventadetalle_id)
*/
    //dd("$sql");
    $datas = DB::select($sql);
    //dd($datas);
    return $datas;
}


function consultaindex(){

    $user = Usuario::findOrFail(auth()->id());
    $sucurArray = $user->sucursales->pluck('id')->toArray();
    $sucurcadena = implode(",", $sucurArray);
    $arraySucFisxUsu = implode(",", sucFisXUsu($user->persona));

    $sql = "SELECT despachoord.id,despachoord.despachosol_id,despachoord.fechahora,despachoord.fechaestdesp,
    notaventa.cliente_id,cliente.razonsocial,notaventa.oc_id,notaventa.oc_file,despachoord.notaventa_id,
    '' as notaventaxk,comuna.nombre as comuna_nombre, sucursal.nombre as sucursal_nombre,
    tipoentrega.nombre as tipoentrega_nombre,tipoentrega.icono,clientebloqueado.descripcion as clientebloqueado_descripcion,
    SUM(despachoorddet.cantdesp * (notaventadetalle.totalkilos / notaventadetalle.cant)) as aux_totalkg,
    (SELECT CONCAT(dte.nrodocto,';',oc_id,';',oc_folder,'/',oc_file) as nrodocto
    FROM dteoc INNER JOIN dte
    ON dteoc.dte_id = dte.id AND ISNULL(dteoc.deleted_at) AND ISNULL(dte.deleted_at)
    INNER JOIN dteguiadesp
    ON dteoc.dte_id = dteguiadesp.dte_id AND ISNULL(dteguiadesp.deleted_at)
    WHERE dteoc.oc_id = notaventa.oc_id
    AND isnull(dteguiadesp.notaventa_id)
    AND dte.cliente_id= notaventa.cliente_id) as dte_nrodocto,
    despachoord.updated_at,
    clientebloqueado.descripcion as clientebloqueado_desc,
    cliente.limitecredito,
    IFNULL(datacobranza.tfac,0) AS datacobranza_tfac,
    IFNULL(datacobranza.tdeuda,0) AS datacobranza_tdeuda,
    IFNULL(datacobranza.tdeudafec,0) AS datacobranza_tdeudafec,
    IFNULL(datacobranza.nrofacdeu,'') AS datacobranza_nrofacdeu,
    modulo.stanvdc as modulo_stanvdc,clientedesbloqueadomodulo.modulo_id
    FROM despachoord INNER JOIN notaventa
    ON despachoord.notaventa_id = notaventa.id AND ISNULL(despachoord.deleted_at) and isnull(notaventa.deleted_at)
    INNER JOIN cliente
    ON cliente.id = notaventa.cliente_id AND isnull(cliente.deleted_at)
    INNER JOIN comuna
    ON comuna.id = despachoord.comunaentrega_id AND isnull(comuna.deleted_at)
    INNER JOIN despachoorddet
    ON despachoorddet.despachoord_id = despachoord.id AND ISNULL(despachoorddet.deleted_at)
    INNER JOIN notaventadetalle
    ON notaventadetalle.id = despachoorddet.notaventadetalle_id AND ISNULL(notaventadetalle.deleted_at)
    INNER JOIN tipoentrega
    ON tipoentrega.id = despachoord.tipoentrega_id AND ISNULL(tipoentrega.deleted_at)
    LEFT JOIN clientebloqueado
    ON clientebloqueado.cliente_id = notaventa.cliente_id AND ISNULL(clientebloqueado.deleted_at)
    INNER JOIN despachosol
    ON despachoord.despachosol_id = despachosol.id AND ISNULL(despachosol.deleted_at)
    INNER JOIN sucursal
    ON despachosol.sucursal_id = sucursal.id AND ISNULL(sucursal.deleted_at)
    INNER JOIN producto
    ON producto.id = notaventadetalle.producto_id AND ISNULL(producto.deleted_at)
    INNER JOIN categoriaprod
    ON categoriaprod.id=producto.categoriaprod_id AND ISNULL(categoriaprod.deleted_at)
    LEFT JOIN datacobranza
    ON datacobranza.cliente_id = notaventa.cliente_id
    LEFT JOIN clientedesbloqueado
    ON clientedesbloqueado.cliente_id = notaventa.cliente_id and clientedesbloqueado.notaventa_id = notaventa.id and not isnull(clientedesbloqueado.notaventa_id) and isnull(clientedesbloqueado.deleted_at)
    LEFT JOIN clientedesbloqueadomodulo
    ON clientedesbloqueadomodulo.clientedesbloqueado_id = clientedesbloqueado.id and clientedesbloqueadomodulo.modulo_id = 8
    LEFT JOIN modulo
    ON modulo.id = clientedesbloqueadomodulo.modulo_id
    WHERE categoriaprod.id in (SELECT categoriaprodsuc.categoriaprod_id 
        FROM categoriaprodsuc 
        WHERE categoriaprodsuc.categoriaprod_id = categoriaprod.id
        AND categoriaprodsuc.sucursal_id IN ($arraySucFisxUsu))
    AND ISNULL(despachoord.aprguiadesp)
    AND despachoord.id NOT IN (SELECT despachoordanul.despachoord_id FROM despachoordanul WHERE ISNULL(despachoordanul.deleted_at))
    AND despachoord.notaventa_id NOT IN (SELECT notaventacerrada.notaventa_id FROM notaventacerrada WHERE ISNULL(notaventacerrada.deleted_at))
    AND despachosol.sucursal_id in ($sucurcadena)
    GROUP BY despachoorddet.despachoord_id;";

    return DB::select($sql);

}

function updatenumguia($despachoord,$request){
    $despachoord->guiadespacho = $request->guiadespacho;
    $despachoord->guiadespachofec = date("Y-m-d H:i:s");
    if ($despachoord->save()) {
        Event(new GuardarGuiaDespacho($despachoord));
        return response()->json([
                                'mensaje' => 'ok',
                                'despachoord' => $despachoord,
                                'guiadespachofec' => date("Y-m-d", strtotime($despachoord->guiadespachofec)),
                                'status' => '1',
                                'id' => $request->id,
                                'nfila' => $request->nfila,
                                ]);
    } else {
        return response()->json([
            'status' => '0',
            'mensaje' => 'Error al guardar despachoord.'
        ]);
    }

}

function llenarArrayBodegasPickingSolDesp($detalles){
    $arrayBodegasPicking = [];
    foreach ($detalles as $detalle) {
        //dd($detalle);
        foreach ($detalle->despachosoldet_invbodegaproductos as $despachosoldet_invbodegaproducto){
            $aux_stock = 0;
            //dd($despachosoldet_invbodegaproducto->invmovdet_bodsoldesps);
            foreach ($despachosoldet_invbodegaproducto->invmovdet_bodsoldesps as $invmovdet_bodsoldesp){
                $aux_stock += $invmovdet_bodsoldesp->invmovdet["cant"];
            }
            //dd($aux_stock);
            //dd($detalle->despachoorddets);
            foreach($detalle->despachoorddets as $despachoorddet){
                //dd($despachoorddet);
                if($despachoorddet->id == 21365){
                    //dd($despachoorddet);
                }
                if($despachoorddet->despachoord->despachoordanul == null){
                    //dd($despachoorddet->despachoorddet_invbodegaproductos);
                    foreach($despachoorddet->despachoorddet_invbodegaproductos as $despachoorddet_invbodegaproducto){                        
                        //if($despachoorddet_invbodegaproducto->invbodegaproducto->invbodega->tipo == 1){
                            if($despachoorddet_invbodegaproducto->despachoorddet_id == 21365){
                                //dd($despachoorddet_invbodegaproducto->invmovdet_bodorddesps);
                            }

                            foreach($despachoorddet_invbodegaproducto->invmovdet_bodorddesps as $invmovdet_bodorddesp){
                                
                                //dd($invmovdet_bodorddesp->invmovdet);
                                if($invmovdet_bodorddesp->invmovdet->invbodegaproducto->invbodega->tipo == 1){
                                    if($invmovdet_bodorddesp->id == 15889){
                                        //dd($invmovdet_bodorddesp->invmovdet->cant);
                                        //dd($invmovdet_bodorddesp->invmovdet->invbodegaproducto->invbodega->tipo);
                                        //dd($invmovdet_bodorddesp);
                                    }
    
                                    //dd($invmovdet_bodorddesp->invmovdet);
                                    $aux_stock += $invmovdet_bodorddesp->invmovdet->cant;
                                    //dd($aux_stock1);
                                }

                            }
                            //dd($aux_stock1);
                            /*
                            if(($despachoorddet_invbodegaproducto->cant * -1) > 0){
                                $aux_stock -= $despachoorddet_invbodegaproducto->cant * -1;
                            }*/
                        //}
                    }
                    //dd($aux_stock1);
                }
                //dd($aux_stock);
/*
                if($despachoorddet->despachoord->despachoordanul == null and $despachoorddet->despachoord->aprguiadesp != 1){
                    foreach($despachoorddet->despachoorddet_invbodegaproductos as $despachoorddet_invbodegaproducto){
                        if($despachosoldet_invbodegaproducto->invbodegaproducto_id == $despachoorddet_invbodegaproducto->invbodegaproducto_id){
                            $aux_stock -= $despachoorddet_invbodegaproducto->cant *-1;
                        }

                        //$aux_stock -= $despachoorddet_invbodegaproducto->cant *-1;
                    }
                }*/
            }
            //dd($aux_stock);
            $sucursal = $despachosoldet_invbodegaproducto->invbodegaproducto->invbodega->sucursal;
            $producto = $despachosoldet_invbodegaproducto->invbodegaproducto->producto;
            $invbodegaproducto = $despachosoldet_invbodegaproducto->invbodegaproducto;
            $invbodega = InvBodega::where("sucursal_id","=",$sucursal->id)
                        ->where("tipo","=",1)
                        ->whereNull('deleted_at')
                        ->get();
            if(count($invbodega) == 0){
                return redirect('despachoord/listarsoldesp')->with([
                    'mensaje' => 'Sucursal ' . $sucursal->nombre . ", no tiene bodega picking. Debe crear una.",
                    'tipo_alert' => 'alert-error'
                ]);    
            }
            if(count($invbodega) > 1){
                return redirect('despachoord/listarsoldesp')->with([
                    'mensaje'=> "Sucursal " . $sucursal->nombre . ", tiene " . strval(count($invbodega)) . " bodegas de picking, solo debe tener 1.",
                    'tipo_alert' => 'alert-error'
                ]);
            }
            $invbodegaproductopicking = InvBodegaProducto::where("producto_id","=",$producto->id)
                                ->where("invbodega_id","=",$invbodega[0]->id)
                                ->whereNull('deleted_at')
                                ->get();
            if(count($invbodegaproductopicking) == 0){
                return redirect('despachoord/listarsoldesp')->with([
                    'mensaje'=> "Falta crear item o registro en tabla invbodegaproducto. Producto: " . $producto->id . " " . $producto->nombre . " Bodega: " . $invbodega[0]->nombre . " Sucursal: " . $sucursal->nombre,
                    'tipo_alert' => 'alert-error'
                ]);
            }
            if(count($invbodegaproductopicking) > 1){
                return redirect('despachoord/listarsoldesp')->with([
                    'mensaje'=> "Se debe eliminar 1 registro. Existen " . strval(count($invbodegaproductopicking)) . " registros en tabla invbodegaproducto. Solo debe existir 1. Producto: " . $producto->id . " " . $producto->nombre . " Bodega: " . $invbodega[0]->nombre . " Sucursal: " . $sucursal->nombre,
                    'tipo_alert' => 'alert-error'
                ]);
            }
            $arrayBodegasPicking[($invbodegaproductopicking[0]->id . "-". $detalle->id)] = [
                "invbodegaproducto_id" => $invbodegaproductopicking[0]->id,
                "producto_id" => $invbodegaproductopicking[0]->producto_id,
                "invbodega_id" => $invbodegaproductopicking[0]->invbodega_id,
                "sucursal_id" => $sucursal->id,
                "stock" => $aux_stock
            ];
        }
    }
    //dd($arrayBodegasPicking);
    return $arrayBodegasPicking;
}

function llenarArrayBodegasPickingOrdDesp($detalles){
    $arrayBodegasPicking = [];
    foreach ($detalles as $detalle) {
        $despachosoldet = DespachoSolDet::findOrFail($detalle->despachosoldet_id);
        foreach ($despachosoldet->despachosoldet_invbodegaproductos as $despachosoldet_invbodegaproducto){
            $aux_stock = 0;
            foreach ($despachosoldet_invbodegaproducto->invmovdet_bodsoldesps as $invmovdet_bodsoldesp){
                $aux_stock += $invmovdet_bodsoldesp->invmovdet["cant"];
            }
            //dd($aux_stock);
            foreach($detalle->despachosoldet->despachoorddets as $despachoorddet){
                if($despachoorddet->despachoord->despachoordanul == null){
                    foreach($despachoorddet->despachoorddet_invbodegaproductos as $despachoorddet_invbodegaproducto){
                        if($detalle->id != $despachoorddet->id){
                            foreach($despachoorddet_invbodegaproducto->invmovdet_bodorddesps as $invmovdet_bodorddesp){
                                if($invmovdet_bodorddesp->invmovdet->invbodegaproducto->invbodega->tipo == 1){
                                    $aux_stock += $invmovdet_bodorddesp->invmovdet->cant;
                                }
                            }
/*
                            if($despachoorddet_invbodegaproducto->invbodegaproducto->invbodega->tipo == 1){
                                $aux_stock -= ($despachoorddet_invbodegaproducto->cant) * -1 ;
                            }
                        */
                        }
                        //$aux_stock += $despachoorddet_invbodegaproducto->cantdesp * -1;
                    }
                }
            }
            $sucursal = $despachosoldet_invbodegaproducto->invbodegaproducto->invbodega->sucursal;
            $producto = $despachosoldet_invbodegaproducto->invbodegaproducto->producto;
            $invbodegaproducto = $despachosoldet_invbodegaproducto->invbodegaproducto;
            $invbodega = InvBodega::where("sucursal_id","=",$sucursal->id)
                        ->where("tipo","=",1)
                        ->whereNull('deleted_at')
                        ->get();
            if(count($invbodega) == 0){
                return redirect('despachoord/listarsoldesp')->with([
                    'mensaje' => 'Sucursal ' . $sucursal->nombre . ", no tiene bodega picking. Debe crear una.",
                    'tipo_alert' => 'alert-error'
                ]);    
            }
            if(count($invbodega) > 1){
                return redirect('despachoord/listarsoldesp')->with([
                    'mensaje'=> "Sucursal " . $sucursal->nombre . ", tiene " . strval(count($invbodega)) . " bodegas de picking, solo debe tener 1.",
                    'tipo_alert' => 'alert-error'
                ]);
            }
            $invbodegaproductopicking = InvBodegaProducto::where("producto_id","=",$producto->id)
                                ->where("invbodega_id","=",$invbodega[0]->id)
                                ->whereNull('deleted_at')
                                ->get();
            if(count($invbodegaproductopicking) == 0){
                return redirect('despachoord/listarsoldesp')->with([
                    'mensaje'=> "Falta crear item o registro en tabla invbodegaproducto. Producto: " . $producto->id . " " . $producto->nombre . " Bodega: " . $invbodega[0]->nombre . " Sucursal: " . $sucursal->nombre,
                    'tipo_alert' => 'alert-error'
                ]);
            }
            if(count($invbodegaproductopicking) > 1){
                return redirect('despachoord/listarsoldesp')->with([
                    'mensaje'=> "Se debe eliminar 1 registro. Existen " . strval(count($invbodegaproductopicking)) . " registros en tabla invbodegaproducto. Solo debe existir 1. Producto: " . $producto->id . " " . $producto->nombre . " Bodega: " . $invbodega[0]->nombre . " Sucursal: " . $sucursal->nombre,
                    'tipo_alert' => 'alert-error'
                ]);
            }
            $arrayBodegasPicking[$invbodegaproductopicking[0]->id . "-". $detalle->id] = [
                "invbodegaproducto_id" => $invbodegaproductopicking[0]->id,
                "producto_id" => $invbodegaproductopicking[0]->producto_id,
                "invbodega_id" => $invbodegaproductopicking[0]->invbodega_id,
                "sucursal_id" => $sucursal->id,
                "stock" => $aux_stock
            ];
        }
    }
    //dd($arrayBodegasPicking);
    return $arrayBodegasPicking;
}

function validarSiExisteBodega($despachoord,$invmoduloBod){
    //ANTES DE PROCESAR LA ORDEN VALIDO QUE LOS PRODUCTOS INVOLUCRADOS TENGAN BODEGA DE DESPACHO CORRESPONDIENTE A LA SUCURSAL DE CADA PRODUCTO
    //ESTO DEBE IR EN EL PROYECTO FINAL
    foreach ($despachoord->despachoorddets as $despachoorddet) {
        foreach ($despachoorddet->despachoorddet_invbodegaproductos as $oddetbodprod) {
            $aux_sucursal_id_producto = $oddetbodprod->invbodegaproducto->invbodega->sucursal_id; 
            $aux_bodegadespacho_id = 0;
            foreach($invmoduloBod->invmovmodulobodents as $invmovmodulobodent){
                //BUSCAR BODEGA DESPACHO CORRESPONDIENTE AL PRODUCTO QUE SE ESTA PROCESANDO DEPENDIENDO DE LA SUCURSAL QUE CORRESPONDE EL PRODUCTO
                if($invmovmodulobodent->sucursal_id == $aux_sucursal_id_producto){
                    $aux_bodegadespacho_id = $invmovmodulobodent->id;
                }
            }
            if($aux_bodegadespacho_id == 0){
                return response()->json([
                    'mensaje' => 'No existe Bodega Despacho en Sucursal: ' . $despachoord->notaventa->sucursal->nombre
                ]);
            }
        }
    }
    //ANTES DE PROCESAR LA ORDEN VALIDO QUE LOS PRODUCTOS INVOLUCRADOS TENGAN BODEGA DE DESPACHO CORRESPONDIENTE A LA SUCURSAL DE CADA PRODUCTO
    //ESTO DEBE IR EN EL PROYECTO FINAL
}

function consultaindexSuc($request){

    $user = Usuario::findOrFail(auth()->id());
    $sucurArray = $user->sucursales->pluck('id')->toArray();
    $sucurcadena = implode(",", $sucurArray);


    $sql = "SELECT despachoord.id,despachoord.despachosol_id,despachoord.fechahora,despachoord.fechaestdesp,
    cliente.razonsocial,notaventa.oc_id,notaventa.oc_file,despachoord.notaventa_id,
    '' as notaventaxk,comuna.nombre as comuna_nombre,
    tipoentrega.nombre as tipoentrega_nombre,tipoentrega.icono,clientebloqueado.descripcion as clientebloqueado_descripcion,
    SUM(despachoorddet.cantdesp * (notaventadetalle.totalkilos / notaventadetalle.cant)) as aux_totalkg,
    despachoord.updated_at
    FROM despachoord INNER JOIN notaventa
    ON despachoord.notaventa_id = notaventa.id AND ISNULL(despachoord.deleted_at) and isnull(notaventa.deleted_at)
    INNER JOIN cliente
    ON cliente.id = notaventa.cliente_id AND isnull(cliente.deleted_at)
    INNER JOIN comuna
    ON comuna.id = despachoord.comunaentrega_id AND isnull(comuna.deleted_at)
    INNER JOIN despachoorddet
    ON despachoorddet.despachoord_id = despachoord.id AND ISNULL(despachoorddet.deleted_at)
    INNER JOIN notaventadetalle
    ON notaventadetalle.id = despachoorddet.notaventadetalle_id AND ISNULL(notaventadetalle.deleted_at)
    INNER JOIN tipoentrega
    ON tipoentrega.id = despachoord.tipoentrega_id AND ISNULL(tipoentrega.deleted_at)
    LEFT JOIN clientebloqueado
    ON clientebloqueado.cliente_id = notaventa.cliente_id AND ISNULL(clientebloqueado.deleted_at)
    INNER JOIN sucursal
    ON notaventa.sucursal_id in $sucurcadena
    WHERE ISNULL(despachoord.aprguiadesp)
    AND despachoord.id NOT IN (SELECT despachoordanul.despachoord_id FROM despachoordanul WHERE ISNULL(despachoordanul.deleted_at))
    AND despachoord.notaventa_id NOT IN (SELECT notaventacerrada.notaventa_id FROM notaventacerrada WHERE ISNULL(notaventacerrada.deleted_at))
    AND notaventa.sucursal_id = $request->sucursal_id
    GROUP BY despachoorddet.despachoord_id;";

    return DB::select($sql);

}