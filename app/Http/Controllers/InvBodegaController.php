<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidarInvBodega;
use App\Models\InvBodega;
use App\Models\Seguridad\Usuario;
use App\Models\Sucursal;
use Illuminate\Http\Request;

class InvBodegaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-invbodega');
        return view('invbodega.index');
    }

    public function invbodegapage(){
        $users = Usuario::findOrFail(auth()->id());
        $sucurArray = $users->sucursales->pluck('id')->toArray();
        //Filtrando las categorias por sucursal, dependiendo de las sucursales asignadas al usuario logueado
        //******************* */

        return datatables()
            ->eloquent(InvBodega::query()
            ->join('sucursal', 'invbodega.sucursal_id', '=', 'sucursal.id')
            ->whereIn('invbodega.sucursal_id', $sucurArray)
            ->select(['invbodega.id',
                'invbodega.bod_desc',
                'invbodega.sucursal_id',
                'sucursal.nombre as nombre_suc'
                ])
            )
            ->toJson();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function crear()
    {
        can('crear-invbodega');
        $users = Usuario::findOrFail(auth()->id());
        $sucurArray = $users->sucursales->pluck('id')->toArray();
        $sucursales = Sucursal::orderBy('id')
                        ->whereIn('sucursal.id', $sucurArray)
                        ->get();
        return view('invbodega.crear',compact('sucursales'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function guardar(ValidarInvBodega $request)
    {
        can('guardar-invbodega');
        //dd($request);
        InvBodega::create($request->all());
        return redirect('invbodega')->with('mensaje','Bodega creado con exito.');
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
        $data = InvBodega::findOrFail($id);
        $users = Usuario::findOrFail(auth()->id());
        $sucurArray = $users->sucursales->pluck('id')->toArray();
        $sucursales = Sucursal::orderBy('id')
                        ->whereIn('sucursal.id', $sucurArray)
                        ->get();
        return view('invbodega.editar', compact('data','sucursales'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function actualizar(ValidarInvBodega $request, $id)
    {
        InvBodega::findOrFail($id)->update($request->all());
        return redirect('invbodega')->with('mensaje','Bodega actualizada con exito');

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
