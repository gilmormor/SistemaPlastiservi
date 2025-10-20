<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidarOperario;
use App\Models\Operario;
use App\Models\Seguridad\Usuario;
use App\Models\Sucursal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OperarioController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-operario');
        //$datas = FormaPago::orderBy('id')->get();
        return view('operario.index');
    }

    public function operariopage(){
        $user = Usuario::findOrFail(auth()->id());
        $sucurArray = $user->sucursales->pluck('id')->toArray();
        $sucurcadena = implode(",", $sucurArray);

        /* $sql = "SELECT operario.id,operario.nombre,sucursal.nombre as sucursal_nombre,
                GROUP_CONCAT(DISTINCT concat(sucursal.nombre, '/' ,areaproduccion.nombre,'/',etapaprod.nombre)) as areaproduccion_nombre
                from operario_areaproduccionsucep INNER JOIN areaproduccionsucetapaprod
                ON operario_areaproduccionsucep.areaproduccionsucep_id = areaproduccionsucetapaprod.id
                INNER JOIN areaproduccionsuc
                ON areaproduccionsucetapaprod.areaproduccionsuc_id = areaproduccionsuc.id
                INNER JOIN etapaprod
                ON etapaprod.id = areaproduccionsucetapaprod.etapaprod_id
                INNER JOIN operario 
                ON operario.id = operario_areaproduccionsucep.operario_id
                INNER JOIN sucursal
                ON sucursal.id = areaproduccionsuc.sucursal_id
                INNER JOIN areaproduccion
                ON areaproduccion.id = areaproduccionsuc.areaproduccion_id
                where areaproduccionsuc.sucursal_id in ($sucurcadena)
                AND isnull(operario.deleted_at)
                GROUP BY operario.id;"; */
        $sql = "SELECT operario.id,operario.nombre,'' as sucursal_nombre,
                '' as areaproduccion_nombre
                from operario
                where isnull(operario.deleted_at)
                GROUP BY operario.id;";

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
        can('crear-operario');
        $user = Usuario::findOrFail(auth()->id());
        $sucurArray = $user->sucursales->pluck('id')->toArray();
        $sucurcadena = implode(",", $sucurArray);
        $tablas['sucursales'] = Sucursal::orderBy('id')->whereIn('sucursal.id', $sucurArray)->get();

        $sql = "SELECT areaproduccionsucetapaprod.id,
                areaproduccionsuc.id as areaproduccionsuc_id,
                areaproduccionsuc.areaproduccion_id,
                areaproduccionsuc.sucursal_id,
                sucursal.nombre as sucursal_nombre,
                areaproduccion.nombre as areaproduccion_nombre,
                etapaprod.nombre AS etapaprod_nombre
                from areaproduccionsucetapaprod INNER JOIN areaproduccionsuc
                ON areaproduccionsucetapaprod.areaproduccionsuc_id = areaproduccionsuc.id
                INNER JOIN etapaprod
                ON etapaprod.id = areaproduccionsucetapaprod.etapaprod_id
                INNER JOIN sucursal
                ON sucursal.id = areaproduccionsuc.sucursal_id
                INNER JOIN areaproduccion
                ON areaproduccion.id = areaproduccionsuc.areaproduccion_id
                where areaproduccionsuc.sucursal_id in ($sucurcadena)
                AND isnull(areaproduccionsuc.deleted_at);";
        $tablas['areaproduccionsucep'] = DB::select($sql);
        //dd($tablas['areaproduccionsuc']);
        return view('operario.crear',compact('tablas'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function guardar(ValidarOperario $request)
    {
        can('guardar-operario');
        $operario = Operario::create($request->all());
        $operario->areaproduccionsuceps()->sync($request->areaproduccionsucep_id);
        return redirect('operario')->with('mensaje','Operario creado con exito');
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
        can('editar-operario');
        $data = Operario::findOrFail($id);
        $user = Usuario::findOrFail(auth()->id());
        $sucurArray = $user->sucursales->pluck('id')->toArray();
        $sucurcadena = implode(",", $sucurArray);

        $tablas['sucursales'] = Sucursal::orderBy('id')->whereIn('sucursal.id', $sucurArray)->get();
        $sql = "SELECT areaproduccionsucetapaprod.id,
                areaproduccionsuc.id as areaproduccionsuc_id,
                areaproduccionsuc.areaproduccion_id,
                areaproduccionsuc.sucursal_id,
                sucursal.nombre as sucursal_nombre,
                areaproduccion.nombre as areaproduccion_nombre,
                etapaprod.nombre AS etapaprod_nombre
                from areaproduccionsucetapaprod INNER JOIN areaproduccionsuc
                ON areaproduccionsucetapaprod.areaproduccionsuc_id = areaproduccionsuc.id
                INNER JOIN etapaprod
                ON etapaprod.id = areaproduccionsucetapaprod.etapaprod_id
                INNER JOIN sucursal
                ON sucursal.id = areaproduccionsuc.sucursal_id
                INNER JOIN areaproduccion
                ON areaproduccion.id = areaproduccionsuc.areaproduccion_id
                where areaproduccionsuc.sucursal_id in ($sucurcadena)
                AND isnull(areaproduccionsuc.deleted_at);";
        $tablas['areaproduccionsucep'] = DB::select($sql);

        return view('operario.editar', compact('data','tablas'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function actualizar(ValidarOperario $request, $id)
    {
        //Operario::findOrFail($id)->update($request->all());
        $operario = Operario::findOrFail($id);
        $operario->update($request->all());
        $operario->areaproduccionsuceps()->sync($request->areaproduccionsucep_id);
        return redirect('operario')->with('mensaje','Maquina actualizado con exito');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function eliminar(Request $request, $id)
    {
        if(can('eliminar-operario',false)){
            if ($request->ajax()) {
                $operario = Operario::findOrFail($request->id);
                $aux_regAso = false;
                $aux_tabla = [];
                if(count($operario->op) > 0){
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
                if (Operario::destroy($request->id)) {
                    //dd('entro');
                    //Despues de eliminar actualizo el campo usuariodel_id=usuario que elimino el registro
                    $operario = Operario::withTrashed()->findOrFail($request->id);
                    $operario->usuariodel_id = auth()->id();
                    $operario->save();
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