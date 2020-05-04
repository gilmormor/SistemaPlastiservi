<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidarCotizacion;
use App\Http\Requests\ValidarNotaVenta;
use App\Models\CategoriaProd;
use App\Models\Cliente;
use App\Models\ClienteDirec;
use App\Models\ClienteSucursal;
use App\Models\ClienteVendedor;
use App\Models\Comuna;
use App\Models\Cotizacion;
use App\Models\CotizacionDetalle;
use App\Models\Empresa;
use App\Models\FormaPago;
use App\Models\Giro;
use App\Models\NotaVenta;
use App\Models\NotaVentaDetalle;
use App\Models\PlazoPago;
use App\Models\Seguridad\Usuario;
use App\Models\SucursalClienteDirec;
use App\Models\TipoEntrega;
use App\Models\Vendedor;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use SplFileInfo;
use Barryvdh\DomPDF\Facade as PDF;

class NotaVentaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-notaventa');
        //session(['aux_aproNV' => '0']) 0=Pantalla Normal CRUD de Nota de Venta
        //session(['aux_aproNV' => '1']) 1=Pantalla Solo para aprobar Nota de Venta para luego emitir Guia de Despacho
        session(['aux_aproNV' => '0']);
        $user = Usuario::findOrFail(auth()->id());
        $sql= 'SELECT COUNT(*) AS contador
        FROM vendedor INNER JOIN persona
        ON vendedor.persona_id=persona.id and vendedor.deleted_at is null
        INNER JOIN usuario 
        ON persona.usuario_id=usuario.id and persona.deleted_at is null
        WHERE usuario.id=' . auth()->id() . ';';
        $counts = DB::select($sql);
        if($counts[0]->contador>0){
            $vendedor_id=$user->persona->vendedor->id;
            $aux_condvend = 'notaventa.vendedor_id = ' . $vendedor_id;
            $aux_condvendcot = 'cotizacion.vendedor_id = ' . $vendedor_id;
        }else{
            $aux_condvend = 'true';
            $aux_condvendcot = 'true';
        }

        //Se consultan los registros que estan sin aprobar por vendedor null o 0 y los rechazados por el supervisor rechazado por el supervisor=4
        $sql = 'SELECT notaventa.id,notaventa.fechahora,notaventa.cotizacion_id,razonsocial,aprobstatus,aprobobs, 
                    (SELECT COUNT(*) 
                    FROM notaventadetalle 
                    WHERE notaventadetalle.notaventa_id=notaventa.id and 
                    notaventadetalle.precioxkilo < notaventadetalle.precioxkiloreal) AS contador
                FROM notaventa inner join cliente
                on notaventa.cliente_id = cliente.id
                where ' . $aux_condvend . 
                ' and anulada is null ' .
                ' and (aprobstatus is null or aprobstatus=0 or aprobstatus=4) 
                and notaventa.deleted_at is null;';
        //where usuario_id='.auth()->id();
        //dd($sql);
        $datas = DB::select($sql);

        //Se consultan los registros que estan sin aprobar por vendedor null o 0 y los rechazados por el supervisor rechazado por el supervisor=4
        $sql = 'SELECT cotizacion.id,cotizacion.fechahora,razonsocial,aprobstatus,aprobobs,total, 
                    (SELECT COUNT(*) 
                    FROM cotizaciondetalle 
                    WHERE cotizaciondetalle.cotizacion_id=cotizacion.id and 
                    cotizaciondetalle.precioxkilo < cotizaciondetalle.precioxkiloreal) AS contador
                FROM cotizacion inner join cliente
                on cotizacion.cliente_id = cliente.id
                where ' . $aux_condvendcot . ' and (aprobstatus=1 or aprobstatus=3) and 
                cotizacion.id not in (SELECT cotizacion_id from notaventa WHERE !(cotizacion_id is NULL) and (anulada is null))
                and cotizacion.deleted_at is null;';
        //where usuario_id='.auth()->id();
        //dd($sql);
        $cotizaciones = DB::select($sql);
        $aux_statusPant = 0; //Estatus para validar loq ue se muestra en la pantalla
        
        //dd($cotizaciones);
        //$datas = Cotizacion::where('usuario_id',auth()->id())->get();
        return view('notaventa.index', compact('datas','cotizaciones','aux_statusPant'));
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function crear()
    {
        can('crear-notaventa');
        $user = Usuario::findOrFail(auth()->id());
        //$vendedor_id=$user->persona->vendedor->id;
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
        }else{
            $clientevendedorArray = ClienteVendedor::pluck('cliente_id')->toArray();
        }
        //dd($vendedor_id);
        //dd($sucurArray);
        //$clientevendedorArray = ClienteVendedor::where('vendedor_id',$vendedor_id)->pluck('cliente_id')->toArray();
    
        //* Filtro solos los clientes que esten asignados a la sucursal y asignado al vendedor logueado*/
        $sucurArray = $user->sucursales->pluck('id')->toArray();
        $clientes = Cliente::select(['cliente.id','cliente.rut','cliente.razonsocial','cliente.direccion','cliente.telefono'])
        ->whereIn('cliente.id' , ClienteSucursal::select(['cliente_sucursal.cliente_id'])
                                ->whereIn('cliente_sucursal.sucursal_id', $sucurArray)
        ->pluck('cliente_sucursal.cliente_id')->toArray())
        ->whereIn('cliente.id',$clientevendedorArray)
        ->get();
        $fecha = date("d/m/Y");
        $formapagos = FormaPago::orderBy('id')->get();
        $plazopagos = PlazoPago::orderBy('id')->get();
        $vendedores = Vendedor::orderBy('id')->where('sta_activo',1)->get();
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
                'producto.diamextpg',
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
        //dd($clientedirecs);
        $empresa = Empresa::findOrFail(1);
        $tipoentregas = TipoEntrega::orderBy('id')->get();
        $giros = Giro::orderBy('id')->get();
        $aux_sta=1;
        $aux_statusPant='0';
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
        //dd($vendedor_id);
        return view('notaventa.crear',compact('formapagos','plazopagos','vendedores','vendedores1','fecha','comunas','productos','clientes','empresa','tipoentregas','vendedor_id','giros','sucurArray','aux_sta','aux_statusPant'));
    }

    public function crearcot($id)
    {
        can('editar-notaventa');
        $data = Cotizacion::findOrFail($id);
        //dd($data);
        $data->plazoentrega = $newDate = date("d/m/Y", strtotime($data->plazoentrega));;
        $detalles = $data->cotizaciondetalles()->get();
        /*$detalles = $data->cotizaciondetalles()
                    ->whereColumn('cotizaciondetalle.cantusada', '<', 'cotizaciondetalle.cant')
                    ->get();*/
        $vendedor_id=$data->vendedor_id;
        $clienteselec = $data->cliente()->get();
        //dd($clienteselec);

        $user = Usuario::findOrFail(auth()->id());
        $sucurArray = $user->sucursales->pluck('id')->toArray();
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
                                'cliente.direccion',
                                'cliente.vendedor_id',
                                'cliente.regionp_id',
                                'cliente.provinciap_id',
                                'cliente.comunap_id',
                                'cliente.contactonombre',
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
        $users = Usuario::findOrFail(auth()->id());

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
/*
        $clientes = Cliente::select(['cliente.id','cliente.rut','cliente.razonsocial','cliente.direccion','cliente.telefono','cliente.giro_id'])
        ->whereIn('cliente.id' , 
                    ClienteDirec::select(['clientedirec.cliente_id'])
                    ->whereIn('clientedirec.id', SucursalClienteDirec::select(['sucursalclientedirec.clientedirec_id'])
                                                 ->whereIn('sucursalclientedirec.sucursal_id', $sucurArray))
        ->pluck('clientedirec.cliente_id')->toArray())
        ->whereIn('cliente.id',$clientevendedorArray)
        ->get();
*/
        //dd($clientes);

        $empresa = Empresa::findOrFail(1);
        $tipoentregas = TipoEntrega::orderBy('id')->get();
        $giros = Giro::orderBy('id')->get();
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

        $aux_sta=3;
        session(['aux_aproNV' => '1']);
        
        $aux_statusPant = 0;
        //dd($aux_aproNV);
        return view('notaventa.crear', compact('data','clienteselec','clientedirecs','clientes','clienteDirec','clientedirecs','detalles','comunas','formapagos','plazopagos','vendedores','vendedores1','productos','fecha','empresa','tipoentregas','giros','sucurArray','aux_sta','aux_cont','aux_statusPant'));


    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function guardar(ValidarNotaVenta $request)
    {
        can('guardar-notaventa');
        //dd($request);
        
        $hoy = date("Y-m-d H:i:s");
        $request->request->add(['fechahora' => $hoy]);
        $dateInput = explode('/',$request->plazoentrega);
        $request["plazoentrega"] = $dateInput[2].'-'.$dateInput[1].'-'.$dateInput[0];
        $comuna = Comuna::findOrFail($request->comuna_id);
        $request->request->add(['provincia_id' => $comuna->provincia_id]);
        $request->request->add(['region_id' => $comuna->provincia->region_id]);
        //dd($request);
        $notaventa = NotaVenta::create($request->all());
        $notaventaid = $notaventa->id;
        if ($foto = NotaVenta::setFotonotaventa($request->oc_file,$notaventaid,$request)){
            $request->request->add(['oc_file' => $foto]);
            $data = NotaVenta::findOrFail($notaventaid);
            $data->oc_file = $foto;
            $data->save();
        }
        $cont_producto = count($request->producto_id);
        if($cont_producto>0){
            for ($i=0; $i < $cont_producto ; $i++){
                if(is_null($request->producto_id[$i])==false && is_null($request->cant[$i])==false){
                    $notaventadetalle = new NotaVentaDetalle();
                    $notaventadetalle->notaventa_id = $notaventaid;
                    $notaventadetalle->producto_id = $request->producto_id[$i];
                    $notaventadetalle->cotizaciondetalle_id = $request->cotizaciondetalle_id[$i];                    
                    $notaventadetalle->cant = $request->cant[$i];
                    $notaventadetalle->unidadmedida_id = $request->unidadmedida_id[$i];
                    $notaventadetalle->descuento = $request->descuento[$i];
                    $notaventadetalle->preciounit = $request->preciounit[$i];
                    $notaventadetalle->precioxkilo = $request->precioxkilo[$i];
                    $notaventadetalle->precioxkiloreal = $request->precioxkiloreal[$i];
                    $notaventadetalle->totalkilos = $request->totalkilos[$i];
                    $notaventadetalle->subtotal = $request->subtotal[$i];
                    $notaventadetalle->save();
                    $idDireccion = $notaventadetalle->id;
                }
            }
        }
        return redirect('notaventa')->with('mensaje','Nota de Venta creada con exito.');
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
        can('editar-notaventa');
        $data = NotaVenta::findOrFail($id);
        $data->plazoentrega = $newDate = date("d/m/Y", strtotime($data->plazoentrega));;
        $detalles = $data->notaventadetalles()->get();
        $vendedor_id=$data->vendedor_id;
        $clienteselec = $data->cliente()->get();
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
        $clienteDirec = $data->clientedirec()->get();
        $fecha = date("d/m/Y", strtotime($data->fechahora));
        $formapagos = FormaPago::orderBy('id')->get();
        $plazopagos = PlazoPago::orderBy('id')->get();
        $vendedores = Vendedor::orderBy('id')->get();
        $comunas = Comuna::orderBy('id')->get();
        $users = Usuario::findOrFail(auth()->id());

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
        //dd($clientedirecs);
        return view('notaventa.editar', compact('data','clienteselec','clientes','clienteDirec','clientedirecs','detalles','comunas','formapagos','plazopagos','vendedores','vendedores1','productos','fecha','empresa','tipoentregas','giros','sucurArray','aux_sta','aux_cont','aux_statusPant'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function actualizar(ValidarCotizacion $request, $id)
    {
        can('guardar-notaventa');
        //dd($request);
        $notaventa = NotaVenta::findOrFail($id);
        $request->request->add(['fechahora' => $notaventa->fechahora]);
        $aux_plazoentrega= DateTime::createFromFormat('d/m/Y', $request->plazoentrega)->format('Y-m-d');
        $request->request->add(['plazoentrega' => $aux_plazoentrega]);
        //dd($request->plazoentrega);
        $notaventaid = $id;

        $notaventa->update($request->all());
        if ($foto = NotaVenta::setFotonotaventa($request->oc_file,$notaventaid,$request)){
            $request->request->add(['oc_file' => $foto]);
            $data = NotaVenta::findOrFail($notaventaid);
            $data->oc_file = $foto;
            $data->save();
        }

        $auxNVDet=NotaVentaDetalle::where('notaventa_id',$id)->whereNotIn('id', $request->NVdet_id)->pluck('id')->toArray(); //->destroy();
        for ($i=0; $i < count($auxNVDet) ; $i++){
            NotaVentaDetalle::destroy($auxNVDet[$i]);
        }
        $cont_cotdet = count($request->NVdet_id);
        if($cont_cotdet>0){
            for ($i=0; $i < count($request->NVdet_id) ; $i++){
                $idcotizaciondet = $request->NVdet_id[$i]; 
                if( $request->NVdet_id[$i] == '0' ){
                    $notaventadetalle = new NotaVentaDetalle();
                    $notaventadetalle->notaventa_id = $notaventaid;
                    $notaventadetalle->producto_id = $request->producto_id[$i];
                    //$notaventadetalle->cotizaciondetalle_id = $request->cotizaciondetalle_id[$i];
                    $notaventadetalle->cant = $request->cant[$i];
                    $notaventadetalle->unidadmedida_id = $request->unidadmedida_id[$i];
                    $notaventadetalle->descuento = $request->descuento[$i];
                    $notaventadetalle->preciounit = $request->preciounit[$i];
                    $notaventadetalle->precioxkilo = $request->precioxkilo[$i];
                    $notaventadetalle->precioxkiloreal = $request->precioxkiloreal[$i];
                    $notaventadetalle->totalkilos = $request->totalkilos[$i];
                    $notaventadetalle->subtotal = $request->subtotal[$i];
                    $notaventadetalle->save();
                    $idcotizaciondet = $notaventadetalle->id;

                    //dd($idDireccion);
                }else{
                    //dd($idDireccion);
                    DB::table('notaventadetalle')->updateOrInsert(
                        ['id' => $request->NVdet_id[$i], 'notaventa_id' => $id],
                        [
                            'producto_id' => $request->producto_id[$i],
                            'cotizaciondetalle_id' => $request->cotizaciondetalle_id[$i],
                            'cant' => $request->cant[$i],
                            'unidadmedida_id' => $request->unidadmedida_id[$i],
                            'descuento' => $request->descuento[$i],
                            'preciounit' => $request->preciounit[$i],
                            'precioxkilo' => $request->precioxkilo[$i],
                            'precioxkiloreal' => $request->precioxkiloreal[$i],
                            'totalkilos' => $request->totalkilos[$i],
                            'subtotal' => $request->subtotal[$i],
                        ]
                    );
                }
            }
        }
        return redirect('notaventa/'.$id.'/editar')->with('mensaje','Cliente actualizado con exito!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function eliminar(Request $request,$id)
    {
        can('eliminar-notaventa');
        //dd($request);
        if ($request->ajax()) {
            //dd($id);
            if (Cotizacion::destroy($id)) {
                //Despues de eliminar actualizo el campo usuariodel_id=usuario que elimino el registro
                $cotizacion = Cotizacion::withTrashed()->findOrFail($id);
                $cotizacion->usuariodel_id = auth()->id();
                $cotizacion->save();
                //Eliminar detalle de cotizacion
                CotizacionDetalle::where('cotizacion_id', $id)->update(['usuariodel_id' => auth()->id()]);
                CotizacionDetalle::where('cotizacion_id', '=', $id)->delete();
                return response()->json(['mensaje' => 'ok']);
            } else {
                return response()->json(['mensaje' => 'ng']);
            }
        } else {
            abort(404);
        }
    }

    public function eliminarCotizacionDetalle(Request $request)
    {
        //can('eliminar-cotizacionDetalle');
        if ($request->ajax()) {
            if (CotizacionDetalle::destroy($request->id)) {
                return response()->json(['mensaje' => 'ok']);
            } else {
                return response()->json(['mensaje' => 'ng']);
            }
        } else {
            abort(404);
        }
    }

    public function buscarCli(Request $request){
        if($request->ajax()){
            $clientedirecs = Cliente::where('rut', $request->rut)
                    ->join('clientedirec', 'cliente.id', '=', 'clientedirec.cliente_id')
                    ->select([
                                'cliente.razonsocial',
                                'cliente.telefono',
                                'cliente.email',
                                'cliente.direccionprinc',
                                'cliente.vendedor_id',
                                'clientedirec.id',
                                'clientedirec.direccion',
                                'clientedirec.comuna_id'
                            ]);
            //dd($clientedirecs->get());
            return response()->json($clientedirecs->get());
        }
    }

    public function aprobarnotaventa(Request $request)
    {
        //dd($request);
        can('guardar-notaventa');
        if ($request->ajax()) {
            $notaventa = NotaVenta::findOrFail($request->id);
            $notaventa->aprobstatus = $request->aprobstatus;    
            if($request->aprobstatus=='1'){
                $notaventa->aprobusu_id = auth()->id();
                $notaventa->aprobfechahora = date("Y-m-d H:i:s");
                $notaventa->aprobobs = 'Aprobado por el mismo vendedor';
            }
            if ($notaventa->save()) {
                return response()->json(['mensaje' => 'ok']);
            } else {
                return response()->json(['mensaje' => 'ng']);
            }
        } else {
            abort(404);
        }

    }

    public function aprobarnvsup(Request $request)
    {
        //dd($request);
        can('guardar-notaventa');
        if ($request->ajax()) {
            $notaventa = NotaVenta::findOrFail($request->id);
            $notaventa->aprobstatus = $request->valor;
            $notaventa->aprobusu_id = auth()->id();
            $notaventa->aprobfechahora = date("Y-m-d H:i:s");
            $notaventa->aprobobs = $request->obs;
            
            if ($notaventa->save()) {
                return response()->json(['mensaje' => 'ok']);
            } else {
                return response()->json(['mensaje' => 'ng']);
            }
        } else {
            abort(404);
        }

    }

    public function exportPdf($id,$stareport = '1')
    {
        $notaventa = NotaVenta::findOrFail($id);
        $notaventaDetalles = $notaventa->notaventadetalles()->get();
        $empresa = Empresa::orderBy('id')->get();
        $rut = number_format( substr ( $notaventa->cliente->rut, 0 , -1 ) , 0, "", ".") . '-' . substr ( $notaventa->cliente->rut, strlen($notaventa->cliente->rut) -1 , 1 );
        //dd($empresa[0]['iva']);
        if($stareport == '1'){
            //return view('notaventa.listado', compact('notaventa','notaventaDetalles','empresa'));
        
            $pdf = PDF::loadView('notaventa.listado', compact('notaventa','notaventaDetalles','empresa'));
            //return $pdf->download('cotizacion.pdf');
            return $pdf->stream(str_pad($notaventa->id, 5, "0", STR_PAD_LEFT) .' - '. $notaventa->cliente->razonsocial . '.pdf');
    
        }else{
            if($stareport == '2'){
                //return view('notaventa.listado1', compact('notaventa','notaventaDetalles','empresa'));
        
                $pdf = PDF::loadView('notaventa.listado1', compact('notaventa','notaventaDetalles','empresa'));
                //return $pdf->download('cotizacion.pdf');
                return $pdf->stream(str_pad($notaventa->id, 5, "0", STR_PAD_LEFT) .' - '. $notaventa->cliente->razonsocial . '.pdf');
    
            }
        }
        
        
    }

    public function anularnotaventa(Request $request)
    {
        //dd($request);
        can('guardar-notaventa');
        if ($request->ajax()) {
            $notaventa = NotaVenta::findOrFail($request->id);
            $notaventa->anulada = date("Y-m-d H:i:s");
            $cotizacion = Cotizacion::findOrFail($notaventa->cotizacion_id);
            $cotizacion->aprobstatus = 0;
            if ($notaventa->save() and $cotizacion->save()) {
                return response()->json(['mensaje' => 'ok']);
            } else {
                return response()->json(['mensaje' => 'ng']);
            }
        } else {
            abort(404);
        }

    }

    public function notaventacerr()
    {
        can('listar-nota-venta-cerrada');
        //session(['aux_aproNV' => '0']) 0=Pantalla Normal CRUD de Nota de Venta
        //session(['aux_aproNV' => '1']) 1=Pantalla Solo para aprobar Nota de Venta para luego emitir Guia de Despacho
        session(['aux_aproNV' => '0']);
        $user = Usuario::findOrFail(auth()->id());

        $sql= 'SELECT COUNT(*) AS contador
        FROM vendedor INNER JOIN persona
        ON vendedor.persona_id=persona.id and vendedor.deleted_at is null
        INNER JOIN usuario 
        ON persona.usuario_id=usuario.id and persona.deleted_at is null
        WHERE usuario.id=' . auth()->id() . ';';
        $counts = DB::select($sql);
        if($counts[0]->contador>0){
            $vendedor_id=$user->persona->vendedor->id;
            $aux_condvend = 'notaventa.vendedor_id = ' . $vendedor_id;
            $aux_condvendcot = 'cotizacion.vendedor_id = ' . $vendedor_id;
        }else{
            $aux_condvend = 'true';
            $aux_condvendcot = 'true';
        }

        //Se consultan los registros que estan sin aprobar por vendedor null o 0 y los rechazados por el supervisor rechazado por el supervisor=4
        $sql = 'SELECT notaventa.id,notaventa.fechahora,notaventa.cotizacion_id,razonsocial,aprobstatus,aprobobs, 
                    (SELECT COUNT(*) 
                    FROM notaventadetalle 
                    WHERE notaventadetalle.notaventa_id=notaventa.id and 
                    notaventadetalle.precioxkilo < notaventadetalle.precioxkiloreal) AS contador
                FROM notaventa inner join cliente
                on notaventa.cliente_id = cliente.id
                where ' . $aux_condvend . 
                ' and anulada is null
                and (aprobstatus=1 or aprobstatus=3)
                and notaventa.deleted_at is null;';
        //where usuario_id='.auth()->id();
        //dd($sql);
        $datas = DB::select($sql);

        //Se consultan los registros que estan sin aprobar por vendedor null o 0 y los rechazados por el supervisor rechazado por el supervisor=4
        $sql = 'SELECT cotizacion.id,cotizacion.fechahora,razonsocial,aprobstatus,aprobobs,total, 
                    (SELECT COUNT(*) 
                    FROM cotizaciondetalle 
                    WHERE cotizaciondetalle.cotizacion_id=cotizacion.id and 
                    cotizaciondetalle.precioxkilo < cotizaciondetalle.precioxkiloreal) AS contador
                FROM cotizacion inner join cliente
                on cotizacion.cliente_id = cliente.id
                where ' . $aux_condvendcot . ' and (aprobstatus=1 or aprobstatus=3) and 
                cotizacion.id not in (SELECT cotizacion_id from notaventa WHERE !(cotizacion_id is NULL) and (anulada is null))
                and cotizacion.deleted_at is null;';
        //where usuario_id='.auth()->id();
        //dd($sql);
        $cotizaciones = DB::select($sql);
        $aux_statusPant = 1; //Estatus para validar loq ue se muestra en la pantalla
        
        //dd($cotizaciones);
        //$datas = Cotizacion::where('usuario_id',auth()->id())->get();
        return view('notaventa.index', compact('datas','cotizaciones','aux_statusPant'));
    }

    public function visto(Request $request)
    {
        //dd($request);
        can('guardar-notaventa');
        if ($request->ajax()) {
            $notaventa = NotaVenta::findOrFail($request->id);
            if($request->aprobstatus=='1' or $request->aprobstatus=='3'){
                $notaventa->visto = '1';
            }
            if ($notaventa->save()) {
                return response()->json(['mensaje' => 'ok']);
            } else {
                return response()->json(['mensaje' => 'ng']);
            }
        } else {
            abort(404);
        }

    }



}