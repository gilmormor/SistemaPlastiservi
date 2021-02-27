<?php

namespace App\Http\Controllers;

use App\Models\EstadisticaVenta;
use App\Models\UnidadMedida;
use DateTime;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class GuiaInternaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-guia-interna');

        //$datas = datatables($datas)->toJson();
        return view('guiainterna.index');
    }
//where('tipofac',2)->
    public function guiainternapage(){
        /*
        $prueba = datatables()
        ->eloquent(EstadisticaVenta::where('tipofac',2)->query())
        ->toJson();
        dd($prueba);*/
        return datatables()
            ->eloquent(EstadisticaVenta::query()->where('tipofact',2))
            ->toJson();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function crear()
    {
        can('crear-guia-interna');
        $unidadmedidas = UnidadMedida::orderBy('id')->pluck('descripcion', 'id')->toArray();
        $aux_sta=1;
        return view('guiainterna.crear',compact('unidadmedidas','aux_sta'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function guardar(Request $request)
    {
        can('guardar-guia-interna');
        $aux_fechadocumento= DateTime::createFromFormat('d/m/Y', $request->fechadocumento)->format('Y-m-d');
        $request->request->add(['fechadocumento' => $aux_fechadocumento]);
        $request->request->add(['sucursal_id' => 1]);
        $request->request->add(['tipofact' => 2]);
        $request->request->add(['tipodocumento' => 'GINT']);
        //dd($request);
        EstadisticaVenta::create($request->all());
        return redirect('guiainterna')->with('mensaje','Color creado con exito');
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
