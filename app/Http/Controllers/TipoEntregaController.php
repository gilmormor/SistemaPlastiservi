<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidarTipoEntrega;
use App\Models\TipoEntrega;
use Illuminate\Http\Request;

class TipoEntregaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-tipoentrega');
        $datas = TipoEntrega::orderBy('id')->get();
        return view('tipoentrega.index', compact('datas'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function crear()
    {
        can('crear-tipoentrega');
        return view('tipoentrega.crear');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function guardar(ValidarTipoEntrega $request)
    {
        can('guardar-tipoentrega');
        TipoEntrega::create($request->all());
        return redirect('tipoentrega')->with('mensaje','Tipo Entrega creado con exito');
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
        can('editar-tipoentrega');
        $data = TipoEntrega::findOrFail($id);
        return view('tipoentrega.editar', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function actualizar(ValidarTipoEntrega $request, $id)
    {
        TipoEntrega::findOrFail($id)->update($request->all());
        return redirect('tipoentrega')->with('mensaje','Tipo Entrega actualizado con exito');
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
            if (TipoEntrega::destroy($id)) {
                return response()->json(['mensaje' => 'ok']);
            } else {
                return response()->json(['mensaje' => 'ng']);
            }
        } else {
            abort(404);
        }
    }
}