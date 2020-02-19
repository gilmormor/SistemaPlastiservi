<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidarGrupoCategoria;
use App\Models\GrupoCategoria;
use Illuminate\Http\Request;

class GrupoCategoriaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-grupocategoria');
        $datas = GrupoCategoria::orderBy('id')->get();
        return view('grupocategoria.index', compact('datas'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function crear()
    {
        can('crear-grupocategoria');
        return view('grupocategoria.crear');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function guardar(ValidarGrupoCategoria $request)
    {
        can('guardar-grupocategoria');
        GrupoCategoria::create($request->all());
        return redirect('grupocategoria')->with('mensaje','GrupoCategoria creado con exito');
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
        can('editar-grupocategoria');
        $data = GrupoCategoria::findOrFail($id);
        return view('grupocategoria.editar', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function actualizar(ValidarGrupoCategoria $request, $id)
    {
        GrupoCategoria::findOrFail($id)->update($request->all());
        return redirect('grupocategoria')->with('mensaje','GrupoCategoria actualizado con exito');
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
            if (GrupoCategoria::destroy($id)) {
                return response()->json(['mensaje' => 'ok']);
            } else {
                return response()->json(['mensaje' => 'ng']);
            }
        } else {
            abort(404);
        }
    }
}
