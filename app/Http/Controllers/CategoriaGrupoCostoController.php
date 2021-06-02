<?php

namespace App\Http\Controllers;

use App\Models\CategoriaGrupoCosto;
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
        can('listar-costo-por-categoria');
        $datas = CategoriaGrupoCosto::orderBy('id')->get();
        return view('categoriagrupocosto.index', compact('datas'));
    }

    public function categoriagrupocostopage(){
        $sql = "SELECT categoriagrupocosto.*,
        grupoprod.gru_nombre,
        categoriaprod.nombre as categorianombre
        FROM categoriagrupocosto INNER JOIN grupoprod
        ON categoriagrupocosto.grupoprod_id = grupoprod.id
        INNER JOIN categoriaprod
        ON grupoprod.categoriaprod_id = categoriaprod.id
        WHERE isnull(categoriagrupocosto.deleted_at) AND isnull(grupoprod.deleted_at)";
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
        can('crear-costo-por-categoria');
        return view('categoriagrupocosto.crear');

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
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
}
