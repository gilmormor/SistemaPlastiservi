<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidarCotizacion;
use App\Models\CategoriaProd;
use App\Models\Cliente;
use App\Models\ClienteDirec;
use App\Models\ClienteSucursal;
use App\Models\ClienteTemp;
use App\Models\ClienteVendedor;
use App\Models\Comuna;
use App\Models\Cotizacion;
use App\Models\CotizacionDetalle;
use App\Models\Empresa;
use App\Models\FormaPago;
use App\Models\Giro;
use App\Models\PlazoPago;
use App\Models\Producto;
use App\Models\Provincia;
use App\Models\Region;
use App\Models\Seguridad\Usuario;
use App\Models\Sucursal;
use App\Models\SucursalClienteDirec;
use App\Models\TipoEntrega;
use App\Models\Vendedor;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade as PDF;

class CotizacionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
        can('listar-cotizacion');
        //session(['aux_aprocot' => '0']) 0=Pantalla Normal CRUD de Cotizaciones
        //session(['aux_aprocot' => '1']) 1=Pantalla Solo para aprobar cotizacion para luego emitir la Nota de Venta

        /*
        
        session(['aux_aprocot' => '0']);
        $user = Usuario::findOrFail(auth()->id());
        $aux_statusPant = 0;
        $sql= 'SELECT COUNT(*) AS contador
            FROM vendedor INNER JOIN persona
            ON vendedor.persona_id=persona.id and vendedor.deleted_at is null
            INNER JOIN usuario 
            ON persona.usuario_id=usuario.id and persona.deleted_at is null
            WHERE usuario.id=' . auth()->id() . ';';
        $counts = DB::select($sql);
        if($counts[0]->contador>0){
            $vendedor_id=$user->persona->vendedor->id;
            $aux_condvend = 'cotizacion.vendedor_id = ' . $vendedor_id;
        }else{
            $aux_condvend = 'true';
        }
        //Se consultan los registros que estan sin aprobar por vendedor null o 0 y los rechazados por el supervisor rechazado por el supervisor=4
        $sql = 'SELECT cotizacion.id,cotizacion.fechahora,
                    if(isnull(cliente.razonsocial),clientetemp.razonsocial,cliente.razonsocial) as razonsocial,
                    aprobstatus,aprobobs, 
                    (SELECT COUNT(*) 
                    FROM cotizaciondetalle 
                    WHERE cotizaciondetalle.cotizacion_id=cotizacion.id and 
                    cotizaciondetalle.precioxkilo < cotizaciondetalle.precioxkiloreal) AS contador
                FROM cotizacion left join cliente
                on cotizacion.cliente_id = cliente.id
                left join clientetemp
                on cotizacion.clientetemp_id = clientetemp.id
                where ' . $aux_condvend . ' and (aprobstatus is null or aprobstatus=0 or aprobstatus=4) 
                and cotizacion.deleted_at is null
                ORDER BY cotizacion.id desc;';

        $datas = DB::select($sql);
        */
        
        //dd(session('aux_aprocot'));
       
        //$datas = Cotizacion::where('usuario_id',auth()->id())->get();
        //return view('cotizacion.index_sin_datatable_servidor', compact('datas'));
        return view('cotizacion.index');
    }

    public function cotizacionpage(){
        session(['aux_aprocot' => '0']);
        $user = Usuario::findOrFail(auth()->id());
        $aux_statusPant = 0;
        $sql= 'SELECT COUNT(*) AS contador
            FROM vendedor INNER JOIN persona
            ON vendedor.persona_id=persona.id and vendedor.deleted_at is null
            INNER JOIN usuario 
            ON persona.usuario_id=usuario.id and persona.deleted_at is null
            WHERE usuario.id=' . auth()->id() . ';';
        $counts = DB::select($sql);
        if($counts[0]->contador>0){
            $vendedor_id=$user->persona->vendedor->id;
            $aux_condvend = 'cotizacion.vendedor_id = ' . $vendedor_id;
        }else{
            $aux_condvend = 'true';
        }
        //Se consultan los registros que estan sin aprobar por vendedor null o 0 y los rechazados por el supervisor rechazado por el supervisor=4
        $sql = "SELECT cotizacion.id,DATE_FORMAT(cotizacion.fechahora,'%d/%m/%Y %h:%i %p') as fechahora,
                    if(isnull(cliente.razonsocial),clientetemp.razonsocial,cliente.razonsocial) as razonsocial,
                    aprobstatus,aprobobs,'' as pdfcot,
                    (SELECT COUNT(*) 
                    FROM cotizaciondetalle 
                    WHERE cotizaciondetalle.cotizacion_id=cotizacion.id and 
                    cotizaciondetalle.precioxkilo < cotizaciondetalle.precioxkiloreal) AS contador
                FROM cotizacion left join cliente
                on cotizacion.cliente_id = cliente.id
                left join clientetemp
                on cotizacion.clientetemp_id = clientetemp.id
                where $aux_condvend and (aprobstatus is null or aprobstatus=0 or aprobstatus=4) 
                and cotizacion.deleted_at is null
                ORDER BY cotizacion.id desc;";

        $datas = DB::select($sql);
        //dd($datas);

        return datatables($datas)->toJson();

        
    }
    /*
    public function consulta(){
        $cotizacionDetalle = CotizacionDetalle::where('cotizacion_id','14')->get()->count();
        return $cotizacionDetalle;
    }
    */
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function crear()
    {
        can('crear-cotizacion');
        //dd('Entro');
        /*
        $user = Usuario::findOrFail(auth()->id());
        $sql= 'SELECT COUNT(*) AS contador
            FROM vendedor INNER JOIN persona
            ON vendedor.persona_id=persona.id
            INNER JOIN usuario 
            ON persona.usuario_id=usuario.id
            WHERE usuario.id=' . auth()->id();
        $counts = DB::select($sql);
        $vendedor_id = '0';
        //dd($counts[0]->contador);
        if($counts[0]->contador>0){
            $vendedor_id=$user->persona->vendedor->id;
            $clientevendedorArray = ClienteVendedor::where('vendedor_id',$vendedor_id)->pluck('cliente_id')->toArray();
        }else{
            $clientevendedorArray = ClienteVendedor::pluck('cliente_id')->toArray();
        }
        //dd($vendedor_id);
        $sucurArray = $user->sucursales->pluck('id')->toArray();
        // Filtro solos los clientes que esten asignados a la sucursal y asignado al vendedor logueado
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

        //dd($clientes);
        //pluck('id','rut','cliente.razonsocial','cliente.direccionprinc')->toArray();

        $fecha = date("d/m/Y");

        $formapagos = FormaPago::orderBy('id')->get();
        $plazopagos = PlazoPago::orderBy('id')->get();
        $vendedores = Vendedor::orderBy('id')->where('sta_activo',1)->get();
/*        $vendedores1 = Vendedor::findOrFail(1);
        dd($vendedores1->persona->usuario);*/
        $comunas = Comuna::orderBy('id')->get();
        $provincias = Provincia::orderBy('id')->get();
        $regiones = Region::orderBy('id')->get();
        $productos = Producto::productosxUsuario();
        /*
        $users = Usuario::findOrFail(auth()->id());
        $sucurArray = $users->sucursales->pluck('id')->toArray();
        //Filtrando las categorias por sucursal, dependiendo de las sucursales asignadas al usuario logueado
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
                'categoriaprodsuc.sucursal_id',
                'categoriaprod.unidadmedida_id'
                ])
                ->whereIn('categoriaprodsuc.sucursal_id', $sucurArray)
                ->get();
        */
        //****************** */
        //dd($clientedirecs);
        $empresa = Empresa::findOrFail(1);
        $tipoentregas = TipoEntrega::orderBy('id')->get();
        $giros = Giro::orderBy('id')->get();
        $sucursales = Sucursal::orderBy('id')->whereIn('sucursal.id', $sucurArray)->get();
        $aux_sta=1;
        $aux_statusPant='0';
        //dd($sucurArray);
        $sql = "SELECT usuario,usuario.email,persona.nombre
        FROM usuario INNER JOIN sucursal_usuario
        ON usuario.id=sucursal_usuario.usuario_id
        INNER JOIN persona 
        ON usuario.id=persona.usuario_id
        INNER JOIN vendedor 
        ON persona.id=vendedor.persona_id AND vendedor.sta_activo=1
        where sucursal_usuario.sucursal_id IN (" . implode(",",$sucurArray) . ")";
        $vend = DB::select($sql);

        $vendedores = Vendedor::orderBy('id')->where('sta_activo',1)->get();

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

        //dd($vendedores1);
        $respuesta = array();
        $respuesta['exito'] = false;
        $respuesta['comuna'] = "";
        $respuesta['comuna'] = "<option value='' selected>Seleccione...</option>";
        foreach($comunas as $comuna){
            $respuesta['comuna'] .= 
            "<option
                value=$comuna->id>
                $comuna->nombre
            </option>";
        }
        //dd($respuesta['comuna']);


        return view('cotizacion.crear',compact('clientes','formapagos','plazopagos','vendedores','vendedores1','fecha','comunas','provincias','regiones','productos','empresa','tipoentregas','vendedor_id','giros','sucurArray','sucursales','aux_sta','aux_statusPant','respuesta'));
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    //ValidarCotizacion
    public function guardar(ValidarCotizacion $request)
    {
        
        can('guardar-cotizacion');
        $aux_rut=str_replace('.','',$request->rut);
        $request->rut = str_replace('-','',$aux_rut);
        //dd($request);
        if(!empty($request->razonsocialCTM)){
            $array_clientetemp = [
                'rut' => $request->rut,
                'razonsocial' => $request->razonsocial,
                'direccion' => $request->direccion,
                'telefono' => $request->telefono,
                'email' => $request->email,
                'vendedor_id' => $request->vendedor_id,
                'giro_id' => $request->giro_id,
                'comunap_id' => $request->comunap_idCTM,
                'formapago_id' => $request->formapago_id,
                'plazopago_id' => $request->plazopago_id,
                'contactonombre'=>  $request->contactonombreCTM,
                'contactoemail' => $request->contactoemailCTM,
                'contactotelef' => $request->contactotelefCTM,
                'finanzascontacto'=>  $request->finanzascontactoCTM,
                'finanzanemail' => $request->finanzanemailCTM,
                'finanzastelefono' => $request->finanzastelefonoCTM,
                'sucursal_id' => $request->sucursal_idCTM,
                'observaciones' => $request->observacionesCTM
            ];
            if(ClienteTemp::where('rut', $request->rut)->count()>0){
                $clientetemp = ClienteTemp::where('rut', $request->rut)->get();
                ClienteTemp::where('rut', $request->rut)->update($array_clientetemp);
                $request->request->add(['clientetemp_id' => $clientetemp[0]->id]);
            }else{
                $clientetemp = ClienteTemp::create($array_clientetemp);
                $request->request->add(['clientetemp_id' => $clientetemp->id]);
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
        $cotizacion = Cotizacion::create($request->all());
        $cotizacionid = $cotizacion->id;

        $cont_producto = count($request->producto_id);
        if($cont_producto>0){
            for ($i=0; $i < $cont_producto ; $i++){
                if(is_null($request->producto_id[$i])==false && is_null($request->cant[$i])==false){
                    $producto = Producto::findOrFail($request->producto_id[$i]);
                    $cotizaciondetalle = new CotizacionDetalle();
                    $cotizaciondetalle->cotizacion_id = $cotizacionid;
                    $cotizaciondetalle->producto_id = $request->producto_id[$i];
                    $cotizaciondetalle->cant = $request->cant[$i];
                    $cotizaciondetalle->unidadmedida_id = $request->unidadmedida_id[$i];
                    $cotizaciondetalle->descuento = $request->descuento[$i];
                    $cotizaciondetalle->preciounit = $request->preciounit[$i];
                    $cotizaciondetalle->peso = $producto->peso;
                    $cotizaciondetalle->precioxkilo = $request->precioxkilo[$i];
                    $cotizaciondetalle->precioxkiloreal = $request->precioxkiloreal[$i];
                    $cotizaciondetalle->totalkilos = $request->totalkilos[$i];
                    $cotizaciondetalle->subtotal = $request->subtotal[$i];
                    $cotizaciondetalle->save();
                    $idDireccion = $cotizaciondetalle->id;
                }
            }
        }
        //return redirect('cotizacion')->with('mensaje','Cotización creada con exito');
        return redirect('cotizacion')->with('mensaje','Cotización creada con exito!');
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
        can('editar-cotizacion');
        $data = Cotizacion::findOrFail($id);
        $data->plazoentrega = $newDate = date("d/m/Y", strtotime($data->plazoentrega));
        $cotizacionDetalles = $data->cotizaciondetalles()->get();
        $vendedor_id=$data->vendedor_id;
        if(empty($data->cliente_id)){
            $clienteselec = $data->clientetemp()->get();
        }else{
            $clienteselec = $data->cliente()->get();
        }
        //dd($clienteselec);

        //Aqui si estoy filtrando solo las categorias de asignadas al usuario logueado
        //******************* */
        $clientedirecs = Cliente::where('rut', $clienteselec[0]->rut)
                    ->join('clientedirec', 'cliente.id', '=', 'clientedirec.cliente_id')
                    ->select([
                                'cliente.id' => 'cliente_id',
                                'cliente.razonsocial',
                                'cliente.telefono',
                                'cliente.email',
                                'cliente.direccion',
                                'cliente.regionp_id',
                                'cliente.provinciap_id',
                                'cliente.comunap_id',
                                'clientedirec.id',
                                'clientedirec.direcciondetalle',
                            ])->get();
        //dd($clientedirecs);

        $clienteDirec = $data->clientedirec()->get();
        //dd($clienteDirec);
        $fecha = date("d/m/Y", strtotime($data->fechahora));
        $formapagos = FormaPago::orderBy('id')->get();
        $plazopagos = PlazoPago::orderBy('id')->get();
        $vendedores = Vendedor::orderBy('id')->get();
        $comunas = Comuna::orderBy('id')->get();
        $provincias = Provincia::orderBy('id')->get();
        $regiones = Region::orderBy('id')->get();
        /*
        $users = Usuario::findOrFail(auth()->id());
        $sucurArray = $users->sucursales->pluck('id')->toArray();
        */
        $clientesArray = Cliente::clientesxUsuario();
        $clientes = $clientesArray['clientes'];
        $vendedor_id = $clientesArray['vendedor_id'];
        $sucurArray = $clientesArray['sucurArray'];
        $productos = Producto::productosxUsuario();
        /*
        //Filtrando las categorias por sucursal, dependiendo de las sucursales asignadas al usuario logueado
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
                'categoriaprodsuc.sucursal_id',
                'categoriaprod.unidadmedida_id'
                ])
                ->whereIn('categoriaprodsuc.sucursal_id', $sucurArray)
                ->get();
        */
        //****************** */
        /*
        $clientevendedorArray = ClienteVendedor::where('vendedor_id',$vendedor_id)->pluck('cliente_id')->toArray();
        // Filtro solos los clientes que esten asignados a la sucursal
        
        $clientes = Cliente::select(['cliente.id','cliente.rut','cliente.razonsocial','cliente.direccion','cliente.telefono','cliente.giro_id'])
        ->whereIn('cliente.id' , ClienteSucursal::select(['cliente_sucursal.cliente_id'])
                                ->whereIn('cliente_sucursal.sucursal_id', $sucurArray)
        ->pluck('cliente_sucursal.cliente_id')->toArray())
        ->whereIn('cliente.id',$clientevendedorArray)
        ->get();
        */


        //dd($clientes);

        $empresa = Empresa::findOrFail(1);
        $tipoentregas = TipoEntrega::orderBy('id')->get();
        $giros = Giro::orderBy('id')->get();
        $sucursales = Sucursal::orderBy('id')->whereIn('sucursal.id', $sucurArray)->get();
        $aux_sta=2;
        $aux_statusPant = 0;

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
        /*
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
        */
        //dd($clientedirecs);
        return view('cotizacion.editar', compact('data','clienteselec','clientes','clienteDirec','clientedirecs','cotizacionDetalles','comunas','provincias','regiones','formapagos','plazopagos','vendedores','vendedores1','productos','fecha','empresa','tipoentregas','giros','sucurArray','sucursales','aux_sta','aux_cont','aux_statusPant','vendedor_id'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    //ValidarCotizacion
    public function actualizar(ValidarCotizacion $request, $id)
    {
        //dd($request);
        can('guardar-cotizacion');
        $cotizacion = Cotizacion::findOrFail($id);
        $request->request->add(['fechahora' => $cotizacion->fechahora]);
        $aux_plazoentrega= DateTime::createFromFormat('d/m/Y', $request->plazoentrega)->format('Y-m-d');
        $request->request->add(['plazoentrega' => $aux_plazoentrega]);
        //dd($request->plazoentrega);
        $cotizacion->update($request->all());
        $auxCotDet=CotizacionDetalle::where('cotizacion_id',$id)->whereNotIn('id', $request->cotdet_id)->pluck('id')->toArray(); //->destroy();
        for ($i=0; $i < count($auxCotDet) ; $i++){
            CotizacionDetalle::destroy($auxCotDet[$i]);
        }
        $cont_cotdet = count($request->cotdet_id);
        if($cont_cotdet>0){
            for ($i=0; $i < count($request->cotdet_id) ; $i++){
                $idcotizaciondet = $request->cotdet_id[$i]; 
                $producto = Producto::findOrFail($request->producto_id[$i]);
                if( $request->cotdet_id[$i] == '0' ){
                    $cotizaciondetalle = new CotizacionDetalle();
                    $cotizaciondetalle->cotizacion_id = $id;
                    $cotizaciondetalle->producto_id = $request->producto_id[$i];
                    $cotizaciondetalle->cant = $request->cant[$i];
                    $cotizaciondetalle->unidadmedida_id = $request->unidadmedida_id[$i];
                    $cotizaciondetalle->descuento = $request->descuento[$i];
                    $cotizaciondetalle->preciounit = $request->preciounit[$i];
                    $cotizaciondetalle->peso = $producto->peso;
                    $cotizaciondetalle->precioxkilo = $request->precioxkilo[$i];
                    $cotizaciondetalle->precioxkiloreal = $request->precioxkiloreal[$i];
                    $cotizaciondetalle->totalkilos = $request->totalkilos[$i];
                    $cotizaciondetalle->subtotal = $request->subtotal[$i];
                    $cotizaciondetalle->save();
                    $idcotizaciondet = $cotizaciondetalle->id;
                    //dd($idDireccion);
                }else{
                    //dd($idDireccion);
                    DB::table('cotizaciondetalle')->updateOrInsert(
                        ['id' => $request->cotdet_id[$i], 'cotizacion_id' => $id],
                        [
                            'producto_id' => $request->producto_id[$i],
                            'cant' => $request->cant[$i],
                            'unidadmedida_id' => $request->unidadmedida_id[$i],
                            'descuento' => $request->descuento[$i],
                            'preciounit' => $request->preciounit[$i],
                            'peso' => $producto->peso,
                            'precioxkilo' => $request->precioxkilo[$i],
                            'precioxkiloreal' => $request->precioxkiloreal[$i],
                            'totalkilos' => $request->totalkilos[$i],
                            'subtotal' => $request->subtotal[$i],
                        ]
                    );
                }
            }
        }
        //return redirect('cotizacion/'.$id.'/editar')->with('mensaje','Cliente actualizado con exito!');
        return redirect('cotizacion')->with('mensaje','Cliente actualizado con exito!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function eliminar(Request $request,$id)
    {
        can('eliminar-cotizacion');
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
                                'cliente.direccion',
                                'cliente.vendedor_id',
                                'clientedirec.id',
                                'clientedirec.direcciondetalle',
                                'clientedirec.comuna_id'
                            ]);
            //dd($clientedirecs->get());
            return response()->json($clientedirecs->get());
        }
    }

    public function aprobarcotvend(Request $request)
    {
        //dd($request);
        can('guardar-cotizacion');
        if ($request->ajax()) {
            $cotizacion = Cotizacion::findOrFail($request->id);
            $cotizacion->aprobstatus = $request->aprobstatus;    
            if($request->aprobstatus=='1'){
                $cotizacion->aprobusu_id = auth()->id();
                $cotizacion->aprobfechahora = date("Y-m-d H:i:s");
                $cotizacion->aprobobs = 'Aprobado por el mismo vendedor';
            }
            if ($cotizacion->save()) {
                return response()->json(['mensaje' => 'ok']);
            } else {
                return response()->json(['mensaje' => 'ng']);
            }
        } else {
            abort(404);
        }

    }

    public function aprobarcotsup(Request $request)
    {
        //dd($request);
        can('guardar-cotizacion');
        if ($request->ajax()) {
            $cotizacion = Cotizacion::findOrFail($request->id);
            $cotizacion->aprobstatus = $request->valor;
            $cotizacion->aprobusu_id = auth()->id();
            $cotizacion->aprobfechahora = date("Y-m-d H:i:s");
            $cotizacion->aprobobs = $request->obs;
            
            if ($cotizacion->save()) {
                return response()->json(['mensaje' => 'ok']);
            } else {
                return response()->json(['mensaje' => 'ng']);
            }
        } else {
            abort(404);
        }

    }

    public function buscarCotizacion(Request $request){
        if($request->ajax()){
            $user = Usuario::findOrFail(auth()->id());
            $sql= 'SELECT COUNT(*) AS contador
                FROM vendedor INNER JOIN persona
                ON vendedor.persona_id=persona.id and vendedor.deleted_at is null
                INNER JOIN usuario 
                ON persona.usuario_id=usuario.id and persona.deleted_at is null
                WHERE usuario.id=' . auth()->id() . ';';
            $counts = DB::select($sql);
            $aux_condvend = "true ";
            if($counts[0]->contador > 0){
                $vendedor_id=$user->persona->vendedor->id;
                $aux_condvend = "cotizacion.vendedor_id= $vendedor_id ";
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
                    where $aux_condvend and (aprobstatus=1 or aprobstatus=3) 
                    and cotizacion.id = $request->id 
                    and cotizacion.deleted_at is null;";
            //where usuario_id='.auth()->id();
            //dd($sql);
            $cotizaciones = DB::select($sql);
            //dd($cotizaciones);
            
            //dd($clientedirecs->get());
            return response()->json($cotizaciones);
        }
    }

    public function exportPdf($id)
    {
        $cotizacion = Cotizacion::findOrFail($id);
        $cotizacionDetalles = $cotizacion->cotizaciondetalles()->get();
        $empresa = Empresa::orderBy('id')->get();
        if($cotizacion->cliente){
            $aux_razonsocial = $cotizacion->cliente->razonsocial;
        }else{
            $aux_razonsocial = $cotizacion->clientetemp->razonsocial;
        }
        //dd($cotizacion->cliente);
        //$rut = number_format( substr ( $cotizacion->cliente->rut, 0 , -1 ) , 0, "", ".") . '-' . substr ( $cotizacion->cliente->rut, strlen($cotizacion->cliente->rut) -1 , 1 );
        //dd($empresa[0]['iva']);
        //return view('cotizacion.listado', compact('cotizacion','cotizacionDetalles','empresa'));
        if(env('APP_DEBUG')){
            return view('cotizacion.listado', compact('cotizacion','cotizacionDetalles','empresa'));
        }
        $pdf = PDF::loadView('cotizacion.listado', compact('cotizacion','cotizacionDetalles','empresa'));
        //return $pdf->download('cotizacion.pdf');
        return $pdf->stream(str_pad($cotizacion->id, 5, "0", STR_PAD_LEFT) .' - '. $aux_razonsocial . '.pdf');
        
    }

    public function exportPdfM($id,$stareport = '1')
    {
        $cotizacion = Cotizacion::findOrFail($id);
        $cotizacionDetalles = $cotizacion->cotizaciondetalles()->get();
        $empresa = Empresa::orderBy('id')->get();
        if($cotizacion->cliente){
            $aux_razonsocial = $cotizacion->cliente->razonsocial;
        }else{
            $aux_razonsocial = $cotizacion->clientetemp->razonsocial;
        }
        //dd($cotizacion->cliente);
        //$rut = number_format( substr ( $cotizacion->cliente->rut, 0 , -1 ) , 0, "", ".") . '-' . substr ( $cotizacion->cliente->rut, strlen($cotizacion->cliente->rut) -1 , 1 );
        //dd($empresa[0]['iva']);
        //return view('cotizacion.listado', compact('cotizacion','cotizacionDetalles','empresa'));
        if(env('APP_DEBUG')){
            return view('cotizacion.listado', compact('cotizacion','cotizacionDetalles','empresa'));
        }
        $pdf = PDF::loadView('cotizacion.listado', compact('cotizacion','cotizacionDetalles','empresa'));
        //return $pdf->download('cotizacion.pdf');
        return $pdf->stream(str_pad($cotizacion->id, 5, "0", STR_PAD_LEFT) .' - '. $aux_razonsocial . '.pdf');
        
    }

}