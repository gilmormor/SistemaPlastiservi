<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidarMateriaPrima;
use App\Models\MateriaPrima;
use Illuminate\Http\Request;

class MateriaPrimaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-materia-prima');
        //$datas = FormaPago::orderBy('id')->get();
        return view('materiaprima.index');
    }

    public function materiaprimapage(){
        return datatables()
            ->eloquent(MateriaPrima::query())
            ->toJson();
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function crear()
    {
        can('crear-materia-prima');
        return view('materiaprima.crear');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function guardar(ValidarMateriaPrima $request)
    {
        can('guardar-materia-prima');
        MateriaPrima::create($request->all());
        return redirect('materiaprima')->with('mensaje','Materia Prima creado con exito');
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
        can('editar-materia-prima');
        $data = MateriaPrima::findOrFail($id);
        return view('materiaprima.editar', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function actualizar(ValidarMateriaPrima $request, $id)
    {
        MateriaPrima::findOrFail($id)->update($request->all());
        return redirect('materiaprima')->with('mensaje','Materia Prima actualizado con exito');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function eliminar(Request $request, $id)
    {
        /*
        if ($request->ajax()) {
            if (FormaPago::destroy($id)) {
                return response()->json(['mensaje' => 'ok']);
            } else {
                return response()->json(['mensaje' => 'ng']);
            }
        } else {
            abort(404);
        }
        */

        if(can('eliminar-materia-prima',false)){
            if ($request->ajax()) {
                $data = MateriaPrima::findOrFail($request->id);
                $aux_contRegistos = $data->acuerdotecnicos->count() + $data->acuerdotecnicotemps->count();
                //dd($aux_contRegistos);
                if($aux_contRegistos > 0){
                    return response()->json(['mensaje' => 'cr']);
                }else{
                    if (MateriaPrima::destroy($request->id)) {
                        //dd('entro');
                        //Despues de eliminar actualizo el campo usuariodel_id=usuario que elimino el registro
                        $FormaPago = MateriaPrima::withTrashed()->findOrFail($request->id);
                        $FormaPago->usuariodel_id = auth()->id();
                        $FormaPago->save();
                        return response()->json(['mensaje' => 'ok']);
                    } else {
                        return response()->json(['mensaje' => 'ng']);
                    }    
                }
            } else {
                abort(404);
            }
    
        }else{
            return response()->json(['mensaje' => 'ne']);
        }
    }
}
