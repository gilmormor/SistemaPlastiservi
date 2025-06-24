<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidarAtributo;
use App\Models\Atributo;
use App\Models\Seguridad\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AtributoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-atributo');
        //$datas = FormaPago::orderBy('id')->get();
        return view('atributo.index');
    }

    public function atributopage(){    
        $sql = "SELECT atributo.id,atributo.nombre
                from atributo
                where isnull(atributo.deleted_at);";
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
        can('crear-atributo');
        $tablas['tipodatos'] = Atributo::tipoDato();
        return view('atributo.crear',compact('tablas'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function guardar(ValidarAtributo $request)
    {
        can('guardar-atributo');
        $atributo = Atributo::create($request->all());
        return redirect('atributo')->with('mensaje','Atributo creada con exito');
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
        can('editar-atributo');
        $data = Atributo::findOrFail($id);
        $tablas['tipodatos'] = Atributo::tipoDato();
        return view('atributo.editar', compact('data','tablas'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function actualizar(ValidarAtributo $request, $id)
    {
        //Atributo::findOrFail($id)->update($request->all());
        $atributo = Atributo::findOrFail($id);
        $atributo->update($request->all());
        return redirect('atributo')->with('mensaje','Atributo actualizado con exito');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function eliminar(Request $request, $id)
    {
        if(can('eliminar-atributo',false)){
            if ($request->ajax()) {
                $atributo = Atributo::findOrFail($request->id);
                $aux_regAso = false;
                $aux_tabla = [];
                if(count($atributo->maquinaatributo) > 0){
                    $aux_regAso = true;
                    $aux_tabla[] = "Maquina Atributo";
                }
                if($aux_regAso){
                    return response()->json([
                        'id' => 1,
                        'mensaje' => "No se puede eliminar, tiene registros asociados a la tabla: " . implode(", ", $aux_tabla) . ".",
                        'tipo_alert' => "error"
                    ]);
                }
                if (Atributo::destroy($request->id)) {
                    //dd('entro');
                    //Despues de eliminar actualizo el campo usuariodel_id=usuario que elimino el registro
                    $atributo = Atributo::withTrashed()->findOrFail($request->id);
                    $atributo->usuariodel_id = auth()->id();
                    $atributo->save();
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
