<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidarFoliocontrol;
use App\Models\Foliocontrol;
use Illuminate\Http\Request;

class FoliocontrolController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-control-folios');
        //$datas = Foliocontrol::orderBy('id')->get();
        return view('foliocontrol.index');
    }

    public function foliocontrolpage(){
        return datatables()
            ->eloquent(Foliocontrol::query())
            ->toJson();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function crear()
    {
        can('crear-control-folios');
        return view('foliocontrol.crear');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function guardar(ValidarFoliocontrol $request)
    {
        can('guardar-control-folios');
        $request->request->add(['usuario_id' => auth()->id()]);
        Foliocontrol::create($request->all());
        return redirect('foliocontrol')->with('mensaje','Registro creado con exito');
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
        can('editar-control-folios');
        $data = Foliocontrol::findOrFail($id);
        return view('foliocontrol.editar', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function actualizar(ValidarFoliocontrol $request, $id)
    {
        Foliocontrol::findOrFail($id)->update($request->all());
        return redirect('foliocontrol')->with('mensaje','Registro actualizado con exito');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function eliminar(Request $request, $id)
    {
        if(can('eliminar-control-folios',false)){
            if ($request->ajax()) {
                $data = Foliocontrol::findOrFail($request->id);
                if (Foliocontrol::destroy($request->id)) {
                    //dd('entro');
                    //Despues de eliminar actualizo el campo usuariodel_id=usuario que elimino el registro
                    $foliocontrol = Foliocontrol::withTrashed()->findOrFail($request->id);
                    $foliocontrol->usuariodel_id = auth()->id();
                    $foliocontrol->save();
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
