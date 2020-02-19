<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidarVendedor;
use App\Models\Persona;
use App\Models\Vendedor;
use Illuminate\Http\Request;

class VendedorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-vendedor');
        $datas = Vendedor::orderBy('id')->get();
        return view('vendedor.index', compact('datas'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function crear()
    {
        can('crear-vendedor');
        $personas = Persona::orderBy('id')->get();
        $aux_sta = 1;
        return view('vendedor.crear', compact('personas','aux_sta'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function guardar(ValidarVendedor $request)
    {
        can('guardar-vendedor');
        //dd($request);
        Vendedor::create($request->all());
        return redirect('vendedor')->with('mensaje','Vendedor creado con exito');
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
        can('editar-vendedor');
        $data = Vendedor::findOrFail($id);
        $personas = Persona::orderBy('id')->get();
        $aux_sta = 2;
        return view('vendedor.editar', compact('data','personas','aux_sta'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function actualizar(ValidarVendedor $request, $id)
    {
        Vendedor::findOrFail($id)->update($request->all());
        return redirect('vendedor')->with('mensaje','Vendedor actualizado con exito');
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
            if (Vendedor::destroy($id)) {
                return response()->json(['mensaje' => 'ok']);
            } else {
                return response()->json(['mensaje' => 'ng']);
            }
        } else {
            abort(404);
        }
    }
}