<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidarCodigo;
use App\Models\Codigo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CodigoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-codigo');
        //$datas = FormaPago::orderBy('id')->get();
        return view('codigo.index');
    }

    public function codigopage(){
        return datatables()
            ->eloquent(Codigo::query())
            ->toJson();
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function crear()
    {
        can('crear-codigo');
        return view('codigo.crear');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function guardar(ValidarCodigo $request)
    {
        can('guardar-codigo');
        /* Codigo::create($request->all());
        return redirect('codigo')->with('mensaje','Código creado con exito'); */
        DB::beginTransaction();
        try {
            $codigo = Codigo::create($request->only(['desc', 'usuario_id']));

            // Guardar codigodet si vienen
            if ($request->has('detalles')) {
                foreach ($request->detalles as $detalle) {
                    $codigo->codigodet()->create([
                        'descdet' => trim($detalle['descdet']),
                        'usuario_id' => auth()->id()
                    ]);
                }
            }

            DB::commit();
            return redirect('codigo')->with('mensaje','Código creado con éxito');
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
        can('editar-codigo');
        $data = Codigo::findOrFail($id);
        return view('codigo.editar', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function actualizar(ValidarCodigo $request, $id)
    /* {
        DB::beginTransaction();
        try {
            $codigo = Codigo::findOrFail($id);
            $codigo->update($request->only(['desc', 'usuario_id']));

            // Eliminar los detalles anteriores
            $codigo->codigodet()->delete();

            // Crear los nuevos detalles
            if ($request->has('detalles')) {
                foreach ($request->detalles as $detalle) {
                    $codigo->codigodet()->create([
                        'descdet' => trim($detalle['descdet']),
                        'usuario_id' => auth()->id()
                    ]);
                }
            }

            DB::commit();
            return redirect('codigo')->with('mensaje', 'Código actualizado con éxito');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('mensaje', 'Error: ' . $e->getMessage())->withInput();
        }
    } */
    /* {
        DB::beginTransaction();
        try {
            $codigo = Codigo::findOrFail($id);
            $codigo->update($request->only(['desc', 'usuario_id']));

            // Procesar detalles
            if ($request->has('detalles')) {
                $detallesIds = [];
                
                foreach ($request->detalles as $detalle) {
                    if (isset($detalle['id'])) {
                        // Actualizar detalle existente
                        $detalleModel = $codigo->codigodet()->find($detalle['id']);
                        if ($detalleModel) {
                            $detalleModel->update([
                                'descdet' => trim($detalle['descdet']),
                                'usuario_id' => auth()->id()
                            ]);
                            $detallesIds[] = $detalle['id'];
                        }
                    } else {
                        // Crear nuevo detalle
                        $newDetalle = $codigo->codigodet()->create([
                            'descdet' => trim($detalle['descdet']),
                            'usuario_id' => auth()->id()
                        ]);
                        $detallesIds[] = $newDetalle->id;
                    }
                }

                // Eliminar detalles que no están en la lista actual
                $codigo->codigodet()->whereNotIn('id', $detallesIds)->delete();
            } else {
                // Si no hay detalles, eliminar todos
                $codigo->codigodet()->delete();
            }

            DB::commit();
            return redirect('codigo')->with('mensaje', 'Código actualizado con éxito');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('mensaje', 'Error: ' . $e->getMessage())->withInput();
        }
    } */

    {
        DB::beginTransaction();
        try {
            $codigo = Codigo::findOrFail($id);
            $codigo->update($request->only(['desc', 'usuario_id']));

            // Procesar detalles
            if ($request->has('detalles')) {
                $detallesIds = [];
                $detallesActuales = $codigo->codigodet()->pluck('id')->toArray();
                
                foreach ($request->detalles as $detalle) {
                    if (isset($detalle['id'])) {
                        // Actualizar detalle existente
                        $detalleModel = $codigo->codigodet()->find($detalle['id']);
                        if ($detalleModel) {
                            $detalleModel->update([
                                'descdet' => trim($detalle['descdet']),
                                'usuario_id' => auth()->id()
                            ]);
                            $detallesIds[] = $detalle['id'];
                        }
                    } else {
                        // Crear nuevo detalle
                        $newDetalle = $codigo->codigodet()->create([
                            'descdet' => trim($detalle['descdet']),
                            'usuario_id' => auth()->id()
                        ]);
                        $detallesIds[] = $newDetalle->id;
                    }
                }

                // Identificar detalles a eliminar
                $detallesAEliminar = array_diff($detallesActuales, $detallesIds);
                
                // Validar y eliminar solo los que no tienen relaciones
                foreach ($detallesAEliminar as $detalleId) {
                    $detalle = $codigo->codigodet()->find($detalleId);
                    
                    if ($detalle->hasRelationships()) {
                        DB::rollBack();
                        return redirect()->back()
                            ->with('error', 'No se puede eliminar el detalle con ID '.$detalleId.' porque tiene registros asociados en otras tablas')
                            ->withInput();
                    }
                    
                    $detalle->delete();
                }
            } else {
                // Validar antes de eliminar todos
                foreach ($codigo->codigodet as $detalle) {
                    if ($detalle->hasRelationships()) {
                        DB::rollBack();
                        return redirect()->back()
                            ->with('error', 'No se puede eliminar algunos detalles porque tienen registros asociados en otras tablas')
                            ->withInput();
                    }
                }
                $codigo->codigodet()->delete();
            }

            DB::commit();
            return redirect('codigo')->with('mensaje', 'Código actualizado con éxito');
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
        if(can('eliminar-codigo',false)){
            if ($request->ajax()) {
                $data = Codigo::findOrFail($request->id);
                $aux_contRegistos = $data->codigodet->count();
                //dd($aux_contRegistos);
                if($aux_contRegistos > 0){
                    return response()->json(['mensaje' => 'cr']);
                }else{
                    if (Codigo::destroy($request->id)) {
                        //dd('entro');
                        //Despues de eliminar actualizo el campo usuariodel_id=usuario que elimino el registro
                        $FormaPago = Codigo::withTrashed()->findOrFail($request->id);
                        $FormaPago->usuariodel_id = auth()->id();
                        $FormaPago->save();
                        return response()->json(['mensaje' => 'ok']);
                    } else {
                        return response()->json(['mensaje' => 'ng']);
                    }    
                }
            } else {
                abort(404);
            }
    
        }else{
            return response()->json(['mensaje' => 'ne']);
        }
    }
}
