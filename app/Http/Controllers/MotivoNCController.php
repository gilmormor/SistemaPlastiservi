<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidarMotivoNC;
use App\Models\MotivoNc;
use Illuminate\Http\Request;

class MotivoNCController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-motivonc');
        $datas = MotivoNc::orderBy('id')->get();
        return view('motivonc.index', compact('datas'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function crear()
    {
        can('crear-motivonc');
        return view('motivonc.crear');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function guardar(ValidarMotivoNC $request)
    {
        can('guardar-motivonc');
        MotivoNc::create($request->all());
        return redirect('motivonc')->with('mensaje','Motivo no conformidad creado con exito');
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
        can('editar-motivonc');
        $data = MotivoNc::findOrFail($id);
        return view('motivonc.editar', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function actualizar(ValidarMotivoNC $request, $id)
    {
        MotivoNc::findOrFail($id)->update($request->all());
        return redirect('motivonc')->with('mensaje','Motivo no conformidad actualizado con exito');
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
            if (MotivoNc::destroy($id)) {
                return response()->json(['mensaje' => 'ok']);
            } else {
                return response()->json(['mensaje' => 'ng']);
            }
        } else {
            abort(404);
        }
    }
}
