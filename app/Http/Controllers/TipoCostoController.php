<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidarTipoCosto;
use App\Models\TipoCosto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TipoCostoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-tipo-costo');
        //$datas = FormaPago::orderBy('id')->get();
        return view('tipocosto.index');
    }

    public function tipocostopage(){
        return datatables()
            ->eloquent(TipoCosto::query())
            ->toJson();
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function crear()
    {
        can('crear-tipo-costo');
        return view('tipocosto.crear');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function guardar(ValidarTipoCosto $request)
    {
        can('guardar-tipo-costo');
        DB::beginTransaction();
        try {
            TipoCosto::create($request->all());
            DB::commit();
            return redirect('tipocosto')->with('mensaje','Tipo Costo creado con éxito');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('mensaje', 'Error: ' . $e->getMessage())->withInput();
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
    public function editar($id)
    {
        can('editar-tipo-costo');
        $data = TipoCosto::findOrFail($id);
        return view('tipocosto.editar', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function actualizar(ValidarTipoCosto $request, $id)
    {
        DB::beginTransaction();
        try {
            TipoCosto::findOrFail($id)->update($request->all());
            DB::commit();
            return redirect('tipocosto')->with('mensaje','Tipo Costo actualizado con exito');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('mensaje', 'Error: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function eliminar(Request $request, $id)
    {
        if(can('eliminar-tipo-costo',false)){
            DB::beginTransaction();
            try {
                if ($request->ajax()) {
                    $tipocosto = TipoCosto::findOrFail($request->id);
                    $aux_regAso = false;
                    $aux_tabla = [];
                    if(count($tipocosto->insumos) > 0){
                        $aux_regAso = true;
                        $aux_tabla[] = "Insumo";
                    }
                    if($aux_regAso){
                        return response()->json([
                            'id' => 1,
                            'mensaje' => "No se puede eliminar, tiene registros asociados a la tabla: " . implode(", ", $aux_tabla) . ".",
                            'tipo_alert' => "error"
                        ]);
                    }
                    TipoCosto::destroy($request->id);
                    //Despues de eliminar actualizo el campo usuariodel_id=usuario que elimino el registro
                    $tipocosto = TipoCosto::withTrashed()->findOrFail($request->id);
                    $tipocosto->usuariodel_id = auth()->id();
                    $tipocosto->save();
                } else {
                    abort(404);
                }
                DB::commit();
                return response()->json(['mensaje' => 'ok']);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    'id' => 1,
                    'mensaje' => 'Error: ' . $e->getMessage(),
                    'tipo_alert' => "error"
                ]);
            }


        }else{
            return response()->json(['mensaje' => 'ne']);
        }
    }
}