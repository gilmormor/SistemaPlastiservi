<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidarDespachoObs;
use App\Models\DespachoObs;
use Illuminate\Http\Request;

class DespachoObsController extends Controller
{
        /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-observaciones-despacho');
        $datas = DespachoObs::orderBy('id')->get();
        return view('despachoobs.index', compact('datas'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function crear()
    {
        can('crear-observaciones-despacho');
        return view('despachoobs.crear');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function guardar(ValidarDespachoObs $request)
    {
        can('guardar-observaciones-despacho');
        DespachoObs::create($request->all());
        return redirect('despachoobs')->with('mensaje','Observacion despacho creado con exito');
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
        can('editar-observaciones-despacho');
        $data = DespachoObs::findOrFail($id);
        return view('despachoobs.editar', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function actualizar(ValidarDespachoObs $request, $id)
    {
        DespachoObs::findOrFail($id)->update($request->all());
        return redirect('despachoobs')->with('mensaje','Observacion despacho actualizado con exito');
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
            if (DespachoObs::destroy($id)) {
                return response()->json(['mensaje' => 'ok']);
            } else {
                return response()->json(['mensaje' => 'ng']);
            }
        } else {
            abort(404);
        }
    }
}
