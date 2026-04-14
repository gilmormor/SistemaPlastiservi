<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidarAreaProduccion;
use App\Models\AreaProduccion;
use App\Models\EtapaProd;
use App\Models\Sucursal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AreaProduccionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-areaproduccion');
        $datas = AreaProduccion::orderBy('id')->get();
        return view('areaproduccion.index', compact('datas'));

    }

    public function areaproduccionpage(){    
        $sql = "SELECT areaproduccion.id,areaproduccion.nombre
        from areaproduccion
        where isnull(areaproduccion.deleted_at);";
        $datas = DB::select($sql);
        return datatables($datas)->toJson();
    }
    
    public function areaproduccionsucetapaprodpage(Request $request){
        //dd($request);
        $aux_areaproduccion_idCond = " true ";
        if(isset($request->areaproduccion_id) and $request->areaproduccion_id >= 0){
            $aux_areaproduccion_idCond = "areaproduccion.id = $request->areaproduccion_id";
        }
        $sql = "SELECT areaproduccionsucetapaprod.id,etapaprod.nombre,
            areaproduccionsucetapaprod.orden,
            UNIX_TIMESTAMP(areaproduccionsucetapaprod.updated_at) as updatednum_at,
            areaproduccionsucetapaprod.updated_at
        from areaproduccion INNER JOIN areaproduccionsuc
        ON areaproduccion.id = areaproduccionsuc.areaproduccion_id
        INNER JOIN areaproduccionsucetapaprod
        ON areaproduccionsuc.id = areaproduccionsucetapaprod.areaproduccionsuc_id
        INNER JOIN etapaprod
        ON areaproduccionsucetapaprod.etapaprod_id = etapaprod.id
        where $aux_areaproduccion_idCond
        AND isnull(areaproduccion.deleted_at);";
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
        can('crear-areaproduccion');
        $tablas = array();
        $tablas['sucursales'] = Sucursal::orderBy('id')->get();
        $tablas['aux_cont'] = 0;
        $tablas['aux_sta'] = 1;
        return view('areaproduccion.crear', compact('tablas'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function guardar(ValidarAreaProduccion $request)
    {
        can('guardar-areaproduccion');
        $areaproduccion = AreaProduccion::create($request->all());
        $areaproduccion->sucursales()->sync($request->sucursal_id);
        return redirect('areaproduccion')->with('mensaje','AreaProduccion creado con exito');
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
        can('editar-areaproduccion');
        $data = AreaProduccion::findOrFail($id);
        $tablas = array();
        $tablas['sucursales'] = Sucursal::orderBy('id')->get();
        $tablas['aux_sta'] = 2;
        return view('areaproduccion.editar', compact('data','tablas'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function actualizar(ValidarAreaProduccion $request, $id)
    {
        can('guardar-areaproduccion');
        $areaproduccion = AreaProduccion::findOrFail($id);
        $areaproduccion->update($request->all());
        $areaproduccion->sucursales()->sync($request->sucursal_id);
        return redirect('areaproduccion')->with('mensaje','AreaProduccion actualizado con exito');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function eliminar(Request $request, $id)
    {
        if ($request->ajax()) {
            if (AreaProduccion::destroy($id)) {
                return response()->json(['mensaje' => 'ok']);
            } else {
                return response()->json(['mensaje' => 'ng']);
            }    
        } else {
            abort(404);
        }
    }
}
