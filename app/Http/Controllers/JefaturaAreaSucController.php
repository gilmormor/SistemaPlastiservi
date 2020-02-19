<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Jefatura;
use App\Models\JefaturaSucursalArea;
use App\Models\Sucursal;
use App\Models\SucursalArea;
use Illuminate\Http\Request;

class JefaturaAreaSucController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //can('listar-jefatura');
        //return view('jefaturaAreaSuc.index', compact('datas'));
        //$sucursales = Sucursal::with('areas')->orderBy('id')->get(); //->pluck('nombre', 'id')->toArray();
        //$jefaturas = Jefatura::orderBy('id')->get();
        $sucursales  = SucursalArea::join('Sucursal','Sucursal_area.Sucursal_id','=','Sucursal.id')
                                    ->join('Area','Sucursal_area.area_id','=','Area.id')
                                    ->select(['sucursal_area.id as id','sucursal.nombre as suc_nombre','area.nombre as are_nombre'])
                                    ->get();
        //dd($sucursales);
        return view('jefaturaAreaSuc.index',compact('sucursales'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function crear()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function guardar(Request $request)
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
    public function editar($id)
    {
        //findOrFail($id)
        $sucursales = SucursalArea::where('sucursal_area.id', '=', $id)
                                    ->join('Sucursal','Sucursal_area.Sucursal_id','=','Sucursal.id')
                                    ->join('Area','Sucursal_area.area_id','=','Area.id')
                                    ->select(['sucursal_area.id as id','sucursal.nombre as suc_nombre','area.nombre as are_nombre'])
                                    ->get();
        $jefaturas = Jefatura::orderBy('id')->pluck('nombre', 'id')->toArray();
        //dd($jefaturas);
        //dd($sucursales);
        return view('jefaturaAreaSuc.editar',compact('sucursales','jefaturas'));

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
        $sucursalArea = SucursalArea::findOrFail($id);
        $sucursalArea->jefaturas()->sync($request->jefatura_id);
        return redirect('jefaturaAreaSuc')->with('mensaje','Sucursal actualizada con exito');
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

    public function ObtAreas1()
    {
        $areas  = SucursalArea::where('sucursal_id',2)
                                  ->join('Area','sucursal_area.area_id','=','Area.id')
                                  ->get();
        $sucursales = SucursalArea::with('SucursalArea:id,area_id,sucursal_id')->where('sucursal_id',2)->get();
        //dd($sucursales);
    }

    public function ObtAreas(Request $request)
    {
        if($request->ajax()){
            //$areas = Area::where('id', 1)->get();  //$request->area_id
            
            $areas  = SucursalArea::where('sucursal_id',$request->sucursal_id)
                                  ->join('Area','sucursal_area.area_id','=','Area.id')
                                  ->select(['sucursal_area.id as id','area.nombre as nombre'])
                                  ->get();
            
            $sucursalAreas  = SucursalArea::where('sucursal_id',$request->sucursal_id)
                                  ->get();
            //dd($areas);
            $areasArray = [];
            //foreach($sucursalAreas as $sucursalArea){
            foreach($areas as $area){
                //$area = Area::findOrFail($sucursalArea->area_id);
                //$areasArray[$sucursalArea->id] = $area->nombre;
                $areasArray[$area->id] = $area->nombre;
            }
            //dd($areasArray);
            return response()->json($areasArray);
        }
    }
}
