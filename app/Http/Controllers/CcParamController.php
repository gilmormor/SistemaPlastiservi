<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidarCcParam;
use App\Models\CcParam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CcParamController extends Controller
{
    public function index()
    {
        can('listar-ccparam');
        return view('ccparam.index');
    }

    public function ccparampage()
    {
        $sql = "SELECT ccparam.id,
                       ccparam.nombre,
                       ccparam.etiqueta,
                       ccparam.tipo,
                       ccparam.unidad,
                       ccparam.decimales,
                       ccparam.orden
                FROM   ccparam
                WHERE  ISNULL(ccparam.deleted_at)
                ORDER  BY ccparam.orden ASC, ccparam.id ASC";
        $datas = DB::select($sql);
        return datatables($datas)->toJson();
    }

    public function crear()
    {
        can('crear-ccparam');
        return view('ccparam.crear');
    }

    public function guardar(ValidarCcParam $request)
    {
        can('guardar-ccparam');
        $data = $request->all();
        $data['usuario_id'] = auth()->id();
        CcParam::create($data);
        return redirect('ccparam')->with('mensaje', 'Parámetro CC creado con éxito.');
    }

    public function editar($id)
    {
        can('editar-ccparam');
        $data = CcParam::findOrFail($id);
        return view('ccparam.editar', compact('data'));
    }

    public function actualizar(ValidarCcParam $request, $id)
    {
        can('actualizar-ccparam');
        $ccparam = CcParam::findOrFail($id);
        $ccparam->update($request->all());
        return redirect('ccparam')->with('mensaje', 'Parámetro CC actualizado con éxito.');
    }

    public function eliminar(Request $request, $id)
    {
        if (can('eliminar-ccparam', false)) {
            if ($request->ajax()) {
                $ccparam = CcParam::findOrFail($request->id);
                // Verificar si tiene parámetros de etapa asociados
                if ($ccparam->ccparamApsucetapaprod()->whereNull('deleted_at')->count() > 0) {
                    return response()->json([
                        'id'         => 1,
                        'mensaje'    => 'No se puede eliminar, tiene etapas de producción asociadas.',
                        'tipo_alert' => 'error'
                    ]);
                }
                if (CcParam::destroy($request->id)) {
                    $ccparam = CcParam::withTrashed()->findOrFail($request->id);
                    $ccparam->usuariodel_id = auth()->id();
                    $ccparam->save();
                    return response()->json(['mensaje' => 'ok']);
                } else {
                    return response()->json(['mensaje' => 'ng']);
                }
            } else {
                abort(404);
            }
        } else {
            return response()->json(['mensaje' => 'ne']);
        }
    }
}
