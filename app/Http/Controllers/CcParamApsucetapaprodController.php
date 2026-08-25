<?php

namespace App\Http\Controllers;

use App\Models\CcParam;
use App\Models\CcParamApsucetapaprod;
use App\Models\AreaProduccionSucEtapaProd;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CcParamApsucetapaprodController extends Controller
{
    /**
     * Lista los parámetros CC configurados para una etapa (apsucetapaprod_id).
     * Devuelve JSON para el modal AJAX.
     */
    public function listar($apsucetapaprod_id)
    {
        can('listar-ccparam-apsucetapaprod');
        $params = DB::select("
            SELECT
                cap.id,
                cap.ccparam_id,
                cp.etiqueta,
                cp.nombre,
                cp.tipo,
                cp.unidad,
                cp.decimales,
                cap.valor_min,
                cap.valor_max,
                cap.at_campo,
                cap.requerido,
                cap.orden
            FROM   ccparam_apsucetapaprod cap
            INNER  JOIN ccparam cp ON cp.id = cap.ccparam_id
            WHERE  cap.apsucetapaprod_id = ?
              AND  ISNULL(cap.deleted_at)
              AND  ISNULL(cp.deleted_at)
            ORDER  BY cap.orden ASC, cap.id ASC
        ", [$apsucetapaprod_id]);

        return response()->json(['params' => $params]);
    }

    /**
     * Crea un nuevo parámetro CC para una etapa.
     */
    public function guardar(Request $request)
    {
        can('guardar-ccparam-apsucetapaprod');

        $request->validate([
            'apsucetapaprod_id' => 'required|integer|exists:areaproduccionsucetapaprod,id',
            'ccparam_id'        => 'required|integer|exists:ccparam,id',
            'orden'             => 'required|integer|min:0',
            'requerido'         => 'required|boolean',
            'valor_min'         => 'nullable|numeric',
            'valor_max'         => 'nullable|numeric',
            'at_campo'          => 'nullable|in:at_espesor,at_ancho,at_largo,at_fuelle',
        ]);

        // Evitar duplicado: mismo parámetro en la misma etapa
        $existe = CcParamApsucetapaprod::where('apsucetapaprod_id', $request->apsucetapaprod_id)
            ->where('ccparam_id', $request->ccparam_id)
            ->whereNull('deleted_at')
            ->first();

        if ($existe) {
            return response()->json([
                'id'         => 0,
                'mensaje'    => 'Este parámetro ya está configurado para esta etapa.',
                'tipo_alert' => 'error'
            ]);
        }

        $cap = CcParamApsucetapaprod::create([
            'apsucetapaprod_id' => $request->apsucetapaprod_id,
            'ccparam_id'        => $request->ccparam_id,
            'valor_min'         => $request->valor_min,
            'valor_max'         => $request->valor_max,
            // Cuando el rango sale del acuerdo tecnico, el min/max fijo no se usa.
            'at_campo'          => $request->at_campo ?: null,
            'requerido'         => $request->requerido,
            'orden'             => $request->orden,
        ]);

        return response()->json([
            'id'         => $cap->id,
            'mensaje'    => 'Parámetro CC guardado con éxito.',
            'tipo_alert' => 'success'
        ]);
    }

    /**
     * Actualiza un parámetro CC de etapa.
     */
    public function actualizar(Request $request, $id)
    {
        can('actualizar-ccparam-apsucetapaprod');

        $request->validate([
            'valor_min' => 'nullable|numeric',
            'valor_max' => 'nullable|numeric',
            'at_campo'  => 'nullable|in:at_espesor,at_ancho,at_largo,at_fuelle',
            'requerido' => 'required|boolean',
            'orden'     => 'required|integer|min:0',
        ]);

        $cap = CcParamApsucetapaprod::findOrFail($id);
        $cap->update([
            'valor_min' => $request->valor_min,
            'valor_max' => $request->valor_max,
            'at_campo'  => $request->at_campo ?: null,
            'requerido' => $request->requerido,
            'orden'     => $request->orden,
        ]);

        return response()->json([
            'id'         => $cap->id,
            'mensaje'    => 'Parámetro CC actualizado con éxito.',
            'tipo_alert' => 'success'
        ]);
    }

    /**
     * Elimina (soft delete) un parámetro CC de etapa.
     */
    public function eliminar(Request $request, $id)
    {
        if (can('eliminar-ccparam-apsucetapaprod', false)) {
            if ($request->ajax()) {
                $cap = CcParamApsucetapaprod::findOrFail($id);
                if (CcParamApsucetapaprod::destroy($id)) {
                    $cap = CcParamApsucetapaprod::withTrashed()->findOrFail($id);
                    $cap->usuariodel_id = auth()->id();
                    $cap->save();
                    return response()->json(['mensaje' => 'ok']);
                }
                return response()->json(['mensaje' => 'ng']);
            }
            abort(404);
        }
        return response()->json(['mensaje' => 'ne']);
    }

    /**
     * Devuelve el listado de parámetros CC disponibles (no asignados aún a la etapa).
     * Usado para el select del modal.
     */
    public function listarDisponibles($apsucetapaprod_id)
    {
        can('listar-ccparam-apsucetapaprod');
        $params = DB::select("
            SELECT cp.id, cp.etiqueta, cp.nombre, cp.tipo, cp.unidad
            FROM   ccparam cp
            WHERE  ISNULL(cp.deleted_at)
            ORDER  BY cp.orden ASC, cp.etiqueta ASC
        ");
        return response()->json(['params' => $params]);
    }
}
