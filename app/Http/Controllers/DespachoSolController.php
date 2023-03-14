<?php

namespace App\Http\Controllers;

use App\Events\CerrarSolDesp;
use App\Events\DevolverSolDesp;
use App\Events\Notificacion;
use App\Http\Requests\ValidarDespachoSol;
use App\Models\AreaProduccion;
use App\Models\CategoriaProd;
use App\Models\Cliente;
use App\Models\ClienteBloqueado;
use App\Models\ClienteSucursal;
use App\Models\ClienteVendedor;
use App\Models\Comuna;
use App\Models\DespachoOrd;
use App\Models\DespachoOrd_InvMov;
use App\Models\DespachoOrdAnul;
use App\Models\DespachoSol;
use App\Models\DespachoSol_InvMov;
use App\Models\DespachoSolAnul;
use App\Models\DespachoSolDet;
use App\Models\DespachoSolDet_InvBodegaProducto;
use App\Models\Empresa;
use App\Models\FormaPago;
use App\Models\Giro;
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
use App\Models\TipoEntrega;
use App\Models\Vendedor;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DespachoSolController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-solicitud-despacho');
        /*
        $despachosolanul = DespachoSolAnul::orderBy('id')->pluck('despachosol_id')->toArray();
        $notaventacerradaArray = NotaVentaCerrada::pluck('notaventa_id')->toArray();
        //dd($notaventacerradaArray);
        //dd($notaventacerrada);
        $datas = DespachoSol::orderBy('id')
                ->whereNull('aprorddesp')
                ->whereNotIn('id', $despachosolanul)
                ->whereNotIn('notaventa_id', $notaventacerradaArray)
                ->get();
        */
