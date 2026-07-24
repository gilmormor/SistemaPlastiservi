<?php

namespace App\Http\Controllers;

use App\Models\AreaProduccionSuc;
use App\Models\Seguridad\Usuario;
use App\Models\Sucursal;
use Illuminate\Http\Request;

class AreaProduccionSucFaseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-area-produccion-suc-fase');
        //$datas = FormaPago::orderBy('id')->get();
        $users = Usuario::findOrFail(auth()->id());
        $sucurArray = $users->sucursales->pluck('id')->toArray();
        $tablas['sucursales'] = Sucursal::orderBy('id')
                        ->whereIn('sucursal.id', $sucurArray)
                        ->get();
        return view('areaproduccionsucetapaprod.index',compact('tablas'));
    }

    public function areaproduccionsucetapaprodpage(Request $request){
        //dd($request);
        $datas = AreaProduccionSuc::report($request);
        return datatables($datas)->toJson();
    }

        /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function editar($id)
    {
        can('editar-area-produccion-suc-fase');
        dd("entro");
        //$data = FaseProduccion::findOrFail($id);
        return view('faseproduccion.editar', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function actualizar(ValidarFaseProduccion $request, $id)
    {
        //FaseProduccion::findOrFail($id)->update($request->all());
        $faseproduccion = FaseProduccion::findOrFail($id);
        $faseproduccion->update($request->all());
        return redirect('faseproduccion')->with('mensaje','Fase produccion actualizado con exito');
    }

}