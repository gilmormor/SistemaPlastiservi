<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidarCategoriaGrupoValMes;
use App\Models\CategoriaGrupoValMes;
use App\Models\CategoriaProd;
use App\Models\UnidadMedida;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoriaGrupoValMesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-costo-por-categoria-grupo');
        $datas = CategoriaGrupoValMes::orderBy('id')->get();
        return view('categoriagrupovalmes.index', compact('datas','categoriaprods'));
    }

    public function CategoriaGrupoValMespage($mesanno){
        $aux_annomes = CategoriaGrupoValMes::annomes($mesanno);
        $sql = "SELECT categoriagrupovalmes.*,
        grupoprod.gru_nombre,
        categoriaprod.nombre as categorianombre
        FROM categoriagrupovalmes INNER JOIN grupoprod
        ON categoriagrupovalmes.grupoprod_id = grupoprod.id
        INNER JOIN categoriaprod
        ON grupoprod.categoriaprod_id = categoriaprod.id
        WHERE annomes='$aux_annomes'
        and isnull(categoriagrupovalmes.deleted_at) AND isnull(grupoprod.deleted_at)";
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
        return view('categoriagrupovalmes.crear', compact('unidadmedidas'));

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    //public function guardar(Request $request)
    public function guardar(ValidarCategoriaGrupoValMes $request)
    {
        can('guardar-costo-por-categoria-grupo');
        $request["annomes"] = CategoriaGrupoValMes::annomes($request->annomes);
        CategoriaGrupoValMes::create($request->all());
        return redirect('categoriagrupovalmes')->with('mensaje','Registro creado con exito.');
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
        $data = CategoriaGrupoValMes::findOrFail($id);
        //$categoriaprods = CategoriaProd::categoriasxUsuario();
        $request['id'] = $data->id;
        $request['annomes'] = $data->annomes;
        $request['categoriaprod_id'] = $data->grupoprod->categoriaprod_id;
        $categoriaprods = CategoriaProd::catxUsuCostoAnnoMes($request);
        $grupoprods = CategoriaGrupoValMes::catgrupNoCreados($request);
        //$grupoprods = GrupoProd::where('categoriaprod_id',$data->grupoprod->categoriaprod_id)->get();
        $unidadmedidas = UnidadMedida::orderBy('id')->pluck('descripcion', 'id')->toArray();
        return view('categoriagrupovalmes.editar', compact('data','categoriaprods','unidadmedidas','grupoprods'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function actualizar(ValidarCategoriaGrupoValMes $request, $id)
    {
        //dd($request);
        can('editar-costo-por-categoria-grupo');
        $request["annomes"] = CategoriaGrupoValMes::annomes($request->annomes);
        CategoriaGrupoValMes::findOrFail($id)->update($request->all());
        return redirect('categoriagrupovalmes')->with('mensaje','Registro actualizado con exito.');
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

    public function CategoriaGrupoValMesfilcat(Request $request){
        $datas = CategoriaProd::catxUsuCostoAnnoMes($request);
        return $datas; //response()->json($data)
    }

    public function CategoriaGrupoValMesfilgrupos(Request $request){
        $datas = CategoriaGrupoValMes::catgrupNoCreados($request);
        return $datas; //response()->json($data)
    }

}
