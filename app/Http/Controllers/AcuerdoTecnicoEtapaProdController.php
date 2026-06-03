<?php

namespace App\Http\Controllers;

use App\Models\AcuerdoTecnico;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AcuerdoTecnicoEtapaProdController extends Controller
{
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
        $sql = "SELECT acuerdotecnico.id, producto.id, producto.nombre,categoriaprod.nombre,categoriaprodsuc.sucursal_id,
                    areaproduccion.nombre AS areaproduccion_nombre, areaproduccionsuc.sucursal_id AS areaproduccionsuc_sucursal_id,
                    areaproduccionsucetapaprod.id AS areaproduccionsucetapaprod_id,
                    areaproduccionsucetapaprod.orden AS areaproduccionsucetapaprod_orden,
                    areaproduccionsucetapaprod.etapaprod_id,concat(areaproduccionsucetapaprod.orden,' ',etapaprod.nombre) AS etapaprod_nombre,
                    sucursal.nombre AS sucursal_nombre
                    FROM acuerdotecnico INNER JOIN producto
                    ON acuerdotecnico.producto_id = producto.id
                    INNER JOIN 	categoriaprod
                    ON categoriaprod.id = producto.categoriaprod_id
                    INNER JOIN categoriaprodsuc
                    ON categoriaprodsuc.categoriaprod_id = categoriaprod.id
                    INNER JOIN areaproduccion
                    ON areaproduccion.id = categoriaprod.areaproduccion_id
                    INNER JOIN areaproduccionsuc
                    ON areaproduccionsuc.areaproduccion_id = areaproduccion.id AND categoriaprodsuc.sucursal_id = areaproduccionsuc.sucursal_id
                    INNER JOIN areaproduccionsucetapaprod
                    ON areaproduccionsucetapaprod.areaproduccionsuc_id = areaproduccionsuc.id
                    INNER JOIN etapaprod
                    ON etapaprod.id = areaproduccionsucetapaprod.etapaprod_id
                    INNER JOIN sucursal
                    ON sucursal.id = areaproduccionsuc.sucursal_id
                    WHERE acuerdotecnico.id = $id
                    ORDER BY areaproduccionsucetapaprod.orden;";
        $tablas['etapaprods'] = DB::select($sql);
        //dd($tablas['etapas']);
        //
        //dd($data->producto->categoriaprod->sucursales->pluck('id')->toArray());


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
        //EtapaProd::findOrFail($id)->update($request->all());
        $acuerdotecnico = AcuerdoTecnico::findOrFail($id);
        $acuerdotecnico->apsucetapaprods()->sync($request->apsucetapaprod_id);
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

        // Misma consulta que editar(): todas las etapas disponibles para el producto
        $sql = "SELECT acuerdotecnico.id,
                    areaproduccionsucetapaprod.id   AS areaproduccionsucetapaprod_id,
                    areaproduccionsucetapaprod.orden AS areaproduccionsucetapaprod_orden,
                    areaproduccionsucetapaprod.etapaprod_id,
                    CONCAT(areaproduccionsucetapaprod.orden, ' ', etapaprod.nombre) AS etapaprod_nombre,
                    sucursal.nombre AS sucursal_nombre
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
                WHERE acuerdotecnico.id = $id
                ORDER BY areaproduccionsucetapaprod.orden";

        $etapaprods = DB::select($sql);

        // IDs de las etapas que el AT ya tiene asignadas (para pre-marcar checkboxes)
        $seleccionadas = $data->apsucetapaprods->pluck('id')->toArray();

        return response()->json([
            'at_id'           => $data->id,
            'nombre_producto' => $data->nombre_producto ?? '',
            'producto_id'     => $data->producto_id,
            'etapaprods'      => $etapaprods,
            'seleccionadas'   => $seleccionadas,
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
        // sync acepta array vacío: si no se seleccionó ninguna etapa, las elimina todas
        $acuerdotecnico->apsucetapaprods()->sync($request->apsucetapaprod_id ?? []);

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
