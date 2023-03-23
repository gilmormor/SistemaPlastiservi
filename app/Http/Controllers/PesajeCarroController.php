<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidarPesajeCarro;
use App\Models\PesajeCarro;
use App\Models\Seguridad\Usuario;
use App\Models\Sucursal;
use Illuminate\Http\Request;

class PesajeCarroController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-pesaje-carro');
        $datas = PesajeCarro::orderBy('id')->get();
        return view('pesajecarro.index', compact('datas'));
    }

    public function pesajecarropage(){
        return datatables()
            ->eloquent(PesajeCarro::query())
            ->toJson();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function crear()
    {
        can('crear-pesaje-carro');
        $user = Usuario::findOrFail(auth()->id());
        $sucurArray = $user->sucursales->pluck('id')->toArray();
        $tablas = array();
        $tablas['sucursales'] = Sucursal::orderBy('id')->whereIn('sucursal.id', $sucurArray)->get();
        return view('pesajecarro.crear', compact('tablas'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function guardar(ValidarPesajeCarro $request)
    {
        can('guardar-pesaje-carro');
        PesajeCarro::create($request->all());
        return redirect('pesajecarro')->with('mensaje','PesajeCarro creado con exito');
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
        can('editar-pesaje-carro');
        $data = PesajeCarro::findOrFail($id);
        $user = Usuario::findOrFail(auth()->id());
        $sucurArray = $user->sucursales->pluck('id')->toArray();
        $tablas = array();
        $tablas['sucursales'] = Sucursal::orderBy('id')->whereIn('sucursal.id', $sucurArray)->get();
        return view('pesajecarro.editar', compact('data','tablas'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function actualizar(ValidarPesajeCarro $request, $id)
    {
        PesajeCarro::findOrFail($id)->update($request->all());
        return redirect('pesajecarro')->with('mensaje','PesajeCarro actualizado con exito');
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
            if (PesajeCarro::destroy($id)) {
                return response()->json(['mensaje' => 'ok']);
            } else {
                return response()->json(['mensaje' => 'ng']);
            }
        } else {
            abort(404);
        }
    }

    public function listar(Request $request)
    {
        if($request->ajax()){
            /*
            $users = Usuario::findOrFail(auth()->id());
            $sucurArray = $users->sucursales->pluck('id')->toArray();
            */
            $pesajecarros = PesajeCarro::where("sucursal_id","=",$request->id)->where("activo",1)->get();
            return $pesajecarros;
        }
    }
}
