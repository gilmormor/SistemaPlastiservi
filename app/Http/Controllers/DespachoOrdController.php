<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidarDespachoOrd;
use App\Models\AreaProduccion;
use App\Models\CategoriaProd;
use App\Models\Cliente;
use App\Models\ClienteSucursal;
use App\Models\ClienteVendedor;
use App\Models\Comuna;
use App\Models\DespachoOrd;
use App\Models\DespachoOrdAnul;
use App\Models\DespachoOrdDet;
use App\Models\DespachoSol;
use App\Models\Empresa;
use App\Models\FormaPago;
use App\Models\Giro;
use App\Models\PlazoPago;
use App\Models\Seguridad\Usuario;
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
        $despachoordanul = DespachoOrdAnul::select(['despachoord_id'])->get();
        $datas = DespachoOrd::orderBy('id')
                ->whereNull('guiadespacho')->whereNull('numfactura')
                ->whereNotIn('id',  $despachoordanul)
                ->get();
        return view('despachoord.index', compact('datas'));
    }

    

    public function indexguia()
    {
        can('listar-orden-despacho');
        $despachoordanul = DespachoOrdAnul::select(['despachoord_id'])->get();
        $datas = DespachoOrd::orderBy('id')
                        ->whereNull('guiadespacho')
                        ->whereNotIn('id',  $despachoordanul)
                        ->get();
        $aux_vista = 'G';
        return view('despachoord.indexguiafact', compact('datas','aux_vista'));
    }

    public function indexfact()
    {
        can('listar-orden-despacho');
        $despachoordanul = DespachoOrdAnul::select(['despachoord_id'])->get();
        $datas = DespachoOrd::orderBy('id')
                        ->whereNotNull('guiadespacho')
                        ->whereNull('numfactura')
                        ->whereNotIn('id',  $despachoordanul)
                        ->get();
        $aux_vista = 'F';
        return view('despachoord.indexguiafact', compact('datas','aux_vista'));
    }

        public function indexcompletado()
    {
        can('listar-orden-despacho');
        $despachoordanul = DespachoOrdAnul::select(['despachoord_id'])->get();
        $datas = DespachoOrd::orderBy('id')
                        ->whereNotNull('guiadespacho')
                        ->whereNotNull('numfactura')
                        ->whereNotIn('id',  $despachoordanul)
                        ->get();
        $aux_vista = 'C';
        return view('despachoord.indexguiafact', compact('datas','aux_vista'));
    }

    public function listards() //Listar solicitudes de despacho
    {
        $user = Usuario::findOrFail(auth()->id());
        $sql= 'SELECT COUNT(*) AS contador
            FROM vendedor INNER JOIN persona
            ON vendedor.persona_id=persona.id
            INNER JOIN usuario 
            ON persona.usuario_id=usuario.id
            WHERE usuario.id=' . auth()->id();
        $counts = DB::select($sql);
        $vendedor_id = '0';
        if($counts[0]->contador>0){
            $vendedor_id=$user->persona->vendedor->id;
            $clientevendedorArray = ClienteVendedor::where('vendedor_id',$vendedor_id)->pluck('cliente_id')->toArray();
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
                ->where('vendedor.id','=',$vendedor_id)
                ->get();
        }else{
            $clientevendedorArray = ClienteVendedor::pluck('cliente_id')->toArray();
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
        }
        $sucurArray = $user->sucursales->pluck('id')->toArray();
        //* Filtro solos los clientes que esten asignados a la sucursal y asignado al vendedor logueado*/
        $clientes = Cliente::select(['cliente.id','cliente.rut','cliente.razonsocial','cliente.direccion','cliente.telefono'])
        ->whereIn('cliente.id' , ClienteSucursal::select(['cliente_sucursal.cliente_id'])
                                ->whereIn('cliente_sucursal.sucursal_id', $sucurArray)
        ->pluck('cliente_sucursal.cliente_id')->toArray())
        ->whereIn('cliente.id',$clientevendedorArray)
        ->get();
        $vendedores = Vendedor::orderBy('id')->where('sta_activo',1)->get();

        $giros = Giro::orderBy('id')->get();
        $areaproduccions = AreaProduccion::orderBy('id')->get();
        $tipoentregas = TipoEntrega::orderBy('id')->get();
        $fechaAct = date("d/m/Y");

        /*
        $request = [
            'fechad'            => '',
            'fechah'            => '',
            'rut'               => '',
            'vendedor_id'       => '',
            'oc_id'             => '',
            'giro_id'           => '',
            'areaproduccion_id' => '',
            'tipoentrega_id'    => '',
            'notaventa_id'      => '',
            'aprobstatus'       => ''
        ];
        $respuesta = reporte1($request);
        */ 

        return view('despachoord.listardespachosol', compact('clientes','vendedores','vendedores1','giros','areaproduccions','tipoentregas','fechaAct'));

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
        $data = DespachoSol::findOrFail($id);
        $data->plazoentrega = $newDate = date("d/m/Y", strtotime($data->plazoentrega));
        $data->fechaestdesp = $newDate = date("d/m/Y", strtotime($data->fechaestdesp));
        $detalles = $data->despachosoldets()->get();
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

        $user = Usuario::findOrFail(auth()->id());
        $sucurArray = $user->sucursales->pluck('id')->toArray();
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

        $users = Usuario::findOrFail(auth()->id());
        $sucurArray = $users->sucursales->pluck('id')->toArray();
        //Filtrando las categorias por sucursal, dependiendo de las sucursales asignadas al usuario logueado
        //******************* */
        $productos = CategoriaProd::join('categoriaprodsuc', 'categoriaprod.id', '=', 'categoriaprodsuc.categoriaprod_id')
        ->join('sucursal', 'categoriaprodsuc.sucursal_id', '=', 'sucursal.id')
        ->join('producto', 'categoriaprod.id', '=', 'producto.categoriaprod_id')
        ->join('claseprod', 'producto.claseprod_id', '=', 'claseprod.id')
        ->select([
                'producto.id',
                'producto.nombre',
                'claseprod.cla_nombre',
                'producto.codintprod',
                'producto.diamextmm',
                'producto.espesor',
                'producto.long',
                'producto.peso',
                'producto.tipounion',
                'producto.precioneto',
                'categoriaprod.precio',
                'categoriaprodsuc.sucursal_id'
                ])
                ->whereIn('categoriaprodsuc.sucursal_id', $sucurArray)
                ->get();
        //****************** */
        $clientevendedorArray = ClienteVendedor::where('vendedor_id',$vendedor_id)->pluck('cliente_id')->toArray();
        //* Filtro solos los clientes que esten asignados a la sucursal */
        $clientes = Cliente::select(['cliente.id','cliente.rut','cliente.razonsocial','cliente.direccion','cliente.telefono','cliente.giro_id'])
        ->whereIn('cliente.id' , ClienteSucursal::select(['cliente_sucursal.cliente_id'])
                                ->whereIn('cliente_sucursal.sucursal_id', $sucurArray)
        ->pluck('cliente_sucursal.cliente_id')->toArray())
        ->whereIn('cliente.id',$clientevendedorArray)
        ->get();

        //dd($clientes);
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

        $sql= 'SELECT COUNT(*) AS contador
            FROM vendedor INNER JOIN persona
            ON vendedor.persona_id=persona.id
            INNER JOIN usuario 
            ON persona.usuario_id=usuario.id
            WHERE usuario.id=' . auth()->id();
        $counts = DB::select($sql);
        $vendedor_id = '0';
        if($counts[0]->contador>0){
            $vendedor_id=$user->persona->vendedor->id;
        }
        //dd($clientedirecs);
        return view('despachoord.crear', compact('data','clienteselec','clientes','clienteDirec','clientedirecs','detalles','comunas','formapagos','plazopagos','vendedores','vendedores1','productos','fecha','empresa','tipoentregas','giros','sucurArray','aux_sta','aux_cont','aux_statusPant','vendedor_id'));
        
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
        //dd(count($request->producto_id));
        
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
        return redirect('despachoord/index')->with('mensaje','Nota de Venta creada con exito.'); 
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
        $data->plazoentrega = $newDate = date("d/m/Y", strtotime($data->plazoentrega));
        $data->fechaestdesp = $newDate = date("d/m/Y", strtotime($data->fechaestdesp));
        $detalles = $data->despachoorddets()->get();
        //dd($detalles);
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

        $users = Usuario::findOrFail(auth()->id());
        $sucurArray = $users->sucursales->pluck('id')->toArray();
        //Filtrando las categorias por sucursal, dependiendo de las sucursales asignadas al usuario logueado
        //******************* */
        $productos = CategoriaProd::join('categoriaprodsuc', 'categoriaprod.id', '=', 'categoriaprodsuc.categoriaprod_id')
        ->join('sucursal', 'categoriaprodsuc.sucursal_id', '=', 'sucursal.id')
        ->join('producto', 'categoriaprod.id', '=', 'producto.categoriaprod_id')
        ->join('claseprod', 'producto.claseprod_id', '=', 'claseprod.id')
        ->select([
                'producto.id',
                'producto.nombre',
                'claseprod.cla_nombre',
                'producto.codintprod',
                'producto.diamextmm',
                'producto.espesor',
                'producto.long',
                'producto.peso',
                'producto.tipounion',
                'producto.precioneto',
                'categoriaprod.precio',
                'categoriaprodsuc.sucursal_id'
                ])
                ->whereIn('categoriaprodsuc.sucursal_id', $sucurArray)
                ->get();
        //****************** */
        $clientevendedorArray = ClienteVendedor::where('vendedor_id',$vendedor_id)->pluck('cliente_id')->toArray();
        //* Filtro solos los clientes que esten asignados a la sucursal */
        $clientes = Cliente::select(['cliente.id','cliente.rut','cliente.razonsocial','cliente.direccion','cliente.telefono','cliente.giro_id'])
        ->whereIn('cliente.id' , ClienteSucursal::select(['cliente_sucursal.cliente_id'])
                                ->whereIn('cliente_sucursal.sucursal_id', $sucurArray)
        ->pluck('cliente_sucursal.cliente_id')->toArray())
        ->whereIn('cliente.id',$clientevendedorArray)
        ->get();

        //dd($clientes);
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

        $sql= 'SELECT COUNT(*) AS contador
            FROM vendedor INNER JOIN persona
            ON vendedor.persona_id=persona.id
            INNER JOIN usuario 
            ON persona.usuario_id=usuario.id
            WHERE usuario.id=' . auth()->id();
        $counts = DB::select($sql);
        $vendedor_id = '0';
        if($counts[0]->contador>0){
            $vendedor_id=$user->persona->vendedor->id;
        }
        //dd($clientedirecs);
        return view('despachoord.editar', compact('data','clienteselec','clientes','clienteDirec','clientedirecs','detalles','comunas','formapagos','plazopagos','vendedores','vendedores1','productos','fecha','empresa','tipoentregas','giros','sucurArray','aux_sta','aux_cont','aux_statusPant','vendedor_id'));
  
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
        //dd($request);
        $dateInput = explode('/',$request->plazoentrega);
        $request["plazoentrega"] = $dateInput[2].'-'.$dateInput[1].'-'.$dateInput[0];
        $dateInput = explode('/',$request->fechaestdesp);
        $request["fechaestdesp"] = $dateInput[2].'-'.$dateInput[1].'-'.$dateInput[0];
        $despachoord = DespachoOrd::findOrFail($id);
        $despachoord->comunaentrega_id = $request->comunaentrega_id;
        $despachoord->tipoentrega_id = $request->tipoentrega_id;
        $despachoord->plazoentrega = $request->plazoentrega;
        $despachoord->lugarentrega = $request->lugarentrega;
        $despachoord->contacto = $request->contacto;
        $despachoord->contactoemail = $request->contactoemail;
        $despachoord->contactotelf = $request->contactotelf;
        $despachoord->observacion = $request->observacion;
        $despachoord->fechaestdesp = $request->fechaestdesp;
        //dd($request);
        if($despachoord->save()){
            $cont_producto = count($request->producto_id);
            if($cont_producto>0){
                for ($i=0; $i < $cont_producto ; $i++){
                    if(is_null($request->producto_id[$i])==false && is_null($request->cantord[$i])==false){
                        $despachoorddet = DespachoOrdDet::findOrFail($request->NVdet_id[$i]);
                        $despachoorddet->cantord = $request->cantord[$i];
                        if($despachoorddet->save()){
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
        return redirect('despachoord/index')->with('mensaje','Orden de Despacho actualizada con exito.');
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
            if(empty($despachoord->guiadespacho) and empty($despachoord->numfactura)){
                $despachoordanul = new DespachoOrdAnul();
                $despachoordanul->despachoord_id = $request->id;
                $despachoordanul->usuario_id = auth()->id();
                if ($despachoordanul->save()) {
                    return response()->json(['mensaje' => 'ok']);
                } else {
                    return response()->json(['mensaje' => 'ng']);
                }
            }else{
                return response()->json(['mensaje' => 'guidesp_factura']);
            }
        } else {
            abort(404);
        }
    }

    public function guardarguiadesp(Request $request)
    {
        if ($request->ajax()) {
            //dd($request);
            $despachoord = DespachoOrd::findOrFail($request->id);
            $despachoord->guiadespacho = $request->guiadespacho;
            $despachoord->guiadespachofec = date("Y-m-d H:i:s");
            if ($despachoord->save()) {
                return response()->json(['mensaje' => 'ok']);
            } else {
                return response()->json(['mensaje' => 'ng']);
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
            $despachoord->numfactura = $request->numfactura;
            $despachoord->numfacturafec = date("Y-m-d H:i:s");
            if ($despachoord->save()) {
                return response()->json(['mensaje' => 'ok']);
            } else {
                return response()->json(['mensaje' => 'ng']);
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
                                        'despachoord' => $despachoord
                                        ]);
            } else {
                return response()->json(['mensaje' => 'ng']);
            }
        } else {
            abort(404);
        }
    }

    public function reporte(Request $request){
        $respuesta = reporte1($request);
        return $respuesta;
    }

    public function exportPdf($id,$stareport = '1')
    {
        $despachoord = DespachoOrd::findOrFail($id);
        //dd($despachoord);
        $despachoorddets = $despachoord->despachoorddets()->get();
        //dd($despachoorddets);
        $empresa = Empresa::orderBy('id')->get();
        $rut = number_format( substr ( $despachoord->notaventa->cliente->rut, 0 , -1 ) , 0, "", ".") . '-' . substr ( $despachoord->notaventa->cliente->rut, strlen($despachoord->notaventa->cliente->rut) -1 , 1 );
        //dd($empresa[0]['iva']);
        if($stareport == '1'){
            //return view('despachoord.reporte', compact('despachoord','despachoorddets','empresa'));
        
            $pdf = PDF::loadView('despachoord.reporte', compact('despachoord','despachoorddets','empresa'));
            //return $pdf->download('cotizacion.pdf');
            return $pdf->stream(str_pad($despachoord->notaventa->id, 5, "0", STR_PAD_LEFT) .' - '. $despachoord->notaventa->cliente->razonsocial . '.pdf');
        }else{
            if($stareport == '2'){
                return view('despachoord.listado1', compact('despachoord','despachoorddets','empresa'));        
                $pdf = PDF::loadView('despachoord.listado1', compact('despachoord','despachoorddets','empresa'));
                //return $pdf->download('cotizacion.pdf');
                return $pdf->stream(str_pad($despachoord->notaventa->id, 5, "0", STR_PAD_LEFT) .' - '. $despachoord->notaventa->cliente->razonsocial . '.pdf');
    
            }
        }
    }

}

function reporte1($request){
    $respuesta = array();
    $respuesta['exito'] = false;
    $respuesta['mensaje'] = "Código no Existe";
    $respuesta['tabla'] = "";

    if($request->ajax()){
        $datas = consulta($request);

        $respuesta['tabla'] .= "<table id='tablacotizacion' name='tablacotizacion' class='table display AllDataTables table-hover table-condensed tablascons' data-page-length='50'>
        <thead>
            <tr>
                <th>ID</th>
                <th>Fecha</th>
                <th class='tooltipsC' title='Fecha Estimada de Despacho'>Fecha ED</th>
                <th>RUT</th>
                <th>Razón Social</th>
                <th class='tooltipsC' title='Solicitud de Despacho'>SD</th>
                <th class='tooltipsC' title='Orden de Compra'>OC</th>
                <th class='tooltipsC' title='Nota de Venta'>NV</th>
                <th class='tooltipsC' title='Total Kg'>Total Kg</th>
                <th class='tooltipsC' title='Orden Despacho'>Despacho</th>
            </tr>
        </thead>
        <tbody>";

        $i = 0;
        foreach ($datas as $data) {

            $rut = number_format( substr ( $data->rut, 0 , -1 ) , 0, "", ".") . '-' . substr ( $data->rut, strlen($data->rut) -1 , 1 );
            if(empty($data->oc_file)){
                $aux_enlaceoc = $data->oc_id;
            }else{
                $aux_enlaceoc = "<a onclick='verpdf2(\"$data->oc_file\",2)'>$data->oc_id</a>";
            }
            $ruta_nuevoOrdDesp = route('crearord_despachoord', ['id' => $data->id]);
            //dd($ruta_nuevoSolDesp);
            $respuesta['tabla'] .= "
            <tr id='fila$i' name='fila$i' class='btn-accion-tabla tooltipsC'>
                <td id='id$i' name='id$i'>$data->id
                </td>
                <td id='fechahora$i' name='fechahora$i'>" . date('d-m-Y', strtotime($data->fechahora)) . "</td>
                <td id='fechaestdesp$i' name='fechaestdesp$i'>" . date('d-m-Y', strtotime($data->fechaestdesp)) . "</td>
                <td id='rut$i' name='rut$i'>$rut</td>
                <td id='razonsocial$i' name='razonsocial$i'>$data->razonsocial</td>
                <td>
                    <a class='btn-accion-tabla btn-sm tooltipsC' title='Solicitud de Despacho' onclick='genpdfSD($data->id,1)'>
                        <i class='fa fa-fw fa-file-pdf-o'></i>
                    </a>
                </td>
                <td id='oc_id$i' name='oc_id$i'>$aux_enlaceoc</td>
                <td>
                    <a class='btn-accion-tabla btn-sm tooltipsC' title='Nota de Venta' onclick='genpdfNV($data->notaventa_id,1)'>
                    <i class='fa fa-fw fa-file-pdf-o'></i>$data->notaventa_id
                    </a>
                </td>
                <td style='text-align:right'>".
                    number_format($data->totalkilos, 2, ",", ".") .
                "</td>
                <td>
                    <a href='$ruta_nuevoOrdDesp' class='btn-accion-tabla tooltipsC' title='Hacer Orden Despacho'>
                        <i class='fa fa-fw fa-truck'></i>
                    </a>

                </td>
            </tr>";

            //dd($data->contacto);
        }

        $respuesta['tabla'] .= "
        </tbody>
        </table>";
        return $respuesta;
    }

}


function consulta($request){
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

    //$suma = DespachoSol::findOrFail(2)->despachosoldets->where('notaventadetalle_id',1);

    $sql = "SELECT despachosol.id,despachosol.fechahora,cliente.rut,cliente.razonsocial,notaventa.oc_id,notaventa.oc_file,
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
            WHERE $vendedorcond
            and $aux_condFecha
            and $aux_condrut
            and $aux_condoc_id
            and $aux_condgiro_id
            and $aux_condareaproduccion_id
            and $aux_condtipoentrega_id
            and $aux_condnotaventa_id
            and $aux_aprobstatus
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