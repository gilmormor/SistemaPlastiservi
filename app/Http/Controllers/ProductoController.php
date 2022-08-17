<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidarProducto;
use App\Models\CategoriaProd;
use App\Models\ClaseProd;
use App\Models\Color;
use App\Models\Empresa;
use App\Models\GrupoProd;
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
        $sql = "SELECT producto.*,categoriaprod.nombre AS categorianombre
        FROM producto INNER JOIN categoriaprod
        ON producto.categoriaprod_id = categoriaprod.id
        WHERE isnull(producto.deleted_at) AND isnull(categoriaprod.deleted_at)";
        $datas = DB::select($sql);
        return datatables($datas)->toJson();
        /*
        return datatables()
        ->eloquent(Producto::query())
        ->toJson();
        */
    }

    public function productobuscarpage(){
        $datas = Producto::productosxUsuarioSQL();
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
        Producto::create($request->all());
        //return redirect('producto')->with('mensaje','Producto creado con exito');
        return redirect('producto/crear')->with('mensaje','Producto creado con exito');
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
        $Producto = Producto::findOrFail($id);
        $Producto->update($request->all());

        return redirect('producto')->with('mensaje','Producto actualizado con exito');
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

    public function buscarUnProducto(Request $request)
    {
        if($request->ajax()){
            // BUscar un producto dependiendo si el usuario tiene acceso a dicho producto. Por la sucursal del Usuario y producto
            $users = Usuario::findOrFail(auth()->id());
            $sucurArray = $users->sucursales->pluck('id')->toArray();
            //Filtrando las categorias por sucursal, dependiendo de las sucursales asignadas al usuario logueado
            //******************* */
            $productos = CategoriaProd::where('producto.id',$request->id)
            ->join('categoriaprodsuc', 'categoriaprod.id', '=', 'categoriaprodsuc.categoriaprod_id')
            ->join('sucursal', 'categoriaprodsuc.sucursal_id', '=', 'sucursal.id')
            ->join('producto', 'categoriaprod.id', '=', 'producto.categoriaprod_id')
            ->join('claseprod', 'producto.claseprod_id', '=', 'claseprod.id')
            ->leftJoin('unidadmedida', 'categoriaprod.unidadmedidafact_id', '=', 'unidadmedida.id')
            ->select([
                    'producto.id',
                    'producto.nombre',
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
                    'categoriaprod.precio',
                    'categoriaprodsuc.sucursal_id',
                    'categoriaprod.unidadmedida_id',
                    'categoriaprod.unidadmedidafact_id',
                    'categoriaprod.mostdatosad',
                    'categoriaprod.mostunimed',
                    'unidadmedida.nombre as unidadmedidanombre'
                    ])
                    ->whereIn('categoriaprodsuc.sucursal_id', $sucurArray)
                    ->where('producto.deleted_at','=',null)
                    ->groupBy('producto.id');
            //dd($productos);
            //****************** */
            $array_productos = $productos->get()->toArray();
            $respuesta = array();
            if(count($array_productos) > 0){
                $respuesta = $array_productos[0];
                $producto = Producto::findOrFail($request->id);
                $respuesta['bodegas'] = $producto->categoriaprod->invbodegas->where('tipo','=',2)->toArray();
                //dd($respuesta['bodegas']);
                foreach ($respuesta['bodegas'] as &$bodega) {
                    $request1 = new Request();
                    $request1["producto_id"] = $request->id;
                    $request1["invbodega_id"] = $bodega["id"];
                    $request1["tipo"] = 2;
                    $aux_stosk = InvBodegaProducto::existencia($request1);
                    $bodega["stock"] = $aux_stosk["stock"]["cant"];
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

}
