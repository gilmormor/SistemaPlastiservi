<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidarDespachoOrdRecMotivo;
use App\Models\DespachoOrdRecMotivo;
use Illuminate\Http\Request;

class DespachoOrdRecMotivoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-motivo-rechazo-despacho');
        return view('despachoordrecmotivo.index');
    }

    public function despachoordrecmotivopage(){
        return datatables()
            ->eloquent(DespachoOrdRecMotivo::query())
            ->toJson();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function crear()
    {
        can('crear-motivo-rechazo-despacho');
        return view('despachoordrecmotivo.crear');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function guardar(ValidarDespachoOrdRecMotivo $request)
    {
        can('guardar-motivo-rechazo-despacho');
        DespachoOrdRecMotivo::create($request->all());
        return redirect('despachoordrecmotivo')->with('mensaje','Motivo Rechazo creado con exito');
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
        can('editar-motivo-rechazo-despacho');
        $data = DespachoOrdRecMotivo::findOrFail($id);
        return view('despachoordrecmotivo.editar', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function actualizar(ValidarDespachoOrdRecMotivo $request, $id)
    {
        DespachoOrdRecMotivo::findOrFail($id)->update($request->all());
        return redirect('despachoordrecmotivo')->with('mensaje','Forma Pago actualizado con exito');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function eliminar(Request $request, $id)
    {
        if(can('eliminar-motivo-rechazo-despacho',false)){
            if ($request->ajax()) {
                $data = DespachoOrdRecMotivo::findOrFail($request->id);
                $aux_contRegistos = $data->despachoordrecmotivo->count();
                //dd($aux_contRegistos);
                if($aux_contRegistos > 0){
                    return response()->json(['mensaje' => 'cr']);
                }else{
                    if (DespachoOrdRecMotivo::destroy($request->id)) {
                        //dd('entro');
                        //Despues de eliminar actualizo el campo usuariodel_id=usuario que elimino el registro
                        $despachoordrecmotivo = DespachoOrdRecMotivo::withTrashed()->findOrFail($request->id);
                        $despachoordrecmotivo->usuariodel_id = auth()->id();
                        $despachoordrecmotivo->save();
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