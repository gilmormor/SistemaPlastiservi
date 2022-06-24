<?php

namespace App\Http\Controllers;

use App\Events\Notificacion;
use App\Http\Requests\ValidarCotizacion;
use App\Http\Requests\ValidarNotaVenta;
use App\Models\AcuerdoTecnico;
use App\Models\CategoriaProd;
use App\Models\Certificado;
use App\Models\Cliente;
use App\Models\ClienteDirec;
use App\Models\ClienteSucursal;
use App\Models\ClienteVendedor;
use App\Models\Color;
use App\Models\Comuna;
use App\Models\Cotizacion;
use App\Models\CotizacionDetalle;
use App\Models\Empresa;
use App\Models\FormaPago;
use App\Models\Giro;
use App\Models\MateriaPrima;
use App\Models\NotaVenta;
use App\Models\NotaVentaCerrada;
use App\Models\NotaVentaDetalle;
use App\Models\Notificaciones;
use App\Models\PlazoPago;
use App\Models\Producto;
use App\Models\ProductoVendedor;
use App\Models\Provincia;
use App\Models\Region;
use App\Models\Seguridad\Usuario;
use App\Models\Sucursal;
use App\Models\SucursalClienteDirec;
use App\Models\TipoEntrega;
use App\Models\UnidadMedida;
use App\Models\Vendedor;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use SplFileInfo;
use Barryvdh\DomPDF\Facade as PDF;
use PhpParser\Node\Stmt\Foreach_;

class NotaVentaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    /*
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
        $sql = "SELECT notaventa.id,notaventa.fechahora,notaventa.cotizacion_id,razonsocial,aprobstatus,aprobobs,
                    (SELECT COUNT(*) 
                    FROM notaventadetalle 
                    WHERE notaventadetalle.notaventa_id=notaventa.id and 
                    notaventadetalle.precioxkilo < notaventadetalle.precioxkiloreal) AS contador
                FROM notaventa inner join cliente
                on notaventa.cliente_id = cliente.id
                where $aux_condvend
                and anulada is null
                and (aprobstatus is null or aprobstatus=0 or aprobstatus=4) 
                and notaventa.id not in (select notaventa_id from notaventacerrada where isnull(notaventacerrada.deleted_at))
                and notaventa.deleted_at is null;";
        //where usuario_id='.auth()->id();
        //dd($sql);
        $datas = DB::select($sql);

        //Se consultan los registros que estan sin aprobar por vendedor null o 0 y los rechazados por el supervisor rechazado por el supervisor=4
        $sql = 'SELECT cotizacion.id,cotizacion.fechahora,razonsocial,aprobstatus,aprobobs,total,
                    clientebloqueado.descripcion as descripbloqueo,
                    (SELECT COUNT(*) 
                    FROM cotizaciondetalle 
                    WHERE cotizaciondetalle.cotizacion_id=cotizacion.id and 
                    cotizaciondetalle.precioxkilo < cotizaciondetalle.precioxkiloreal) AS contador
                FROM cotizacion inner join cliente
                on cotizacion.cliente_id = cliente.id
                LEFT join clientebloqueado
                on cotizacion.cliente_id = clientebloqueado.cliente_id and isnull(clientebloqueado.deleted_at)
                where ' . $aux_condvendcot . ' and (aprobstatus=1 or aprobstatus=3 or aprobstatus=6) and 
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
    */
    
    public function index()
    {
        can('listar-notaventa');
        //session(['aux_aproNV' => '0']) 0=Pantalla Normal CRUD de Nota de Venta
        //session(['aux_aproNV' => '1']) 1=Pantalla Solo para aprobar Nota de Venta para luego emitir Guia de Despacho
        session(['aux_aproNV' => '0']);
        $user = Usuario::findOrFail(auth()->id());
        $sucurArray = $user->sucursales->pluck('id')->toArray();
        $sucurcadena = implode(",", $sucurArray);

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
        $sql = "SELECT cotizacion.id,cotizacion.fechahora,razonsocial,aprobstatus,aprobobs,total,
                    clientebloqueado.descripcion as descripbloqueo,
                    (SELECT COUNT(*) 
                    FROM cotizaciondetalle 
                    WHERE cotizaciondetalle.cotizacion_id=cotizacion.id and 
                    cotizaciondetalle.precioxkilo < cotizaciondetalle.precioxkiloreal) AS contador
                FROM cotizacion inner join cliente
                on cotizacion.cliente_id = cliente.id
                LEFT join clientebloqueado
                on cotizacion.cliente_id = clientebloqueado.cliente_id and isnull(clientebloqueado.deleted_at)
                where $aux_condvendcot and (aprobstatus=1 or aprobstatus=3 or aprobstatus=6) and 
                cotizacion.id not in (SELECT cotizacion_id from notaventa WHERE !(cotizacion_id is NULL) and (anulada is null))
                and cotizacion.deleted_at is null
                AND cotizacion.sucursal_id in ($sucurcadena);";
        //where usuario_id='.auth()->id();
        //dd($sql);
        $cotizaciones = DB::select($sql);
        $aux_statusPant = 0; //Estatus para validar loq ue se muestra en la pantalla
        
        //dd($cotizaciones);
        //$datas = Cotizacion::where('usuario_id',auth()->id())->get();
        return view('notaventa.index', compact('cotizaciones','aux_statusPant'));
    }
    
    public function notaventapage(){
        //session(['aux_aproNV' => '0']) 0=Pantalla Normal CRUD de Nota de Venta
        //session(['aux_aproNV' => '1']) 1=Pantalla Solo para aprobar Nota de Venta para luego emitir Guia de Despacho
        session(['aux_aproNV' => '0']);
        $user = Usuario::findOrFail(auth()->id());
        $sucurArray = $user->sucursales->pluck('id')->toArray();
        $sucurcadena = implode(",", $sucurArray);

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
        $sql = "SELECT notaventa.id,notaventa.fechahora,notaventa.cotizacion_id,razonsocial,aprobstatus,aprobobs,
                    oc_id,oc_file,'' as btnguardar,'' as btnanular,'' as pdfnv,
                    (SELECT COUNT(*) 
                    FROM notaventadetalle 
                    WHERE notaventadetalle.notaventa_id=notaventa.id and 
                    notaventadetalle.precioxkilo < notaventadetalle.precioxkiloreal) AS contador
                FROM notaventa inner join cliente
                on notaventa.cliente_id = cliente.id
                where $aux_condvend
                and anulada is null
                and (aprobstatus is null or aprobstatus=0 or aprobstatus=4) 
                and notaventa.id not in (select notaventa_id from notaventacerrada where isnull(notaventacerrada.deleted_at))
                and notaventa.deleted_at is null
                AND notaventa.sucursal_id in ($sucurcadena);";
        //where usuario_id='.auth()->id();
        //dd($sql);
        $datas = DB::select($sql);  
        //dd($datas);       
        return datatables($datas)->toJson();  
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function crear()
    {
        can('crear-notaventa');
        /*
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
        // Filtro solos los clientes que esten asignados a la sucursal y asignado al vendedor logueado
        $sucurArray = $user->sucursales->pluck('id')->toArray();
        $clientes = Cliente::select(['cliente.id','cliente.rut','cliente.razonsocial','cliente.direccion','cliente.telefono'])
        ->whereIn('cliente.id' , ClienteSucursal::select(['cliente_sucursal.cliente_id'])
                                ->whereIn('cliente_sucursal.sucursal_id', $sucurArray)
        ->pluck('cliente_sucursal.cliente_id')->toArray())
        ->whereIn('cliente.id',$clientevendedorArray)
        ->get();
        */
        $clientesArray = Cliente::clientesxUsuario();
        $clientes = $clientesArray['clientes'];
        $vendedor_id = $clientesArray['vendedor_id'];
        $sucurArray = $clientesArray['sucurArray'];

        $fecha = date("d/m/Y");
        $formapagos = FormaPago::orderBy('id')->get();
        $plazopagos = PlazoPago::orderBy('id')->get();
        $vendedores = Vendedor::orderBy('id')->where('sta_activo',1)->get();
        $comunas = Comuna::orderBy('id')->get();
        $productos = Producto::productosxUsuario();

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
        $tablas = array();
        $tablas['unidadmedida'] = UnidadMedida::orderBy('id')->where('mostrarfact',1)->get();
        $tablas['sucursales'] = Sucursal::orderBy('id')->whereIn('sucursal.id', $sucurArray)->get();

        $tablas['formapagos'] = FormaPago::orderBy('id')->get();
        $tablas['plazopagos'] = PlazoPago::orderBy('id')->get();
        $tablas['comunas'] = Comuna::orderBy('id')->get();
        $tablas['provincias'] = Provincia::orderBy('id')->get();
        $tablas['regiones'] = Region::orderBy('id')->get();
        $tablas['tipoentregas'] = TipoEntrega::orderBy('id')->get();
        $tablas['giros'] = Giro::orderBy('id')->get();
        $tablas['empresa'] = Empresa::findOrFail(1);
        $tablas['materiPrima'] = MateriaPrima::orderBy('id')->get();
        $tablas['color'] = Color::orderBy('id')->get();
        $tablas['certificado'] = Certificado::orderBy('id')->get();
        session(['editaracutec' => '0']);
    
        //dd($vendedor_id);
        return view('notaventa.crear',compact('formapagos','plazopagos','vendedores','vendedores1','fecha','comunas','productos','clientes','empresa','tipoentregas','vendedor_id','giros','sucurArray','aux_sta','aux_statusPant','tablas'));
    }

    public function crearcot($id)
    {
        can('crear-notaventa');
        $data = Cotizacion::findOrFail($id);
        //dd($data);
        $data->plazoentrega = $newDate = date("d/m/Y", strtotime($data->plazoentrega));;
        //$detalles = $data->cotizaciondetalles()->get();
        $detalles = $data->cotizaciondetalles;
        /*$detalles = $data->cotizaciondetalles()
                    ->whereColumn('cotizaciondetalle.cantusada', '<', 'cotizaciondetalle.cant')
                    ->get();*/
        $vendedor_id=$data->vendedor_id;
        $clienteselec = $data->cliente()->get();
        //dd($clienteselec);

        $clientesArray = Cliente::clientesxUsuario('0',$data->cliente_id); //Paso vendedor en 0 y el id del cliente para que me traiga las Sucursales que coinciden entre el vendedor y el cliente
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
        $productos = Producto::productosxUsuario();
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

        $tablas = array();
        $tablas['formapagos'] = FormaPago::orderBy('id')->get();
        $tablas['plazopagos'] = PlazoPago::orderBy('id')->get();
        $tablas['comunas'] = Comuna::orderBy('id')->get();
        $tablas['provincias'] = Provincia::orderBy('id')->get();
        $tablas['regiones'] = Region::orderBy('id')->get();
        $tablas['tipoentregas'] = TipoEntrega::orderBy('id')->get();
        $tablas['giros'] = Giro::orderBy('id')->get();
        $tablas['sucursales'] = $clientesArray['sucursales'];
        $tablas['empresa'] = Empresa::findOrFail(1);
        $tablas['unidadmedida'] = UnidadMedida::orderBy('id')->where('mostrarfact',1)->get();
        $tablas['materiPrima'] = MateriaPrima::orderBy('id')->get();
        $tablas['color'] = Color::orderBy('id')->get();
        $tablas['certificado'] = Certificado::orderBy('id')->get();
        session(['editaracutec' => '0']);

        //dd($aux_aproNV);
        return view('notaventa.crear', compact('data','clienteselec','clientedirecs','clientes','clienteDirec','clientedirecs','detalles','comunas','formapagos','plazopagos','vendedores','vendedores1','productos','fecha','empresa','tipoentregas','giros','sucurArray','aux_sta','aux_cont','aux_statusPant','tablas','vendedor_id'));

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function guardar(ValidarNotaVenta $request)
    //public function guardar(Request $request)
    {
        //dd($request);
        can('guardar-notaventa');
        $cont_producto = count($request->producto_id);
        if($cont_producto <=0 ){
            return redirect('notaventa')->with([
                'mensaje'=>'Nota de Venta sin items, no se guardó.',
                'tipo_alert' => 'alert-error'
            ]);
        }
        if(!empty($request->cotizacion_id)){
            $cotizacion = Cotizacion::findOrFail($request->cotizacion_id);
            if(empty($cotizacion->aprobstatus) or strpos("136",(string)$cotizacion->aprobstatus) === false){
                //SI POR ALGUNA RAZON CAMBIAN EL VALOR DE $cotizacion->aprobstatus EN EL MOMENTO QUE SE ESTAR CREANDO LA NOTA DE VENTA
                return redirect('notaventa')->with([
                    'mensaje'=>'Nota de venta no fue guardada, ya que cambio el estatus de aprobación de la Cotización',
                    'tipo_alert' => 'alert-error'
                ]);
            }
        }
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

        //$notaventaid = 1;
        //SI ESTA VACIO EL NUMERO DE COTIZACION SE CREA EL DETALLE DE LA NOTA DE VENTA DE LA TABLA DEL LADO DEL CLIENTE
        //SI NO ESTA VACIO EL NUMERO DE COTIZACION SE LLENA EL DETALLE DE LA NOTA DE VENTA DE LA TABLA DETALLE COTIZACION
        if(empty($request->cotizacion_id)){
            if($cont_producto>0){
                for ($i=0; $i < $cont_producto ; $i++){
                    if(is_null($request->producto_id[$i])==false && is_null($request->cant[$i])==false){
                        $producto = Producto::findOrFail($request->producto_id[$i]);
                        $notaventadetalle = new NotaVentaDetalle();
                        $notaventadetalle->notaventa_id = $notaventaid;
                        $notaventadetalle->producto_id = $request->producto_id[$i];
                        $notaventadetalle->cotizaciondetalle_id = $request->cotizaciondetalle_id[$i];                    
                        $notaventadetalle->cant = $request->cant[$i];
                        $notaventadetalle->cantgrupo = $request->cant[$i];
                        $notaventadetalle->cantxgrupo = 1;
                        $notaventadetalle->unidadmedida_id = $request->unidadmedida_id[$i];
                        $notaventadetalle->descuento = $request->descuento[$i];
                        $notaventadetalle->preciounit = $request->preciounit[$i];
                        $notaventadetalle->peso = $producto->peso;
                        $notaventadetalle->precioxkilo = $request->precioxkilo[$i];
                        $notaventadetalle->precioxkiloreal = $request->precioxkiloreal[$i];
                        $notaventadetalle->totalkilos = $request->totalkilos[$i];
                        $notaventadetalle->subtotal = $request->subtotal[$i];
    
                        $notaventadetalle->producto_nombre = $producto->nombre;
                        $notaventadetalle->ancho = $request->ancho[$i];
                        $notaventadetalle->largo = $request->long[$i];
                        $notaventadetalle->espesor = $request->espesor[$i];
                        $notaventadetalle->diametro = $producto->diametro;
                        $notaventadetalle->categoriaprod_id = $producto->categoriaprod_id;
                        $notaventadetalle->claseprod_id = $producto->claseprod_id;
                        $notaventadetalle->grupoprod_id = $producto->grupoprod_id;
                        $notaventadetalle->color_id = $producto->color_id;
                        $notaventadetalle->obs = $request->obs[$i];
                        $notaventadetalle->save();
                        $idDireccion = $notaventadetalle->id;    
                    }
                }
            }
        }else{
            $cotizacion = Cotizacion::findOrFail($request->cotizacion_id);
            $cotizaciondetalles = $cotizacion->cotizaciondetalles;
            foreach ($cotizaciondetalles as $cotizaciondetalle) {
                //dd($cotizaciondetalle->acuerdotecnicotemp);
                $array_cotizaciondetalle = $cotizaciondetalle->attributesToArray();
                $array_cotizaciondetalle["notaventa_id"] = $notaventaid;
                $array_cotizaciondetalle["cotizaciondetalle_id"] = $array_cotizaciondetalle["id"];
                unset($array_cotizaciondetalle["id"],$array_cotizaciondetalle["usuariodel_id"],$array_cotizaciondetalle["deleted_at"],$array_cotizaciondetalle["created_at"],$array_cotizaciondetalle["updated_at"]);
                //dd($array_cotizaciondetalle);
                if($cotizaciondetalle->acuerdotecnicotemp){
                    //SI EXISTE ACUERDO TECNICO SE CREA EL PRODUCTO
                    //dd($cotizaciondetalle->acuerdotecnicotemp->attributesToArray());
                    $array_acuerdotecnicotemp = $cotizaciondetalle->acuerdotecnicotemp->attributesToArray();
                    $producto = Producto::findOrFail($cotizaciondetalle->producto_id);
                    $array_producto = $producto->attributesToArray();
                    $array_producto["nombre"] = $array_acuerdotecnicotemp["at_desc"];
                    $array_producto["descripcion"] = $array_acuerdotecnicotemp["at_desc"];
                    $array_producto["precioneto"] = $cotizaciondetalle->precioxkilo;
                    $array_producto["tipoprod"] = 0;
                    $productonew = Producto::create($array_producto);
                    //CREAR RELACION CON VENDEDOR ASOCIADO AL PRODUCTO PARA LUEGO FILTRAR LOS PRODUCTOS POR VENDEDOR
                    //$productonew->vendedores()->sync($request->vendedor_id);
                    ProductoVendedor::create([
                        'producto_id' => $array_producto['id'],
                        'vendedor_id' => $request->vendedor_id
                    ]);
                    $array_acuerdotecnicotemp["producto_id"] = $productonew->id;
                    $acuerdotecnico = AcuerdoTecnico::create($array_acuerdotecnicotemp);
                    $array_cotizaciondetalle["producto_id"] = $productonew->id;
                }
                $notaventadetalle = NotaVentaDetalle::create($array_cotizaciondetalle);
            }    
        }

        return redirect('notaventa')->with([
                                            'mensaje'=>'Nota de Venta creada con exito.',
                                            'tipo_alert' => 'alert-success'
                                        ]);
        
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
        /*
        foreach($data->notaventadetalles as $detalle){
            dd($detalle->producto->acuerdotecnico);
        }
        */
        //$detalles = $data->notaventadetalles()->get();
        $detalles = $data->notaventadetalles;
        $data->plazoentrega = $newDate = date("d/m/Y", strtotime($data->plazoentrega));
        $vendedor_id=$data->vendedor_id;
        $clienteselec = $data->cliente()->get();
        //session(['aux_aprocot' => '0']);
        //dd($clienteselec[0]->rut);

        $clientesArray = Cliente::clientesxUsuario('0',$data->cliente_id); //Paso vendedor en 0 y el id del cliente para que me traiga las Sucursales que coinciden entre el vendedor y el cliente
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
        $tablas = array();
        $tablas['unidadmedida'] = UnidadMedida::orderBy('id')->where('mostrarfact',1)->get();
        $tablas['sucursales'] = $clientesArray['sucursales'];
        //$tablas['sucursales'] = Sucursal::orderBy('id')->whereIn('sucursal.id', $sucurArray)->get();
        return view('notaventa.editar', compact('data','detalles','clienteselec','clientes','clienteDirec','clientedirecs','comunas','formapagos','plazopagos','vendedores','vendedores1','productos','fecha','empresa','tipoentregas','giros','sucurArray','aux_sta','aux_cont','aux_statusPant','vendedor_id','tablas'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function actualizar(ValidarNotaVenta $request, $id)
    //public function actualizar(Request $request, $id)
    {
        can('guardar-notaventa');
        //dd($request);
        $cont_cotdet = count($request->NVdet_id);
        if($cont_cotdet <=0 ){
            return redirect('notaventa')->with([
                'mensaje'=>'Nota de Venta sin items, no se actualizó.',
                'tipo_alert' => 'alert-error'
            ]);
        }

        $notaventa = NotaVenta::findOrFail($id);
        $aux_oc_fileold = $notaventa->oc_file;
        if($notaventa->updated_at == $request->updated_at){
            $notaventa->updated_at = date("Y-m-d H:i:s");
            $request->request->add(['fechahora' => $notaventa->fechahora]);
            $aux_plazoentrega= DateTime::createFromFormat('d/m/Y', $request->plazoentrega)->format('Y-m-d');
            $request->request->add(['plazoentrega' => $aux_plazoentrega]);
            //dd($request->plazoentrega);
            $notaventaid = $id;
    
            $notaventa->update($request->all());
            $aux_imagen = null;
            if(empty($request->oc_file) and empty($request->imagen)){
                $aux_imagen = $notaventa->oc_file;
            }
            if(!empty($request->oc_file) and !empty($aux_oc_fileold)){
                $aux_imagen = $aux_oc_fileold; //Si la imagen cambia el nombre del archivo anterior para que sea eliminado
            }
            if ($foto = NotaVenta::setFotonotaventa($request->oc_file,$notaventaid,$request,$aux_imagen)){
                $foto = $foto == "null" ? null : $foto;
                $request->request->add(['oc_file' => $foto]);
                $data = NotaVenta::findOrFail($notaventaid);
                $data->oc_file = $foto;
                $data->save();
            }
    
            $auxNVDet=NotaVentaDetalle::where('notaventa_id',$id)->whereNotIn('id', $request->NVdet_id)->pluck('id')->toArray(); //->destroy();
            for ($i=0; $i < count($auxNVDet) ; $i++){
                NotaVentaDetalle::destroy($auxNVDet[$i]);
            }
            if($cont_cotdet>0){
                for ($i=0; $i < count($request->NVdet_id) ; $i++){
                    $idcotizaciondet = $request->NVdet_id[$i]; 
                    $producto = Producto::findOrFail($request->producto_id[$i]);
                    if( $request->NVdet_id[$i] == '0' ){
                        $notaventadetalle = new NotaVentaDetalle();
                        $notaventadetalle->notaventa_id = $notaventaid;
                        $notaventadetalle->producto_id = $request->producto_id[$i];
                        //$notaventadetalle->cotizaciondetalle_id = $request->cotizaciondetalle_id[$i];
                        $notaventadetalle->cant = $request->cant[$i];
                        $notaventadetalle->cantgrupo = $request->cant[$i];
                        $notaventadetalle->cantxgrupo = 1;
                        $notaventadetalle->unidadmedida_id = $request->unidadmedida_id[$i];
                        $notaventadetalle->descuento = $request->descuento[$i];
                        $notaventadetalle->preciounit = $request->preciounit[$i];
                        $notaventadetalle->peso = $producto->peso;
                        $notaventadetalle->precioxkilo = $request->precioxkilo[$i];
                        $notaventadetalle->precioxkiloreal = $request->precioxkiloreal[$i];
                        $notaventadetalle->totalkilos = $request->totalkilos[$i];
                        $notaventadetalle->subtotal = $request->subtotal[$i];
                        
                        $notaventadetalle->producto_nombre = $producto->nombre;
                        $notaventadetalle->ancho = $request->ancho[$i];
                        $notaventadetalle->largo = $request->long[$i];
                        $notaventadetalle->espesor = $request->espesor[$i];    
                        $notaventadetalle->diametro = $producto->diametro;
                        $notaventadetalle->categoriaprod_id = $producto->categoriaprod_id;
                        $notaventadetalle->claseprod_id = $producto->claseprod_id;
                        $notaventadetalle->grupoprod_id = $producto->grupoprod_id;
                        $notaventadetalle->color_id = $producto->color_id; 
                        $notaventadetalle->obs = $request->obs[$i];   
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
                                'cantgrupo' => $request->cant[$i],
                                'cantxgrupo' => 1,
                                'unidadmedida_id' => $request->unidadmedida_id[$i],
                                'descuento' => $request->descuento[$i],
                                'preciounit' => $request->preciounit[$i],
                                'peso' => $producto->peso,
                                'precioxkilo' => $request->precioxkilo[$i],
                                'precioxkiloreal' => $request->precioxkiloreal[$i],
                                'totalkilos' => $request->totalkilos[$i],
                                'subtotal' => $request->subtotal[$i],
                                'producto_nombre' => $producto->nombre,
                                'espesor' => $request->espesor[$i],
                                'ancho' => $request->ancho[$i],
                                'largo' => $request->long[$i],
                                'diametro' => $producto->diametro,
                                'categoriaprod_id' => $producto->categoriaprod_id,
                                'claseprod_id' => $producto->claseprod_id,
                                'grupoprod_id' => $producto->grupoprod_id,
                                'color_id' => $producto->color_id,
                                'obs' => $request->obs[$i],
                            ]
                        );
                    }
                }
            }
            return redirect('notaventa/'.$id.'/editar')->with([
                                                                'mensaje'=>'Nota de Venta Actualizada con exito.',
                                                                'tipo_alert' => 'alert-success'
                                                            ]);
        }else{
            return redirect('notaventa/'.$id.'/editar')->with([
                                                                'mensaje'=>'Registro no fue modificado. Registro Editado por otro usuario. Fecha Hora: '.$notaventa->updated_at,
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

    public function eliminarDetalle(Request $request)
    {
        //can('eliminar-cotizacionDetalle');
        if ($request->ajax()) {
            if (NotaVentaDetalle::destroy($request->id)) {
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
                $notaventa->aprobobs = 'Aprobado por el vendedor';
            }
            if ($notaventa->save()) {
                $sql = "SELECT COUNT(*) as cont
                    FROM notaventadetalle 
                    WHERE notaventadetalle.notaventa_id=$notaventa->id 
                    and notaventadetalle.precioxkilo < notaventadetalle.precioxkiloreal
                    and isnull(notaventadetalle.deleted_at);";
                //where usuario_id='.auth()->id();
                //dd($sql);
                $datas = DB::select($sql);
                if($datas[0]->cont > 0){
                    //Notificaciones cuando el precio esta por debajo de lo establecido en tabla de productos
                    //Notificacion para Cristian
                    $notificacion = [
                        'usuarioorigen_id' => auth()->id(),
                        'usuariodestino_id' => 2,
                        'vendedor_id' => $notaventa->vendedor_id,
                        'status' => 1,
                        'nombretabla' => 'notaventa',
                        'mensaje' => 'Precio menor NV: '. $notaventa->id,
                        'mensajetitle' => 'Prec menor al valor tabla: Requiere Aprobación',
                        'nombrepantalla' => 'notaventa.index',
                        'rutaorigen' => 'notaventa',
                        'rutadestino' => 'notaventaaprobar',
                        'tabla_id' => $notaventa->id,
                        'accion' => 'Prec menor al valor tabla: Requiere Aprobación',
                        'detalle' => 'Nota Venta: ' .$notaventa->id. ' Prec menor al valor tabla: Requiere Aprobación',
                        'icono' => 'fa fa-fw fa-thumbs-o-down text-red',
                    ];
                    event(new Notificacion($notificacion));
                    //Notificacion para el usuario que aprobo la Nota Venta
                    $notificacion = [
                        'usuarioorigen_id' => auth()->id(),
                        'usuariodestino_id' => auth()->id(),
                        'vendedor_id' => $notaventa->vendedor_id,
                        'status' => 1,
                        'nombretabla' => 'notaventa',
                        'mensaje' => 'Precio menor NV: '. $notaventa->id,
                        'mensajetitle' => 'Prec menor al valor tabla: Requiere Aprobación',
                        'nombrepantalla' => 'notaventa.index',
                        'rutaorigen' => 'notaventa',
                        'rutadestino' => 'notaventaconsulta',
                        'tabla_id' => $notaventa->id,
                        'accion' => 'Prec menor al valor tabla: Requiere Aprobación',
                        'detalle' => 'Nota Venta: ' .$notaventa->id. ' Prec menor al valor tabla: Requiere Aprobación',
                        'icono' => 'fa fa-fw fa-thumbs-o-down text-red',
                    ];
                    event(new Notificacion($notificacion));
                }
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
        if(can('ver-pdf-nota-de-venta',false)){
            $notaventa = NotaVenta::findOrFail($id);
            $notaventaDetalles = $notaventa->notaventadetalles()->get();
            $empresa = Empresa::orderBy('id')->get();
            $rut = number_format( substr ( $notaventa->cliente->rut, 0 , -1 ) , 0, "", ".") . '-' . substr ( $notaventa->cliente->rut, strlen($notaventa->cliente->rut) -1 , 1 );
            //dd($empresa[0]['iva']);
            if($stareport == '1'){
                if(env('APP_DEBUG')){
                    return view('notaventa.listado', compact('notaventa','notaventaDetalles','empresa'));
                }
                $pdf = PDF::loadView('notaventa.listado', compact('notaventa','notaventaDetalles','empresa'));
                //return $pdf->download('cotizacion.pdf');
                return $pdf->stream(str_pad($notaventa->id, 5, "0", STR_PAD_LEFT) .' - '. $notaventa->cliente->razonsocial . '.pdf');
        
            }else{
                if($stareport == '2'){
                    if(env('APP_DEBUG')){
                        return view('notaventa.listado1', compact('notaventa','notaventaDetalles','empresa'));
                    }
            
                    $pdf = PDF::loadView('notaventa.listado1', compact('notaventa','notaventaDetalles','empresa'));
                    //return $pdf->download('cotizacion.pdf');
                    return $pdf->stream(str_pad($notaventa->id, 5, "0", STR_PAD_LEFT) .' - '. $notaventa->cliente->razonsocial . '.pdf');
        
                }
            }    
        }else{
            //return false;            
            $pdf = PDF::loadView('generales.pdfmensajesinacceso');
            return $pdf->stream("mensajesinacceso.pdf");
        }
        
        
    }

    public function anularnotaventa(Request $request)
    {
        //dd($request);
        can('guardar-notaventa');
        if ($request->ajax()) {
            $notaventa = NotaVenta::findOrFail($request->id);
            $notaventa->anulada = date("Y-m-d H:i:s");
            $sta_save = false;
            if($notaventa->cotizacion_id>0){
                $cotizacion = Cotizacion::findOrFail($notaventa->cotizacion_id);
                $cotizacion->aprobstatus = 0;
                if ($notaventa->save() and $cotizacion->save()) {
                    $sta_save = true;
                }
            }else{
                if ($notaventa->save()) {
                    $sta_save = true;
                }
            }
            if ($sta_save) {
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
        /*
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
        $sql = "SELECT notaventa.id,notaventa.fechahora,notaventa.cotizacion_id,razonsocial,aprobstatus,aprobobs,oc_id,oc_file,
                    (SELECT COUNT(*) 
                    FROM notaventadetalle 
                    WHERE notaventadetalle.notaventa_id=notaventa.id and 
                    notaventadetalle.precioxkilo < notaventadetalle.precioxkiloreal) AS contador
                FROM notaventa inner join cliente
                on notaventa.cliente_id = cliente.id
                where $aux_condvend
                and isnull(anulada)
                and (aprobstatus=1 or aprobstatus=3)
                and notaventa.id not in (select notaventa_id from notaventacerrada where isnull(notaventacerrada.deleted_at))
                and isnull(notaventa.deleted_at);";
        //where usuario_id='.auth()->id();
        //dd($sql);
        $datas = DB::select($sql);

        //Se consultan los registros que estan sin aprobar por vendedor null o 0 y los rechazados por el supervisor rechazado por el supervisor=4
        $sql = "SELECT cotizacion.id,cotizacion.fechahora,razonsocial,aprobstatus,aprobobs,total, 
                    (SELECT COUNT(*) 
                    FROM cotizaciondetalle 
                    WHERE cotizaciondetalle.cotizacion_id=cotizacion.id and 
                    cotizaciondetalle.precioxkilo < cotizaciondetalle.precioxkiloreal) AS contador
                FROM cotizacion inner join cliente
                on cotizacion.cliente_id = cliente.id
                where $aux_condvendcot and (aprobstatus=1 or aprobstatus=3) and 
                cotizacion.id not in (SELECT cotizacion_id from notaventa WHERE !(cotizacion_id is NULL) and (anulada is null))
                and cotizacion.deleted_at is null;";
        //where usuario_id='.auth()->id();
        //dd($sql);
        $cotizaciones = DB::select($sql);
        $aux_statusPant = 1; //Estatus para validar loq ue se muestra en la pantalla
        */
        //dd($cotizaciones);
        //$datas = Cotizacion::where('usuario_id',auth()->id())->get();
        //return view('notaventacerrada.index', compact('datas','cotizaciones','aux_statusPant'));
        return view('notaventacerrada.index', compact('cotizaciones'));
    }

    public function notaventacerrpage(){
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
        $sql = "SELECT notaventa.id,DATE_FORMAT(notaventa.fechahora,'%d/%m/%Y %h:%i %p') as fechahora,
                    notaventa.cotizacion_id,razonsocial,aprobstatus,aprobobs,oc_id,oc_file,
                    (SELECT COUNT(*) 
                    FROM notaventadetalle 
                    WHERE notaventadetalle.notaventa_id=notaventa.id and 
                    notaventadetalle.precioxkilo < notaventadetalle.precioxkiloreal) AS contador
                FROM notaventa inner join cliente
                on notaventa.cliente_id = cliente.id
                where $aux_condvend
                and isnull(anulada)
                and (aprobstatus=1 or aprobstatus=3)
                and notaventa.id not in (select notaventa_id from notaventacerrada where isnull(notaventacerrada.deleted_at))
                and isnull(notaventa.deleted_at);";
        //where usuario_id='.auth()->id();
        //dd($sql);
        $datas = DB::select($sql);
        /*
        //Se consultan los registros que estan sin aprobar por vendedor null o 0 y los rechazados por el supervisor rechazado por el supervisor=4
        $sql = "SELECT cotizacion.id,cotizacion.fechahora,razonsocial,aprobstatus,aprobobs,total, 
                    (SELECT COUNT(*) 
                    FROM cotizaciondetalle 
                    WHERE cotizaciondetalle.cotizacion_id=cotizacion.id and 
                    cotizaciondetalle.precioxkilo < cotizaciondetalle.precioxkiloreal) AS contador
                FROM cotizacion inner join cliente
                on cotizacion.cliente_id = cliente.id
                where $aux_condvendcot and (aprobstatus=1 or aprobstatus=3) and 
                cotizacion.id not in (SELECT cotizacion_id from notaventa WHERE !(cotizacion_id is NULL) and (anulada is null))
                and cotizacion.deleted_at is null;";
        //where usuario_id='.auth()->id();
        //dd($sql);
        $cotizaciones = DB::select($sql);
        */
        
        return datatables($datas)->toJson();  
    }

    public function visto(Request $request)
    {
        //dd($request);
        can('guardar-notaventa');
        if ($request->ajax()) {
            $notaventa = NotaVenta::findOrFail($request->id);
            if(empty($notaventa->visto)){
                $notaventa->visto = date("Y-m-d H:i:s");
            }else{
                $notaventa->visto = NULL;                
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

    public function inidespacho(Request $request)
    {
        //dd($request);
        if ($request->ajax()) {
            $notaventa = NotaVenta::findOrFail($request->id);
            if(empty($notaventa->inidespacho)){
                $notaventa->inidespacho = date("Y-m-d H:i:s");
            }else{
                $notaventa->inidespacho = NULL;                
            }
            if ($notaventa->save()) {
                return response()->json(['mensaje' => 'ok',
                                         'inidespacho' => $notaventa->inidespacho
                                         ]
                                        );
            } else {
                return response()->json(['mensaje' => 'ng']);
            }
        } else {
            abort(404);
        }

    }

    public function buscarguiadespacho(Request $request)
    {
        //dd($request);
        if ($request->ajax()) {
            $notaventa = NotaVenta::findOrFail($request->id);
            return response()->json(['mensaje' => 'ok',
                                         'guiasdespacho' => $notaventa->guiasdespacho
                                        ]
                                        );
        }
    }

    public function actguiadespacho(Request $request)
    {
        //dd($request);
        if ($request->ajax()) {
            $notaventa = NotaVenta::findOrFail($request->id);
            $notaventa->guiasdespacho = $request->guiasdespacho;
            if ($notaventa->save()) {
                return response()->json(['mensaje' => 'ok']);
            } else {
                return response()->json(['mensaje' => 'ng']);
            }
        } else {
            abort(404);
        }

    }

    public function findespacho(Request $request)
    {
        //dd($request);
        if ($request->ajax()) {
            $notaventa = NotaVenta::findOrFail($request->id);
            if(empty($notaventa->findespacho)){
                $notaventa->findespacho = date("Y-m-d H:i:s");
            }
            if(empty($notaventa->guiasdespacho)){
                return response()->json(['mensaje' => 'empty']);
            }
            if ($notaventa->save()) {
                return response()->json(['mensaje' => 'ok',
                                         'findespacho' => $notaventa->findespacho
                                         ]
                                        );
            } else {
                return response()->json(['mensaje' => 'ng']);
            }
        } else {
            abort(404);
        }

    }

    public function buscaroc_id(Request $request)
    {
        //dd($request);
        if ($request->ajax()) {
            $notaventa = NotaVenta::where('oc_id' ,'=',$request->oc_id)->get();
            if(count($notaventa) > 0){
                return response()->json(['mensaje' => 'ok',
                'Mensaje' => 'Encontrado'
               ]);
            }else{
                return response()->json(['mensaje' => 'no',
                'Mensaje' => 'Orden de compra no existe.'
               ]);
            }
        }
    }

    public function cerrartodasNV(){
        $sql = "SELECT *
            FROM notaventa 
            WHERE notaventa.id not in (select notaventa_id from notaventacerrada where isnull(notaventacerrada.deleted_at))
            and isnull(notaventa.deleted_at);";
        $datas = DB::select($sql);
        foreach ($datas as $data){
            $notaventacerrada = new NotaVentaCerrada();
            $notaventacerrada->notaventa_id = $data->id;
            $notaventacerrada->observacion = 'Cierre 2020';
            $notaventacerrada->motcierre_id = 1;                    
            $notaventacerrada->usuario_id = 1;
            $notaventacerrada->save();
        }
    }

    public function buscarNV(Request $request){
        $sql = "SELECT COUNT(*) as cont
            FROM notaventa
            where id = $request->id
            and !isnull(anulada)
            and isnull(notaventa.deleted_at);";
        $datas = DB::select($sql);
        if($datas[0]->cont > 0){
            return response()->json(['mensaje' => 'anulada']);
        }
        $sql = "SELECT COUNT(*) as cont
            FROM notaventa
            where id = $request->id
            and isnull(Anulada)
            and id in (SELECT notaventa_id from notaventacerrada where isnull(notaventacerrada.deleted_at))
            and isnull(notaventa.deleted_at);";
        $datas = DB::select($sql);
        if($datas[0]->cont > 0){
            return response()->json(['mensaje' => 'Cerrada']);
        }

        if($request->ajax()){
            $sql = "SELECT COUNT(*) as cont
                FROM notaventa
                where id = $request->id
                and isnull(anulada)
                and notaventa.id not in (select notaventa_id from notaventacerrada where isnull(notaventacerrada.deleted_at))
                and isnull(notaventa.deleted_at);";
            $datas = DB::select($sql);
            //dd($datas[0]->cont);
            if($datas[0]->cont > 0){
                return response()->json(['mensaje' => 'ok']);
            }else{
                return response()->json(['mensaje' => 'no']);
            }
        }
    }

    public function actualizarFileOC(Request $request){
        if ($foto = NotaVenta::setFotonotaventa($request->oc_file,$request->id,$request)){
            $request->request->add(['oc_file' => $foto]);
            $data = NotaVenta::findOrFail($request->id);
            $data->oc_file = $foto;
            if($data->save()){
                return response()->json(['mensaje' => 'ok']); 
            }
        }
    }

    public function cambiarUnidadMedida(){
        $datas = Producto::orderBy('id')->get();
        foreach ($datas as $data) {
            $producto = Producto::findOrFail($data->id);
            if ($data->categoriaprod->unidadmedida_id==3){
                $producto->diametro = $data->diamextpg;
            }else{
                if($data->diamextmm == 0){
                    $producto->diametro = $data->diamextpg;
                }else{
                    $producto->diametro = $data->diamextmm . "mm";
                }
            }
            $producto->save();
        }
        $datas = CotizacionDetalle::orderBy('id')->get();
        foreach ($datas as $data) {
            $cotizaciondetalle = CotizacionDetalle::findOrFail($data->id);
            $cotizaciondetalle->unidadmedida_id = $data->producto->categoriaprod->unidadmedidafact_id;
            $cotizaciondetalle->producto_nombre = $data->producto->nombre;
            $cotizaciondetalle->espesor = $data->producto->espesor;
            $cotizaciondetalle->largo = $data->producto->long;
            $cotizaciondetalle->diametro = $data->producto->diametro;
            $cotizaciondetalle->categoriaprod_id = $data->producto->categoriaprod_id;
            $cotizaciondetalle->claseprod_id = $data->producto->claseprod_id;
            $cotizaciondetalle->grupoprod_id = $data->producto->grupoprod_id;
            $cotizaciondetalle->color_id = $data->producto->color_id;
            $cotizaciondetalle->save();
        }
        $datas = NotaVentaDetalle::orderBy('id')->get();
        foreach ($datas as $data) {
            $notaventadetalle = NotaVentaDetalle::findOrFail($data->id);
            $notaventadetalle->unidadmedida_id = $data->producto->categoriaprod->unidadmedidafact_id;
            $notaventadetalle->producto_nombre = $data->producto->nombre;
            $notaventadetalle->espesor = $data->producto->espesor;
            $notaventadetalle->largo = $data->producto->long;
            $notaventadetalle->diametro = $data->producto->diametro;
            $notaventadetalle->categoriaprod_id = $data->producto->categoriaprod_id;
            $notaventadetalle->claseprod_id = $data->producto->claseprod_id;
            $notaventadetalle->grupoprod_id = $data->producto->grupoprod_id;
            $notaventadetalle->color_id = $data->producto->color_id;
            $notaventadetalle->save();
        }
    }

}

function consultaNVaprobadas($id){
    $aux_condid = "true";
    if($id!=""){
        $aux_condid = "notaventa.id=$id";
    }
    //Consultar registros que estan sin aprobar por vendedor null o 0 y los rechazados por el supervisor rechazado por el supervisor=4

    $sql = "SELECT notaventa.id,notaventa.fechahora,notaventa.cotizacion_id,razonsocial,aprobstatus,aprobobs,oc_id,oc_file,
                (SELECT COUNT(*) 
                FROM notaventadetalle 
                WHERE notaventadetalle.notaventa_id=notaventa.id and 
                notaventadetalle.precioxkilo < notaventadetalle.precioxkiloreal) AS contador
            FROM notaventa inner join cliente
            on notaventa.cliente_id = cliente.id
            where $aux_condid
            and isnull(notaventa.findespacho)
            and isnull(anulada)
            and (aprobstatus=1 or aprobstatus=3)
            and notaventa.id not in (SELECT notaventa_id 
                                    FROM despachosol 
                                    where isnull(despachosol.deleted_at) and despachosol.id 
                                    not in (SELECT despachosolanul.despachosol_id 
                                            from despachosolanul 
                                            where isnull(despachosolanul.deleted_at)
                                           )
                                    )
            and isnull(notaventa.deleted_at)
            order by notaventa.id desc;";
        //dd($sql);
    $datas = DB::select($sql);
    return $datas;

}