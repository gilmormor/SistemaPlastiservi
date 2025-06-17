<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidarEtapaProd;
use App\Models\EtapaProd;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EtapaProdController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-etapa-de-produccion');
        //$datas = FormaPago::orderBy('id')->get();
        return view('etapaprod.index');
    }

    public function etapaprodpage(){
        $sql = "SELECT etapaprod.id,etapaprod.nombre
                from etapaprod
                where isnull(etapaprod.deleted_at);";
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
        can('crear-etapa-de-produccion');
        return view('etapaprod.crear');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function guardar(ValidarEtapaProd $request)
    {
        can('guardar-etapa-de-produccion');
        //dd($request);
        $etapaprod = EtapaProd::create($request->all());
        return redirect('etapaprod')->with('mensaje','Etapa Produccion creado con exito');
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
        can('editar-etapa-de-produccion');
        $data = EtapaProd::findOrFail($id);
        return view('etapaprod.editar', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function actualizar(ValidarEtapaProd $request, $id)
    {
        //EtapaProd::findOrFail($id)->update($request->all());
        $etapaprod = EtapaProd::findOrFail($id);
        $etapaprod->update($request->all());
        return redirect('etapaprod')->with('mensaje','Etapa produccion actualizado con exito');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function eliminar(Request $request, $id)
    {
        if(can('eliminar-etapa-de-produccion',false)){
            if ($request->ajax()) {
                $etapaprod = EtapaProd::findOrFail($request->id);
                $aux_regAso = false;
                $aux_tabla = [];
                if(count($etapaprod->areaproduccion) > 0){
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
                if (EtapaProd::destroy($request->id)) {
                    //dd('entro');
                    //Despues de eliminar actualizo el campo usuariodel_id=usuario que elimino el registro
                    $etapaprod = EtapaProd::withTrashed()->findOrFail($request->id);
                    $etapaprod->usuariodel_id = auth()->id();
                    $etapaprod->save();
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
