<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Admin\Permiso;
use App\Models\Admin\Rol;
use Illuminate\Support\Facades\DB;

class PermisoRolController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $rols = Rol::orderBy('id')->pluck('nombre', 'id')->toArray();
        $roles = Rol::get();
        $permisos = Permiso::get();
        $permisosRols = Permiso::with('roles')->get()->pluck('roles', 'id')->toArray();
        return view('admin.permiso-rol.index', compact('rols','roles', 'permisos', 'permisosRols'));
    }

    public function permisorolpage(Request $request){
        if(empty($request->rol_id)){
            $aux_condrol_id = " true";
        }else{
    
            if(is_array($request->rol_id)){
                $aux_rolid = implode ( ',' , $request->rol_id);
            }else{
                $aux_rolid = $request->rol_id;
            }
            $aux_condrol_id = " rol.id in ($aux_rolid) ";
        }
    
        $permisosRols = Permiso::with('roles')->get()->pluck('roles', 'id')->toArray();
        $sql = "SELECT *
        FROM permiso
        WHERE isnull(permiso.deleted_at);";
        $permisos = DB::select($sql);
        $arraypermisos = (array) $permisos;
        //dd($arraypermisos);
        $sql = "SELECT *
            FROM rol
            WHERE $aux_condrol_id
            AND isnull(rol.deleted_at);";
        $roles = DB::select($sql);

        foreach ($permisos as &$permiso) {
            $permisoArray = (array) $permiso; // Convertir el objeto en un array
            foreach ($roles as $rol) {
                $permisoArray["id" . $rol->id] = in_array($rol->id, array_column($permisosRols[$permiso->id], "id"))? "checked" : "";
            }
            $permiso = (object) $permisoArray; // Convertir el array en un objeto de nuevo
        }
        //dd($permisos);
        return datatables($permisos)->toJson();
    }

    public function encabezadoTabla(Request $request){
        if(empty($request->rol_id)){
            $aux_condrol_id = " true";
        }else{
    
            if(is_array($request->rol_id)){
                $aux_rolid = implode ( ',' , $request->rol_id);
            }else{
                $aux_rolid = $request->rol_id;
            }
            $aux_condrol_id = " rol.id in ($aux_rolid) ";
        }
        $sql = "SELECT *
            FROM rol
            WHERE  $aux_condrol_id
            AND isnull(rol.deleted_at);";
        $rols = DB::select($sql);
        $respuesta = [];
        $respuesta["campos"] = [];
        $respuesta["campos_id"] = [];
        $respuesta["rol_id"] = [];
        $respuesta["encabezadotabla"] = "<thead>
                        <tr>
                            <th>ID</th>
                            <th>Permiso</th>";
        foreach ($rols as $rol){
            $respuesta["encabezadotabla"] .="<th class='text-center'>$rol->nombre</th>";
            $respuesta["campos"][] = $rol->nombre;
            $respuesta["campos_id"][] = "id" .strval($rol->id);
            $respuesta["rol_id"][] = $rol->id;
        }
        $respuesta["encabezadotabla"] .= "</tr>
                        </thead>";
        return $respuesta;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function guardar(Request $request)
    {
        if ($request->ajax()) {
            $permisos = new Permiso();
            if ($request->input('estado') == 1) {
                $permisos->find($request->input('permiso_id'))->roles()->attach($request->input('rol_id'));
                return response()->json(['respuesta' => 'El rol se asigno correctamente']);
            } else {
                $permisos->find($request->input('permiso_id'))->roles()->detach($request->input('rol_id'));
                return response()->json(['respuesta' => 'El rol se elimino correctamente']);
            }
        } else {
            abort(404);
        }
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
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
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
