<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidarPersona;
use App\Models\Cargo;
use App\Models\JefaturaSucursalArea;
use App\Models\Persona;
use App\Models\PersonaEtapaProd;
use App\Models\Seguridad\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PersonaEtapaProdController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-persona-etapaprod');
        $datas = Persona::orderBy('id')->get();
        //dd($datas->user);
        return view('personaetapaprod.index', compact('datas'));
    }

    public function personaetapaprodpage(){
        $sql = "SELECT persona.*, concat(persona.nombre, ' ' ,persona.apellido) AS nombreapellido,usuario.email
            from persona LEFT JOIN usuario
            ON persona.usuario_id = usuario.id
            where isnull(persona.deleted_at);";
        $datas = DB::select($sql);
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
        can('editar-persona-etapaprod');
        $data = Persona::findOrFail($id);
        $arraySucFisxUsu = implode(",", sucFisXUsu($data));
        //dd($arraySucFisxUsu);
        $etapaProdSucs = PersonaEtapaProd::etapasProdSucAreaProd($arraySucFisxUsu);
        $cargos = Cargo::orderBy('id')->get();
        $jefaturasucursalareas = JefaturaSucursalArea::orderBy('id')->get();
        
        $users = Usuario::orderBy('id')->get();
        $aux_sta=2;
        return view('personaetapaprod.editar', compact('data','cargos','jefaturasucursalareas','users','aux_sta','etapaProdSucs'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function actualizar(Request $request, $id)
    {
        can('guardar-persona-etapaprod');
        $persona = Persona::findOrFail($id);
        //$persona->update($request->all());
        $persona->etapaprods()->sync($request->areaproduccionsucetapaprod_id);
        return redirect('personaetapaprod')->with('mensaje','Persona actualizado con exito');
    }
}