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

}
