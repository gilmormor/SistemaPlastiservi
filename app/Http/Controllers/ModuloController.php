<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidarModulo;
use App\Models\Modulo;
use Illuminate\Http\Request;

class ModuloController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-modulo');
        //$datas = FormaPago::orderBy('id')->get();
        return view('modulo.index');
    }

    public function modulopage(){
        return datatables()
            ->eloquent(Modulo::query())
            ->toJson();
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function crear()
    {
        can('crear-modulo');
        return view('modulo.crear');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function guardar(ValidarModulo $request)
    {
        can('guardar-modulo');
        Modulo::create($request->all());
        return redirect('modulo')->with('mensaje','Modulo creado con exito');
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
        can('editar-modulo');
        $data = Modulo::findOrFail($id);
        return view('modulo.editar', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function actualizar(ValidarModulo $request, $id)
    {
        Modulo::findOrFail($id)->update($request->all());
        return redirect('modulo')->with('mensaje','Modulo actualizado con exito');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function eliminar(Request $request, $id)
    {
        if(can('eliminar-modulo',false)){
            if ($request->ajax()) {
                $modulo = Modulo::findOrFail($request->id);
                $aux_regAso = false;
                $aux_tabla = [];
                if(count($modulo->clientedesbloqueadomodulos) > 0){
                    $aux_regAso = true;
                    $aux_tabla[] = "Modulo";
                }
                if($aux_regAso){
                    return response()->json([
                        'id' => 1,
                        'mensaje' => "No se puede eliminar, tiene registros asociados a la tabla: " . implode(", ", $aux_tabla) . ".",
                        'tipo_alert' => "error"
                    ]);
                }
                if (Modulo::destroy($request->id)) {
                    //dd('entro');
                    //Despues de eliminar actualizo el campo usuariodel_id=usuario que elimino el registro
                    $modulo = Modulo::withTrashed()->findOrFail($request->id);
                    $modulo->usuariodel_id = auth()->id();
                    $modulo->save();
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