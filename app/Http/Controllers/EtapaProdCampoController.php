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
    /** Tipos permitidos */
    const TIPOS = ['number', 'text', 'calculated'];

    /* Los campos estandar mapeables los define el modelo, que es tambien
       quien declara las variables que una formula puede nombrar: una sola
       lista para validar y para mostrar la ayuda. */
    const CAMPOS_ESTANDAR = EtapaProdCampo::CAMPOS_ESTANDAR;

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

        /* Variables que se pueden nombrar en una formula de ESTA etapa: las
           del producto y los kilos, mas los otros campos ya configurados aqui.
           Se devuelven para mostrarlas como ayuda al escribir la formula. */
        $aux_variables = [];
        foreach (EtapaProdCampo::VARIABLES_CONTEXTO as $aux_nombre => $aux_desc) {
            $aux_variables[] = ['nombre' => $aux_nombre, 'desc' => $aux_desc, 'propia' => 0];
        }
        foreach ($campos as $aux_campo) {
            if ($aux_campo->tipo === 'text') {
                continue; // un texto no sirve en una cuenta
            }
            $aux_variables[] = [
                'nombre' => $aux_campo->nombre,
                'desc'   => $aux_campo->etiqueta,
                'propia' => 1,
            ];
        }

        return response()->json([
            'resp'              => 1,
            'etapa_nombre'      => $apsuc->etapaprod->nombre ?? '—',
            'apsucetapaprod_id' => $apsucetapaprod_id,
            'campos'            => $campos,
            'tipos'             => self::TIPOS,
            'campos_estandar'   => self::CAMPOS_ESTANDAR,
            'variables'         => $aux_variables,
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
            'mapea_campo'       => 'nullable|in:cantprod,kgprod,kgscrap,cantunid',
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

        /* Una formula que no se puede calcular no se guarda: el error se ve
           aca y no cuando el operario esta registrando produccion. */
        if ($request->tipo === 'calculated') {
            $aux_error = $this->validarFormula(
                $request->formula,
                $request->apsucetapaprod_id,
                $request->nombre
            );
            if ($aux_error) {
                return response()->json(['resp' => 0, 'mensaje' => $aux_error]);
            }
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
            'mapea_campo' => 'nullable|in:cantprod,kgprod,kgscrap,cantunid',
        ]);

        // Misma revision al editar: la formula pudo cambiar
        if ($request->tipo === 'calculated') {
            $aux_error = $this->validarFormula(
                $request->formula,
                $campo->apsucetapaprod_id,
                $campo->nombre,
                $campo->id
            );
            if ($aux_error) {
                return response()->json(['resp' => 0, 'mensaje' => $aux_error]);
            }
        }

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


    /**
     * Revisa que una formula se pueda calcular antes de guardarla.
     *
     * Sin esto una formula con un nombre mal escrito se guarda igual y el
     * error recien aparece cuando el operario esta registrando produccion y
     * ve el campo vacio, sin saber por que.
     *
     * Se valida que:
     *   - solo tenga numeros, nombres de variables y los operadores + - * / ( )
     *   - cada nombre exista: variable del producto, kgprod/kgscrap, u otro
     *     campo numerico o calculado de la MISMA etapa
     *   - no se nombre a si misma, ni directa ni a traves de otro campo
     *   - los parentesis cierren y la cuenta se pueda resolver
     *
     * @param  string    $aux_formula
     * @param  int       $aux_apsucetapaprod_id
     * @param  string    $aux_nombre         nombre interno del campo que se esta guardando
     * @param  int|null  $aux_campo_id       id del campo, si se esta editando
     * @return string|null  mensaje de error, o null si la formula esta bien
     */
    private function validarFormula($aux_formula, $aux_apsucetapaprod_id, $aux_nombre, $aux_campo_id = null)
    {
        $aux_formula = trim($aux_formula);

        if ($aux_formula === '') {
            return 'La formula no puede quedar vacia.';
        }

        // Lista blanca de caracteres: nada de funciones, comillas ni punto y coma
        if (preg_match('/[^a-z0-9_\s\.\+\-\*\/\(\)]/i', $aux_formula)) {
            return 'La formula solo admite numeros, nombres de campos y los signos + - * / ( ).';
        }

        if (substr_count($aux_formula, '(') !== substr_count($aux_formula, ')')) {
            return 'Los parentesis de la formula no estan balanceados.';
        }

        // Otros campos de la etapa que se pueden nombrar, y su propia formula
        $aux_otros = EtapaProdCampo::where('apsucetapaprod_id', $aux_apsucetapaprod_id)
            ->where('tipo', '!=', 'text')
            ->get(['id', 'nombre', 'tipo', 'formula'])
            ->keyBy('nombre');

        $aux_conocidas = array_keys(EtapaProdCampo::VARIABLES_CONTEXTO);

        // Nombres que aparecen en la formula
        preg_match_all('/[a-z_][a-z0-9_]*/i', $aux_formula, $aux_coincidencias);
        $aux_usadas = array_unique($aux_coincidencias[0]);

        foreach ($aux_usadas as $aux_usada) {
            if ($aux_usada === $aux_nombre) {
                return "La formula se nombra a si misma ('{$aux_usada}').";
            }
            if (in_array($aux_usada, $aux_conocidas)) {
                continue;
            }
            if (isset($aux_otros[$aux_usada]) and $aux_otros[$aux_usada]->id != $aux_campo_id) {
                continue;
            }
            return "'{$aux_usada}' no es un campo de esta etapa ni una variable conocida.";
        }

        // Ciclos: A usa B, B usa A. Se recorre lo que cada campo calculado nombra.
        $aux_ciclo = $this->buscarCiclo($aux_nombre, $aux_usadas, $aux_otros, [$aux_nombre]);
        if ($aux_ciclo) {
            return 'La formula se calcula a si misma dando la vuelta por: ' . $aux_ciclo . '.';
        }

        /* Ultima prueba: se reemplaza cada nombre por 1 y queda una cuenta de
           numeros, que debe poder resolverse. Llegado aca la expresion ya paso
           la lista blanca de caracteres y no le queda ningun nombre, asi que
           es una cuenta y nada mas. */
        $aux_prueba = preg_replace('/[a-z_][a-z0-9_]*/i', '1', $aux_formula);

        try {
            $aux_valor = eval('return (' . $aux_prueba . ');');
        } catch (\DivisionByZeroError $aux_e) {
            /* La cuenta esta bien escrita; que divida por cero depende de los
               valores del dia, no de la formula. El formulario ya deja el
               campo vacio cuando eso pasa. */
            return null;
        } catch (\Throwable $aux_e) {
            return 'La formula esta mal escrita: revise los operadores y los parentesis.';
        }

        if (!is_numeric($aux_valor)) {
            return 'La formula esta mal escrita: revise los operadores y los parentesis.';
        }

        return null;
    }

    /**
     * Sigue las formulas de los campos nombrados hasta ver si se vuelve al
     * campo de partida. Devuelve el camino encontrado, o null si no hay ciclo.
     *
     * @param  string  $aux_origen
     * @param  array   $aux_usadas
     * @param  mixed   $aux_otros
     * @param  array   $aux_camino
     * @return string|null
     */
    private function buscarCiclo($aux_origen, $aux_usadas, $aux_otros, $aux_camino)
    {
        foreach ($aux_usadas as $aux_usada) {
            if (!isset($aux_otros[$aux_usada]) or $aux_otros[$aux_usada]->tipo !== 'calculated') {
                continue;
            }
            if (in_array($aux_usada, $aux_camino)) {
                continue; // ya recorrido: no se vuelve a entrar
            }

            preg_match_all('/[a-z_][a-z0-9_]*/i', (string)$aux_otros[$aux_usada]->formula, $aux_c);
            $aux_hijas = array_unique($aux_c[0]);

            if (in_array($aux_origen, $aux_hijas)) {
                return implode(' -> ', array_merge($aux_camino, [$aux_usada, $aux_origen]));
            }

            $aux_mas = $this->buscarCiclo(
                $aux_origen,
                $aux_hijas,
                $aux_otros,
                array_merge($aux_camino, [$aux_usada])
            );
            if ($aux_mas) {
                return $aux_mas;
            }
        }

        return null;
    }
}
