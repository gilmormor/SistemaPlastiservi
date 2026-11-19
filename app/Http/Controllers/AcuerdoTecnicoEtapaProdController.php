<?php

namespace App\Http\Controllers;

use App\Models\AcuerdoTecnico;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AcuerdoTecnicoEtapaProdController extends Controller
{
    /**
     * Etapas disponibles para el producto del acuerdo tecnico, con la unidad de
     * salida y la de conteo que ya tenga configuradas.
     *
     * La usan la pantalla de edicion y el modal de otitemprogramacion, para que
     * las dos muestren lo mismo.
     */
    private function etapasDisponiblesAt($id)
    {
        $sql = "SELECT acuerdotecnico.id,
                    areaproduccionsucetapaprod.id   AS areaproduccionsucetapaprod_id,
                    areaproduccionsucetapaprod.orden AS areaproduccionsucetapaprod_orden,
                    areaproduccionsucetapaprod.etapaprod_id,
                    CONCAT(areaproduccionsucetapaprod.orden, ' ', etapaprod.nombre) AS etapaprod_nombre,
                    sucursal.nombre AS sucursal_nombre,
                    areaproduccionsucetapaprod.unidadmedida_id AS unidad_etapa_id,
                    atep.unidadmedidasal_id AS unidadmedidasal_id,
                    atep.unidadmedidacant_id AS unidadmedidacant_id
                FROM acuerdotecnico
                INNER JOIN producto              ON producto.id = acuerdotecnico.producto_id
                INNER JOIN categoriaprod         ON categoriaprod.id = producto.categoriaprod_id
                INNER JOIN categoriaprodsuc      ON categoriaprodsuc.categoriaprod_id = categoriaprod.id
                INNER JOIN areaproduccion        ON areaproduccion.id = categoriaprod.areaproduccion_id
                INNER JOIN areaproduccionsuc     ON areaproduccionsuc.areaproduccion_id = areaproduccion.id
                                                AND categoriaprodsuc.sucursal_id = areaproduccionsuc.sucursal_id
                INNER JOIN areaproduccionsucetapaprod
                                                 ON areaproduccionsucetapaprod.areaproduccionsuc_id = areaproduccionsuc.id
                INNER JOIN etapaprod             ON etapaprod.id = areaproduccionsucetapaprod.etapaprod_id
                INNER JOIN sucursal              ON sucursal.id = areaproduccionsuc.sucursal_id
                LEFT  JOIN acuerdotecnicoapsucetapaprod atep
                                                 ON atep.apsucetapaprod_id = areaproduccionsucetapaprod.id
                                                AND atep.acuerdotecnico_id = acuerdotecnico.id
                WHERE acuerdotecnico.id = ?
                ORDER BY areaproduccionsucetapaprod.orden";

        return DB::select($sql, [$id]);
    }

    /**
     * Unidades que se pueden elegir como unidad de salida, mas el id de Kgs.
     *
     * Solo las unidades marcadas para produccion: las dimensionales -pulgadas,
     * milimetros- miden el producto y no sirven como unidad de inventario. El id
     * de kilos se manda aparte para que la vista aplique la regla de "contar
     * tambien en" sin tenerlo escrito a mano.
     */
    private function catalogoUnidadesProd()
    {
        $aux_unidades = DB::select("SELECT id, nombre, descripcion
                                      FROM unidadmedida
                                     WHERE usa_produccion = 1
                                       AND ISNULL(deleted_at)
                                     ORDER BY nombre");

        $aux_kg = DB::selectOne("SELECT id FROM unidadmedida
                                  WHERE nombre = 'Kgs' AND ISNULL(deleted_at)
                                  LIMIT 1");

        return [
            'unidades'     => $aux_unidades,
            'unidad_kg_id' => $aux_kg ? $aux_kg->id : null,
        ];
    }

    /**
     * Arma el arreglo que espera sync() con los datos de la tabla pivote.
     *
     * Llega una unidad de salida por etapa: la unidad en que el producto sale de
     * esa etapa, que es propia del producto y no de la etapa. Opcionalmente la
     * unidad en que hay que contar ademas de la cantidad, para los productos que
     * se venden en kilos pero igual interesa saber cuantas unidades salieron.
     *
     * Devuelve ['sync' => array, 'faltan' => cuantas etapas quedaron sin unidad],
     * para que quien llame decida como avisar (JSON o redirect).
     */
    private function armarSyncEtapas(Request $request)
    {
        $aux_etapas     = $request->apsucetapaprod_id ?? [];
        $aux_unidades   = $request->unidadmedidasal_id ?? [];
        $aux_cantidades = $request->unidadmedidacant_id ?? [];

        $aux_sync = [];
        $aux_faltan = 0;

        foreach ($aux_etapas as $aux_etapa_id) {
            $aux_unidad = isset($aux_unidades[$aux_etapa_id]) ? $aux_unidades[$aux_etapa_id] : null;
            if (empty($aux_unidad)) { $aux_faltan++; continue; }

            $aux_cant = isset($aux_cantidades[$aux_etapa_id]) ? $aux_cantidades[$aux_etapa_id] : null;

            $aux_sync[$aux_etapa_id] = [
                'unidadmedidasal_id'  => $aux_unidad,
                'unidadmedidacant_id' => empty($aux_cant) ? null : $aux_cant,
            ];
        }

        return ['sync' => $aux_sync, 'faltan' => $aux_faltan];
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-acuerdo-tecnico-etapa-de-produccion');
        //$datas = FormaPago::orderBy('id')->get();
        return view('acuerdotecnicoetapaprod.index');
    }

    public function acuerdotecnicoetapaprodpage(){
        /* $sql = "SELECT acuerdotecnico.id,acuerdotecnico.producto_id,acuerdotecnico.at_desc
                from acuerdotecnico
                where isnull(acuerdotecnico.deleted_at);";
        $datas = DB::select($sql); */
        //return datatables($datas)->toJson();
        $acuerdos = AcuerdoTecnico::with('producto')->whereNull('deleted_at')->get();
        return datatables($acuerdos)->toJson();
    }



    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function crear()
    {
        can('crear-acuerdo-tecnico-etapa-de-produccion');
        return view('acuerdotecnicoetapaprod.crear');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function editar($id)
    {
        can('editar-acuerdo-tecnico-etapa-de-produccion');
        $data = AcuerdoTecnico::findOrFail($id);

        /* Antes esta pantalla solo elegia las etapas, y la unidad en que el
           producto sale de cada una quedaba sin definir: la produccion caia
           entonces a la unidad de la etapa (ver AreaProduccionSucEtapaProd::
           umProducto), que no siempre es la correcta para el producto. Ahora
           se piden las dos cosas, igual que en el modal de otitemprogramacion. */
        $tablas['etapaprods'] = $this->etapasDisponiblesAt($id);
        $tablas = array_merge($tablas, $this->catalogoUnidadesProd());

        // Etapas que el AT ya tiene asignadas, para marcar las casillas
        $tablas['seleccionadas'] = $data->apsucetapaprods->pluck('id')->toArray();

        return view('acuerdotecnicoetapaprod.editar', compact('data','tablas'));
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function actualizar(Request $request, $id)
    {
        can('guardar-acuerdo-tecnico-etapa-de-produccion');
        $acuerdotecnico = AcuerdoTecnico::findOrFail($id);

        /* Antes era un sync() con los ids pelados: las etapas nuevas quedaban sin
           unidad de salida y la produccion caia a la unidad de la etapa. Ahora se
           guarda la unidad de cada etapa, con la misma logica que usa el modal de
           otitemprogramacion (armarSyncEtapas). */
        $aux_datos = $this->armarSyncEtapas($request);

        /* Sin unidad no se guarda: un movimiento de inventario con la unidad
           equivocada es peor que no poder guardar todavia. Se vuelve al
           formulario para no perder lo que el usuario ya marco. */
        if ($aux_datos['faltan'] > 0) {
            return redirect()->back()->withInput()->with([
                'mensaje'    => 'Falta indicar la unidad de salida en ' . $aux_datos['faltan'] . ' etapa(s).',
                'tipo_alert' => 'alert-error'
            ]);
        }

        // sync acepta arreglo vacio: si no se marco ninguna etapa, las quita todas
        $acuerdotecnico->apsucetapaprods()->sync($aux_datos['sync']);

        return redirect('acuerdotecnicoetapaprod')->with('mensaje','Etapas de produccion actualizado con exito');
    }

    /**
     * modalData — devuelve JSON con las etapas disponibles para el AT y cuáles
     * están ya seleccionadas. Usado por el modal de edición en otitemprogramacion.
     *
     * GET /acuerdotecnicoetapaprod/{id}/modal-data
     */
    public function modalData($id)
    {
        can('editar-acuerdo-tecnico-etapa-de-produccion');

        $data = AcuerdoTecnico::findOrFail($id);

        /* Las etapas, las unidades y el id de kilos salen de los mismos helpers
           que usa la pantalla acuerdotecnicoetapaprod/{id}/editar: antes la
           consulta estaba escrita dos veces y podian quedar distintas. */
        $etapaprods = $this->etapasDisponiblesAt($id);
        $aux_cat    = $this->catalogoUnidadesProd();

        // IDs de las etapas que el AT ya tiene asignadas (para pre-marcar checkboxes)
        $seleccionadas = $data->apsucetapaprods->pluck('id')->toArray();

        return response()->json([
            'at_id'           => $data->id,
            'nombre_producto' => $data->nombre_producto ?? '',
            'producto_id'     => $data->producto_id,
            'etapaprods'      => $etapaprods,
            'seleccionadas'   => $seleccionadas,
            'unidades'        => $aux_cat['unidades'],
            'unidad_kg_id'    => $aux_cat['unidad_kg_id'],
        ]);
    }
    /**
     * actualizarAjax — sincroniza las etapas del AT y devuelve JSON.
     * Usado por el modal de edición en otitemprogramacion (no hace redirect).
     *
     * POST /acuerdotecnicoetapaprod/{id}/actualizar-ajax
     *
     * Respuesta JSON:
     *   resp=1  → éxito, incluye detetapaprod_array actualizado para refrescar la fila
     *   resp=0  → error
     */
    public function actualizarAjax(Request $request, $id)
    {
        can('guardar-acuerdo-tecnico-etapa-de-produccion');

        $acuerdotecnico = AcuerdoTecnico::findOrFail($id);

        /* Mismo armado que la pantalla de edicion (armarSyncEtapas): unidad de
           salida por etapa, y opcionalmente la unidad en que contar aparte. */
        $aux_datos = $this->armarSyncEtapas($request);

        /* Sin unidad no se guarda: un movimiento de inventario con la unidad
           equivocada es peor que no poder guardar todavia. */
        if ($aux_datos['faltan'] > 0) {
            return response()->json([
                'resp'    => 0,
                'mensaje' => 'Falta indicar la unidad de salida en ' . $aux_datos['faltan'] . ' etapa(s).',
            ]);
        }

        // sync acepta array vacío: si no se seleccionó ninguna etapa, las elimina todas
        $acuerdotecnico->apsucetapaprods()->sync($aux_datos['sync']);

        // Reconstruir el detetapaprod_array con el mismo formato que usa el DataTable:
        // "apsucetapaprod_id|etapaprod_id|etapaprod_nombre;..."
        $sql = "SELECT GROUP_CONCAT(
                    CONCAT_WS('|',
                        areaproduccionsucetapaprod.id,
                        etapaprod.id,
                        etapaprod.nombre
                    )
                    ORDER BY areaproduccionsucetapaprod.orden ASC
                    SEPARATOR ';'
                ) AS detetapaprod_array
                FROM acuerdotecnicoapsucetapaprod
                INNER JOIN areaproduccionsucetapaprod
                    ON areaproduccionsucetapaprod.id = acuerdotecnicoapsucetapaprod.apsucetapaprod_id
                INNER JOIN etapaprod
                    ON etapaprod.id = areaproduccionsucetapaprod.etapaprod_id
                WHERE acuerdotecnicoapsucetapaprod.acuerdotecnico_id = $id";

        $result = DB::selectOne($sql);

        return response()->json([
            'resp'                => 1,
            'mensaje'             => 'Etapas de producción actualizadas.',
            'detetapaprod_array'  => $result->detetapaprod_array ?? '',
        ]);
    }

}
