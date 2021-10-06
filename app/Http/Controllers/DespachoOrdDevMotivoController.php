<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidarDespachoOrdDevMotivo;
use App\Models\DespachoOrdDevMotivo;
use Illuminate\Http\Request;

class DespachoOrdDevMotivoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-motivo-devolucion-despacho');
        return view('despachoorddevmotivo.index');
    }

    public function despachoorddevmotivopage(){
        return datatables()
            ->eloquent(DespachoOrdDevMotivo::query())
            ->toJson();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function crear()
    {
        can('crear-motivo-devolucion-despacho');
        return view('despachoorddevmotivo.crear');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function guardar(ValidarDespachoOrdDevMotivo $request)
    {
        can('guardar-motivo-devolucion-despacho');
        DespachoOrdDevMotivo::create($request->all());
        return redirect('despachoorddevmotivo')->with('mensaje','Motivo devolución creado con exito');
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
        can('editar-motivo-devolucion-despacho');
        $data = DespachoOrdDevMotivo::findOrFail($id);
        return view('despachoorddevmotivo.editar', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function actualizar(ValidarDespachoOrdDevMotivo $request, $id)
    {
        DespachoOrdDevMotivo::findOrFail($id)->update($request->all());
        return redirect('despachoorddevmotivo')->with('mensaje','Forma Pago actualizado con exito');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function eliminar(Request $request, $id)
    {
        if(can('eliminar-motivo-devolucion-despacho',false)){
            if ($request->ajax()) {
                $data = DespachoOrdDevMotivo::findOrFail($request->id);
                $aux_contRegistos = $data->despachoorddevmotivo->count();
                //dd($aux_contRegistos);
                if($aux_contRegistos > 0){
                    return response()->json(['mensaje' => 'cr']);
                }else{
                    if (DespachoOrdDevMotivo::destroy($request->id)) {
                        //dd('entro');
                        //Despues de eliminar actualizo el campo usuariodel_id=usuario que elimino el registro
                        $despachoorddevmotivo = DespachoOrdDevMotivo::withTrashed()->findOrFail($request->id);
                        $despachoorddevmotivo->usuariodel_id = auth()->id();
                        $despachoorddevmotivo->save();
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