//        return view('despachosol.index', compact('datas'));
        return view('despachosol.index');
    }

    public function despachosolpage(){
        $datas = consultaindex();
        return datatables($datas)->toJson();
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
        $datas = Cliente::Temp();
        return datatables($datas)->toJson();
    }


    public function listarnv()
    {
        $arrayvend = Vendedor::vendedores(); //Viene del modelo vendedores
        $vendedores1 = $arrayvend['vendedores'];

        $vendedores = Vendedor::orderBy('id')->where('sta_activo',1)->get();

        $giros = Giro::orderBy('id')->get();
        $areaproduccions = AreaProduccion::orderBy('id')->get();
        $tipoentregas = TipoEntrega::orderBy('id')->get();
        $comunas = Comuna::orderBy('id')->get();
        $fechaAct = date("d/m/Y");
        $tablashtml['comunas'] = Comuna::selectcomunas();
        $tablashtml['vendedores'] = Vendedor::selectvendedores();
        return view('despachosol.listarnotaventa', compact('giros','areaproduccions','tipoentregas','fechaAct','tablashtml'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function crear($id)
    {
    }

    public function crearsol($id)
    {
        can('crear-solicitud-despacho');
        $data = NotaVenta::findOrFail($id);
        $data->plazoentrega = $newDate = date("d/m/Y", strtotime($data->plazoentrega));
        $detalles = $data->notaventadetalles()->get();
        $vendedor_id=$data->vendedor_id;
        $clienteselec = $data->cliente()->get();

        $clientesArray = Cliente::clientesxUsuario();
        $clientes = $clientesArray['clientes'];
        $vendedor_id = $clientesArray['vendedor_id'];
        $sucurArray = $clientesArray['sucurArray'];


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
        $clienteDirec = $data->clientedirec()->get();
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
        $aux_sta=2;
        $aux_statusPant = 0;
        $invmovmodulo = InvMovModulo::where("cod","=","SOLDESP")->get();
        $array_bodegasmodulo = $invmovmodulo[0]->invmovmodulobodsals->pluck('id')->toArray();
        return view('despachosol.crear', compact('data','clienteselec','clientes','clienteDirec','clientedirecs','detalles','comunas','formapagos','plazopagos','vendedores','vendedores1','productos','fecha','empresa','tipoentregas','giros','sucurArray','aux_sta','aux_cont','aux_statusPant','vendedor_id','array_bodegasmodulo'));
    }

    /**
     * Store a newly created resource in storage.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function guardar(ValidarDespachoSol $request)
    {
        can('guardar-solicitud-despacho');
        //dd($request);
        $cont_producto = count($request->producto_id);
        if($cont_producto<=0){
            return redirect('despachosol')->with([
                'mensaje'=>'Registro no fue creado. No hay registros en el detalle.',
                'tipo_alert' => 'alert-error'
            ]);
        }
        $notaventacerrada = NotaVentaCerrada::where('notaventa_id',$request->notaventa_id)->get();
        //dd($notaventacerrada);
        if(count($notaventacerrada) == 0){
            $notaventa = NotaVenta::findOrFail($request->notaventa_id);
            //dd('cliente bloquedo');
            
            $clibloq = ClienteBloqueado::where("cliente_id" , "=" ,$notaventa->cliente_id)->get();
            if(count($clibloq) > 0){
                return redirect('despachosol')->with([
                    'mensaje'=>'Registro no fue guardado. Cliente Bloqueado: ' . $clibloq[0]->descripcion ,
                    'tipo_alert' => 'alert-error'
                ]);
            }
            if($notaventa->updated_at == $request->updated_at){
                //dd($request->invbodegaproducto_id);
                $notaventa->updated_at = date("Y-m-d H:i:s");
                $notaventa->save();
                $hoy = date("Y-m-d H:i:s");
                $request->request->add(['fechahora' => $hoy]);
                $request->request->add(['usuario_id' => auth()->id()]);
                $dateInput = explode('/',$request->plazoentrega);
                $request["plazoentrega"] = $dateInput[2].'-'.$dateInput[1].'-'.$dateInput[0];
                $dateInput = explode('/',$request->fechaestdesp);
                $request["fechaestdesp"] = $dateInput[2].'-'.$dateInput[1].'-'.$dateInput[0];
                $comuna = Comuna::findOrFail($request->comuna_id);
                $request->request->add(['provincia_id' => $comuna->provincia_id]);
                $request->request->add(['region_id' => $comuna->provincia->region_id]);
                $despachosol = DespachoSol::create($request->all());
                $despachosolid = $despachosol->id;
                //$cont_producto = count($request->producto_id);
                if($cont_producto>0){
                    //CONTROLAR EL STOCK AGRUPADO POR invbodegaproducto_id
                    //ESTO ES CUANDO SE REPITE EL CODIGO DE PRODUCTO EN LA MISMA BODEGA
                    $aux_arraystocks = arraystocks($request);
                    //FIN CONTROLAR EL STOCK AGRUPADO POR invbodegaproducto_id
                    for ($i=0; $i < $cont_producto ; $i++){
                        $aux_cantsol = $request->cantsol[$i];
                        if(is_null($request->producto_id[$i])==false && is_null($aux_cantsol)==false && $aux_cantsol > 0){
                            $despachosoldet = new DespachoSolDet();
                            $despachosoldet->despachosol_id = $despachosolid;
                            $despachosoldet->notaventadetalle_id = $request->NVdet_id[$i];
                            $despachosoldet->cantsoldesp = $request->cantsoldesp[$i];
                            if($despachosoldet->save()){
                                $cont_bodegas = count($request->invcant);
                                if($cont_bodegas>0){
                                    for ($b=0; $b < $cont_bodegas ; $b++){
                                        if($request->invbodegaproducto_producto_id[$b] == $request->producto_id[$i] and $request->invbodegaproductoNVdet_id[$b] == $request->NVdet_id[$i] and ($request->invcant[$b] != 0)){
                                            $despachosoldet_invbodegaproducto = new DespachoSolDet_InvBodegaProducto();
                                            $despachosoldet_invbodegaproducto->despachosoldet_id = $despachosoldet->id;
                                            $despachosoldet_invbodegaproducto->invbodegaproducto_id = $request->invbodegaproducto_id[$b];
                                            $array_request["invbodegaproducto_id"] = $request->invbodegaproducto_id[$b];
                                            //$existencia = InvBodegaProducto::existencia($array_request);
                                            if($request->staex[$b] == 1){
                                                $despachosoldet_invbodegaproducto->cant = 0;
                                                $despachosoldet_invbodegaproducto->cantex = $request->invcant[$b] * -1;
                                                $despachosoldet_invbodegaproducto->staex = $request->staex[$b];
                                            }
                                            else{
                                                $invbodegaproducto_id = $request->invbodegaproducto_id[$i]; //TOMO EL CODIGO invbodegaproducto_id
                                                $aux_existencia = $aux_arraystocks[$invbodegaproducto_id]["stock"]; //AQUI ME UBICO EN LA POSICION DONDE ESTA LA BODEGA ESPECIFICA PARA TOMAR EL STOCK ACTUAL
                                                if($request->invcant[$b] > $aux_existencia) {//$existencia["stock"]["cant"]){
                                                    $despachosoldet_invbodegaproducto->cant = $aux_existencia * -1; //$existencia["stock"]["cant"] * -1;
                                                    $despachosoldet_invbodegaproducto->cantex = ($request->invcant[$b] - $aux_existencia) * -1; // ($request->invcant[$b] - $existencia["stock"]["cant"]) * -1;
                                                }else{
                                                    $despachosoldet_invbodegaproducto->cant = $request->invcant[$b] * -1;
                                                    $despachosoldet_invbodegaproducto->cantex = 0;
                                                }
                                                //ACTUALIZAR SALDO DE STOCK EN ARREGLO QUE CONTIENE LA BODEGA DE CADA PRODUCTO
                                                $aux_arraystocks[$invbodegaproducto_id]["stock"] = $aux_arraystocks[$invbodegaproducto_id]["stock"] + $despachosoldet_invbodegaproducto->cant;
                                            }
                                            $despachosoldet_invbodegaproducto->save();
                                        }
                                    }
                                }

                                /*
                                $notaventadetalle = NotaVentaDetalle::findOrFail($request->NVdet_id[$i]);
                                $notaventadetalle->cantsoldes   p = $request->cantsoldesp[$i];
                                $notaventadetalle->save();
                                */
                                //$despacho_id = $despachosol->id;    
                            }
                        }
                    }
                }
                return redirect('despachosol')->with([
                    'mensaje'=>'Registro creado con exito.',
                    'tipo_alert' => 'alert-success'
                ]);
            }else{
                return redirect('despachosol')->with([
                    'mensaje'=>'Registro no fue creado. Registro Editado por otro usuario. Fecha Hora: '.$notaventa->updated_at,
                    'tipo_alert' => 'alert-error'
                ]);
            }    
        }else{
            //dd($notaventacerrada);
            return redirect('despachosol')->with([
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
        can('editar-solicitud-despacho');
        $data = DespachoSol::findOrFail($id);
        //dd($data);
        $data->plazoentrega = $newDate = date("d/m/Y", strtotime($data->plazoentrega));
        $data->fechaestdesp = $newDate = date("d/m/Y", strtotime($data->fechaestdesp));
        $detalles = $data->despachosoldets()->get();
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

        $clientesArray = Cliente::clientesxUsuario();
        $clientes = $clientesArray['clientes'];
        $vendedor_id = $clientesArray['vendedor_id'];
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
        $aux_sta=2;
        $aux_statusPant = 0;
        $invmovmodulo = InvMovModulo::where("cod","=","SOLDESP")->get();
        $array_bodegasmodulo = $invmovmodulo[0]->invmovmodulobodsals->pluck('id')->toArray();

        //dd($clientedirecs);
        return view('despachosol.editar', compact('data','clienteselec','clientes','clienteDirec','clientedirecs','detalles','comunas','formapagos','plazopagos','vendedores','vendedores1','productos','fecha','empresa','tipoentregas','giros','sucurArray','aux_sta','aux_cont','aux_statusPant','vendedor_id','invmovmodulo','array_bodegasmodulo'));
  
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function actualizar(ValidarDespachoSol $request, $id)
    {
        can('guardar-solicitud-despacho');
        $notaventacerrada = NotaVentaCerrada::where('notaventa_id',$request->notaventa_id)->get();
        //dd($notaventacerrada);
        if(count($notaventacerrada) == 0){
            $dateInput = explode('/',$request->plazoentrega);
            $request["plazoentrega"] = $dateInput[2].'-'.$dateInput[1].'-'.$dateInput[0];
            $dateInput = explode('/',$request->fechaestdesp);
            $request["fechaestdesp"] = $dateInput[2].'-'.$dateInput[1].'-'.$dateInput[0];
            $despachosol = DespachoSol::findOrFail($id);
            $clibloq = ClienteBloqueado::where("cliente_id" , "=" ,$despachosol->notaventa->cliente_id)->get();
            if(count($clibloq) > 0){
                return redirect('despachosol')->with([
                    'mensaje'=>'Registro no fue guardado. Cliente Bloqueado: ' . $clibloq[0]->descripcion ,
                    'tipo_alert' => 'alert-error'
                ]);
            }
            if($despachosol->updated_at == $request->updated_at){
                $despachosol->updated_at = date("Y-m-d H:i:s");
                $despachosol->comunaentrega_id = $request->comunaentrega_id;
                $despachosol->tipoentrega_id = $request->tipoentrega_id;
                $despachosol->plazoentrega = $request->plazoentrega;
                $despachosol->lugarentrega = $request->lugarentrega;
                $despachosol->contacto = $request->contacto;
                $despachosol->contactoemail = $request->contactoemail;
                $despachosol->contactotelf = $request->contactotelf;
                $despachosol->observacion = $request->observacion;
                $despachosol->fechaestdesp = $request->fechaestdesp;
                //dd($request);
                if($despachosol->save()){
                    $aux_arraystocks = arraystocks($request);
                    //dd($aux_arraystocks);
                    $cont_producto = count($request->producto_id);
                    if($cont_producto>0){
                        for ($i=0; $i < $cont_producto ; $i++){
                            if(is_null($request->producto_id[$i])==false && is_null($request->cant[$i])==false){
                                $despachosoldet = DespachoSolDet::findOrFail($request->NVdet_id[$i]);
                                $despachosoldet->cantsoldesp = $request->cantsoldesp[$i];
                                if($despachosoldet->save()){ //Si al editar dejo en cero la cantidad solicitada elimino el registro en detalle solicitud
                                    if($request->cantsoldesp[$i]==0){
                                        $despachosoldet->usuariodel_id = auth()->id();
                                        $despachosoldet->save();
                                        DB::table('despachosoldet_invbodegaproducto')->where('despachosoldet_id', $despachosoldet->id)->delete();
                                        $despachosoldet->delete();
                                    }else{
                                        //dd($request);
                                        $cont_bodegas = count($request->invcant);
                                        if($cont_bodegas>0){
                                            for ($b=0; $b < $cont_bodegas ; $b++){
                                                if($request->invbodegaproducto_producto_id[$b] == $request->producto_id[$i] and $request->invbodegaproductoNVdet_id[$b] == $request->NVdet_id[$i]){
                                                    if($request->invcant[$b] > 0){
                                                        $array_request["invbodegaproducto_id"] = $request->invbodegaproducto_id[$b];
                                                        //$existencia = InvBodegaProducto::existencia($array_request);
                                                        
                                                        $invbodegaproducto_id = $request->invbodegaproducto_id[$i]; //TOMO EL CODIGO invbodegaproducto_id
                                                        $aux_existencia = $aux_arraystocks[$invbodegaproducto_id]["stock"];//AQUI ME UBICO EN LA POSICION DONDE ESTA LA BODEGA ESPECIFICA PARA TOMAR EL STOCK ACTUAL
        
                                                        if($request->invcant[$b] > $aux_existencia) { //$existencia["stock"]["cant"]){
                                                            $aux_cant = $aux_existencia * -1; //$existencia["stock"]["cant"] * -1;
                                                            $aux_cantex = ($request->invcant[$b] - $aux_existencia) * -1; // $existencia["stock"]["cant"]) * -1;
                                                        }else{
                                                            $aux_cant = $request->invcant[$b] * -1;
                                                            $aux_cantex = 0;
                                                        }
                                                        if($request->staex[$b] == 1){
                                                            $aux_cant = 0;
                                                            $aux_cantex = $request->invcant[$b] * -1;
                                                        }else{
                                                            $aux_cant = $request->invcant[$b] * -1;
                                                            $aux_cantex = 0;
                                                        }
                                                        //ACTUALIZAR SALDO DE STOCK EN ARREGLO QUE CONTIENE LA BODEGA DE CADA PRODUCTO
                                                        $aux_arraystocks[$invbodegaproducto_id]["stock"] = $aux_arraystocks[$invbodegaproducto_id]["stock"] + $aux_cant;
                                                        /*
                                                        DB::table('despachosoldet_invbodegaproducto')->updateOrInsert(
                                                            ['despachosoldet_id' => $request->NVdet_id[$i], 'invbodegaproducto_id' => $request->invbodegaproducto_id[$b]],
                                                            [
                                                                'cant' => $aux_cant,
                                                                'cantex' => $aux_cantex
                                                            ]
                                                        );
                                                        */
                                                        DespachoSolDet_InvBodegaProducto::updateOrCreate(
                                                            ['despachosoldet_id' => $request->NVdet_id[$i], 'invbodegaproducto_id' => $request->invbodegaproducto_id[$b]],
                                                            [
                                                                'cant' => $aux_cant,
                                                                'cantex' => $aux_cantex,
                                                                'staex' => $request->staex[$b]
                                                            ]
                                                        );
                            
                                                    }else{
                                                        DB::table('despachosoldet_invbodegaproducto')
                                                            ->where('despachosoldet_id', $request->NVdet_id[$i])
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
                return redirect('despachosol')->with([
                                                            'mensaje'=>'Registro actualizado con exito.',
                                                            'tipo_alert' => 'alert-success'
                                                        ]);
            }else{
                return redirect('despachosol')->with([
                                                            'mensaje'=>'Registro no fue modificado. Registro Editado por otro usuario. Fecha Hora: '.$despachosol->updated_at,
                                                            'tipo_alert' => 'alert-error'
                                                        ]);
            }
        }else{
            return redirect('despachosol')->with([
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

    public function reporte(Request $request){
        $respuesta = reporte1($request);
        return $respuesta;
    }

    public function reportesoldesp(Request $request){
        $respuesta = reportesoldesp1($request);
        return $respuesta;
    }

    public function reportesoldespcerrarNV(Request $request){
        $respuesta = reportesoldespcerrarNV1($request);
        return $respuesta;
    }


    public function anular(Request $request)
    {
        if ($request->ajax()) {
            $despachosol = DespachoSol::findOrFail($request->id);
            if($request->updated_at != $despachosol->updated_at){
                return response()->json([
                    'id' => 0,
                    'error' => '0',
                    'mensaje' => 'Registro fué modificado por otro usuario.',
                    'tipo_alert' => 'error'
                ]);
            }
            $sql = "SELECT COUNT(*) AS cont
                FROM despachosol INNER JOIN despachoord
                ON despachosol.id=despachoord.despachosol_id
                WHERE despachosol.id = $request->id
                AND despachoord.id NOT IN (SELECT despachoordanul.despachoord_id FROM despachoordanul WHERE ISNULL(despachoordanul.deleted_at))
                AND isnull(despachosol.deleted_at)
                AND ISNULL(despachoord.deleted_at)";
            $cont = DB::select($sql);
            if($cont[0]->cont == 0){
                $despachosolanul = new DespachoSolAnul();
                $despachosolanul->despachosol_id = $request->id;
                $despachosolanul->usuario_id = auth()->id();
                if ($despachosolanul->save()) {
                    /*
                    $despachosoldets = $despachosol->despachosoldets;
                    foreach ($despachosoldets as $despachosoldet){
                        $notaventadetalle = NotaVentaDetalle::findOrFail($despachosoldet->notaventadetalle_id);
                        $notaventadetalle->cantsoldesp = $notaventadetalle->cantsoldesp - $despachosoldet->cantsoldesp;
                        $notaventadetalle->save();
                    }*/
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
                //return response()->json(['mensaje' => 'hijo']);
                return response()->json([
                    'error'=>'0',
                    'mensaje'=>'Registro no fue anulado. Solicitud tiene despachos asociados.',
                    'tipo_alert' => 'error'
                ]);
    
            }
        } else {
            abort(404);
        }
    }

    /*DEVOLVER SOLICITUD DESPACHO A SOLICITUDES PENDIENTES POR APROBAR*/
    public function devolversoldesp(Request $request)
    {
        if ($request->ajax()) {
            $sql = "SELECT COUNT(*) as cont
            FROM despachoord
            WHERE despachoord.despachosol_id=$request->id
            AND despachoord.id 
            NOT IN (SELECT despachoordanul.despachoord_id FROM despachoordanul WHERE ISNULL(despachoordanul.deleted_at));";

            $contorddesp = DB::select($sql);
            if($contorddesp[0]->cont > 0){
                return response()->json(['mensaje' => 'Solicitud ya fue procesada en Orden de Despacho']);
            }

            $despachosol = DespachoSol::findOrFail($request->id);
            if($despachosol->aprorddesp != 1){
                return response()->json(['mensaje' => 'Registro fue modificado previamente.']);
            }
            $invmodulo = InvMovModulo::where("cod","SOLDESP")->get();
            $invmoduloBod = InvMovModulo::findOrFail($invmodulo[0]->id);
            foreach ($despachosol->despachosoldets as $despachoorddet) {
                foreach ($despachoorddet->despachosoldet_invbodegaproductos as $oddetbodprod) {
                    //ESTO DEBE IR EN EL PROYECTO FINAL
                    $aux_sucursal_id_producto = $oddetbodprod->invbodegaproducto->invbodega->sucursal_id; 
                    $aux_bodegadespacho_id = 0;
                    foreach($invmoduloBod->invmovmodulobodsals as $invmovmodulobodsal){
                        //BUSCAR BODEGA PROD TERMINADO CORRESPONDIENTE AL PRODUCTO QUE SE ESTA PROCESANDO DEPENDIENDO DE LA SUCURSAL QUE CORRESPONDE EL PRODUCTO
                        if($invmovmodulobodsal->sucursal_id == $aux_sucursal_id_producto){
                            $aux_bodegadespacho_id = $invmovmodulobodsal->id;
                        }
                    }
                    if($aux_bodegadespacho_id == 0){
                        return response()->json([
                            'status'=>'0',
                            'mensaje'=> "No existe Bodega Picking de salida en modulo invmodulo: " . $invmoduloBod->nombre . ". Debe ser creada. ",
                            'tipo_alert' => 'error'
                        ]);         
                    }
                }
            }
            //dd($invmoduloBod->invmovmodulobodents[0]->id);
            if(count($invmodulo) == 0){
                return response()->json([
                    'mensaje' => 'No existe modulo SOLDESP'
                ]);
            }

            //dd($invmoduloBod->invmovmodulobodents[0]->id);
            if(count($invmodulo) == 0){
                return response()->json([
                    'mensaje' => 'No existe modulo SOLDESP'
                ]);
            }
            //$despachosol = DespachoSol::findOrFail($request->id);
            $aux_bandera = true;
            foreach ($despachosol->despachosoldets as $despachosoldet) {
                $aux_respuesta = InvBodegaProducto::validarExistenciaStock($despachosoldet->despachosoldet_invbodegaproductos);
                if($aux_respuesta["bandera"] == false){
                    $aux_bandera = $aux_respuesta["bandera"];
                    break;
                }
            }
            $aux_banderacant = true; //VALIDAR QUE EXISTE AL MENOS 1 PRODUCTO CON CANTIDAD
            //dd("entro");
            foreach ($despachosol->despachosoldets as $despachosoldet) {
                foreach ($despachosoldet->despachosoldet_invbodegaproductos as $despachosoldet_invbodegaproducto){
                    //CONSULTA EL STOCK SOLO CUANDO LA CANT ES DIFERENTE A 0, ES DECIR SOLO CUANDO SE FUE A PICKING
                    if($despachosoldet_invbodegaproducto->cant != 0){
                        $aux_invbodega_id = $despachosoldet_invbodegaproducto->invbodegaproducto->invbodega_id;
                        //$aux_cant = $despachosoldet_invbodegaproducto->cant * -1;
                        $aux_cant = 0;
                        foreach ($despachosoldet_invbodegaproducto->invmovdet_bodsoldesps as $invmovdet_bodsoldesp) {
                            $aux_cant += $invmovdet_bodsoldesp->invmovdet->cant;
                        }
                        if($aux_cant > 0){
                            $aux_sucursal_id_producto = $despachosoldet_invbodegaproducto->invbodegaproducto->invbodega->sucursal_id; 
                            $aux_bodegadespacho_id = 0;
                            foreach($invmoduloBod->invmovmodulobodents as $invmovmodulobodent){
                                //BUSCAR BODEGA PICKING CORRESPONDIENTE AL PRODUCTO QUE SE ESTA PROCESANDO DEPENDIENDO DE LA SUCURSAL QUE CORRESPONDE EL PRODUCTO
                                if($invmovmodulobodent->sucursal_id == $aux_sucursal_id_producto){
                                    $aux_bodegadespacho_id = $invmovmodulobodent->id;
                                }
                            }
                            if($aux_bodegadespacho_id == 0){
                                return response()->json([
                                    "status" => "0",
                                    "title" => "No existe Bodega Picking",
                                    'mensaje' => "" . "Debe crear Bodega Picking para \nProducto Id:" . $despachosoldet_invbodegaproducto->invbodegaproducto->producto_id . "\nNombre:" . $despachosoldet_invbodegaproducto->invbodegaproducto->producto->nombre,
                                    'tipo_alert' => 'error',
                                ]);
                            }
                            $aux_banderacant = true;
                            $aux_producto =$despachosoldet_invbodegaproducto->invbodegaproducto->producto;
                            $invbodegaproducto = InvBodegaProducto::where("producto_id","=",$aux_producto->id)
                            ->where("invbodega_id","=",$aux_bodegadespacho_id)
                            ->select([
                                'id as invbodegaproducto_id',
                                'producto_id',
                                'invbodega_id'
                            ])
                            ->get();
                            $aux_respuesta = InvBodegaProducto::existencia($invbodegaproducto[0]);
                            if($aux_respuesta["stock"]["cant"] < $aux_cant){ //VALIDAR STOCK DE PRODUCTO EN BODEGA
                                //dd($aux_respuesta);
                                //dd($invbodegaproducto);
                                //dd($despachosoldet_invbodegaproducto->invmovdet_bodsoldesps[1]->invmovdet);
                                //dd($aux_respuesta["stock"]["cant"]);
                                return response()->json([
                                    "status" => "0",
                                    'mensaje' => "Producto sin Stock,  ID: " . $aux_producto->id . ", Nombre: " . $aux_producto->nombre . ", Stock: " . $aux_respuesta["stock"]["cant"],
                                    'tipo_alert' => 'error',
                                ]);               
                                break;
                            }
                        }    
                    }
                }
            }
            if($aux_banderacant){
                $invmov_array = array();
                $invmov_array["fechahora"] = date("Y-m-d H:i:s");
                $invmov_array["annomes"] = date("Ym");
                $invmov_array["desc"] = "Entrada por devolucion SD NV:" . $despachosol->notaventa_id . " SD:" . $request->id;
                $invmov_array["obs"] = "Entrada por devolucion SD NV:" . $despachosol->notaventa_id . " SD:" . $request->id;
                $invmov_array["invmovmodulo_id"] = $invmodulo[0]->id; //Modulo Solicitud Despacho
                $invmov_array["idmovmod"] = $request->id;
                $invmov_array["invmovtipo_id"] = 1;
                $invmov_array["sucursal_id"] = $despachosol->notaventa->sucursal_id;
                $invmov_array["usuario_id"] = auth()->id();
                $arrayinvmov_id = array();
                
                $invmov = InvMov::create($invmov_array);
                array_push($arrayinvmov_id, $invmov->id);
                foreach ($despachosol->despachosoldets as $despachosoldet) {
                    foreach ($despachosoldet->despachosoldet_invbodegaproductos as $oddetbodprod) {
                        $aux_cant = $oddetbodprod->cant * -1;
                        $aux_cant = 0;
                        foreach ($oddetbodprod->invmovdet_bodsoldesps as $invmovdet_bodsoldesp) {
                            $aux_cant += $invmovdet_bodsoldesp->invmovdet->cant;
                        }
    
                        if($aux_cant > 0){
                            $array_invmovdet = $oddetbodprod->attributesToArray();
                            $array_invmovdet["producto_id"] = $oddetbodprod->invbodegaproducto->producto_id;
                            $array_invmovdet["invbodega_id"] = $oddetbodprod->invbodegaproducto->invbodega_id;
                            $array_invmovdet["sucursal_id"] = $oddetbodprod->invbodegaproducto->invbodega->sucursal_id;
                            $array_invmovdet["unidadmedida_id"] = $despachosoldet->notaventadetalle->unidadmedida_id;
                            $array_invmovdet["invmovtipo_id"] = 1;
                            $array_invmovdet["cant"] = $aux_cant;
                            $array_invmovdet["cantgrupo"] = $aux_cant;
                            $array_invmovdet["cantxgrupo"] = 1;
                            $array_invmovdet["peso"] = $despachosoldet->notaventadetalle->producto->peso;
                            $array_invmovdet["cantkg"] = ($despachosoldet->notaventadetalle->totalkilos / $despachosoldet->notaventadetalle->cant) * $aux_cant;
                            $array_invmovdet["invmov_id"] = $invmov->id;
                            $invmovdet = InvMovDet::create($array_invmovdet);
                        }
                    }
                }
                $invmov_array = array();
                $invmov_array["fechahora"] = date("Y-m-d H:i:s");
                $invmov_array["annomes"] = date("Ym");
                $invmov_array["desc"] = "Salida por devolución SD NV:" . $despachosol->notaventa_id . " SD:" . $request->id;
                $invmov_array["obs"] = "Salida por devolución SD NV:" . $despachosol->notaventa_id . " SD:" . $request->id;
                $invmov_array["invmovmodulo_id"] = $invmodulo[0]->id; //Modulo Solicitud Despacho
                $invmov_array["idmovmod"] = $request->id;
                $invmov_array["invmovtipo_id"] = 2;
                $invmov_array["sucursal_id"] = $despachosol->notaventa->sucursal_id;
                $invmov_array["usuario_id"] = auth()->id();
                
                $invmov = InvMov::create($invmov_array);
                array_push($arrayinvmov_id, $invmov->id);
                foreach ($despachosol->despachosoldets as $despachosoldet) {
                    foreach ($despachosoldet->despachosoldet_invbodegaproductos as $oddetbodprod) {
                        //$aux_cant = $oddetbodprod->cant * -1;
                        $aux_cant = 0;
                        foreach ($oddetbodprod->invmovdet_bodsoldesps as $invmovdet_bodsoldesp) {
                            $aux_cant += $invmovdet_bodsoldesp->invmovdet->cant;
                        }
                        if($aux_cant > 0){
                            //ESTO DEBE IR EN EL PROYECTO FINAL
                            $aux_sucursal_id_producto = $oddetbodprod->invbodegaproducto->invbodega->sucursal_id; 
                            foreach($invmoduloBod->invmovmodulobodents as $invmovmodulobodent){
                                //BUSCAR BODEGA PICKING CORRESPONDIENTE AL PRODUCTO QUE SE ESTA PROCESANDO DEPENDIENDO DE LA SUCURSAL QUE CORRESPONDE EL PRODUCTO
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
                            $array_invmovdet["unidadmedida_id"] = $despachosoldet->notaventadetalle->unidadmedida_id;
                            $array_invmovdet["invmovtipo_id"] = 2;
                            $array_invmovdet["cant"] = $aux_cant * -1 ;
                            $array_invmovdet["cantgrupo"] = $aux_cant * -1;
                            $array_invmovdet["cantxgrupo"] = 1;
                            $array_invmovdet["peso"] = $despachosoldet->notaventadetalle->producto->peso;
                            $array_invmovdet["cantkg"] = ($despachosoldet->notaventadetalle->totalkilos / $despachosoldet->notaventadetalle->cant) * $aux_cant * -1;
                            $array_invmovdet["invmov_id"] = $invmov->id;
                            $invmovdet = InvMovDet::create($array_invmovdet);
                            $invmovdet_bodsoldesp = InvMovDet_BodSolDesp::create([
                                'invmovdet_id' => $invmovdet->id,
                                'despachosoldet_invbodegaproducto_id' => $oddetbodprod->id
                            ]);
                        }
                    }
                }
                //$despachosol->invmovs()->sync($arrayinvmov_id);
                $despachosol_invmov = DespachoSol_InvMov::create([
                        'despachosol_id' => $despachosol->id,
                        'invmov_id' => $arrayinvmov_id[0]
                    ]);
                $despachosol_invmov = DespachoSol_InvMov::create(
                    [
                        'despachosol_id' => $despachosol->id,
                        'invmov_id' => $arrayinvmov_id[1]
                    ]);

            }
            $despachosol->aprorddesp = null;
            $despachosol->aprorddespfh = null;        
            if ($despachosol->save()) {
                event(new DevolverSolDesp($despachosol,$request));
                return response()->json([
                    "status" => "1",
                    'mensaje' => 'Registro procesado con exito',
                    'tipo_alert' => 'success'
                ]);
            } else {
                return response()->json([
                    "status" => "0",
                    'mensaje' => 'Error al guardar en despachosol',
                    'tipo_alert' => 'error'
                ]);
            }
        } else {
            abort(404);
        }
    }

    /*CERRAR O DEVOLVER PARCIALMENTE SOLICITUD DESPACHO A SOLICITUDES PENDIENTES POR APROBAR*/
    /*ASIGNO RESTO AL CAMPO cantsoldesp LO QUE NO SE HA DESPACHADO */
    public function cerrarsoldesp(Request $request)
    {
        if ($request->ajax()) {
            $despachosol = DespachoSol::findOrFail($request->id);

            /******************************************************/
            $despachosol = DespachoSol::findOrFail($request->id);
            if($despachosol->aprorddesp != 1){
                return response()->json(['mensaje' => 'Registro fue modificado previamente.']);
            }
            $invmodulo = InvMovModulo::where("cod","SOLDESP")->get();
            $invmoduloBod = InvMovModulo::findOrFail($invmodulo[0]->id);
            //dd($invmoduloBod->invmovmodulobodents[0]->id);
            if(count($invmodulo) == 0){
                return response()->json([
                    'mensaje' => 'No existe modulo SOLDESP'
                ]);
            }

            //dd($invmoduloBod->invmovmodulobodents[0]->id);
            if(count($invmodulo) == 0){
                return response()->json([
                    'mensaje' => 'No existe modulo SOLDESP'
                ]);
            }

            /*****************PRUEBA PARA DEVOLVER SOLICITUD CUANDO HAY YA ORDENES DE UNA SOLICITUD DE DESPACHO */
            /***** 15/07/2022 */
            /***** ESTO DEBO ACTIVARLO CUANDO PASE POR LO MENOS 1 SEMANA PARA QUE TODAS SOLDESP Y ORDDESP HAYAN SIDO PROCESADAS */
            /*
            $aux_stacrearmovinv = 0;
            foreach ($despachosol->despachosoldets as $despachosoldet) {
                $aux_cant = 0;
                foreach ($despachosoldet->despachosoldet_invbodegaproductos as $despachosoldet_invbodegaproducto) {
                    $aux_cant = 0;
                    foreach ($despachosoldet_invbodegaproducto->invmovdet_bodsoldesps as $invmovdet_bodsoldesp) {
                        $aux_cant += $invmovdet_bodsoldesp->invmovdet->cant;
                    }
                    foreach($despachosoldet->despachoorddets as $despachoorddet){
                        if(!$despachoorddet->despachoord->despachoordanul){ //QUE NO ESTE ANULADO
                            if(!$despachoorddet->despachoord->aprguiadesp){ //SUMA SI NO ESTA APROBADO PARA GUIA DE DESPACHO ES DECIR ENTRA SI EN = A NULL
                                foreach($despachoorddet->despachoorddet_invbodegaproductos as $despachoorddet_invbodegaproducto){
                                    $aux_cant += $despachoorddet_invbodegaproducto->cant;
                                }
                            }
                        }
                    }
                    $invbodegaproducto_idEntrada = $despachosoldet_invbodegaproducto->invbodegaproducto->id;
                    $invbodegaproducto_idSalida = $invmovdet_bodsoldesp->invmovdet->invbodegaproducto_id;
                    $producto_id = $despachosoldet_invbodegaproducto->invbodegaproducto->producto_id;
                    $invbodega_idEntrada = $despachosoldet_invbodegaproducto->invbodegaproducto->invbodega_id;
                    $invbodega_idSalida = $invmovdet_bodsoldesp->invmovdet->invbodega_id;
                    $sucursal_id = $despachosoldet_invbodegaproducto->invbodegaproducto->invbodega->sucursal_id;
                    $unidadmedida_id = $despachosoldet->notaventadetalle->unidadmedida_id;
                    $peso = $despachosoldet->notaventadetalle->producto->peso;
                    $cantkg = ($despachosoldet->notaventadetalle->totalkilos / $despachosoldet->notaventadetalle->cant) * $aux_cant;
                    $despachosoldet_invbodegaproducto_id = $despachosoldet_invbodegaproducto->id;
                }
                if($aux_cant > 0){
                    if($aux_stacrearmovinv == 0){
                        $invmov_array = array();
                        $invmov_array["fechahora"] = date("Y-m-d H:i:s");
                        $invmov_array["annomes"] = date("Ym");
                        $invmov_array["desc"] = "Entrada a Bodega por devolucion NV:" . $despachosol->notaventa_id . " SD:" . $request->id;
                        $invmov_array["obs"] = "Entrada a Bodega por devolucion NV:" . $despachosol->notaventa_id . " SD:" . $request->id;
                        $invmov_array["invmovmodulo_id"] = $invmodulo[0]->id; //Modulo Solicitud Despacho
                        $invmov_array["idmovmod"] = $request->id;
                        $invmov_array["invmovtipo_id"] = 1;
                        $invmov_array["sucursal_id"] = $despachosol->notaventa->sucursal_id;
                        $invmov_array["usuario_id"] = auth()->id();
                        $arrayinvmov_id = array();
                        
                        $invmovEntDesp = InvMov::create($invmov_array);
                        array_push($arrayinvmov_id, $invmovEntDesp->id);

                        $invmov_array = array();
                        $invmov_array["fechahora"] = date("Y-m-d H:i:s");
                        $invmov_array["annomes"] = date("Ym");
                        $invmov_array["desc"] = "Salida por devolución de Bodega NV:" . $despachosol->notaventa_id . " SD:" . $request->id;
                        $invmov_array["obs"] = "Salida por devolución de Bodega NV:" . $despachosol->notaventa_id . " SD:" . $request->id;
                        $invmov_array["invmovmodulo_id"] = $invmodulo[0]->id; //Modulo Solicitud Despacho
                        $invmov_array["idmovmod"] = $request->id;
                        $invmov_array["invmovtipo_id"] = 2;
                        $invmov_array["sucursal_id"] = $despachosol->notaventa->sucursal_id;
                        $invmov_array["usuario_id"] = auth()->id();
                        
                        $invmovSalDesp = InvMov::create($invmov_array);
                        array_push($arrayinvmov_id, $invmovSalDesp->id);

                        $despachosol_invmov = DespachoSol_InvMov::create(
                        [
                            'despachosol_id' => $despachosol->id,
                            'invmov_id' => $arrayinvmov_id[0]
                        ]);
    
                        $despachosol_invmov = DespachoSol_InvMov::create(
                        [
                            'despachosol_id' => $despachosol->id,
                            'invmov_id' => $arrayinvmov_id[1]
                        ]);
                        $aux_stacrearmovinv = 1;
                    }

                    $array_invmovdet = array();
                    $array_invmovdet["invbodegaproducto_id"] = $invbodegaproducto_idEntrada;
                    $array_invmovdet["producto_id"] = $producto_id;
                    $array_invmovdet["invbodega_id"] = $invbodega_idEntrada;
                    $array_invmovdet["sucursal_id"] = $sucursal_id;
                    $array_invmovdet["unidadmedida_id"] = $unidadmedida_id;
                    $array_invmovdet["invmovtipo_id"] = 1;
                    $array_invmovdet["cant"] = $aux_cant;
                    $array_invmovdet["cantgrupo"] = $aux_cant;
                    $array_invmovdet["cantxgrupo"] = 1;
                    $array_invmovdet["peso"] = $peso;
                    $array_invmovdet["cantkg"] = $cantkg;
                    $array_invmovdet["invmov_id"] = $invmovEntDesp->id;
                    $invmovdet = InvMovDet::create($array_invmovdet);
                    if(count($despachosoldet->despachoorddets)>0 and isset($despachoorddet_invbodegaproducto_id)){
                        $invmovdet_bodorddesp = InvMovDet_BodOrdDesp::create([
                            'invmovdet_id' => $invmovdet->id,
                            'despachoorddet_invbodegaproducto_id' => $despachoorddet_invbodegaproducto_id
                        ]);
    
                    }

                    $array_invmovdet = array();
                    $array_invmovdet["invbodegaproducto_id"] = $invbodegaproducto_idSalida;
                    $array_invmovdet["producto_id"] = $producto_id;
                    $array_invmovdet["invbodega_id"] = $invbodega_idSalida;
                    $array_invmovdet["sucursal_id"] = $sucursal_id;
                    $array_invmovdet["unidadmedida_id"] = $unidadmedida_id;
                    $array_invmovdet["invmovtipo_id"] = 2;
                    $array_invmovdet["cant"] = $aux_cant * -1;
                    $array_invmovdet["cantgrupo"] = $aux_cant * -1;
                    $array_invmovdet["cantxgrupo"] = 1;
                    $array_invmovdet["peso"] = $peso;
                    $array_invmovdet["cantkg"] = $cantkg;
                    $array_invmovdet["invmov_id"] = $invmovSalDesp->id;
                    $invmovdet = InvMovDet::create($array_invmovdet);
                    $invmovdet_bodsoldesp = InvMovDet_BodSolDesp::create([
                        'invmovdet_id' => $invmovdet->id,
                        'despachosoldet_invbodegaproducto_id' => $despachosoldet_invbodegaproducto_id
                    ]);
                }

            }
            */
            /*****************PRUEBA PARA DEVOLVER SOLICITUD CUANDO HAY YA ORDENES DE UNA SOLICITUD DE DESPACHO */
            /***** 15/07/2022 */

            $aux_stacrearmovinv = 0;
            $cantkg  = 0;
            foreach ($despachosol->despachosoldets as $despachosoldet) {
                $aux_cantBodSD = 0;
                $invbodegaproducto_id = 0;
                foreach ($despachosoldet->despachosoldet_invbodegaproductos as $despachosoldet_invbodegaproducto) {
                    if(($despachosoldet_invbodegaproducto->cant * -1) > 0){
                        foreach ($despachosoldet_invbodegaproducto->invmovdet_bodsoldesps as $invmovdet_bodsoldesp){
                            $aux_cantBodSD += $invmovdet_bodsoldesp->invmovdet->cant;
                            $invbodegaproducto_idEntrada = $despachosoldet_invbodegaproducto->invbodegaproducto->id;
                            $invbodegaproducto_idSalida = $invmovdet_bodsoldesp->invmovdet->invbodegaproducto_id;
                            $producto_id = $despachosoldet_invbodegaproducto->invbodegaproducto->producto_id;
                            $invbodega_idEntrada = $despachosoldet_invbodegaproducto->invbodegaproducto->invbodega_id;
                            $invbodega_idSalida = $invmovdet_bodsoldesp->invmovdet->invbodega_id;
                            $sucursal_id = $despachosoldet_invbodegaproducto->invbodegaproducto->invbodega->sucursal_id;
                            $unidadmedida_id = $despachosoldet->notaventadetalle->unidadmedida_id;
                            $peso = $despachosoldet->notaventadetalle->producto->peso;
                            $cantkg += ($despachosoldet->notaventadetalle->totalkilos / $despachosoldet->notaventadetalle->cant) * $invmovdet_bodsoldesp->invmovdet->cant;
                            $despachosoldet_invbodegaproducto_id = $despachosoldet_invbodegaproducto->id;
                        }
                    }
                }
                foreach ($despachosoldet->despachoorddets as $despachoorddet){
                    $DespachoOrd = DespachoOrd::findOrFail($despachoorddet->despachoord_id);
                    if(!$DespachoOrd->despachoordanul ){
                        foreach ($despachoorddet->despachoorddet_invbodegaproductos as $despachoorddet_invbodegaproducto){
                            /* SOLO DEBO BUSCAR EL MOVIMIENTO DE PICKING DEL PRODUCTO ENTRADAS Y SALIDAS
                            $despachoorddet_invbodegaproducto_id = $despachoorddet_invbodegaproducto->id;
                            foreach ($despachoorddet_invbodegaproducto->invmovdet_bodorddesps as $invmovdet_bodorddesp){
                                //SUMO SOLO EL MOVIMIENTO DE LA BODEGA DE SOL DESPACHO
                                if($invmovdet_bodorddesp->invmovdet->invbodegaproducto->invbodega->nomabre == "SolDe"){
                                    $aux_cantBodSD += $invmovdet_bodorddesp->invmovdet->cant;
                                    $DespachoOrd_id = $DespachoOrd->id;
                                }
                            }
                            */
                            //SI AUN NO HAY MOVIMIENTO DE INVENTARIO RESTA LOS QUE ESTA EN despachoorddet_invbodegaproducto 
                            //ESTO ES POR SI ACASO HAY UNA ORDEN DE DESPACHO SIN GUARDAR EN LA PANTALLA INDEX DE ORDEN DE DESPACHO
                            if(is_null($DespachoOrd->aprguiadesp)){
                                $aux_cantBodSD += $despachoorddet_invbodegaproducto->cant;
                                $cantkg += ($despachoorddet->notaventadetalle->totalkilos / $despachoorddet->notaventadetalle->cant) * $despachoorddet_invbodegaproducto->cant;
                                $DespachoOrd_id = $DespachoOrd->id;
                            }
                            /*
                            if (sizeof($despachoorddet_invbodegaproducto->invmovdet_bodorddesps) == 0){
                            }*/
                        }
                    }
                }
                if($aux_cantBodSD > 0){
                    if($aux_stacrearmovinv == 0){
                        $invmov_array = array();
                        $invmov_array["fechahora"] = date("Y-m-d H:i:s");
                        $invmov_array["annomes"] = date("Ym");
                        $invmov_array["desc"] = "Entrada por cerrar SD NV:" . $despachosol->notaventa_id . " SD:" . $request->id;
                        $invmov_array["obs"] = "Entrada por cerrar SD NV:" . $despachosol->notaventa_id . " SD:" . $request->id;
                        $invmov_array["invmovmodulo_id"] = $invmodulo[0]->id; //Modulo Solicitud Despacho
                        $invmov_array["idmovmod"] = $request->id;
                        $invmov_array["invmovtipo_id"] = 1;
                        $invmov_array["sucursal_id"] = $despachosol->notaventa->sucursal_id;
                        $invmov_array["usuario_id"] = auth()->id();
                        $arrayinvmov_id = array();
                        
                        $invmovEntDesp = InvMov::create($invmov_array);
                        array_push($arrayinvmov_id, $invmovEntDesp->id);

                        $invmov_array = array();
                        $invmov_array["fechahora"] = date("Y-m-d H:i:s");
                        $invmov_array["annomes"] = date("Ym");
                        $invmov_array["desc"] = "Salida por cerrar SD NV:" . $despachosol->notaventa_id . " SD:" . $request->id;
                        $invmov_array["obs"] = "Salida por cerrar SD NV:" . $despachosol->notaventa_id . " SD:" . $request->id;
                        $invmov_array["invmovmodulo_id"] = $invmodulo[0]->id; //Modulo Solicitud Despacho
                        $invmov_array["idmovmod"] = $request->id;
                        $invmov_array["invmovtipo_id"] = 2;
                        $invmov_array["sucursal_id"] = $despachosol->notaventa->sucursal_id;
                        $invmov_array["usuario_id"] = auth()->id();
                        
                        $invmovSalDesp = InvMov::create($invmov_array);
                        array_push($arrayinvmov_id, $invmovSalDesp->id);

                        $despachosol_invmov = DespachoSol_InvMov::create(
                        [
                            'despachosol_id' => $despachosol->id,
                            'invmov_id' => $arrayinvmov_id[0]
                        ]);
    
                        $despachosol_invmov = DespachoSol_InvMov::create(
                        [
                            'despachosol_id' => $despachosol->id,
                            'invmov_id' => $arrayinvmov_id[1]
                        ]);
                        $aux_stacrearmovinv = 1;
                    }

                    $array_invmovdet = array();
                    $array_invmovdet["invbodegaproducto_id"] = $invbodegaproducto_idEntrada;
                    $array_invmovdet["producto_id"] = $producto_id;
                    $array_invmovdet["invbodega_id"] = $invbodega_idEntrada;
                    $array_invmovdet["sucursal_id"] = $sucursal_id;
                    $array_invmovdet["unidadmedida_id"] = $unidadmedida_id;
                    $array_invmovdet["invmovtipo_id"] = 1;
                    $array_invmovdet["cant"] = $aux_cantBodSD;
                    $array_invmovdet["cantgrupo"] = $aux_cantBodSD;
                    $array_invmovdet["cantxgrupo"] = 1;
                    $array_invmovdet["peso"] = $peso;
                    $array_invmovdet["cantkg"] = $cantkg;
                    $array_invmovdet["invmov_id"] = $invmovEntDesp->id;
                    $invmovdet = InvMovDet::create($array_invmovdet);
                    /*
                    if(count($despachosoldet->despachoorddets)>0 and isset($despachoorddet_invbodegaproducto_id)){
                        $invmovdet_bodorddesp = InvMovDet_BodOrdDesp::create([
                            'invmovdet_id' => $invmovdet->id,
                            'despachoorddet_invbodegaproducto_id' => $despachoorddet_invbodegaproducto_id
                        ]);
    
                    }*/

                    $array_invmovdet = array();
                    $array_invmovdet["invbodegaproducto_id"] = $invbodegaproducto_idSalida;
                    $array_invmovdet["producto_id"] = $producto_id;
                    $array_invmovdet["invbodega_id"] = $invbodega_idSalida;
                    $array_invmovdet["sucursal_id"] = $sucursal_id;
                    $array_invmovdet["unidadmedida_id"] = $unidadmedida_id;
                    $array_invmovdet["invmovtipo_id"] = 2;
                    $array_invmovdet["cant"] = $aux_cantBodSD * -1;
                    $array_invmovdet["cantgrupo"] = $aux_cantBodSD * -1;
                    $array_invmovdet["cantxgrupo"] = 1;
                    $array_invmovdet["peso"] = $peso;
                    $array_invmovdet["cantkg"] = $cantkg * -1;
                    $array_invmovdet["invmov_id"] = $invmovSalDesp->id;
                    $invmovdet = InvMovDet::create($array_invmovdet);
                    $invmovdet_bodsoldesp = InvMovDet_BodSolDesp::create([
                        'invmovdet_id' => $invmovdet->id,
                        'despachosoldet_invbodegaproducto_id' => $despachosoldet_invbodegaproducto_id
                    ]);
                }
            }

            
            /************************************* */
            foreach($despachosol->despachosoldets as $despchosoldet){
                $sql = "SELECT *
                    FROM vista_sumorddespdet
                    WHERE despachosoldet_id=$despchosoldet->id;";
                $vista_sumorddespdet = DB::select($sql);
                if($vista_sumorddespdet){
                    $cantsoldespdev = 0;
                    if($despchosoldet->cantsoldesp > $vista_sumorddespdet['0']->cantdesp){
                        $cantsoldespdev = $despchosoldet->cantsoldesp - $vista_sumorddespdet['0']->cantdesp;
                    }
                }else{
                    $cantsoldespdev = $despchosoldet->cantsoldesp;
                }

                if($cantsoldespdev > 0){
                    $despchosoldet->cantsoldesp = $despchosoldet->cantsoldesp - $cantsoldespdev;
                    $despchosoldet->cantsoldespdev = $cantsoldespdev;
                    $despchosoldet->save();
                }
            }
            event(new CerrarSolDesp($despachosol,$request));
            return response()->json(['mensaje' => 'ok']);
        } else {
            abort(404);
        }
    }

    public function aproborddesp(Request $request)
    {
        if ($request->ajax()) {
            $despachosol = DespachoSol::findOrFail($request->id);
            if($despachosol == null){
                return response()->json([
                    'id' => 0,
                    'mensaje' => 'Registro fue eliminado previamente.',
                    'tipo_alert' => 'error'
                ]);
            }
            if($request->updated_at != $despachosol->updated_at){
                return response()->json([
                    'id' => 0,
                    'mensaje'=>'Registro fué modificado por otro usuario.',
                    'tipo_alert' => 'error'
                ]);
            }
            if($despachosol->despachosolanul == null){
                //VALIDAR SI LA SOLICITUD DE DESPACHO YA FUE ASIGNADA A UNA ORDEN DE DESPACHO Y QUE NO ESTE ANULADA
                $sql = "SELECT COUNT(*) AS cont
                    FROM despachosol INNER JOIN despachoord
                    ON despachosol.id=despachoord.despachosol_id
                    WHERE despachosol.id = $request->id
                    AND despachoord.id NOT IN (SELECT despachoordanul.despachoord_id FROM despachoordanul WHERE ISNULL(despachoordanul.deleted_at))
                    AND isnull(despachosol.deleted_at)
                    AND ISNULL(despachoord.deleted_at)";
                $cont = DB::select($sql);
                //if($despachosol->despachoords->count() == 0){
                if($cont[0]->cont == 0){
                    $invmodulo = InvMovModulo::where("cod","SOLDESP")->get();
                    $invmoduloBod = InvMovModulo::findOrFail($invmodulo[0]->id);
                    
                    if(count($invmodulo) == 0){
                        return response()->json([
                            'mensaje' => 'No existe modulo SOLDESP'
                        ]);
                    }

                    //ANTES DE PROCESAR SOLICITUD VALIDO QUE LOS PRODUCTOS INVOLUCRADOS TENGAS BODEGA DE PICKING CORRESPONDIENTE A LA SUCURSAL DE CADA PRODUCTO
                    //ESTO DEBE IR EL EL PROYECTO FINAL
                    $aux_arraysuc = [];
                    foreach ($despachosol->despachosoldets as $despachosoldet) {
                        foreach ($despachosoldet->despachosoldet_invbodegaproductos as $sddetbodprod) {
                            $aux_sucursal_id_producto = $sddetbodprod->invbodegaproducto->invbodega->sucursal_id; 
                            $aux_bodega_idPicking = 0;
                            foreach($invmoduloBod->invmovmodulobodents as $invmovmodulobodent){
                                //BUSCAR BODEGA DESPACHO CORRESPONDIENTE AL PRODUCTO QUE SE ESTA PROCESANDO DEPENDIENDO DE LA SUCURSAL QUE CORRESPONDE EL PRODUCTO
                                if($invmovmodulobodent->sucursal_id == $aux_sucursal_id_producto){
                                    $aux_arraysuc[] = $invmovmodulobodent->id;
                                    $aux_bodega_idPicking = $invmovmodulobodent->id;
                                }
                            }
                            if($aux_bodega_idPicking == 0){
                                return response()->json([
                                    'mensaje' => 'No existe Bodega Picking en Sucursal: ' . $despachosol->notaventa->sucursal->nombre
                                ]);
                            }
                        }
                    }
                    //ANTES DE PROCESAR SOLICITUD VALIDO QUE LOS PRODUCTOS INVOLUCRADOS TENGAS BODEGA DE PICKING CORRESPONDIENTE A LA SUCURSAL DE CADA PRODUCTO
                    //ESTO DEBE IR EL EL PROYECTO FINAL

                    //$despachosol = DespachoSol::findOrFail($request->id);
                    $aux_bandera = true;
                    foreach ($despachosol->despachosoldets as $despachosoldet) {
                        $aux_respuesta = InvBodegaProducto::validarExistenciaStock($despachosoldet->despachosoldet_invbodegaproductos);
                        if($aux_respuesta["bandera"] == false){
                            $aux_bandera = $aux_respuesta["bandera"];
                            break;
                        }
                    }
                    $aux_banderacant = false; //VALIDAR QUE EXISTE AL MENOS 1 PRODUCTO CON CANTIDAD
                    foreach ($despachosol->despachosoldets as $despachosoldet) {
                        foreach ($despachosoldet->despachosoldet_invbodegaproductos as $despachosoldet_invbodegaproducto){
                            $aux_cant = $despachosoldet_invbodegaproducto->cant * -1;
                            if($aux_cant > 0){
                                $aux_banderacant = true;
                                break;
                                /*
                                $aux_respuesta = InvBodegaProducto::existencia($despachosoldet_invbodegaproducto);
                                $aux_cantStock = $aux_respuesta['stock']['cant'];
                                if(($aux_cantStock > 0) and ($aux_cantStock <= $aux_cant)){
                                    $aux_banderacant = true;
                                    break;
                                }
                                */
                            }
                        }
                    }

                    if($aux_bandera){
                        if($aux_banderacant){
                            $invmov_array = array();
                            $invmov_array["fechahora"] = date("Y-m-d H:i:s");
                            $invmov_array["annomes"] = $aux_respuesta["annomes"];
                            $invmov_array["desc"] = "Salida de Bodega NV:" . $despachosol->notaventa_id . " SD:" . $request->id;
                            $invmov_array["obs"] = "Salida de Bodega por aprobacion NV:" . $despachosol->notaventa_id . " SD:" . $request->id;
                            $invmov_array["invmovmodulo_id"] = $invmodulo[0]->id; //Modulo Orden Despacho
                            $invmov_array["idmovmod"] = $request->id;
                            $invmov_array["invmovtipo_id"] = 2;
                            $invmov_array["sucursal_id"] = $despachosol->notaventa->sucursal_id;
                            $invmov_array["usuario_id"] = auth()->id();
                            $arrayinvmov_id = array();
                            
                            $invmov = InvMov::create($invmov_array);
                            array_push($arrayinvmov_id, $invmov->id);
                            foreach ($despachosol->despachosoldets as $despachosoldet) {
                                foreach ($despachosoldet->despachosoldet_invbodegaproductos as $oddetbodprod) {
                                    $aux_cant = $oddetbodprod->cant * -1;
                                    if($aux_cant > 0){
                                        $array_invmovdet = $oddetbodprod->attributesToArray();
                                        $array_invmovdet["producto_id"] = $oddetbodprod->invbodegaproducto->producto_id;
                                        $array_invmovdet["invbodega_id"] = $oddetbodprod->invbodegaproducto->invbodega_id;
                                        $array_invmovdet["sucursal_id"] = $oddetbodprod->invbodegaproducto->invbodega->sucursal_id;
                                        $array_invmovdet["unidadmedida_id"] = $despachosoldet->notaventadetalle->unidadmedida_id;
                                        $array_invmovdet["invmovtipo_id"] = 2;
                                        $array_invmovdet["cantgrupo"] = $array_invmovdet["cant"];
                                        $array_invmovdet["cantxgrupo"] = 1;
                                        $array_invmovdet["peso"] = $despachosoldet->notaventadetalle->producto->peso;
                                        $array_invmovdet["cantkg"] = ($despachosoldet->notaventadetalle->totalkilos / $despachosoldet->notaventadetalle->cant) * $array_invmovdet["cant"];
                                        $array_invmovdet["invmov_id"] = $invmov->id;
                                        $invmovdet = InvMovDet::create($array_invmovdet);
                                    }
                
                                }
                            }
                            $invmov_array = array();
                            $invmov_array["fechahora"] = date("Y-m-d H:i:s");
                            $invmov_array["annomes"] = $aux_respuesta["annomes"];
                            $invmov_array["desc"] = "Entrada a Bodega NV:" . $despachosol->notaventa_id . " SD:" . $request->id;
                            $invmov_array["obs"] = "Entrada a Bodega NV:" . $despachosol->notaventa_id . " SD:" . $request->id;
                            $invmov_array["invmovmodulo_id"] = $invmodulo[0]->id; //Modulo Orden Despacho
                            $invmov_array["idmovmod"] = $request->id;
                            $invmov_array["invmovtipo_id"] = 1;
                            $invmov_array["sucursal_id"] = $despachosol->notaventa->sucursal_id;
                            $invmov_array["usuario_id"] = auth()->id();
                            
                            $invmov = InvMov::create($invmov_array);
                            array_push($arrayinvmov_id, $invmov->id);
                            foreach ($despachosol->despachosoldets as $despachosoldet) {
                                foreach ($despachosoldet->despachosoldet_invbodegaproductos as $oddetbodprod) {
                                    $aux_cant = $oddetbodprod->cant * -1;
                                    if($aux_cant > 0){
                                        $aux_sucursal_id_producto = $oddetbodprod->invbodegaproducto->invbodega->sucursal_id; 
                                        foreach($invmoduloBod->invmovmodulobodents as $invmovmodulobodent){
                                            //BUSCAR BODEGA PICKING CORRESPONDIENTE AL PRODUCTO QUE SE ESTA PROCESANDO DEPENDIENDO DE LA SUCURSAL QUE CORRESPONDE EL PRODUCTO
                                            if($invmovmodulobodent->sucursal_id == $aux_sucursal_id_producto){
                                                $aux_bodega_idPicking = $invmovmodulobodent->id;
                                            }
                                        }            
                                        $invbodegaproducto = InvBodegaProducto::updateOrCreate(
                                            ['producto_id' => $oddetbodprod->invbodegaproducto->producto_id,'invbodega_id' => $aux_bodega_idPicking],
                                            [
                                                'producto_id' => $oddetbodprod->invbodegaproducto->producto_id,
                                                'invbodega_id' => $aux_bodega_idPicking
                                            ]
                                        );
                                        $array_invmovdet = $oddetbodprod->attributesToArray();
                                        $array_invmovdet["invbodegaproducto_id"] = $invbodegaproducto->id;
                                        $array_invmovdet["producto_id"] = $oddetbodprod->invbodegaproducto->producto_id;
                                        $array_invmovdet["invbodega_id"] = $aux_bodega_idPicking;
                                        $array_invmovdet["sucursal_id"] = $invbodegaproducto->invbodega->sucursal_id;
                                        $array_invmovdet["unidadmedida_id"] = $despachosoldet->notaventadetalle->unidadmedida_id;
                                        $array_invmovdet["invmovtipo_id"] = 1;
                                        $array_invmovdet["cant"] = $array_invmovdet["cant"] * -1;
                                        $array_invmovdet["cantgrupo"] = $array_invmovdet["cant"];
                                        $array_invmovdet["cantxgrupo"] = 1;
                                        $array_invmovdet["peso"] = $despachosoldet->notaventadetalle->producto->peso;
                                        $array_invmovdet["cantkg"] = ($despachosoldet->notaventadetalle->totalkilos / $despachosoldet->notaventadetalle->cant) * $array_invmovdet["cant"];
                                        $array_invmovdet["invmov_id"] = $invmov->id;
                                        $invmovdet = InvMovDet::create($array_invmovdet);
                                        $invmovdet_bodsoldesp = InvMovDet_BodSolDesp::create([
                                            'invmovdet_id' => $invmovdet->id,
                                            'despachosoldet_invbodegaproducto_id' => $oddetbodprod->id
                                            ]);
                                    }
                                }
                            }
                            //$despachosol->invmovs()->sync($arrayinvmov_id);
                            $despachosol_invmov = DespachoSol_InvMov::create([
                                    'despachosol_id' => $despachosol->id,
                                    'invmov_id' => $arrayinvmov_id[0]
                                ]);
                            $despachosol_invmov = DespachoSol_InvMov::create(
                                [
                                    'despachosol_id' => $despachosol->id,
                                    'invmov_id' => $arrayinvmov_id[1]
                                ]);
                            $aux_usuariodestino_id = NULL;
                            if($despachosol->notaventa->vendedor->persona->usuario){
                                $aux_usuariodestino_id = $despachosol->notaventa->vendedor->persona->usuario->id;
                            }
                            Event(new Notificacion( //ENVIO ARRAY CON LOS DATOS PARA CREAR LA NOTIFICACION
                                [
                                    'usuarioorigen_id' => auth()->id(),
                                    'usuariodestino_id' => $aux_usuariodestino_id,
                                    'vendedor_id' => $despachosol->notaventa->vendedor_id,
                                    'status' => 1,
                                    'nombretabla' => 'despachosol',
                                    'mensaje' => 'Nueva Solicitud Despacho Nro:'.$despachosol->id,
                                    'rutadestino' => 'notaventaconsulta',
                                    'tabla_id' => $despachosol->id,
                                    'accion' => 'Nueva Solicitud Despacho',
                                    'mensajetitle' => 'OD:'.$despachosol->id.' NV:'.$despachosol->notaventa_id,
                                    'icono' => 'fa fa-fw fa-male text-primary',
                                    'detalle' => "
                                        <p><b>Datos:</b></p>
                                        <ul>
                                            <li><b>PASO PREVIO A PREPARACION DE DESPACHO</b></li>
                                            <li><b>Nro. Nota Venta: </b> $despachosol->notaventa_id </li>
                                            <li><b>Nro. Solicitud Despacho: </b> $despachosol->id </li>
                                            <li><b>RUT:</b> " . $despachosol->notaventa->cliente->rut . "</li>
                                            <li><b>Razon Social:</b> " . $despachosol->notaventa->cliente->razonsocial . "</li>
                                            <li><b>Vendedor:</b> " . $despachosol->notaventa->vendedor->persona->nombre . " " . $despachosol->notaventa->vendedor->persona->apellido . "</li>
                                        </ul>                    
                                    "
                                ]
                            ));
                        }
                        $despachosol->aprorddesp = 1;
                        $despachosol->aprorddespfh = date("Y-m-d H:i:s");
        
                        if ($despachosol->save()) {
                            return response()->json([
                                'mensaje' => 'ok',
                                'id' => $request->id,
                                'nfila' => $request->nfila,
                            ]);
                        } else {
                            return response()->json(['mensaje' => 'ng']);
                        }
                    }else{
                        return response()->json([
                            'mensaje' => 'MensajePersonalizado',
                            'menper' => "Producto sin Stock,  ID: " . $aux_respuesta["producto_id"] . ", Nombre: " . $aux_respuesta["producto_nombre"] . ", Stock: " . $aux_respuesta["stock"]
                        ]);
                    }
                }else{
                    return response()->json(['mensaje' => 'hijo']);
                }
            }else{
                return response()->json([
                    'id' => 0,
                    'mensaje' => 'Registro fue anulado previamente.',
                    'tipo_alert' => 'error'
                ]);
            }
        } else {
            abort(404);
        }

/*
        if ($request->ajax()) {
            $despachosol = DespachoSol::findOrFail($request->id);
            //VALIDAR SI LA SOLICITUD DE DESPACHO YA FUE ASIGNADA A UNA ORDEN DE DESPACHO Y QUE NO ESTE ANULADA
            $sql = "SELECT COUNT(*) AS cont
                FROM despachosol INNER JOIN despachoord
                ON despachosol.id=despachoord.despachosol_id
                WHERE despachosol.id = $request->id
                AND despachoord.id NOT IN (SELECT despachoordanul.despachoord_id FROM despachoordanul WHERE ISNULL(despachoordanul.deleted_at))
                AND isnull(despachosol.deleted_at)
                AND ISNULL(despachoord.deleted_at)";
            $cont = DB::select($sql);
            //if($despachosol->despachoords->count() == 0){
            if($cont[0]->cont == 0){
                $despachosol->aprorddesp = 1;
                $despachosol->aprorddespfh = date("Y-m-d H:i:s");;
                if ($despachosol->save()) {
                    return response()->json(['mensaje' => 'ok']);
                } else {
                    return response()->json(['mensaje' => 'ng']);
                }
            }else{
                return response()->json(['mensaje' => 'hijo']);
            }
        } else {
            abort(404);
        }
        */
    }


    public function exportPdf($id,$stareport = '1')
    {
        if(can('ver-pdf-solicitud-despacho',false)){
            $despachosol = DespachoSol::findOrFail($id);
            $despachosoldets = $despachosol->despachosoldets()->get();
            $empresa = Empresa::orderBy('id')->get();
            $rut = number_format( substr ( $despachosol->notaventa->cliente->rut, 0 , -1 ) , 0, "", ".") . '-' . substr ( $despachosol->notaventa->cliente->rut, strlen($despachosol->notaventa->cliente->rut) -1 , 1 );
            if($stareport == '1'){
                if(env('APP_DEBUG')){
                    return view('despachosol.reporte', compact('despachosol','despachosoldets','empresa'));
                }
            
                $pdf = PDF::loadView('despachosol.reporte', compact('despachosol','despachosoldets','empresa'));
                //return $pdf->download('cotizacion.pdf');
                return $pdf->stream(str_pad($despachosol->notaventa->id, 5, "0", STR_PAD_LEFT) .' - '. $despachosol->notaventa->cliente->razonsocial . '.pdf');
            }else{
                if($stareport == '2'){
                    return view('despachosol.listado1', compact('despachosol','despachosoldets','empresa'));        
                    $pdf = PDF::loadView('despachosol.listado1', compact('despachosol','despachosoldets','empresa'));
                    //return $pdf->download('cotizacion.pdf');
                    return $pdf->stream(str_pad($despachosol->notaventa->id, 5, "0", STR_PAD_LEFT) .' - '. $despachosol->notaventa->cliente->razonsocial . '.pdf');
        
                }
            }
        }else{
            //return false;            
            $pdf = PDF::loadView('generales.pdfmensajesinacceso');
            return $pdf->stream("mensajesinacceso.pdf");
        }
    }

    public function vistaprevODPdf($id,$stareport = '1')
    {
        if(can('ver-pdf-vista-previa-orden-despacho',false)){
            $despachosol = DespachoSol::findOrFail($id);
            $despachosoldets = $despachosol->despachosoldets()->get();
            $empresa = Empresa::orderBy('id')->get();
            $rut = number_format( substr ( $despachosol->notaventa->cliente->rut, 0 , -1 ) , 0, "", ".") . '-' . substr ( $despachosol->notaventa->cliente->rut, strlen($despachosol->notaventa->cliente->rut) -1 , 1 );
            if($stareport == '1'){
                if(env('APP_DEBUG')){
                    return view('despachosol.vistaprevod', compact('despachosol','despachosoldets','empresa'));
                }
                $pdf = PDF::loadView('despachosol.vistaprevod', compact('despachosol','despachosoldets','empresa'));
                //return $pdf->download('cotizacion.pdf');
                return $pdf->stream(str_pad($despachosol->notaventa->id, 5, "0", STR_PAD_LEFT) .' - '. $despachosol->notaventa->cliente->razonsocial . '.pdf');
            }else{
                if($stareport == '2'){
                    return view('despachosol.listado1', compact('despachosol','despachosoldets','empresa'));        
                    $pdf = PDF::loadView('despachosol.listado1', compact('despachosol','despachosoldets','empresa'));
                    //return $pdf->download('cotizacion.pdf');
                    return $pdf->stream(str_pad($despachosol->notaventa->id, 5, "0", STR_PAD_LEFT) .' - '. $despachosol->notaventa->cliente->razonsocial . '.pdf');
                }
            }    
        }else{
            //return false;            
            $pdf = PDF::loadView('generales.pdfmensajesinacceso');
            return $pdf->stream("mensajesinacceso.pdf");
        }
    }


    //Reporte previo a la solicitud de Despacho, para saber como esta la nota de venta
    public function pdfSolDespPrev($id,$stareport = '1')
    {
        if(can('ver-pdf-vista-previa-solicitud-despacho',false)){
            $notaventa = NotaVenta::findOrFail($id);
            $notaventaDetalles = $notaventa->notaventadetalles()->get();
            $empresa = Empresa::orderBy('id')->get();
            $rut = number_format( substr ( $notaventa->cliente->rut, 0 , -1 ) , 0, "", ".") . '-' . substr ( $notaventa->cliente->rut, strlen($notaventa->cliente->rut) -1 , 1 );
            //dd($empresa[0]['iva']);
            if(env('APP_DEBUG')){
                return view('despachosol.reportesolprev', compact('notaventa','notaventaDetalles','empresa'));
            }
            $pdf = PDF::loadView('despachosol.reportesolprev', compact('notaventa','notaventaDetalles','empresa'));
            //return $pdf->download('cotizacion.pdf');
            return $pdf->stream(str_pad($notaventa->id, 5, "0", STR_PAD_LEFT) .' - '. $notaventa->cliente->razonsocial . '.pdf');
        }else{
            //return false;            
            $pdf = PDF::loadView('generales.pdfmensajesinacceso');
            return $pdf->stream("mensajesinacceso.pdf");
        }

    }

    public function pdfpendientesoldesp()
    {
        $request = new Request();
        $request->fechad = $_GET["fechad"];
        $request->fechah = $_GET["fechah"];
        $request->fechaestdesp = $_GET["fechaestdesp"];
        $request->rut = $_GET["rut"];
        $request->vendedor_id = $_GET["vendedor_id"];
        $request->oc_id = $_GET["oc_id"];
        $request->giro_id = $_GET["giro_id"];
        $request->areaproduccion_id = $_GET["areaproduccion_id"];
        $request->tipoentrega_id = $_GET["tipoentrega_id"];
        $request->notaventa_id = $_GET["notaventa_id"];
        $request->aprobstatus = $_GET["aprobstatus"];
        $request->comuna_id = $_GET["comuna_id"];
        $request->id = $_GET["id"];
        $request->producto_id = $_GET["producto_id"];
        $request->filtro = $_GET["filtro"];
        $request->aux_titulo = $_GET["aux_titulo"];

        $datas = consultasoldesp($request);

        $aux_fdesde= $request->fechad;
        if(empty($request->fechad)){
            $aux_fdesde= '  /  /    ';
        }
        $aux_fhasta= $request->fechah;

        $empresa = Empresa::orderBy('id')->get();
        $usuario = Usuario::findOrFail(auth()->id());
        $nombreAreaproduccion = "Todos";
        if($request->areaproduccion_id){
            $areaProduccion = AreaProduccion::findOrFail($request->areaproduccion_id);
            $nombreAreaproduccion=$areaProduccion->nombre;
        }
        $nombreGiro = "Todos";
        if($request->giro_id){
            $giro = Giro::findOrFail($request->giro_id);
            $nombreGiro=$giro->nombre;
        }

        $request->nombreVendedor = "Todos";
        if($request->vendedor_id){
            $vendedor = Vendedor::whereIn('vendedor.id', explode (",",$request->vendedor_id))
                        ->join('persona', 'vendedor.persona_id', '=', 'persona.id')
                        ->select([
                            'persona.nombre'
                        ])
                        ->pluck('persona.nombre')->toArray();
            //implode(",", $vendedor);
            $request->nombreVendedor = implode(",", $vendedor);
            //$request->nombreVendedor = $vendedor->persona->nombre;
        }

        //return armarReportehtml($request);
        if($datas){
                if(env('APP_DEBUG')){
                    return view('despachoord.listadosolpend', compact('datas','empresa','usuario','aux_fdesde','aux_fhasta','nombreAreaproduccion','nombreGiro','request'));
                }
                $pdf = PDF::loadView('despachoord.listadosolpend', compact('datas','empresa','usuario','aux_fdesde','aux_fhasta','nombreAreaproduccion','nombreGiro','request')); //->setPaper('a4', 'landscape');
                return $pdf->stream("soldespend.pdf");
        }else{
            dd('Ningún dato disponible en esta consulta.');
        } 
    }

    public function pdfnotaventapendiente()
    {
        $request = new Request();
        $request->fechad = $_GET["fechad"];
        $request->fechah = $_GET["fechah"];
        $request->rut = $_GET["rut"];
        $request->vendedor_id = $_GET["vendedor_id"];
        $request->oc_id = $_GET["oc_id"];
        $request->giro_id = $_GET["giro_id"];
        $request->areaproduccion_id = $_GET["areaproduccion_id"];
        $request->tipoentrega_id = $_GET["tipoentrega_id"];
        $request->notaventa_id = $_GET["notaventa_id"];
        $request->aprobstatus = $_GET["aprobstatus"];
        $request->comuna_id = $_GET["comuna_id"];
        $request->plazoentrega = $_GET["plazoentrega"];
        $request->filtro = $_GET["filtro"];
        $request->aux_titulo = $_GET["aux_titulo"];
        $request->numrep = $_GET["numrep"];
        $request->aux_sql = $_GET["aux_sql"];
        $request->aux_orden = $_GET["aux_orden"];
        $request->producto_id = $_GET["producto_id"];
        


        $datas = consulta($request,$request->aux_sql,$request->aux_orden);
        //dd($datas);
        $aux_fdesde= $request->fechad;
        if(empty($request->fechad)){
            $aux_fdesde= '  /  /    ';
        }
        $aux_fhasta= $request->fechah;

        $empresa = Empresa::orderBy('id')->get();
        $usuario = Usuario::findOrFail(auth()->id());
        $nombreAreaproduccion = "Todos";
        if($request->areaproduccion_id){
            $areaProduccion = AreaProduccion::findOrFail($request->areaproduccion_id);
            $nombreAreaproduccion=$areaProduccion->nombre;
        }
        $nombreGiro = "Todos";
        if($request->giro_id){
            $giro = Giro::findOrFail($request->giro_id);
            $nombreGiro=$giro->nombre;
        }

        //return armarReportehtml($request);
        if($datas){
            //return view('despachosol.reportenotaventapendiente', compact('datas','empresa','usuario','aux_fdesde','aux_fhasta','nombreAreaproduccion','nombreGiro','request'));
            if($request->numrep == 1){
                $pdf = PDF::loadView('despachosol.reportenotaventapendiente', compact('datas','empresa','usuario','aux_fdesde','aux_fhasta','nombreAreaproduccion','nombreGiro','request')); //->setPaper('a4', 'landscape');
                return $pdf->stream("reportenotaventapendiente.pdf");
            }
            if($request->numrep == 2){
                $pdf = PDF::loadView('despachosol.reportependientexclientenv', compact('datas','empresa','usuario','aux_fdesde','aux_fhasta','nombreAreaproduccion','nombreGiro','request')); //->setPaper('a4', 'landscape');
                return $pdf->stream("soldespend.pdf");
            }
            if($request->numrep == 3){
                $pdf = PDF::loadView('despachosol.reportependientexproductonv', compact('datas','empresa','usuario','aux_fdesde','aux_fhasta','nombreAreaproduccion','nombreGiro','request')); //->setPaper('a4', 'landscape');
                return $pdf->stream("reportependientexproductonv.pdf");
            }

        }else{
            dd('Ningún dato disponible en esta consulta.');
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

    public function guardarfechaed(Request $request)
    {
        can('guardar-solicitud-despacho');
        //dd($request);
        $despachosol = DespachoSol::findOrFail($request->id);
        if(count($despachosol->notaventa->notaventacerradas) == 0){
            $clibloq = ClienteBloqueado::where("cliente_id" , "=" ,$despachosol->notaventa->cliente_id)->get();
            if(count($clibloq) > 0){
                return [
                    'mensaje'=>'Registro no fue guardado. Cliente Bloqueado: ' . $clibloq[0]->descripcion ,
                    'tipo_alert' => 'error'
                ];
            }
            if($despachosol->updated_at == $request->updated_at){
                $dateInput = explode('/',$request->aux_fechaestdesp);
                $request["fechaestdesp"] = $dateInput[2].'-'.$dateInput[1].'-'.$dateInput[0];  
                $despachosol->fechaestdesp = $request->fechaestdesp;
                if($despachosol->save()){
                    return response()->json([
                        'error'=>'0',
                        'mensaje'=>'Registro actualizado con exito.',
                        'updated_at' => date('Y-m-d H:i:s', strtotime($despachosol->updated_at)),
                        'tipo_alert' => 'success'
                    ]);
                }else{
                    return response()->json([
                        'error'=>'1',
                        'mensaje'=>'Registro no fue actualizado.',
                        'tipo_alert' => 'error'
                    ]);
                }
            }else{
                return response()->json([
                    'error'=>'1',
                    'mensaje'=>'Registro no fue modificado. Registro Editado por otro usuario. Fecha Hora: '.$despachosol->updated_at,
                    'tipo_alert' => 'error'
                ]);
/*
                return redirect('despachosol')->with([
                                                        'mensaje'=>'Registro no fue modificado. Registro Editado por otro usuario. Fecha Hora: '.$despachosol->updated_at,
                                                        'tipo_alert' => 'alert-error'
                                                    ]);*/
            }
        }else{
            return response()->json([
                'error'=>'1',
                'mensaje'=>'Registro no fue Modificado. La nota de venta fue Cerrada. Observ: ' . $despachosol->notaventa->id,
                'tipo_alert' => 'error'
            ]);
/*
            return redirect('despachosol')->with([
                'mensaje'=>'Registro no fue Modificado. La nota de venta fue Cerrada. Observ: ' . $notaventacerrada[0]->observacion . ' Fecha: ' . date("d/m/Y h:i:s A", strtotime($notaventacerrada[0]->created_at)),
                'tipo_alert' => 'alert-error'
            ]);*/
        }

    }

}


function consulta($request,$aux_sql,$orden){
    if($orden==1){
        $aux_orden = "notaventadetalle.notaventa_id desc";
    }else{
        //$aux_orden = "notaventa.cliente_id,notaventa.comunaentrega_id";
        $aux_orden = "cliente.razonsocial,notaventa.comunaentrega_id";
        
    }
    if(empty($request->vendedor_id)){
        $user = Usuario::findOrFail(auth()->id());
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
        if(is_array($request->vendedor_id)){
            $aux_vendedorid = implode ( ',' , $request->vendedor_id);
        }else{
            $aux_vendedorid = $request->vendedor_id;
        }
        $vendedorcond = " notaventa.vendedor_id in ($aux_vendedorid) ";

        //$vendedorcond = "notaventa.vendedor_id='$request->vendedor_id'";
    }

    if(empty($request->fechad) or empty($request->fechah)){
        $aux_condFecha = " true";
    }else{
        $fecha = date_create_from_format('d/m/Y', $request->fechad);
        $fechad = date_format($fecha, 'Y-m-d')." 00:00:00";
        $fecha = date_create_from_format('d/m/Y', $request->fechah);
        $fechah = date_format($fecha, 'Y-m-d')." 23:59:59";
        $aux_condFecha = "notaventa.fechahora>='$fechad' and notaventa.fechahora<='$fechah'";
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
        $aux_condcomuna_id = " notaventa.comunaentrega_id in ($aux_comuna) ";
    }


    if(empty($request->plazoentrega)){
        $aux_condplazoentrega = " true";
    }else{
        $fecha = date_create_from_format('d/m/Y', $request->plazoentrega);
        $fechad = date_format($fecha, 'Y-m-d');
        $aux_condplazoentrega = "notaventa.plazoentrega='$fechad'";
    }
    //dd($aux_condplazoentrega);

    $aux_condproducto_id = " true";
    if(!empty($request->producto_id)){
        /*
        $aux_condproducto_id = str_replace(".","",$request->producto_id);
        $aux_condproducto_id = str_replace("-","",$aux_condproducto_id);
        $aux_condproducto_id = "notaventadetalle.producto_id='$aux_condproducto_id'";
        */

        $aux_codprod = explode(",", $request->producto_id);
        $aux_codprod = implode ( ',' , $aux_codprod);
        $aux_condproducto_id = "notaventadetalle.producto_id in ($aux_codprod)";
    }


    //$suma = DespachoSol::findOrFail(2)->despachosoldets->where('notaventadetalle_id',1);
    if($aux_sql==1){
        $sql = "SELECT notaventadetalle.notaventa_id as id,notaventa.fechahora,notaventa.cliente_id,notaventa.comuna_id,notaventa.comunaentrega_id,
        notaventa.oc_id,notaventa.anulada,cliente.rut,cliente.razonsocial,aprobstatus,visto,oc_file,
        comuna.nombre as comunanombre,
        vista_notaventatotales.cant,
        vista_notaventatotales.precioxkilo,
        vista_notaventatotales.totalkilos,
        vista_notaventatotales.subtotal,
        sum(if(areaproduccion.id=1,notaventadetalle.totalkilos,0)) AS pvckg,
        sum(if(areaproduccion.id=2,notaventadetalle.totalkilos,0)) AS cankg,
        sum(if(areaproduccion.id=1,notaventadetalle.subtotal,0)) AS pvcpesos,
        sum(if(areaproduccion.id=2,notaventadetalle.subtotal,0)) AS canpesos,
        sum(notaventadetalle.subtotal) AS totalps,
        (SELECT sum(kgsoldesp) as kgsoldesp
                FROM vista_sumsoldespdet
                WHERE notaventa_id=notaventa.id) as totalkgsoldesp,
        (SELECT sum(subtotalsoldesp) as subtotalsoldesp
                FROM vista_sumsoldespdet
                WHERE notaventa_id=notaventa.id) as totalsubtotalsoldesp,
        notaventa.inidespacho,notaventa.guiasdespacho,notaventa.findespacho,
        tipoentrega.nombre as tipentnombre,tipoentrega.icono
        FROM notaventa INNER JOIN notaventadetalle
        ON notaventa.id=notaventadetalle.notaventa_id and 
        if((SELECT cantsoldesp
                FROM vista_sumsoldespdet
                WHERE notaventadetalle_id=notaventadetalle.id
                ) >= notaventadetalle.cant,false,true)
        INNER JOIN producto
        ON notaventadetalle.producto_id=producto.id
        INNER JOIN categoriaprod
        ON categoriaprod.id=producto.categoriaprod_id
        INNER JOIN areaproduccion
        ON areaproduccion.id=categoriaprod.areaproduccion_id
        INNER JOIN cliente
        ON cliente.id=notaventa.cliente_id
        INNER JOIN comuna
        ON comuna.id=notaventa.comunaentrega_id
        INNER JOIN tipoentrega
        ON tipoentrega.id=notaventa.tipoentrega_id
        INNER JOIN vista_notaventatotales
        ON notaventa.id=vista_notaventatotales.id
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
        and $aux_condplazoentrega
        and $aux_condproducto_id
        and notaventa.anulada is null
        and notaventa.findespacho is null
        and notaventa.deleted_at is null and notaventadetalle.deleted_at is null
        and notaventa.id not in (select notaventa_id from notaventacerrada where isnull(notaventacerrada.deleted_at))
        GROUP BY notaventadetalle.notaventa_id,notaventa.fechahora,notaventa.cliente_id,notaventa.comuna_id,notaventa.comunaentrega_id,
        notaventa.oc_id,notaventa.anulada,cliente.rut,cliente.razonsocial,aprobstatus,visto,oc_file,
        notaventa.inidespacho,notaventa.guiasdespacho,notaventa.findespacho
        ORDER BY $aux_orden;";
    }
    if($aux_sql==2){
        //if(categoriaprod.unidadmedida_id=3,producto.diamextpg,producto.diamextmm) AS diametro,
        $sql = "SELECT notaventadetalle.producto_id,producto.nombre,
        producto.diametro,
        claseprod.cla_nombre,producto.long,producto.peso,producto.tipounion,
        cant,cantsoldesp,
        totalkilos,
        subtotal,
        kgsoldesp,subtotalsoldesp,
        sum(cant-if(isnull(cantsoldesp),0,cantsoldesp)) as saldocant,
        sum(totalkilos-if(isnull(kgsoldesp),0,kgsoldesp)) as saldokg,
        sum(subtotal-if(isnull(subtotalsoldesp),0,subtotalsoldesp)) as saldoplata
        FROM notaventadetalle INNER JOIN notaventa
        ON notaventadetalle.notaventa_id=notaventa.id
        INNER JOIN producto
        ON notaventadetalle.producto_id=producto.id
        INNER JOIN claseprod
        ON producto.claseprod_id=claseprod.id
        INNER JOIN categoriaprod
        ON producto.categoriaprod_id=categoriaprod.id
        INNER JOIN cliente
        ON cliente.id=notaventa.cliente_id
        LEFT JOIN vista_sumsoldespdet
        ON vista_sumsoldespdet.notaventadetalle_id=notaventadetalle.id
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
        and $aux_condplazoentrega
        and $aux_condproducto_id
        AND isnull(notaventa.findespacho)
        AND isnull(notaventa.anulada)
        AND isnull(notaventa.deleted_at) AND isnull(notaventadetalle.deleted_at)
        and notaventadetalle.notaventa_id not in (select notaventa_id from notaventacerrada where isnull(notaventacerrada.deleted_at))
        GROUP BY notaventadetalle.producto_id
        ORDER BY producto.nombre,producto.peso;";
    }
    

    if($aux_sql==3){
        $sql = "SELECT notaventadetalle.notaventa_id as id,notaventa.fechahora,notaventa.cliente_id,notaventa.comuna_id,notaventa.comunaentrega_id,
        notaventa.oc_id,notaventa.anulada,cliente.rut,cliente.razonsocial,aprobstatus,visto,oc_file,
        comuna.nombre as comunanombre,
        vista_notaventatotales.cant,
        vista_notaventatotales.precioxkilo,
        sum(vista_notaventatotales.totalkilos) as totalkilos,
        sum(vista_notaventatotales.subtotal) as subtotal,
        sum(if(areaproduccion.id=1,notaventadetalle.totalkilos,0)) AS pvckg,
        sum(if(areaproduccion.id=2,notaventadetalle.totalkilos,0)) AS cankg,
        sum(if(areaproduccion.id=1,notaventadetalle.subtotal,0)) AS pvcpesos,
        sum(if(areaproduccion.id=2,notaventadetalle.subtotal,0)) AS canpesos,
        sum(notaventadetalle.subtotal) AS totalps,
        sum((SELECT sum(kgsoldesp) as kgsoldesp
                FROM vista_sumsoldespdet
                WHERE notaventa_id=notaventa.id)) as totalkgsoldesp,
        sum((SELECT sum(subtotalsoldesp) as subtotalsoldesp
                FROM vista_sumsoldespdet
                WHERE notaventa_id=notaventa.id)) as totalsubtotalsoldesp,
        notaventa.inidespacho,notaventa.guiasdespacho,notaventa.findespacho,
        tipoentrega.nombre as tipentnombre,tipoentrega.icono
        FROM notaventa INNER JOIN notaventadetalle
        ON notaventa.id=notaventadetalle.notaventa_id and 
        if((SELECT cantsoldesp
                FROM vista_sumsoldespdet
                WHERE notaventadetalle_id=notaventadetalle.id
                ) >= notaventadetalle.cant,false,true)
        INNER JOIN producto
        ON notaventadetalle.producto_id=producto.id
        INNER JOIN categoriaprod
        ON categoriaprod.id=producto.categoriaprod_id
        INNER JOIN areaproduccion
        ON areaproduccion.id=categoriaprod.areaproduccion_id
        INNER JOIN cliente
        ON cliente.id=notaventa.cliente_id
        INNER JOIN comuna
        ON comuna.id=notaventa.comunaentrega_id
        INNER JOIN tipoentrega
        ON tipoentrega.id=notaventa.tipoentrega_id
        INNER JOIN vista_notaventatotales
        ON notaventa.id=vista_notaventatotales.id
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
        and $aux_condplazoentrega
        and $aux_condproducto_id
        and notaventa.anulada is null
        and notaventa.findespacho is null
        and notaventa.deleted_at is null and notaventadetalle.deleted_at is null
        and notaventa.id not in (select notaventa_id from notaventacerrada where isnull(notaventacerrada.deleted_at))
        GROUP BY notaventa.cliente_id
        ORDER BY $aux_orden;";
        //dd($sql);
    }

    $datas = DB::select($sql);
    //dd($datas);
    return $datas;
}

function reporte1($request){
    $respuesta = array();
    $respuesta['exito'] = false;
    $respuesta['mensaje'] = "Código no Existe";
    $respuesta['tabla'] = "";
    $respuesta['tabla2'] = "";
    $respuesta['tabla3'] = "";

    if($request->ajax()){
        $datas = consulta($request,1,1);
        $aux_colvistoth = "";
        if(auth()->id()==1 or auth()->id()==2){
            $aux_colvistoth = "<th class='tooltipsC' title='Leido'>Leido</th>";
        }
        $aux_colvistoth = "<th class='tooltipsC' title='Leido'>Leido</th>";

        $respuesta['tabla'] .= "<table id='tabla-data-listar1' name='tabla-data-listar1' class='table display AllDataTables table-hover table-condensed' data-page-length='50'>
        <thead>
            <tr>
                <th class='tooltipsC' title='Nota de Venta PDF'>NV</th>
                <th>Fecha</th>
                <th>Razón Social</th>
                <th class='tooltipsC' title='Orden de Compra'>OC</th>
                <th class='tooltipsC' title='Precio x Kg'>$ x Kg</th>
                <th>Comuna</th>
                <th style='text-align:right' class='tooltipsC' title='Kg Pendiente'>Kg Pend</th>
                <th style='text-align:right' class='tooltipsC' title='$ Pendiente'>$ Pend</th>
                <!--<th style='text-align:right' class='tooltipsC' title='Precio Promedio x Kg'>Prom</th>-->
                <th class='tooltipsC' title='Solicitud Despacho'>Despacho</th>
            </tr>
        </thead>
        <tbody>";

        $i = 0;
        $aux_Tpvckg = 0;
        $aux_Tpvcpesos= 0;
        $aux_Tcankg = 0;
        $aux_Tcanpesos = 0;
        $aux_totalKG = 0;
        $aux_totalps = 0;
        $aux_prom = 0;
        foreach ($datas as $data) {
            $colorFila = "";
            $aux_data_toggle = "";
            $aux_title = "";

            $rut = number_format( substr ( $data->rut, 0 , -1 ) , 0, "", ".") . '-' . substr ( $data->rut, strlen($data->rut) -1 , 1 );
            $prompvc = 0;
            $promcan = 0;
            $aux_prom = 0;
            if($data->pvckg!=0){
                $prompvc = $data->pvcpesos / $data->pvckg;
            }
            if($data->cankg!=0){
                $promcan = $data->canpesos / $data->cankg;
            }
            if($data->totalkilos>0){
                $aux_prom = $data->subtotal / $data->totalkilos;
            }

            $Visto       = $data->visto;
            $checkVisto  = 'checked';
            if(empty($data->visto))
                $checkVisto = '';

            $aux_colvistotd = "";
            if(empty($data->visto)){
                $fechavisto = '';
            }else{
                $fechavisto = 'Leido:' . date('d-m-Y h:i:s A', strtotime($data->visto));
            }
            
            $aux_colvistotd = "
            <td class='tooltipsC' style='text-align:center' class='tooltipsC' title='$fechavisto'>
                <div class='checkbox'>
                    <label style='font-size: 1.2em'>";
                    if(!empty($data->anulada)){
                        $aux_colvistotd .= "<input type='checkbox' id='visto$i' name='visto$i' value='$Visto' $checkVisto disabled>";
                    }else{
                        if(auth()->id()==1 or auth()->id()==2){
                            $aux_colvistotd .= "<input type='checkbox' id='visto$i' name='visto$i' value='$Visto' $checkVisto onclick='visto($data->id,$i)'>";
                        }else{
                            $aux_colvistotd .= "<input type='checkbox' id='visto$i' name='visto$i' value='$Visto' $checkVisto disabled>";
                        }
                    }
                    $aux_colvistotd .= "<span class='cr'><i class='cr-icon fa fa-check'></i></span>
                    </label>
                </div>
            </td>";
            if(empty($data->oc_file)){
                $aux_enlaceoc = $data->oc_id;
            }else{
                $aux_enlaceoc = "<a onclick='verpdf2(\"$data->oc_file\",2)'>$data->oc_id</a>";
            }
            $nuevoSolDesp = "<a class='btn-accion-tabla btn-sm tooltipsC' title='Vista Previa SD' onclick='pdfSolDespPrev($data->id,2)'>
                                <i class='fa fa-fw fa-file-pdf-o'></i>                                    
                            </a>";
            $clibloq = ClienteBloqueado::where("cliente_id" , "=" ,$data->cliente_id)->get();
            if(count($clibloq) > 0){
                $aux_descbloq = $clibloq[0]->descripcion;
                $nuevoSolDesp .= "<a class='btn-accion-tabla tooltipsC' title='Cliente Bloqueado: $aux_descbloq'>
                                    <i class='fa fa-fw fa-lock text-danger'></i>
                                </a>";
            }else{
                $ruta_nuevoSolDesp = route('crearsol_despachosol', ['id' => $data->id]);
                $nuevoSolDesp .= "<a href='$ruta_nuevoSolDesp' class='btn-accion-tabla tooltipsC' title='Hacer solicitud despacho: $data->tipentnombre'>
                    <i class='fa fa-fw $data->icono'></i>
                    </a>";
            }
            if(!empty($data->anulada)){
                $colorFila = 'background-color: #87CEEB;';
                $aux_data_toggle = "tooltip";
                $aux_title = "Anulada Fecha:" . $data->anulada;
                $nuevoSolDesp = "";
            }
            $aux_kgpend = $data->totalkilos - $data->totalkgsoldesp;
            $aux_dinpend = $data->subtotal - $data->totalsubtotalsoldesp;
            $respuesta['tabla'] .= "
            <tr id='fila$i' name='fila$i' style='$colorFila' title='$aux_title' data-toggle='$aux_data_toggle' class='btn-accion-tabla tooltipsC'>
                <td id='id$i' name='id$i'>
                    <a class='btn-accion-tabla btn-sm tooltipsC' title='Nota de Venta PDF' onclick='genpdfNV($data->id,1)'>
                        $data->id
                    </a>
                </td>
                <td id='fechahora$i' name='fechahora$i' data-order='$data->fechahora'>" . date('d-m-Y', strtotime($data->fechahora)) . "</td>
                <td id='razonsocial$i' name='razonsocial$i'>$data->razonsocial</td>
                <td id='oc_id$i' name='oc_id$i'>$aux_enlaceoc</td>
                <td>
                    <a class='btn-accion-tabla btn-sm tooltipsC' title='Precio x Kg' onclick='genpdfNV($data->id,2)'>
                        <i class='fa fa-fw fa-file-pdf-o'></i>                                    
                    </a>
                </td>
                <td>$data->comunanombre</td>
                <td class='kgpend' data-order='$aux_kgpend' data-search='$aux_kgpend' style='text-align:right'>".number_format($aux_kgpend, 2, ",", ".") ."</td>
                <td class='dinpend' data-order='$aux_dinpend' data-search='$aux_dinpend' style='text-align:right'>".number_format($aux_dinpend, 0, ",", ".") ."</td>
                <td>
                    $nuevoSolDesp
                </td>
            </tr>";

            if(empty($data->anulada)){
                $aux_Tpvckg += $data->pvckg;
                $aux_Tpvcpesos += $data->pvcpesos;
                $aux_Tcankg += $data->cankg;
                $aux_Tcanpesos += $data->canpesos;
                $aux_totalKG += ($aux_kgpend);
                $aux_totalps += ($aux_dinpend);    
            }


            //dd($data->contacto);
        }

        $aux_promGeneral = 0;
        if($aux_totalKG>0){
            $aux_promGeneral = $aux_totalps / $aux_totalKG;
        }
        $respuesta['tabla'] .= "
        </tbody>
        <tfoot>
            <tr>
                <th colspan='6' style='text-align:right'>Total página</th>
                <th id='totalkg' name='totalkg' style='text-align:right'>0,00</th>
                <th id='totaldinero' name='totaldinero' style='text-align:right'>0,00</th>
                <th style='text-align:right'></th>
            </tr>

            <tr>
                <th colspan='6' style='text-align:right'>TOTAL GENERAL</th>
                <th style='text-align:right'>". number_format($aux_totalKG, 2, ",", ".") ."</th>
                <th style='text-align:right'>". number_format($aux_totalps, 0, ",", ".") ."</th>
                <!--<th style='text-align:right'>". number_format($aux_promGeneral, 2, ",", ".") ."</th>-->
                <th style='text-align:right'></th>
            </tr>
        </tfoot>

        </table>";

        return $respuesta;
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
            $ruta_nuevoOrdDesp = route('crearord_despachoord', ['id' => $data->id]);
            //dd($ruta_nuevoSolDesp);

            $clibloq = ClienteBloqueado::where("cliente_id" , "=" ,$data->cliente_id)->get();
            if(count($clibloq) > 0){
                $aux_descbloq = $clibloq[0]->descripcion;
                $nuevoOrdDesp = "<a class='btn-accion-tabla tooltipsC' title='Cliente Bloqueado: $aux_descbloq'>
                                    <button type='button' class='btn btn-default btn-xs' disabled>
                                        <i class='fa fa-fw fa-lock text-danger'></i>
                                    </button>
                                </a>";
            }else{
                $ruta_nuevoSolDesp = route('crearsol_despachosol', ['id' => $data->id]);
                $nuevoOrdDesp = "<a href='$ruta_nuevoOrdDesp' class='btn-accion-tabla tooltipsC' title='Hacer orden despacho: $data->tipentnombre'>
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
                $aux_bancDevSolDesp = true;
                $despachosol = DespachoSol::findOrFail($data->id);
                foreach ($despachosol->despachosoldets as $despachosoldet) {
                    $aux_cant = 0;
                    foreach ($despachosoldet->despachosoldet_invbodegaproductos as $despachosoldet_invbodegaproducto) {
                        $aux_cant += $despachosoldet_invbodegaproducto->cant + $despachosoldet_invbodegaproducto->cantex;
                    }
                    if($despachosoldet->cantsoldesp != ($aux_cant * -1)){
                        $nuevoOrdDesp .= "<a href='/despachosol/cerrarsoldesp' fila='$i' id='btnanular$i' name='btnanular$i' class='btn-accion-tabla tooltipsC btncerrarsol' title='Cerrar Solicitud Despacho' data-toggle='tooltip'>
                        <button type='button' class='btn btn-danger btn-xs'><i class='fa fa-fw fa-archive'></i></button>
                        </a>";
                        $aux_bancDevSolDesp = false;
                        break;
                    }
                }
                if($aux_bancDevSolDesp){
                    $nuevoOrdDesp .= "<a href='/despachosol/devolversoldesp' fila='$i' id='btndevsol$i' name='btndevsol$i' class='btn-accion-tabla btn-sm tooltipsC btndevsol' title='Devolver Solicitud Despacho' data-toggle='tooltip'>
                                        <button type='button' class='btn btn-warning btn-xs'><i class='fa fa-fw fa-reply'></i></button>
                                    </a>";
                }

            }else{
                $nuevoOrdDesp .= "<a href='/despachosol/cerrarsoldesp' fila='$i' id='btnanular$i' name='btnanular$i' class='btn-accion-tabla tooltipsC btncerrarsol' title='Cerrar Solicitud Despacho' data-toggle='tooltip'>
                                    <button type='button' class='btn btn-danger btn-xs'><i class='fa fa-fw fa-archive'></i></button>
                                </a>";
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
                <td id='fechahora$i' name='fechahora$i'>" . date('d-m-Y', strtotime($data->fechahora)) . "</td>
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

function reportesoldespcerrarNV1($request){
    $respuesta = array();
    $respuesta['exito'] = false;
    $respuesta['mensaje'] = "Código no Existe";
    $respuesta['tabla'] = "";
    $respuesta['tabla2'] = "";
    $respuesta['tabla3'] = "";

    if($request->ajax()){
        $datas = consulta($request,1,1);
        $aux_colvistoth = "";
        if(auth()->id()==1 or auth()->id()==2){
            $aux_colvistoth = "<th class='tooltipsC' title='Leido'>Leido</th>";
        }
        $aux_colvistoth = "<th class='tooltipsC' title='Leido'>Leido</th>";

        $respuesta['tabla'] .= "<table id='tabla-data-listar' name='tabla-data-listar' class='table display AllDataTables table-hover table-condensed tablascons' data-page-length='10'>
        <thead>
            <tr>
                <th>ID</th>
                <th>Fecha</th>
                <th>Razón Social</th>
                <th class='tooltipsC' title='Orden de Compra'>OC</th>
                <th class='tooltipsC' title='Nota de Venta'>NV</th>
                <th class='tooltipsC' title='Precio x Kg'>$ x Kg</th>
                <th>Comuna</th>
            </tr>
        </thead>
        <tbody>";

        $i = 0;
        $aux_Tpvckg = 0;
        $aux_Tpvcpesos= 0;
        $aux_Tcankg = 0;
        $aux_Tcanpesos = 0;
        $aux_totalKG = 0;
        $aux_totalps = 0;
        $aux_prom = 0;
        foreach ($datas as $data) {
            $colorFila = "";
            $aux_data_toggle = "";
            $aux_title = "";
            
            if(empty($data->oc_file)){
                $aux_enlaceoc = $data->oc_id;
            }else{
                $aux_enlaceoc = "<a onclick='verpdf2(\"$data->oc_file\",2)'>$data->oc_id</a>";
            }
            $nuevoSolDesp = "<a class='btn-accion-tabla btn-sm tooltipsC' title='Vista Previa SD' onclick='pdfSolDespPrev($data->id,2)'>
                                <i class='fa fa-fw fa-file-pdf-o'></i>                                    
                            </a>";
            $clibloq = ClienteBloqueado::where("cliente_id" , "=" ,$data->cliente_id)->get();
            if(count($clibloq) > 0){
                $aux_descbloq = $clibloq[0]->descripcion;
                $nuevoSolDesp .= "<a class='btn-accion-tabla tooltipsC' title='Cliente Bloqueado: $aux_descbloq'>
                                    <i class='fa fa-fw fa-lock text-danger'></i>
                                </a>";
            }else{
                $ruta_nuevoSolDesp = route('crearsol_despachosol', ['id' => $data->id]);
                $nuevoSolDesp .= "<a href='$ruta_nuevoSolDesp' class='btn-accion-tabla tooltipsC' title='Hacer solicitud despacho: $data->tipentnombre'>
                    <i class='fa fa-fw $data->icono'></i>
                    </a>";
            }
            if(!empty($data->anulada)){
                $colorFila = 'background-color: #87CEEB;';
                $aux_data_toggle = "tooltip";
                $aux_title = "Anulada Fecha:" . $data->anulada;
                $nuevoSolDesp = "";
            }

            $respuesta['tabla'] .= "
            <tr id='fila$i' name='fila$i' style='$colorFila' title='$aux_title' data-toggle='$aux_data_toggle' class='btn-accion-tabla tooltipsC'>
                <td id='id$i' name='id$i'>
                    <a href='#' class='copiar_notaventaid' onclick='copiar_notaventaid($data->id)'> $data->id </a>
                </td>
                <td id='fechahora$i' name='fechahora$i'>" . date('d-m-Y', strtotime($data->fechahora)) . "</td>
                <td id='razonsocial$i' name='razonsocial$i'>$data->razonsocial</td>
                <td id='oc_id$i' name='oc_id$i'>$aux_enlaceoc</td>
                <td>
                    <a class='btn-accion-tabla btn-sm tooltipsC' title='Nota de Venta' onclick='genpdfNV($data->id,1)'>
                        <i class='fa fa-fw fa-file-pdf-o'></i>$data->id
                    </a>
                </td>
                <td>
                    <a class='btn-accion-tabla btn-sm tooltipsC' title='Precio x Kg' onclick='genpdfNV($data->id,2)'>
                        <i class='fa fa-fw fa-file-pdf-o'></i>                                    
                    </a>
                </td>
                <td>$data->comunanombre</td>
            </tr>";

            if(empty($data->anulada)){
                $aux_Tpvckg += $data->pvckg;
                $aux_Tpvcpesos += $data->pvcpesos;
                $aux_Tcankg += $data->cankg;
                $aux_Tcanpesos += $data->canpesos;
                $aux_totalKG += ($data->totalkilos - $data->totalkgsoldesp);
                $aux_totalps += ($data->subtotal - $data->totalsubtotalsoldesp);    
            }


            //dd($data->contacto);
        }

        $aux_promGeneral = 0;
        if($aux_totalKG>0){
            $aux_promGeneral = $aux_totalps / $aux_totalKG;
        }
        $respuesta['tabla'] .= "
        </tbody>
        </table>";

        return $respuesta;
    }

}

function consultasoldesp($request){
    if(empty($request->vendedor_id)){
        $user = Usuario::findOrFail(auth()->id());
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
        if(is_array($request->vendedor_id)){
            $aux_vendedorid = implode ( ',' , $request->vendedor_id);
        }else{
            $aux_vendedorid = $request->vendedor_id;
        }
        $vendedorcond = " notaventa.vendedor_id in ($aux_vendedorid) ";

        //$vendedorcond = "notaventa.vendedor_id='$request->vendedor_id'";
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
            comuna.nombre as comunanombre,
            despachosol.notaventa_id,despachosol.fechaestdesp,tipoentrega.nombre as tipentnombre,tipoentrega.icono,
            IFNULL(vista_despordxdespsoltotales.totalkilos,0) as totalkilosdesp,
            IFNULL(vista_despordxdespsoltotales.subtotal,0) as subtotaldesp,
            vista_despsoltotales.totalkilos,
            vista_despsoltotales.subtotalsoldesp,despachosol.updated_at
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
            and notaventa.id not in (select notaventa_id from notaventacerrada where isnull(notaventacerrada.deleted_at))
            and isnull(despachosol.deleted_at) AND isnull(notaventa.deleted_at) AND isnull(notaventadetalle.deleted_at)
            and isnull(despachosoldet.deleted_at)
            GROUP BY despachosol.id
            ORDER BY despachosol.id DESC;";
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

    $sql = "SELECT despachosol.id,despachosol.fechahora,cliente.razonsocial,notaventa.oc_id,notaventa.oc_file,despachosol.notaventa_id,
    '' as notaventaxk,comuna.nombre as comuna_nombre,
    tipoentrega.nombre as tipoentrega_nombre,tipoentrega.icono,clientebloqueado.descripcion as clientebloqueado_descripcion,
    SUM(despachosoldet.cantsoldesp * (notaventadetalle.totalkilos / notaventadetalle.cant)) as aux_totalkg,
    despachosol.updated_at
    FROM despachosol INNER JOIN notaventa
    ON despachosol.notaventa_id = notaventa.id AND ISNULL(despachosol.deleted_at) and isnull(notaventa.deleted_at)
    INNER JOIN cliente
    ON cliente.id = notaventa.cliente_id AND isnull(cliente.deleted_at)
    INNER JOIN comuna
    ON comuna.id = despachosol.comunaentrega_id AND isnull(comuna.deleted_at)
    INNER JOIN despachosoldet
    ON despachosoldet.despachosol_id = despachosol.id AND ISNULL(despachosoldet.deleted_at)
    INNER JOIN notaventadetalle
    ON notaventadetalle.id = despachosoldet.notaventadetalle_id AND ISNULL(notaventadetalle.deleted_at)
    INNER JOIN tipoentrega
    ON tipoentrega.id = despachosol.tipoentrega_id AND ISNULL(tipoentrega.deleted_at)
    LEFT JOIN clientebloqueado
    ON clientebloqueado.cliente_id = notaventa.cliente_id AND ISNULL(clientebloqueado.deleted_at)
    WHERE ISNULL(despachosol.aprorddesp)
    AND despachosol.id NOT IN (SELECT despachosolanul.despachosol_id FROM despachosolanul WHERE ISNULL(despachosolanul.deleted_at))
    AND despachosol.notaventa_id NOT IN (SELECT notaventacerrada.notaventa_id FROM notaventacerrada WHERE ISNULL(notaventacerrada.deleted_at))
    AND notaventa.sucursal_id in ($sucurcadena)
    GROUP BY despachosoldet.despachosol_id;";

    return DB::select($sql);

}

function arraystocks($request){
    //CONTROLAR EL STOCK AGRUPADO POR invbodegaproducto_id
    //ESTO ES CUANDO SE REPITE EL CODIGO DE PRODUCTO EN LA MISMA BODEGA
    $aux_arraystocks = array();
    foreach ($request->invbodegaproducto_id as $i) {
        $aux_arraystocks[$i] = [
                                "invbodegaproducto_id" => $i,
                                "stock" => 0,
                                "cant" => 0
                            ];
    }
    for($i = 0; $i < count($request->invbodegaproducto_id); $i++){
        $aux_arraystocks[$request->invbodegaproducto_id[$i]]["cant"] += $request->invcant[$i];
    }
    foreach ($aux_arraystocks as $i => $aux_arraystock) {
        $aux_existencia = InvBodegaProducto::existencia($aux_arraystock);
        $aux_arraystocks[$i]["stock"] = $aux_existencia["stock"]["cant"];
    }
    return $aux_arraystocks;
}
                    