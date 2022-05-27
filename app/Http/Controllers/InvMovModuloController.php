<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidarInvBodega;
use App\Http\Requests\ValidarInvMovModulo;
use App\Models\InvBodega;
use App\Models\InvMovModulo;
use App\Models\InvMovModuloBSal;
use App\Models\Seguridad\Usuario;
use Illuminate\Http\Request;

class InvMovModuloController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-modulos-movimiento-inventario');
        return view('invmovmodulo.index');
    }

    public function invmovmodulopage(){
        return datatables()
            ->eloquent(InvMovModulo::query())
            ->toJson();
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function crear()
    {
        can('crear-modulos-movimiento-inventario');
        $invbodegas = InvBodega::orderBy('id')->get();
        return view('invmovmodulo.crear',compact('invbodegas'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function guardar(ValidarInvMovModulo $request)
    {
        can('guardar-modulos-movimiento-inventario');
        $request->request->add(['usuario_id' => auth()->id()]);
        $invmovmodulo = InvMovModulo::create($request->all());
        $invmovmodulo->invmovmodulobodsals()->sync($request->invmovmodulobodsal_id);
        $invmovmodulo->invmovmodulobodents()->sync($request->invmovmodulobodent_id);
        return redirect('invmovmodulo')->with('mensaje','Módulo creado con exito.');
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
        can('editar-invbodega');
        $data = InvMovModulo::findOrFail($id);
        $invbodegas = InvBodega::orderBy('id')->get();
        return view('invmovmodulo.editar', compact('data','invbodegas'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function actualizar(ValidarInvMovModulo $request, $id)
    {
        $invmovmodulo = InvMovModulo::findOrFail($id);
        $invmovmodulo->update($request->all());
        $invmovmodulo->invmovmodulobodsals()->sync($request->invmovmodulobodsal_id);
        $invmovmodulo->invmovmodulobodents()->sync($request->invmovmodulobodent_id);
        return redirect('invmovmodulo')->with('mensaje','Módulo actualizado con exito');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
