<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidarComuna;
use App\Models\Ciudad;
use App\Models\Comuna;
use App\Models\Provincia;
use Illuminate\Http\Request;

class ComunaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-comuna');
        //$datas = FormaPago::orderBy('id')->get();
        return view('comuna.index');
    }

    public function comunapage(){
        return datatables()
            ->eloquent(Comuna::query())
            ->toJson();
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function crear()
    {
        can('crear-comuna');
        $provincias = Provincia::orderBy('id')->get();
        $ciudades = Ciudad::orderBy('id')->get();
        return view('comuna.crear',compact('provincias','ciudades'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function guardar(ValidarComuna $request)
    {
        can('guardar-comuna');
        Comuna::create($request->all());
        return redirect('comuna')->with('mensaje','Comuna creada con exito');
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
        can('editar-comuna');
        $data = Comuna::findOrFail($id);
        $provincias = Provincia::orderBy('id')->get();
        $ciudades = Ciudad::orderBy('id')->get();
        return view('comuna.editar', compact('data','provincias','ciudades'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function actualizar(ValidarComuna $request, $id)
    {
        Comuna::findOrFail($id)->update($request->all());
        return redirect('comuna')->with('mensaje','Comuna actualizado con exito');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function eliminar(Request $request, $id)
    {
        if(can('eliminar-comuna',false)){
            if ($request->ajax()) {
                $comuna = Comuna::findOrFail($request->id);
                $aux_regAso = false;
                $aux_tabla = [];
                if(count($comuna->comunas) > 0){
                    $aux_regAso = true;
                    $aux_tabla[] = "Comuna";
                }
                if(count($comuna->clientes) > 0){
                    $aux_regAso = true;
                    $aux_tabla[] = "Cliente";
                }
                if(count($comuna->dtes) >0){
                    $aux_regAso = true;
                    $aux_tabla[] = "DTE";
                }
                if($aux_regAso){
                    return response()->json([
                        'id' => 1,
                        'mensaje' => "No se puede eliminar, tiene registros asociados a la tabla: " . implode(", ", $aux_tabla) . ".",
                        'tipo_alert' => "error"
                    ]);
                }
                if (Comuna::destroy($request->id)) {
                    //dd('entro');
                    //Despues de eliminar actualizo el campo usuariodel_id=usuario que elimino el registro
                    $comuna = Comuna::withTrashed()->findOrFail($request->id);
                    $comuna->usuariodel_id = auth()->id();
                    $comuna->save();
                    return response()->json(['mensaje' => 'ok']);
                } else {
                    return response()->json(['mensaje' => 'ng']);
                }
            } else {
                abort(404);
            }
        }else{
            return response()->json(['mensaje' => 'ne']);
        }
    }
}