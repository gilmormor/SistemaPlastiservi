<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidarTipoTraslado;
use App\Models\TipoTraslado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TipoTrasladoController extends Controller
{
    public function index()
    {
        can('listar-tipotraslado');
        return view('tipotraslado.index');
    }

    public function tipoTrasladoPage()
    {
        return datatables()
            ->eloquent(TipoTraslado::query())
            ->toJson();
    }

    public function crear()
    {
        can('crear-tipotraslado');
        return view('tipotraslado.crear');
    }

    public function guardar(ValidarTipoTraslado $request)
    {
        can('guardar-tipotraslado');
        DB::beginTransaction();
        try {
            TipoTraslado::create($request->all());
            DB::commit();
            return redirect('tipotraslado')->with('mensaje', 'Tipo de Traslado creado con éxito');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('mensaje', 'Error: ' . $e->getMessage())->withInput();
        }
    }

    public function editar($id)
    {
        can('editar-tipotraslado');
        $data = TipoTraslado::findOrFail($id);
        return view('tipotraslado.editar', compact('data'));
    }

    public function actualizar(ValidarTipoTraslado $request, $id)
    {
        can('editar-tipotraslado');
        $original = TipoTraslado::findOrFail($id);
        DB::beginTransaction();
        try {
            $original->update($request->all());
            $actualizado = TipoTraslado::findOrFail($id);
            guardarLogCambioModelo($actualizado, [], $original);
            DB::commit();
            return redirect('tipotraslado')->with('mensaje', 'Tipo de Traslado actualizado con éxito');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('mensaje', 'Error: ' . $e->getMessage())->withInput();
        }
    }

    public function eliminar(Request $request, $id)
    {
        if (can('eliminar-tipotraslado', false)) {
            DB::beginTransaction();
            try {
                if ($request->ajax()) {
                    $tipotraslado = TipoTraslado::findOrFail($request->id);
                    $aux_regAso = false;
                    $aux_tabla  = [];
                    if ($tipotraslado->dtetrans()->count() > 0) {
                        $aux_regAso = true;
                        $aux_tabla[] = 'DteTrans';
                    }
                    if ($aux_regAso) {
                        return response()->json([
                            'id'        => 1,
                            'mensaje'   => 'No se puede eliminar, tiene registros asociados a la tabla: ' . implode(', ', $aux_tabla) . '.',
                            'tipo_alert' => 'error',
                        ]);
                    }
                    $tipotraslado->delete();
                } else {
                    abort(404);
                }
                DB::commit();
                return response()->json(['mensaje' => 'ok']);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json(['mensaje' => 'Error: ' . $e->getMessage()]);
            }
        } else {
            return response()->json(['mensaje' => 'ne']);
        }
    }
}
