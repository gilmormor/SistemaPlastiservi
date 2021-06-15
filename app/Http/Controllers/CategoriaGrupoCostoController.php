<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidarCategoriaGrupoCosto;
use App\Models\CategoriaGrupoCosto;
use App\Models\CategoriaProd;
use App\Models\GrupoProd;
use App\Models\UnidadMedida;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoriaGrupoCostoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-costo-por-categoria-grupo');
        $datas = CategoriaGrupoCosto::orderBy('id')->get();
        return view('categoriagrupocosto.index', compact('datas','categoriaprods'));
    }

    public function categoriagrupocostopage($mesanno){
        $aux_annomes = categoriagrupocosto::annomes($mesanno);
        $sql = "SELECT categoriagrupocosto.*,
        grupoprod.gru_nombre,
        categoriaprod.nombre as categorianombre
        FROM categoriagrupocosto INNER JOIN grupoprod
        ON categoriagrupocosto.grupoprod_id = grupoprod.id
        INNER JOIN categoriaprod
        ON grupoprod.categoriaprod_id = categoriaprod.id
        WHERE annomes='$aux_annomes'
        and isnull(categoriagrupocosto.deleted_at) AND isnull(grupoprod.deleted_at)";
        $datas = DB::select($sql);
        return datatables($datas)->toJson();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function crear()
    {
        can('crear-costo-por-categoria-grupo');
        $unidadmedidas = UnidadMedida::orderBy('id')->pluck('descripcion', 'id')->toArray();
        return view('categoriagrupocosto.crear', compact('unidadmedidas'));

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    //public function guardar(Request $request)
    public function guardar(ValidarCategoriaGrupoCosto $request)
    {
        can('guardar-costo-por-categoria-grupo');
        $request["annomes"] = CategoriaGrupoCosto::annomes($request->annomes);
        CategoriaGrupoCosto::create($request->all());
        return redirect('categoriagrupocosto')->with('mensaje','Registro creado con exito.');
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
        can('editar-costo-por-categoria-grupo');
        $data = CategoriaGrupoCosto::findOrFail($id);
        //$categoriaprods = CategoriaProd::categoriasxUsuario();
        $request['id'] = $data->id;
        $request['annomes'] = $data->annomes;
        $request['categoriaprod_id'] = $data->grupoprod->categoriaprod_id;
        $categoriaprods = CategoriaProd::catxUsuCostoAnnoMes($request);
        $grupoprods = CategoriaGrupoCosto::catgrupNoCreados($request);
        //$grupoprods = GrupoProd::where('categoriaprod_id',$data->grupoprod->categoriaprod_id)->get();
        $unidadmedidas = UnidadMedida::orderBy('id')->pluck('descripcion', 'id')->toArray();
        return view('categoriagrupocosto.editar', compact('data','categoriaprods','unidadmedidas','grupoprods'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function actualizar(ValidarCategoriaGrupoCosto $request, $id)
    {
        //dd($request);
        can('editar-costo-por-categoria-grupo');
        $request["annomes"] = CategoriaGrupoCosto::annomes($request->annomes);
        CategoriaGrupoCosto::findOrFail($id)->update($request->all());
        return redirect('categoriagrupocosto')->with('mensaje','Registro actualizado con exito.');
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

    public function categoriagrupocostofilcat(Request $request){
        $datas = CategoriaProd::catxUsuCostoAnnoMes($request);
        return $datas; //response()->json($data)
    }

    public function categoriagrupocostofilgrupos(Request $request){
        $datas = CategoriaGrupoCosto::catgrupNoCreados($request);
        return $datas; //response()->json($data)
    }

}
