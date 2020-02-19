<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidarPersona;
use App\Models\Cargo;
use App\Models\JefaturaSucursalArea;
use App\Models\JefaturaSucursalAreaPersona;
use App\Models\Persona;
use App\User;
use Illuminate\Http\Request;

class PersonaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-persona');
        $datas = Persona::orderBy('id')->get();
        //dd($datas->user);
        return view('persona.index', compact('datas'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function crear()
    {
        can('crear-persona');
        $cargos = Cargo::orderBy('id')->get();
        $jefaturasucursalareas = JefaturaSucursalArea::orderBy('id')->get();
        $users = User::orderBy('id')->get();
        $aux_sta=1;
        return view('persona.crear',compact('cargos','jefaturasucursalareas','users','aux_sta'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function guardar(ValidarPersona $request)
    {
        can('guardar-persona');
        //dd($request);
        $persona = Persona::create($request->all());
        $persona->jefaturasucursalareas()->sync($request->persona_id);
        return redirect('persona')->with('mensaje','Persona creado con exito');
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
        can('editar-persona');
        $data = Persona::findOrFail($id);
        //dd($data->jefaturasucursalareas);
        $cargos = Cargo::orderBy('id')->get();
        $jefaturasucursalareas = JefaturaSucursalArea::orderBy('id')->get();
        $users = User::orderBy('id')->get();
        $aux_sta=2;
        return view('persona.editar', compact('data','cargos','jefaturasucursalareas','users','aux_sta'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function actualizar(ValidarPersona $request, $id)
    {
        $persona = Persona::findOrFail($id);
        $persona->update($request->all());
        $persona->jefaturasucursalareas()->sync($request->persona_id);
        return redirect('persona')->with('mensaje','Persona actualizado con exito');
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
            if (Persona::destroy($id)) {
                //Eliminar los hijos en categoriaprodsuc
                $jefaturasucursalareapersona = JefaturaSucursalAreaPersona::where('persona_id', '=', $id);
                $jefaturasucursalareapersona->delete();
                return response()->json(['mensaje' => 'ok']);
            } else {
                return response()->json(['mensaje' => 'ng']);
            }
        } else {
            abort(404);
        }
    }
}
