<?php

namespace App\Http\Controllers;

use App\Models\EstadisticaVenta;
use App\Models\EstadisticaVentaGI;
use App\Models\UnidadMedida;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EstadisticaVentaGIController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-guia-interna');

        //$datas = datatables($datas)->toJson();
        return view('estadisticaventagi.index');
    }

    //where('tipofac',2)->
    public function estadisticaventagipage(){
        /*
        $prueba = datatables()
        ->eloquent(EstadisticaVenta::where('tipofac',2)->query())
        ->toJson();
        dd($prueba);*/
        return datatables()
            ->eloquent(EstadisticaVentaGI::query()->where('tipofact',2))
            ->toJson();
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function crear()
    {
        can('crear-guia-interna');
        $unidadmedidas = UnidadMedida::orderBy('id')->pluck('descripcion', 'id')->toArray();
        $descprods = EstadisticaVenta::select('descripcion')
                        ->groupBy('descripcion')
                        ->get();
        $matprimdescs = EstadisticaVenta::select('matprimdesc')
                        ->groupBy('matprimdesc')
                        ->get();

                        //dd($descripprod);
        $aux_sta=1;
        return view('estadisticaventagi.crear',compact('unidadmedidas','aux_sta','descprods','matprimdescs'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function guardar(Request $request)
    {
        can('guardar-guia-interna');
        $aux_fechadocumento= DateTime::createFromFormat('d/m/Y', $request->fechadocumento)->format('Y-m-d');
        $request->request->add(['fechadocumento' => $aux_fechadocumento]);
        $request->request->add(['sucursal_id' => 1]);
        $request->request->add(['tipofact' => 2]);
        $request->request->add(['tipodocumento' => 'GINT']);
        //dd($request);
        EstadisticaVentaGI::create($request->all());
        return redirect('estadisticaventagi')->with('mensaje','Color creado con exito');
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
        can('editar-guia-interna');
        $data = EstadisticaVentaGI::findOrFail($id);
        $data->fechadocumento = $newDate = date("d/m/Y", strtotime($data->fechadocumento));

        $unidadmedidas = UnidadMedida::orderBy('id')->pluck('descripcion', 'id')->toArray();
        $descprods = EstadisticaVenta::select('descripcion')
                        ->groupBy('descripcion')
                        ->get();
        $matprimdescs = EstadisticaVenta::select('matprimdesc')
                        ->groupBy('matprimdesc')
                        ->get();
        $aux_sta=2;
        return view('estadisticaventagi.editar', compact('data','unidadmedidas','aux_sta','descprods','matprimdescs'));
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
        $fechadocumento= DateTime::createFromFormat('d/m/Y', $request->fechadocumento)->format('Y-m-d');
        $request->request->add(['fechadocumento' => $fechadocumento]);
        EstadisticaVentaGI::findOrFail($id)->update($request->all());
        

        return redirect('estadisticaventagi')->with('mensaje','Guia Interna actualizada con exito.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function reporte(Request $request){
        $respuesta = array();
		$respuesta['exito'] = false;
		$respuesta['mensaje'] = "Código no Existe";
		$respuesta['tabla'] = "";

        if($request->ajax()){
            $datas = consulta($request);
            //dd($datas);
            $respuesta['tabla'] .= "<table id='tabla-data' name='tabla-data' class='table display AllDataTables table-hover table-condensed tablascons' data-page-length='10'>
			<thead>
				<tr>
					<th>Dia</th>
					<th>Docum</th>
                    <th>Razón Social</th>
                    <th>Producto</th>
                    <th>Medidas</th>
                    <th>Materia<br>Prima</th>
                    <th style='text-align:right'>Kilos</th>
                    <th style='text-align:right'>Contenedor</th>
                    <th style='text-align:right'>Unidades</th>
                    <th style='text-align:right'>Valor<br>Unitario</th>
                    <th style='text-align:right'>Valor<br>Total</th>
                </tr>
			</thead>
            <tbody>";

            $i = 0;
            $aux_totalsubtotal = 0;
            $aux_totalkilos = 0;
            foreach ($datas as $data) {
                $contenedor = 0;
                $unidades = 0;
                if($data->unidadmedida_id == 8){
                    $contenedor = $data->unidades;
                }else{
                    $unidades = $data->unidades;
                }
                $respuesta['tabla'] .= "
                <tr id='fila$i' name='fila$i'>
                    <td>" . date('d', strtotime($data->fechadocumento)) . "</td>
                    <td>$data->numerodocumento</td>
                    <td>".substr($data->razonsocial,0,20)."</td>
                    <td>$data->descripcion</td>
                    <td>$data->medidas</td>
                    <td>$data->matprimdesc</td>
                    <td style='text-align:right'>".number_format($data->kilos, 2, ",", ".") ."</td>
                    <td style='text-align:right'>".number_format($contenedor, 0, ",", ".") ."</td>
                    <td style='text-align:right'>".number_format($unidades , 0, ",", ".") ."</td>
                    <td style='text-align:right'>".number_format($data->valorcosto, 2, ",", ".") ."</td>
                    <td style='text-align:right'>".number_format($data->subtotal, 0, ",", ".") ."</td>
                </tr>";
                $i++;
                $aux_totalsubtotal += $data->subtotal;
                $aux_totalkilos += $data->kilos;
            }
            $aux_tabla = "
            </tbody>
            <tfoot>
                <tr>
                    <th colspan='6' style='text-align:right'>TOTALES</th>
                    <th style='text-align:right'>". number_format($aux_totalkilos, 2, ",", ".") ."</th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th style='text-align:right'>". number_format($aux_totalsubtotal, 0, ",", ".") ."</th>
                </tr>
            </tfoot>

            </table>";
            $respuesta['tabla'] .= $aux_tabla;

            return $respuesta;
        }
    }
}


function consulta($request){
    if(empty($request->fechad) or empty($request->fechah)){
        $aux_condFecha = " true";
    }else{
        $fecha = date_create_from_format('d/m/Y', $request->fechad);
        $fechad = date_format($fecha, 'Y-m-d')." 00:00:00";
        $fecha = date_create_from_format('d/m/Y', $request->fechah);
        $fechah = date_format($fecha, 'Y-m-d')." 23:59:59";
        $aux_condFecha = "estadisticaventaGI.fechadocumento>='$fechad' and estadisticaventaGI.fechadocumento<='$fechah'";
    }
    if(empty($request->producto)){
        $aux_condproducto = " true";
    }else{
        $aux_condproducto = "estadisticaventaGI.producto='$request->producto'";
    }
    if(empty($request->matprimdesc)){
        $aux_condmatprimdesc = " true";
    }else{
        $aux_condmatprimdesc = "estadisticaventaGI.matprimdesc='$request->matprimdesc'";
    }

    $sql = "SELECT *
            FROM estadisticaventaGI
            WHERE $aux_condFecha
            and $aux_condproducto
            and $aux_condmatprimdesc;";

    $datas = DB::select($sql);
    return $datas; 
}