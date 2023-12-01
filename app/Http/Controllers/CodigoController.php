<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidarCodigo;
use App\Models\codigo;
use Illuminate\Http\Request;

class CodigoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-codigo');
        //$datas = FormaPago::orderBy('id')->get();
        return view('codigo.index');
    }

    public function codigopage(){
        return datatables()
            ->eloquent(Codigo::query())
            ->toJson();
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function crear()
    {
        can('crear-codigo');
        return view('codigo.crear');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function guardar(ValidarCodigo $request)
    {
        can('guardar-codigo');
        Codigo::create($request->all());
        return redirect('codigo')->with('mensaje','Código creado con exito');
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
        can('editar-codigo');
        $data = Codigo::findOrFail($id);
        return view('codigo.editar', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function actualizar(ValidarCodigo $request, $id)
    {
        Codigo::findOrFail($id)->update($request->all());
        return redirect('codigo')->with('mensaje','Código actualizado con exito');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function eliminar(Request $request, $id)
    {
        if(can('eliminar-codigo',false)){
            if ($request->ajax()) {
                $data = Codigo::findOrFail($request->id);
                $aux_contRegistos = $data->codigodet->count();
                //dd($aux_contRegistos);
                if($aux_contRegistos > 0){
                    return response()->json(['mensaje' => 'cr']);
                }else{
                    if (Codigo::destroy($request->id)) {
                        //dd('entro');
                        //Despues de eliminar actualizo el campo usuariodel_id=usuario que elimino el registro
                        $FormaPago = Codigo::withTrashed()->findOrFail($request->id);
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
