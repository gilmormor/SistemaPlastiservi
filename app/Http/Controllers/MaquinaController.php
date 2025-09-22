<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidarMaquina;
use App\Models\EtapaProd;
use App\Models\Maquina;
use App\Models\MaquinaGrupo;
use App\Models\Seguridad\Usuario;
use App\Models\Sucursal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MaquinaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-maquina');
        //$datas = FormaPago::orderBy('id')->get();
        return view('maquina.index');
    }

    public function maquinapage(){    
        $user = Usuario::findOrFail(auth()->id());
        $sucurArray = $user->sucursales->pluck('id')->toArray();
        $sucurcadena = implode(",", $sucurArray);

        $sql = "SELECT maquina.id,maquina.nombre,sucursal.nombre as sucursal_nombre,
                maquinagrupo.nombre as maquinagrupo_nombre,
                GROUP_CONCAT(DISTINCT etapaprod.nombre) as etapaprod_nombre
                from maquina INNER JOIN sucursal
                ON sucursal.id = maquina.sucursal_id
                INNER JOIN maquinagrupo
                ON maquinagrupo.id = maquina.maquinagrupo_id
                LEFT JOIN maquinaetapaprod
                ON maquinaetapaprod.maquina_id = maquina.id
                LEFT JOIN etapaprod
                ON etapaprod.id = maquinaetapaprod.etapaprod_id
                where sucursal_id in ($sucurcadena)
                AND isnull(maquina.deleted_at)
                GROUP BY maquina.id;";
        $datas = DB::select($sql);
        return datatables($datas)->toJson();
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function crear()
    {
        can('crear-maquina');
        $user = Usuario::findOrFail(auth()->id());
        $sucurArray = $user->sucursales->pluck('id')->toArray();
        $tablas['sucursales'] = Sucursal::orderBy('id')->whereIn('sucursal.id', $sucurArray)->get();
        $tablas['maquinagrupo'] = MaquinaGrupo::orderBy('id')->get();
        $tablas['etapaprod'] = EtapaProd::orderBy('id')->get();
        return view('maquina.crear',compact('tablas'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function guardar(ValidarMaquina $request)
    {
        can('guardar-maquina');
        $maquina = Maquina::create($request->all());
        $maquina->etapaprods()->sync($request->etapaprod_id);
        return redirect('maquina')->with('mensaje','Maquina creada con exito');
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
        can('editar-maquina');
        $data = Maquina::findOrFail($id);
        $user = Usuario::findOrFail(auth()->id());
        $sucurArray = $user->sucursales->pluck('id')->toArray();
        $tablas['sucursales'] = Sucursal::orderBy('id')->whereIn('sucursal.id', $sucurArray)->get();
        $tablas['maquinagrupo'] = MaquinaGrupo::orderBy('id')->get();
        $tablas['etapaprod'] = EtapaProd::orderBy('id')->get();
        //dd($tablas['etapaprod']);
        return view('maquina.editar', compact('data','tablas'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function actualizar(ValidarMaquina $request, $id)
    {
        //Maquina::findOrFail($id)->update($request->all());
        $maquina = Maquina::findOrFail($id);
        $maquina->etapaprods()->sync($request->etapaprod_id);
        return redirect('maquina')->with('mensaje','Maquina actualizado con exito');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function eliminar(Request $request, $id)
    {
        if(can('eliminar-maquina',false)){
            if ($request->ajax()) {
                $maquina = Maquina::findOrFail($request->id);
                $aux_regAso = false;
                $aux_tabla = [];
                if(count($maquina->op) > 0){
                    $aux_regAso = true;
                    $aux_tabla[] = "Orden de Produccion";
                }
                if($aux_regAso){
                    return response()->json([
                        'id' => 1,
                        'mensaje' => "No se puede eliminar, tiene registros asociados a la tabla: " . implode(", ", $aux_tabla) . ".",
                        'tipo_alert' => "error"
                    ]);
                }
                if (Maquina::destroy($request->id)) {
                    //dd('entro');
                    //Despues de eliminar actualizo el campo usuariodel_id=usuario que elimino el registro
                    $maquina = Maquina::withTrashed()->findOrFail($request->id);
                    $maquina->usuariodel_id = auth()->id();
                    $maquina->save();
                    return response()->json(['mensaje' => 'ok']);
                } else {
                    return response()->json(['mensaje' => 'ng']);
                }
            } else {
                abort(404);
            }
        }else{
            return response()->json(['mensaje' => 'ne']);
        }
    }
}