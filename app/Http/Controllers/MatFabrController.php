<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidarMatFabr;
use App\Models\MatFabr;
use Illuminate\Http\Request;

class MatFabrController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-material-fabricacion');
        $datas = MatFabr::orderBy('id')->get();
        return view('matfabr.index', compact('datas'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function crear()
    {
        can('crear-material-fabricacion');
        return view('matfabr.crear');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function guardar(ValidarMatFabr $request)
    {
        can('guardar-material-fabricacion');
        MatFabr::create($request->all());
        return redirect('matfabr')->with('mensaje','Material de Fabricación creado con exito');
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
        can('editar-material-fabricacion');
        $data = MatFabr::findOrFail($id);
        return view('matfabr.editar', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function actualizar(ValidarMatFabr $request, $id)
    {
        MatFabr::findOrFail($id)->update($request->all());
        return redirect('matfabr')->with('mensaje','Material de Fabricación actualizado con exito');
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
            if (MatFabr::destroy($id)) {
                return response()->json(['mensaje' => 'ok']);
            } else {
                return response()->json(['mensaje' => 'ng']);
            }
        } else {
            abort(404);
        }
    }
}
