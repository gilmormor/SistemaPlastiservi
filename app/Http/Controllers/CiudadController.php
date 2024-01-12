<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidarCiudad;
use App\Models\Ciudad;
use App\Models\Cliente;
use App\Models\Comuna;
use App\Models\Provincia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CiudadController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-ciudad');
        $datas = Ciudad::orderBy('id')->get();
        return view('ciudad.index', compact('datas'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function crear()
    {
        can('crear-ciudad');
        return view('ciudad.crear');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function guardar(ValidarCiudad $request)
    {
        can('guardar-ciudad');
        Ciudad::create($request->all());
        return redirect('ciudad')->with('mensaje','Ciudad creada con exito');
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
        can('editar-ciudad');
        $data = Ciudad::findOrFail($id);
        return view('ciudad.editar', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function actualizar(ValidarCiudad $request, $id)
    {
        Ciudad::findOrFail($id)->update($request->all());
        return redirect('ciudad')->with('mensaje','Ciudad actualizada con exito');
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
            if (Ciudad::destroy($id)) {
                return response()->json(['mensaje' => 'ok']);
            } else {
                return response()->json(['mensaje' => 'ng']);
            }
        } else {
            abort(404);
        }
    }

    public function actualizardatoscomuna()
    {
        /* $sql = "SELECT comuna.id AS comuna_id,comuna.nombre AS comunaweb,comunaciudad.id AS comunaciudad_id,comunaciudad.comuna,
        comunaciudad.ciudad_id,
        comunaciudad.ciudad,ciudad.id AS ciudad_id
        FROM comuna INNER JOIN comunaciudad
        ON comuna.nombre= comunaciudad.comuna
        INNER JOIN ciudad
        ON ciudad.id = comunaciudad.ciudad_id
        ORDER BY comunaciudad.id";
        $ciudades = DB::select($sql);

        foreach ($ciudades as $ciudad) {
            $comuna = Comuna::findOrFail($ciudad->comuna_id);
            $comuna->ciudad_id = $ciudad->ciudad_id;
            $comuna->save();
        } */

        $provincias = Provincia::orderBy('id')->get();
        //dd($provincia);
        /* foreach ($provincias as $provincia) {
            Ciudad::create(
                [
                    "nombre" => $provincia->nombre
                ]
            );
        } */
        $clientes = Cliente::orderBy('id')->get();
        foreach ($clientes as $cliente) {
            $cliente->ciudad_id = $cliente->provinciap_id;
            $cliente->save();
        }
        $comunas = Comuna::orderBy('id')->get();
        foreach ($comunas as $comuna) {
            $comuna->ciudad_id = $comuna->provincia_id;
            $comuna->save();
        }

    }

}
