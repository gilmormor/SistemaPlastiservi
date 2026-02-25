<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidarProducto;
use App\Models\AreaProduccionSucLinea;
use App\Models\CategoriaProd;
use App\Models\CategoriaProd_Giro;
use App\Models\ClaseProd;
use App\Models\Cliente;
use App\Models\Color;
use App\Models\Empresa;
use App\Models\GrupoProd;
use App\Models\InvBodega;
use App\Models\InvBodegaProducto;
use App\Models\Producto;
use App\Models\Seguridad\Usuario;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Support\Facades\DB;

class ProductoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-producto');
        //$datas = Producto::orderBy('id')->get();
        //return view('producto.index', compact('datas'));
        return view('producto.index');
    }

    public function productopage(){
        /*        
        return datatables()
            ->eloquent(Producto::query()
            ->join('categoriaprod', 'producto.categoriaprod_id', '=', 'categoriaprod.id')
            ->select([
                'producto.*',
                'categoriaprod.nombre as nombrecateg'
            ])
            )
            ->toJson();
        */
        $sql = "SELECT producto.*,producto.glosa as nombre_producto,
        claseprod.cla_nombre,
        categoriaprod.nombre AS categorianombre,gru_nombre,
        grupocatprom.nombre AS grupocatprom_nombre
        FROM producto INNER JOIN categoriaprod
        ON producto.categoriaprod_id = categoriaprod.id
        INNER JOIN grupoprod
        ON grupoprod.id = producto.grupoprod_id AND isnull(grupoprod.deleted_at)
        LEFT JOIN grupocatpromcategoriaprod
        ON grupocatpromcategoriaprod.categoriaprod_id = producto.categoriaprod_id
        LEFT JOIN grupocatprom
        ON grupocatprom.id = grupocatpromcategoriaprod.grupocatprom_id AND isnull(grupocatprom.deleted_at)
        LEFT JOIN claseprod
        ON claseprod.id = producto.claseprod_id AND isnull(claseprod.deleted_at)
        WHERE isnull(producto.deleted_at) AND isnull(categoriaprod.deleted_at)";
        $datas = DB::select($sql);
        return datatables($datas)->toJson();
        /*
        return datatables()
        ->eloquent(Producto::query())
        ->toJson();
        */
    }

    /*
    //SAN BERNARDO
    public function productobuscarpage(Request $request){
        $datas = Producto::productosxClienteTemp($request);
        return datatables($datas)->toJson();
    }
    public function productobuscarpageid(Request $request){
        $datas = Producto::productosxClienteTemp($request);
        return datatables($datas)->toJson();
    }
    */

    public function productobuscarpage(){
        $datas = Producto::productosxUsuarioSQL();
        return datatables($datas)->toJson();

    }
    public function productobuscarpageid(Request $request){
        $datas = Producto::productosxCliente($request);
        return datatables($datas)->toJson();
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function crear()
    {
        can('crear-producto');
        //$categoriaprods = CategoriaProd::orderBy('id')->get();//->pluck('nombre', 'id')->toArray();
        $categoriaprods = CategoriaProd::categoriasxUsuario();
        /*
        $categoriaprods = CategoriaProd::join('categoriaprodsuc', function ($join) {
            $user = Usuario::findOrFail(auth()->id());
            $sucurArray = $user->sucursales->pluck('id')->toArray();
            $join->on('categoriaprod.id', '=', 'categoriaprodsuc.categoriaprod_id')
            ->whereIn('categoriaprodsuc.sucursal_id', $sucurArray);
                    })
            ->select([
                'categoriaprod.id',
                'categoriaprod.nombre',
                'categoriaprod.descripcion',
                'categoriaprod.precio',
                'categoriaprod.areaproduccion_id',
                'categoriaprod.sta_precioxkilo',
                'categoriaprod.unidadmedida_id',
                'categoriaprod.unidadmedidafact_id'
            ])
            ->get();
        */
        $colores = Color::orderBy('id')->get();
        $aux_sta=1;
        return view('producto.crear',compact('categoriaprods','colores','aux_sta'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function guardar(Request $request)
    {
        //dd($request);
        can('guardar-producto');
        DB::beginTransaction();
        try {
            $producto = Producto::create($request->all());
            //return redirect('producto')->with('mensaje','Producto creado con exito');
            $producto->sku = $producto->id;
            $producto->save();

            // Procesar componentes
            if ($request->has('detalles')) {
                foreach ($request->detalles as $detalle) {
                    // Crear nuevo detalle
                    $producto->productocomps()->create([
                        'producto_id' => $producto->id,
                        'productocomp_id' => trim($detalle['productocompiddet']),
                        'cant' => trim($detalle['cantdet']),
                        'obs' => trim($detalle['obsdet']),
                        'usuario_id' => auth()->id()
                    ]);
                }
            }            
            DB::commit();
            return redirect('producto/crear')->with('mensaje','Producto creado con exito');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect('producto/crear')->with([
                'mensaje'=> 'Error: ' . $e->getMessage(),
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
        can('editar-producto');
        $data = Producto::findOrFail($id);
        //$categoriaprods = CategoriaProd::orderBy('id')->get();
        $categoriaprods = CategoriaProd::categoriasxUsuario();
        $invbodegaproductos = $data->invbodegaproductos();//->select(['id as invbodegaproducto_id'])->get();
        /*
        foreach ($invbodegaproductos->get() as $invbodegaproducto) {
            dd($invbodegaproducto);
        }
*/
        /*
        $categoriaprods = CategoriaProd::join('categoriaprodsuc', function ($join) {
            $user = Usuario::findOrFail(auth()->id());
            $sucurArray = $user->sucursales->pluck('id')->toArray();
            $join->on('categoriaprod.id', '=', 'categoriaprodsuc.categoriaprod_id')
            ->whereIn('categoriaprodsuc.sucursal_id', $sucurArray);
                    })
            ->select([
                'categoriaprod.id',
                'categoriaprod.nombre',
                'categoriaprod.descripcion',
                'categoriaprod.precio',
                'categoriaprod.areaproduccion_id',
                'categoriaprod.sta_precioxkilo',
                'categoriaprod.unidadmedida_id',
                'categoriaprod.unidadmedidafact_id'
            ])
            ->get();
        */
        $claseprods = ClaseProd::where('categoriaprod_id',$data->categoriaprod_id)->orderBy('id')->get();
        $grupoprods = GrupoProd::where('categoriaprod_id',$data->categoriaprod_id)->orderBy('id')->get();
        //dd($claseprods);
        $colores = Color::orderBy('id')->get();
        $aux_sta=2;
        return view('producto.editar', compact('data','categoriaprods','claseprods','grupoprods','colores','aux_sta','invbodegaproductos'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function actualizar(Request $request, $id)
    {
        can('guardar-producto');
        //dd($request);
        /* $Producto = Producto::findOrFail($id);
        $detallesActuales = $Producto->productocomps()->pluck('id')->toArray();
        dd($detallesActuales); */

        DB::beginTransaction();
        try {
            $Producto = Producto::findOrFail($id);
            $ProductoOriginal = Producto::findOrFail($id);
            $Producto->update($request->all());
            $ProductoModificado = Producto::findOrFail($id);
            $aux_resp = guardarLogCambioModelo(
                $ProductoModificado,
                [],
                $ProductoOriginal // <- se lo pasamos como estado original
            );
            if(isset($Producto->acuerdotecnico)){
                $Producto->acuerdotecnico->at_claseprod_id = $Producto->claseprod_id;
                $Producto->acuerdotecnico->save();
            }
            // Procesar componentes
            $detallesActuales = $Producto->productocomps()->pluck('id')->toArray();
            $detallesIds = [];


            if ($request->filled('detalles')) {


                foreach ($request->detalles as $detalle) {


                    if (!empty($detalle['id'])) {
                        // Actualizar
                        $detalleModel = $Producto->productocomps()->find($detalle['id']);


                        if ($detalleModel) {
                            $detalleModel->update([
                            'productocomp_id' => trim($detalle['productocompiddet']),
                            'cant' => trim($detalle['cantdet']),
                            'obs' => trim($detalle['obsdet']),
                            'usuario_id' => auth()->id()
                            ]);


                        $detallesIds[] = $detalleModel->id;
                    }


                    } else {
                        // Crear
                        $newDetalle = $Producto->productocomps()->create([
                        'productocomp_id' => trim($detalle['productocompiddet']),
                        'cant' => trim($detalle['cantdet']),
                        'obs' => trim($detalle['obsdet']),
                        'usuario_id' => auth()->id()
                        ]);


                    $detallesIds[] = $newDetalle->id;
                    }
                }


                // Eliminar los que ya no vienen
                $detallesAEliminar = array_diff($detallesActuales, $detallesIds);


            } else {
                // 🔥 NO vienen detalles → eliminar TODOS
                $detallesAEliminar = $detallesActuales;
            }

            // Ejecutar eliminación
            foreach ($detallesAEliminar as $detalleId) {
                $detalle = $Producto->productocomps()->find($detalleId);


                if ($detalle) {
                    $detalle->update(['usuariodel_id' => auth()->id()]);
                    $detalle->delete();
                }
            }
            DB::commit();
            return redirect('producto')->with('mensaje','Producto actualizado con exito');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect('producto')->with([
                'mensaje'=> 'Error: ' . $e->getMessage(),
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
        if(can('eliminar-producto',false)){
            if ($request->ajax()) {
                //dd($request->id);
                $data = Producto::findOrFail($request->id);
                $aux_contRegistos = $data->cotizaciondetalles->count() + $data->notaventadetalles->count();
                //dd($aux_contRegistos);
                if($aux_contRegistos > 0){
                    return response()->json(['mensaje' => 'cr']);
                }else{
                    if (Producto::destroy($request->id)) {
                        //dd('entro');
                        //Despues de eliminar actualizo el campo usuariodel_id=usuario que elimino el registro
                        $producto = Producto::withTrashed()->findOrFail($request->id);
                        $producto->usuariodel_id = auth()->id();
                        $producto->save();
                        return response()->json(['mensaje' => 'ok']);
                    } else {
                        return response()->json(['mensaje' => 'ng']);
                    }    
                }
            } else {
                abort(404);
            }
        }else{
            return response()->json(['mensaje' => 'ne']);
        }
    }

    public function obtClaseProd(Request $request)
    {
        if($request->ajax()){

            $claseprods = ClaseProd::where('categoriaprod_id', $request->categoriaprod_id)->get();
            foreach($claseprods as $claseprod){
                $claseprodsArray[$claseprod->id] = $claseprod->cla_nombre;
            }
            //dd($claseprods);
            return response()->json($claseprods);
        }
    }

    public function buscarproducto()
    {
        $productos = Producto::productosxUsuario();
        return response()->json($productos);
    }

    public function buscarUnProductoComp(Request $request){
        $respuesta = $this->buscarUnProducto($request);
        foreach ($respuesta['productocomps'] as &$productocomp) {
            $requestComp = $request;
            $requestComp->id = $productocomp->productocomp_id;
            $productocomp["producto"] = $this->buscarUnProducto($requestComp);
        }
        return $respuesta;
    }

    public function buscarUnProducto(Request $request)
    {
        if($request->ajax()){
            //$request->mostrarTodo VIENE DEL FORMULARIO, SI NO EXISTE $mostrarTodo LE ASIGNO POR DEFECTO TRUE, BUSCA TODOS LOS PRODUCTOS
            //SI $request->mostrarTodo EXISTE Y PASA POR EL PRIMER IF Y ES IGUAL A 0 $mostrasTodo = false, ES DECIR QUE SOLO BUSCA LOS PRODUCTOS QUE SEAN tipoprod = 0 PRODUCTOS NORMALES
            $mostrarTodo = true;
            if(isset($request->mostrarTodo) and $request->mostrarTodo != null and $request->mostrarTodo != ""){
                if($request->mostrarTodo == "0"){
                    $mostrarTodo = false;
                }
            }
            // BUscar un producto dependiendo si el usuario tiene acceso a dicho producto. Por la sucursal del Usuario y producto
            $users = Usuario::findOrFail(auth()->id());
            $sucurArray = $users->sucursales->pluck('id')->toArray();

            $valor = $request->id;

            /* $productoBase = Producto::where('id', $valor)
                ->orWhere('sku', $valor)
                ->whereNull('deleted_at')
                ->first();
            if ($productoBase) {
                $productoId = $productoBase->id;
            } else {
                $productoId = 0;
            } */
            $productoId = $valor;
            //Filtrando las categorias por sucursal, dependiendo de las sucursales asignadas al usuario logueado
            //******************* */
            $productos = CategoriaProd::where('producto.id',$productoId)
            ->when(!$mostrarTodo,function ($query) {
                // Si $mostrarTodo es falso, agrega la condición para tipoprod=0
                return $query->where(function ($q) {
                    $q->where('producto.tipoprod', 0)
                      ->orWhere('producto.tipoprod', 3);
                });
            })
            ->join('categoriaprodsuc', 'categoriaprod.id', '=', 'categoriaprodsuc.categoriaprod_id')
            ->join('sucursal', 'categoriaprodsuc.sucursal_id', '=', 'sucursal.id')
            ->join('producto', 'categoriaprod.id', '=', 'producto.categoriaprod_id')
            ->join('claseprod', 'producto.claseprod_id', '=', 'claseprod.id')
            ->leftJoin('unidadmedida', 'categoriaprod.unidadmedidafact_id', '=', 'unidadmedida.id')
            //->leftJoin('acuerdotecnico', 'producto.id', '=', 'acuerdotecnico.producto_id')
            ->leftJoin('acuerdotecnico', function($join) {
                $join->on('producto.id', '=', 'acuerdotecnico.producto_id')
                    ->where('categoriaprod.omitiracutec', 0);
            })
            ->leftJoin('tiposello', 'acuerdotecnico.at_tiposello_id', '=', 'tiposello.id')
            ->select([
                    'producto.id',
                    'producto.nombre',
                    'producto.glosa',
                    'claseprod.cla_nombre',
                    'producto.codintprod',
                    'producto.diamextmm',
                    'producto.diamextpg',
                    'producto.diametro',
                    'producto.espesor',
                    'producto.long',
                    'producto.peso',
                    'producto.tipounion',
                    'producto.precioneto',
                    'producto.estado',
                    'producto.tipoprod',
                    'producto.categoriaprod_id',
                    'categoriaprod.nombre as categoriaprod_nombre',
                    'categoriaprod.precio',
                    'categoriaprodsuc.sucursal_id',
                    'categoriaprod.unidadmedida_id',
                    'categoriaprod.unidadmedidafact_id',
                    'categoriaprod.mostdatosad',
                    'categoriaprod.mostunimed',
                    'categoriaprod.stakilos',
                    'unidadmedida.nombre as unidadmedidanombre',
                    'acuerdotecnico.id as acuerdotecnico_id',
                    'acuerdotecnico.at_ancho',
                    'acuerdotecnico.at_largo',
                    'acuerdotecnico.at_espesor',
                    'acuerdotecnico.at_fuelle',
                    'tiposello.desc as at_tiposello_desc',
                    'acuerdotecnico.at_unidadmedida_id',
                    'claseprod.cla_descripcion'
                    ])
            ->whereIn('categoriaprodsuc.sucursal_id', $sucurArray)
            ->where('producto.deleted_at','=',null)
            ->groupBy('producto.id');
            //****************** */
            $array_productos = $productos->get()->toArray();
            $respuesta = array();
            if(count($array_productos) > 0){
                $respuesta = $array_productos[0];
                if($request->cliente_id){
                    $cliente = Cliente::findOrFail($request->cliente_id);
                    $categoriaprod_giro = CategoriaProd_Giro::where('categoriaprod_id', $array_productos[0]["categoriaprod_id"])
                        ->where('giro_id', $cliente->giro_id)
                        ->where('preciokg','>',0)
                        ->get();
                    if(count($categoriaprod_giro) > 0){
                        $respuesta["precio"] = $categoriaprod_giro[0]->preciokg;
                    }
                }
                //dd($respuesta);
                $producto = Producto::findOrFail($productoId);
                //dd($producto->productocomps); // Cargar los componentes relacionados
                $respuesta['nombre'] = $producto->glosa;
                $respuesta['sku'] = $producto->sku;
                $respuesta['precioneto'] = $producto->precioneto;
                foreach ($producto->productocomps as &$productocomp) {
                    $productocomp->precioneto = $productocomp->productocomp->precioneto;
                }
                $respuesta['productocomps'] = $producto->productocomps;
                //dd($producto->productocomps);
                //dd($respuesta);
                $respuesta['precioneto'] = $producto->precioneto;

                $respuesta['bodegas'] = $producto->categoriaprod->invbodegas->where('tipo','=',2)->where('activo','=',1)->toArray();
                $respuesta['bodegasPicking'] = $producto->invbodegaproductos()
                                                ->whereHas('invbodega', function($query) {
                                                    $query->where('tipo', 1)->where('activo', 1);
                                                })
                                                ->with([
                                                    'invbodega.sucursal', // Bodega con su sucursal
                                                    'producto.categoriaprod' // Producto con su categoría
                                                ])
                                                ->get();
                //$respuesta['areaproduccion'] = $producto->categoriaprod->areaproduccion->toArray();
                $respuesta['areaproduccionsucs'] = $producto->categoriaprod->areaproduccion->areaproduccionsucs->toArray();
                $respuesta['areaproduccionsuclineas'] = AreaProduccionSucLinea::get();
                $respuesta['acuerdotecnico'] = $producto->acuerdotecnico;
                if($producto->acuerdotecnico){
                    $respuesta['at_color_nombre'] = $producto->acuerdotecnico->color->descripcion;
                    $respuesta['at_materiaprima_nombre'] = $producto->acuerdotecnico->materiaprima->descfact;
                }
                //dd($respuesta['bodegas']);
                foreach ($respuesta['bodegas'] as &$bodega) {
                    $request1 = new Request();
                    $request1["producto_id"] = $productoId;
                    $request1["invbodega_id"] = $bodega["id"];
                    $request1["tipo"] = 2;
                    $aux_stosk = InvBodegaProducto::existencia($request1);
                    $bodega["stock"] = $aux_stosk["stock"]["cant"];
                    $invbodega = InvBodega::findOrFail($bodega["id"]);
                    $bodega["sucursal_id"] = $invbodega->sucursal_id;
                    $bodega["sucursal_nombre"] = $invbodega->sucursal->nombre;
                    //dd($bodega);
                }
            }
            $respuesta['cont'] = count($array_productos);
            //$array_productos['array'] = count($array_productos);
            //dd($respuesta);

            //dd(response()->json($productos->get()));

            return $respuesta;
            //return response()->json($productos->get());
        }
    }

    public function listar($id)
    {
        $productos = Producto::orderBy('id')->get();;
        $empresa = Empresa::orderBy('id')->get();
        //dd($productos);
        return view('producto.listado', compact('productos','empresa'));
        
        $pdf = PDF::loadView('producto.listado', compact('productos','empresa'));
        //return $pdf->download('cotizacion.pdf');
        return $pdf->stream();
        
    }

    public function obtGrupoProd(Request $request)
    {
        if($request->ajax()){

            $grupoprods = GrupoProd::where('categoriaprod_id', $request->categoriaprod_id)->get();
            foreach($grupoprods as $grupoprod){
                $grupoprodsArray[$grupoprod->id] = $grupoprod->gru_nombre;
            }
            //dd($claseprods);
            return response()->json($grupoprods);
        }
    }

    public function AcuTecExportPdf($id)
    {
        $producto = Producto::findOrFail($id);
        $empresa = Empresa::orderBy('id')->get();
        //dd($empresa[0]['iva']);
        if(env('APP_DEBUG')){
            return view('generales.acuerdotecnicopdf', compact('producto','empresa'));
        }   
        $pdf = PDF::loadView('generales.acuerdotecnicopdf', compact('producto','empresa'));
        //return $pdf->download('cotizacion.pdf');
        return $pdf->stream("AcuTecProd_" . str_pad($producto->id, 5, "0", STR_PAD_LEFT) . '.pdf');
    }

}
