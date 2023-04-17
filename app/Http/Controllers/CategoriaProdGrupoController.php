<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidarCategoriaProdGrupo;
use App\Models\CategoriaProdGrupo;
use Illuminate\Http\Request;

class CategoriaProdGrupoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-grupo-categoria-producto');
        $datas = CategoriaProdGrupo::orderBy('id')->get();
        return view('categoriaprodgrupo.index', compact('datas'));
    }

    public function categoriaprodgrupopage(){
        return datatables()
            ->eloquent(CategoriaProdGrupo::query())
            ->toJson();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function crear()
    {
        can('crear-grupo-categoria-producto');
        return view('categoriaprodgrupo.crear');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function guardar(ValidarCategoriaProdGrupo $request)
    {
        can('guardar-grupo-categoria-producto');
        CategoriaProdGrupo::create($request->all());
        return redirect('categoriaprodgrupo')->with('mensaje','Grupo Categoria creado con exito');
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
        can('editar-grupo-categoria-producto');
        $data = CategoriaProdGrupo::findOrFail($id);
        return view('categoriaprodgrupo.editar', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function actualizar(ValidarCategoriaProdGrupo $request, $id)
    {
        CategoriaProdGrupo::findOrFail($id)->update($request->all());
        return redirect('categoriaprodgrupo')->with('mensaje','Grupo Categoria actualizado con exito');
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
            if (CategoriaProdGrupo::destroy($id)) {
                return response()->json(['mensaje' => 'ok']);
            } else {
                return response()->json(['mensaje' => 'ng']);
            }
        } else {
            abort(404);
        }
    }
}
