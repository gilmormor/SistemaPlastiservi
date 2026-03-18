<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidarInsumo;
use App\Models\Insumo;
use App\Models\TipoCosto;
use App\Models\UnidadMedida;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InsumoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-insumo');
        //$datas = FormaPago::orderBy('id')->get();
        return view('insumo.index');
    }

    public function insumopage(){
        return datatables()
            ->eloquent(Insumo::query())
            ->toJson();
    }

    public function insumobuscarpage(){
        return datatables()
            ->eloquent(Insumo::query())
            ->toJson();
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function crear()
    {
        can('crear-insumo');
        $unidadmedidas = UnidadMedida::orderBy('id')->pluck('descripcion', 'id')->toArray();
        $tipocostos = TipoCosto::orderBy('id')->pluck('nombre', 'id')->toArray();
        return view('insumo.crear',compact('unidadmedidas','tipocostos'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function guardar(ValidarInsumo $request)
    {
        can('guardar-insumo');
        DB::beginTransaction();
        try {
            Insumo::create($request->all());
            DB::commit();
            return redirect('insumo')->with('mensaje','Insumo creado con éxito');
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
        can('editar-insumo');
        $data = Insumo::findOrFail($id);
        $unidadmedidas = UnidadMedida::orderBy('id')->pluck('descripcion', 'id')->toArray();
        $tipocostos = TipoCosto::orderBy('id')->pluck('nombre', 'id')->toArray();
        return view('insumo.editar', compact('data','unidadmedidas','tipocostos'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function actualizar(ValidarInsumo $request, $id)
    {
        $insumoOriginal = Insumo::findOrFail($id);
        DB::beginTransaction();
        try {
            Insumo::findOrFail($id)->update($request->all());
           // Recargar el modelo actualizado con relaciones
            $insumoActualizado = Insumo::findOrFail($id);

            // Guardar log comparando original con actualizado
            $aux_resp = guardarLogCambioModelo(
                $insumoActualizado,
                [],
                $insumoOriginal // <- se lo pasamos como estado original
            );
            DB::commit();
            return redirect('insumo')->with('mensaje','Insumo actualizado con éxito');
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
        if(can('eliminar-insumo',false)){
            DB::beginTransaction();
            try {

                if ($request->ajax()) {
                    $insumo = Insumo::findOrFail($request->id);
                    $aux_regAso = false;
                    $aux_tabla = [];
                    if(count($insumo->productoinsumos) > 0){
                        $aux_regAso = true;
                        $aux_tabla[] = "ProductoInsumos";
                    }
                    if($aux_regAso){
                        return response()->json([
                            'id' => 1,
                            'mensaje' => "No se puede eliminar, tiene registros asociados a la tabla: " . implode(", ", $aux_tabla) . ".",
                            'tipo_alert' => "error"
                        ]);
                    }
                    Insumo::destroy($request->id);
                    //dd('entro');
                    //Despues de eliminar actualizo el campo usuariodel_id=usuario que elimino el registro
                    $insumo = Insumo::withTrashed()->findOrFail($request->id);
                    $insumo->usuariodel_id = auth()->id();
                    $insumo->save();
                } else {
                    abort(404);
                }
                return response()->json(['mensaje' => 'ok']);
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json(['mensaje' => 'Error: ' . $e->getMessage()]);
            }
        }else{
            return response()->json(['mensaje' => 'ne']);
        }
    }

    public function buscarUnInsumo(Request $request)
    {
        if ($request->ajax()) {
            // Validar que el ID sea proporcionado
            if (!$request->has('id') || empty($request->id)) {
                return response()->json([
                    'cont' => 0,
                    'mensaje' => 'ID de insumo no proporcionado',
                    'tipo_alert' => 'warning'
                ], 400);
            }

            $insumo = Insumo::find($request->id);
            if (!$insumo) {
                // no existe, devolvemos cont = 0 sin lanzar excepción
                return response()->json([
                    'cont' => 0,
                    'id' => null,
                    'mensaje' => 'Insumo no encontrado',
                    'tipo_alert' => 'error'
                ]);
            }

            // existe
            return response()->json([
                'cont' => 1,
                'id' => $insumo->id,
                'nombre' => $insumo->nombre,
                'activo' => $insumo->activo,
                'costounitario' => $insumo->costounitario,
                'unidadmedida_id' => $insumo->unidadmedida_id,
                'unidadmedida_nombre' => $insumo->unidadmedida->nombre,
                'tipocosto_id' => $insumo->tipocosto_id,
                'mensaje' => 'Insumo encontrado',
                'tipo_alert' => 'success'
            ]);
        } else {
            abort(404);
        }
    }
}