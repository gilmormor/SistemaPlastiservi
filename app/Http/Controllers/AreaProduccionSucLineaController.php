<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidarAreaProduccionSucLinea;
use App\Models\AreaProduccionSuc;
use App\Models\AreaProduccionSucLinea;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AreaProduccionSucLineaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-area-produccion-sucursal-linea');
        $datas = AreaProduccionSucLinea::orderBy('id')->get();
        return view('areaproduccionsuclinea.index', compact('datas'));
    }

    public function areaproduccionsuclineapage(){
        return datatables()
            ->eloquent(AreaProduccionSucLinea::query())
            ->toJson();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function crear()
    {
        can('crear-area-produccion-sucursal-linea');
        $tablas = array();
        $tablas['areaproduccionsucs'] = AreaProduccionSuc::orderBy('id')->get();
        return view('areaproduccionsuclinea.crear', compact('tablas'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function guardar(ValidarAreaProduccionSucLinea $request)
    {
        can('guardar-area-produccion-sucursal-linea');
        AreaProduccionSucLinea::create($request->all());
        return redirect('areaproduccionsuclinea')->with('mensaje','Linea Produccion - Area Produccion Sucursal  creado con exito');
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
        can('editar-area-produccion-sucursal-linea');
        $data = AreaProduccionSucLinea::findOrFail($id);
        $tablas = array();
        $tablas['areaproduccionsucs'] = AreaProduccionSuc::orderBy('id')->get();
        return view('areaproduccionsuclinea.editar', compact('data','tablas'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function actualizar(ValidarAreaProduccionSucLinea $request, $id)
    {
        AreaProduccionSucLinea::findOrFail($id)->update($request->all());
        return redirect('areaproduccionsuclinea')->with('mensaje','Linea Produccion - Area Produccion Sucursal  actualizado con exito');
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
            if (AreaProduccionSucLinea::destroy($id)) {
                return response()->json(['mensaje' => 'ok']);
            } else {
                return response()->json(['mensaje' => 'ng']);
            }
        } else {
            abort(404);
        }
    }
    public function listarlineasProduccionSuc(Request $request)
    {
        $aux_condsucursal_id = " true";
        if(!empty($request->sucursal_id)){
            $aux_condsucursal_id = "areaproduccionsuc.sucursal_id='$request->sucursal_id'";
        }
        $aux_condareaproduccion_id = " true";
        if(!empty($request->areaproduccion_id)){
            $aux_condareaproduccion_id = "areaproduccionsuc.areaproduccion_id='$request->areaproduccion_id'";
        }

        $sql = "SELECT areaproduccionsuclinea.id,areaproduccionsuclinea.nombre
        FROM areaproduccionsuc INNER JOIN areaproduccionsuclinea
        ON areaproduccionsuc.id=areaproduccionsuclinea.areaproduccionsuc_id
        WHERE $aux_condsucursal_id
        AND $aux_condareaproduccion_id;";
        //dd($sql);
        $datas = DB::select($sql);
        return $datas;
    }
}
