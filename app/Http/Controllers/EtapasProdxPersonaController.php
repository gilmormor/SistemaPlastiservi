<?php

namespace App\Http\Controllers;

use App\Models\Seguridad\Usuario;
use App\Models\Sucursal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EtapasProdxPersonaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-etapasprod-x-persona');
        $users = Usuario::findOrFail(auth()->id());
        $sucurArray = $users->sucursales->pluck('id')->toArray();
        $tablas['sucursales'] = Sucursal::orderBy('id')
                        ->whereIn('sucursal.id', $sucurArray)
                        ->get();
        //dd($datas->user);
        return view('etapasprodxpersona.index', compact('tablas'));
    }

    public function etapasprodxpersonapage(Request $request){
        $aux_sucursal_idCond = "";
        if(isset($request->sucursal_id) && $request->sucursal_id>0){
            $aux_sucursal_idCond = "areaproduccionsuc.sucursal_id = $request->sucursal_id";
        }
        $sql = "SELECT personaetapaprod.areaproduccionsucetapaprod_id,
                areaproduccionsucetapaprod.etapaprod_id,etapaprod.nombre AS etapaprod_nombre,
                personaetapaprod.persona_id
                FROM personaetapaprod INNER JOIN areaproduccionsucetapaprod
                ON personaetapaprod.areaproduccionsucetapaprod_id = areaproduccionsucetapaprod.id
                INNER JOIN etapaprod 
                ON areaproduccionsucetapaprod.etapaprod_id = etapaprod.id
                INNER JOIN persona
                ON persona.id = personaetapaprod.persona_id
                INNER JOIN areaproduccionsuc
                ON areaproduccionsuc.id = areaproduccionsucetapaprod.areaproduccionsuc_id
                WHERE $aux_sucursal_idCond;";
        $datas = DB::select($sql);
        return datatables($datas)->toJson();
    }
}
