<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidarProducto;
use App\Models\CategoriaProd;
use App\Models\ClaseProd;
use App\Models\Empresa;
use App\Models\GrupoProd;
use App\Models\Producto;
use App\Models\Seguridad\Usuario;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade as PDF;

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
        $datas = Producto::orderBy('id')->get();
        return view('producto.index', compact('datas'));
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

        $categoriaprods = CategoriaProd::join('categoriaprodsuc', function ($join) {
            $user = Usuario::findOrFail(auth()->id());
            $sucurArray = $user->sucursales->pluck('id')->toArray();
            $join->on('categoriaprod.id', '=', 'categoriaprodsuc.categoriaprod_id')
            ->whereIn('categoriaprodsuc.sucursal_id', $sucurArray);
                    })
            ->get();

        $aux_sta=1;
        return view('producto.crear',compact('categoriaprods','aux_sta'));
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
        $categoriaprods = CategoriaProd::join('categoriaprodsuc', function ($join) {
            $user = Usuario::findOrFail(auth()->id());
            $sucurArray = $user->sucursales->pluck('id')->toArray();
            $join->on('categoriaprod.id', '=', 'categoriaprodsuc.categoriaprod_id')
            ->whereIn('categoriaprodsuc.sucursal_id', $sucurArray);
                    })
            ->get();

        $claseprods = ClaseProd::where('categoriaprod_id',$data->categoriaprod_id)->orderBy('id')->get();
        $grupoprods = GrupoProd::where('categoriaprod_id',$data->categoriaprod_id)->orderBy('id')->get();
        //dd($claseprods);
        $aux_sta=2;
        return view('producto.editar', compact('data','categoriaprods','claseprods','grupoprods','aux_sta'));
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
    public function eliminar($id)
    {
        //
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
                ->whereIn('categoriaprodsuc.sucursal_id', $sucurArray);
        //****************** */
        return response()->json($productos->get());
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
                    ->whereIn('categoriaprodsuc.sucursal_id', $sucurArray);
            //dd($productos);
            //****************** */
            return response()->json($productos->get());
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
