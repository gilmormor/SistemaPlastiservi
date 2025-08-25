<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidarCValAt;
use App\Models\CValAt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CValAtController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-cvalat');
        //$datas = FormaPago::orderBy('id')->get();
        return view('cvalat.index');
    }

    public function cvalatpage(){
        return datatables()
            ->eloquent(CValAt::query())
            ->toJson();
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function crear()
    {
        can('crear-cvalat');
        return view('cvalat.crear');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function guardar(ValidarCValAt $request)
    {
        can('guardar-cvalat');
        /* DB::beginTransaction();
        try {
            CValAt::create($request->all());

            DB::commit();
            return redirect('cvalat')->with('mensaje','CValAt creado con exito');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('mensaje', 'Error: ' . $e->getMessage())->withInput();
        } */
        DB::beginTransaction();
        try {
            $cvalat = CValAt::create($request->only(['nombre', 'desc', 'orden', 'usuario_id']));

            // Guardar cvalatdets si vienen
            if ($request->has('detalles')) {
                foreach ($request->detalles as $detalle) {
                    $cvalat->cvalatdets()->create([
                        'nombre' => trim($detalle['nombredet']),
                        'desc' => trim($detalle['descdet']),
                        'orden' => trim($detalle['ordendet']),
                        'usuario_id' => auth()->id()
                    ]);
                }
            }

            DB::commit();
            return redirect('cvalat')->with('mensaje','Campo Val At creado con éxito');
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
        can('editar-cvalat');
        $data = CValAt::findOrFail($id);
        return view('cvalat.editar', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function actualizar(ValidarCValAt $request, $id)
    {
        /* DB::beginTransaction();
        try {
            CValAt::findOrFail($id)->update($request->all());
            DB::commit();
            return redirect('cvalat')->with('mensaje','CValAt actualizado con exito');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('mensaje', 'Error: ' . $e->getMessage())->withInput();
        } */

        DB::beginTransaction();
        try {
            $cvalat = CValAt::findOrFail($id);
            $cvalat->update($request->only(['nombre', 'desc', 'orden', 'usuario_id']));

            // Procesar detalles
            if ($request->has('detalles')) {
                $detallesIds = [];
                $detallesActuales = $cvalat->cvalatdets()->pluck('id')->toArray();
                
                foreach ($request->detalles as $detalle) {
                    if (isset($detalle['id'])) {
                        // Actualizar detalle existente
                        $detalleModel = $cvalat->cvalatdets()->find($detalle['id']);
                        if ($detalleModel) {
                            $detalleModel->update([
                                'nombre' => trim($detalle['nombredet']),
                                'desc' => trim($detalle['descdet']),
                                'orden' => trim($detalle['ordendet']),
                                'usuario_id' => auth()->id()
                            ]);
                            $detallesIds[] = $detalle['id'];
                        }
                    } else {
                        // Crear nuevo detalle
                        $newDetalle = $cvalat->cvalatdets()->create([
                            'nombre' => trim($detalle['nombredet']),
                            'desc' => trim($detalle['descdet']),
                            'orden' => trim($detalle['ordendet']),
                            'usuario_id' => auth()->id()
                        ]);
                        $detallesIds[] = $newDetalle->id;
                    }
                }

                // Identificar detalles a eliminar
                $detallesAEliminar = array_diff($detallesActuales, $detallesIds);
                
                // Validar y eliminar solo los que no tienen relaciones
                foreach ($detallesAEliminar as $detalleId) {
                    $detalle = $cvalat->cvalatdets()->find($detalleId);
                    
                    /* if ($detalle->hasRelationships()) {
                        DB::rollBack();
                        return redirect()->back()
                            ->with('error', 'No se puede eliminar el detalle con ID '.$detalleId.' porque tiene registros asociados en otras tablas')
                            ->withInput();
                    } */
                    $detalle->update(['usuariodel_id' => auth()->id()]); // Actualizar el campo usuariodel_id
                    $detalle->delete();
                }
            } else {
                // Validar antes de eliminar todos
                /* foreach ($cvalat->cvalatdets as $detalle) {
                    if ($detalle->hasRelationships()) {
                        DB::rollBack();
                        return redirect()->back()
                            ->with('error', 'No se puede eliminar algunos detalles porque tienen registros asociados en otras tablas')
                            ->withInput();
                    }
                }
                $cvalat->cvalatdets()->delete(); */
            }

            DB::commit();
            return redirect('cvalat')->with('mensaje', 'Campo Val At actualizado con éxito');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage())->withInput();
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
        if(can('eliminar-cvalat',false)){
            if ($request->ajax()) {
                $cvalat = CValAt::findOrFail($request->id);
                $aux_regAso = false;
                $aux_tabla = [];
                if(count($cvalat->clientedesbloqueadocvalats) > 0){
                    $aux_regAso = true;
                    $aux_tabla[] = "CValAt";
                }
                if($aux_regAso){
                    return response()->json([
                        'id' => 1,
                        'mensaje' => "No se puede eliminar, tiene registros asociados a la tabla: " . implode(", ", $aux_tabla) . ".",
                        'tipo_alert' => "error"
                    ]);
                }
                if (CValAt::destroy($request->id)) {
                    //dd('entro');
                    //Despues de eliminar actualizo el campo usuariodel_id=usuario que elimino el registro
                    $cvalat = CValAt::withTrashed()->findOrFail($request->id);
                    $cvalat->usuariodel_id = auth()->id();
                    $cvalat->save();
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