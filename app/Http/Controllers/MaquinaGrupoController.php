<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidarMaquinaGrupo;
use App\Models\MaquinaGrupo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MaquinaGrupoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-maquina-grupo');
        //$datas = FormaPago::orderBy('id')->get();
        return view('maquinagrupo.index');
    }

    public function maquinagrupopage(){    
        $sql = "SELECT maquinagrupo.id,maquinagrupo.nombre
        from maquinagrupo
        where isnull(maquinagrupo.deleted_at);";
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
        can('crear-maquina-grupo');
        return view('maquinagrupo.crear');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function guardar(ValidarMaquinaGrupo $request)
    {
        can('guardar-maquina-grupo');
        $maquinagrupo = MaquinaGrupo::create($request->all());
        return redirect('maquinagrupo')->with('mensaje','MaquinaGrupo creado con exito');
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
        can('editar-maquina-grupo');
        $data = MaquinaGrupo::findOrFail($id);
        return view('maquinagrupo.editar', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function actualizar(ValidarMaquinaGrupo $request, $id)
    {
        //MaquinaGrupo::findOrFail($id)->update($request->all());
        $maquinagrupo = MaquinaGrupo::findOrFail($id);
        $maquinagrupo->update($request->all());
        return redirect('maquinagrupo')->with('mensaje','MaquinaGrupo actualizado con exito');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function eliminar(Request $request, $id)
    {
        if(can('eliminar-maquina-grupo',false)){
            if ($request->ajax()) {
                $maquinagrupo = MaquinaGrupo::findOrFail($request->id);
                $aux_regAso = false;
                $aux_tabla = [];
                if(count($maquinagrupo->maquina) > 0){
                    $aux_regAso = true;
                    $aux_tabla[] = "Maquina";
                }
                if($aux_regAso){
                    return response()->json([
                        'id' => 1,
                        'mensaje' => "No se puede eliminar, tiene registros asociados a la tabla: " . implode(", ", $aux_tabla) . ".",
                        'tipo_alert' => "error"
                    ]);
                }
                if (MaquinaGrupo::destroy($request->id)) {
                    //dd('entro');
                    //Despues de eliminar actualizo el campo usuariodel_id=usuario que elimino el registro
                    $maquinagrupo = MaquinaGrupo::withTrashed()->findOrFail($request->id);
                    $maquinagrupo->usuariodel_id = auth()->id();
                    $maquinagrupo->save();
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