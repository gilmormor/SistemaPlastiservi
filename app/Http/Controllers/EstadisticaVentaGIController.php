<?php

namespace App\Http\Controllers;

use App\Models\EstadisticaVenta;
use App\Models\EstadisticaVentaGI;
use App\Models\UnidadMedida;
use DateTime;
use Illuminate\Http\Request;

class EstadisticaVentaGIController extends Controller
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
        return view('estadisticaventagi.index');
    }

    //where('tipofac',2)->
    public function estadisticaventagipage(){
        /*
        $prueba = datatables()
        ->eloquent(EstadisticaVenta::where('tipofac',2)->query())
        ->toJson();
        dd($prueba);*/
        return datatables()
            ->eloquent(EstadisticaVentaGI::query()->where('tipofact',2))
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
        $descprods = EstadisticaVenta::select('descripcion')
                        ->groupBy('descripcion')
                        ->get();
        $matprimdescs = EstadisticaVenta::select('matprimdesc')
                        ->groupBy('matprimdesc')
                        ->get();

                        //dd($descripprod);
        $aux_sta=1;
        return view('estadisticaventagi.crear',compact('unidadmedidas','aux_sta','descprods','matprimdescs'));
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
        EstadisticaVentaGI::create($request->all());
        return redirect('estadisticaventagi')->with('mensaje','Color creado con exito');
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
        can('editar-guia-interna');
        $data = EstadisticaVentaGI::findOrFail($id);
        $data->fechadocumento = $newDate = date("d/m/Y", strtotime($data->fechadocumento));

        $unidadmedidas = UnidadMedida::orderBy('id')->pluck('descripcion', 'id')->toArray();
        $descprods = EstadisticaVenta::select('descripcion')
                        ->groupBy('descripcion')
                        ->get();
        $matprimdescs = EstadisticaVenta::select('matprimdesc')
                        ->groupBy('matprimdesc')
                        ->get();
        $aux_sta=2;
        return view('estadisticaventagi.editar', compact('data','unidadmedidas','aux_sta','descprods','matprimdescs'));
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
        $fechadocumento= DateTime::createFromFormat('d/m/Y', $request->fechadocumento)->format('Y-m-d');
        $request->request->add(['fechadocumento' => $fechadocumento]);
        EstadisticaVentaGI::findOrFail($id)->update($request->all());
        

        return redirect('estadisticaventagi')->with('mensaje','Guia Interna actualizada con exito.');
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
