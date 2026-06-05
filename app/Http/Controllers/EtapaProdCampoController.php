<?php

namespace App\Http\Controllers;

use App\Models\AreaProduccionSucEtapaProd;
use App\Models\EtapaProdCampo;
use Illuminate\Http\Request;

/**
 * CRUD AJAX para campos adicionales por etapa de producción.
 * Todas las respuestas son JSON. Se accede desde la pantalla de edición
 * de areaproduccionsucetapaprod.
 */
class EtapaProdCampoController extends Controller
{
    /** Tipos permitidos y campos estándar mapeables */
    const TIPOS             = ['number', 'text', 'calculated'];
    const CAMPOS_ESTANDAR   = ['cantprod', 'kgprod', 'kgscrap'];

    /**
     * Listado de campos de una etapa.
     * GET /etapaprodcampo/{apsucetapaprod_id}/listar
     */
    public function listar($apsucetapaprod_id)
    {
        can('editar-area-produccion-suc-etapa-prod');

        $apsuc = AreaProduccionSucEtapaProd::findOrFail($apsucetapaprod_id);

        $campos = EtapaProdCampo::where('apsucetapaprod_id', $apsucetapaprod_id)
            ->orderBy('orden')
            ->get(['id','nombre','etiqueta','tipo','formula','unidad',
                   'decimales','requerido','orden','mapea_campo']);

        return response()->json([
            'resp'              => 1,
            'etapa_nombre'      => $apsuc->etapaprod->nombre ?? '—',
            'apsucetapaprod_id' => $apsucetapaprod_id,
            'campos'            => $campos,
            'tipos'             => self::TIPOS,
            'campos_estandar'   => self::CAMPOS_ESTANDAR,
        ]);
    }

    /**
     * Crear un nuevo campo.
     * POST /etapaprodcampo
     */
    public function guardar(Request $request)
    {
        can('editar-area-produccion-suc-etapa-prod');

        $request->validate([
            'apsucetapaprod_id' => 'required|integer|exists:areaproduccionsucetapaprod,id',
            'nombre'            => 'required|string|max:50|regex:/^[a-z_][a-z0-9_]*$/',
            'etiqueta'          => 'required|string|max:100',
            'tipo'              => 'required|in:number,text,calculated',
            'formula'           => 'nullable|string|max:300',
            'unidad'            => 'nullable|string|max:20',
            'decimales'         => 'nullable|integer|min:0|max:6',
            'requerido'         => 'nullable|boolean',
            'orden'             => 'nullable|integer|min:0',
            'mapea_campo'       => 'nullable|in:cantprod,kgprod,kgscrap',
        ]);

        // Verificar nombre único dentro de la etapa
        $existe = EtapaProdCampo::where('apsucetapaprod_id', $request->apsucetapaprod_id)
            ->where('nombre', $request->nombre)
            ->whereNull('deleted_at')
            ->exists();
        if ($existe) {
            return response()->json([
                'resp'    => 0,
                'mensaje' => "Ya existe un campo con el nombre '{$request->nombre}' en esta etapa.",
            ]);
        }

        $campo = EtapaProdCampo::create([
            'apsucetapaprod_id' => $request->apsucetapaprod_id,
            'nombre'            => $request->nombre,
            'etiqueta'          => $request->etiqueta,
            'tipo'              => $request->tipo,
            'formula'           => $request->tipo === 'calculated' ? $request->formula : null,
            'unidad'            => $request->unidad,
            'decimales'         => $request->decimales ?? 2,
            'requerido'         => $request->boolean('requerido'),
            'orden'             => $request->orden ?? 0,
            'mapea_campo'       => $request->mapea_campo,
        ]);

        return response()->json([
            'resp'    => 1,
            'mensaje' => 'Campo creado correctamente.',
            'campo'   => $campo,
        ]);
    }

    /**
     * Actualizar un campo existente.
     * PUT /etapaprodcampo/{id}
     */
    public function actualizar(Request $request, $id)
    {
        can('editar-area-produccion-suc-etapa-prod');

        $campo = EtapaProdCampo::findOrFail($id);

        $request->validate([
            'etiqueta'    => 'required|string|max:100',
            'tipo'        => 'required|in:number,text,calculated',
            'formula'     => 'nullable|string|max:300',
            'unidad'      => 'nullable|string|max:20',
            'decimales'   => 'nullable|integer|min:0|max:6',
            'requerido'   => 'nullable|boolean',
            'orden'       => 'nullable|integer|min:0',
            'mapea_campo' => 'nullable|in:cantprod,kgprod,kgscrap',
        ]);

        $campo->update([
            'etiqueta'    => $request->etiqueta,
            'tipo'        => $request->tipo,
            'formula'     => $request->tipo === 'calculated' ? $request->formula : null,
            'unidad'      => $request->unidad,
            'decimales'   => $request->decimales ?? $campo->decimales,
            'requerido'   => $request->boolean('requerido'),
            'orden'       => $request->orden ?? $campo->orden,
            'mapea_campo' => $request->mapea_campo,
        ]);

        return response()->json([
            'resp'    => 1,
            'mensaje' => 'Campo actualizado correctamente.',
            'campo'   => $campo->fresh(),
        ]);
    }

    /**
     * Eliminar (soft delete) un campo.
     * DELETE /etapaprodcampo/{id}
     */
    public function eliminar($id)
    {
        can('editar-area-produccion-suc-etapa-prod');

        $campo = EtapaProdCampo::findOrFail($id);

        // Verificar que no tenga valores registrados en producción aprobada
        $tieneValores = $campo->campovalsProd()->count();
        if ($tieneValores > 0) {
            return response()->json([
                'resp'    => 0,
                'mensaje' => "No se puede eliminar: el campo tiene {$tieneValores} valor(es) registrado(s) en producción aprobada.",
            ]);
        }

        $campo->delete();

        return response()->json([
            'resp'    => 1,
            'mensaje' => 'Campo eliminado correctamente.',
        ]);
    }
}
