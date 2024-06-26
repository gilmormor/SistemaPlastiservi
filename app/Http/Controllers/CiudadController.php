<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidarCiudad;
use App\Models\Ciudad;
use App\Models\Cliente;
use App\Models\Comuna;
use App\Models\Provincia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use ReflectionClass;
use Illuminate\Database\Eloquent\Relations\Relation;

class CiudadController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-ciudad');
        //$datas = FormaPago::orderBy('id')->get();
        return view('ciudad.index');
    }

    public function ciudadpage(){
        return datatables()
            ->eloquent(Ciudad::query())
            ->toJson();
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function crear()
    {
        can('crear-ciudad');
        return view('ciudad.crear');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function guardar(ValidarCiudad $request)
    {
        can('guardar-ciudad');
        Ciudad::create($request->all());
        return redirect('ciudad')->with('mensaje','Ciudad creado con exito');
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
        can('editar-ciudad');
        $data = Ciudad::findOrFail($id);
        return view('ciudad.editar', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function actualizar(ValidarCiudad $request, $id)
    {
        Ciudad::findOrFail($id)->update($request->all());
        return redirect('ciudad')->with('mensaje','Ciudad actualizado con exito');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function eliminar(Request $request, $id)
    {
        if(can('eliminar-ciudad',false)){
            if ($request->ajax()) {
                $ciudad = Ciudad::findOrFail($request->id);
                $aux_regAso = false;
                $aux_tabla = [];
                if(count($ciudad->comunas) > 0){
                    $aux_regAso = true;
                    $aux_tabla[] = "Comuna";
                }
                if(count($ciudad->clientes) > 0){
                    $aux_regAso = true;
                    $aux_tabla[] = "Cliente";
                }
                if(count($ciudad->dtes) >0){
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
                if (Ciudad::destroy($request->id)) {
                    //dd('entro');
                    //Despues de eliminar actualizo el campo usuariodel_id=usuario que elimino el registro
                    $ciudad = Ciudad::withTrashed()->findOrFail($request->id);
                    $ciudad->usuariodel_id = auth()->id();
                    $ciudad->save();
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