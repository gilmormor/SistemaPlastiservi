<?php

namespace App\Http\Controllers;

use App\Models\AreaProduccion;
use App\Models\Empresa;
use App\Models\Giro;
use App\Models\Seguridad\Usuario;
use App\Models\TipoEntrega;
use App\Models\Vendedor;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EstadisticaVentaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('consulta-estadistica-venta');
        $user = Usuario::findOrFail(auth()->id());

        $fechaServ = [
                    'fecha1erDiaMes' => date("01/m/Y"),
                    'fechaAct' => date("d/m/Y"),
                    ];

        $sql = "SELECT matprimdesc
        FROM estadisticaventa
        GROUP BY matprimdesc;";
        $materiaprimas = DB::select($sql);

        $sql = "SELECT producto,descripcion
        FROM estadisticaventa
        GROUP BY producto,descripcion;";
        $productos = DB::select($sql);
        return view('estadisticaventa.index', compact('materiaprimas','fechaServ','productos'));

        
    }

    public function reporte(Request $request){
        $respuesta = array();
		$respuesta['exito'] = false;
		$respuesta['mensaje'] = "Código no Existe";
		$respuesta['tabla'] = "";
        $respuesta['tablaT'] = "";

        if($request->ajax()){
            $datas = consulta($request);
            //dd($datas);
            $respuesta['tabla'] .= "<table id='tabla-data' name='tabla-data' class='table display AllDataTables table-hover table-condensed tablascons' data-page-length='50'>
			<thead>
				<tr>
					<th>Dia</th>
					<th>Docum</th>
                    <th>Razón Social</th>
                    <th>Producto</th>
                    <th>Medidas</th>
                    <th>Materia<br>Prima</th>
                    <th>Unid</th>
                    <th style='text-align:right'>Valor<br>Neto</th>
                    <th style='text-align:right'>Kilos</th>
                    <th style='text-align:right'>Conver<br>Kilos</th>
                    <th style='text-align:right'>Difer<br>Kilos</th>
                    <th style='text-align:right'>Precio<br>Kilo</th>
                    <th style='text-align:right'>Precio<br>Costo</th>
                    <th style='text-align:right'>Difer<br>Precio</th>
                    <th style='text-align:right'>Difer<br>Val</th>
                </tr>
			</thead>
            <tbody>";

            $i = 0;
            $aux_totalsubtotal = 0;
            $aux_totalkilos = 0;
            $aux_totaldiferenciakilos = 0;
            $aux_totalvalorcosto = 0;
            $aux_totaldiferenciaval = 0;
            foreach ($datas as $data) {
                $respuesta['tabla'] .= "
                <tr id='fila$i' name='fila$i'>
                    <td>" . date('d', strtotime($data->fechadocumento)) . "</td>
                    <td>$data->numerodocumento</td>
                    <td>$data->razonsocial</td>
                    <td>$data->descripcion</td>
                    <td>$data->medidas</td>
                    <td>$data->matprimdesc</td>
                    <td style='text-align:right'>".number_format($data->unidades, 0, ",", ".") ."</td>
                    <td style='text-align:right'>".number_format($data->subtotal, 0, ",", ".") ."</td>
                    <td style='text-align:right'>".number_format($data->kilos, 2, ",", ".") ."</td>
                    <td style='text-align:right'>".number_format($data->conversionkilos, 2, ",", ".") ."</td>
                    <td style='text-align:right'>".number_format($data->diferenciakilos, 2, ",", ".") ."</td>
                    <td style='text-align:right'>".number_format($data->precioxkilo, 2, ",", ".") ."</td>
                    <td style='text-align:right'>".number_format($data->valorcosto, 2, ",", ".") ."</td>
                    <td style='text-align:right'>".number_format($data->diferenciaprecio, 2, ",", ".") ."</td>
                    <td style='text-align:right'>".number_format($data->diferenciaval, 0, ",", ".") ."</td>
                </tr>";
                $i++;
                $aux_totalsubtotal += $data->subtotal;
                $aux_totalkilos += $data->kilos;
                $aux_totaldiferenciakilos += $data->diferenciakilos;
                $aux_totalvalorcosto += $data->valorcosto;
            }
            $aux_promprecioxkilo = round($aux_totalsubtotal / $aux_totalkilos,2);
            $aux_promvalorcosto = round($aux_totalvalorcosto / $i ,2);
            $aux_diferenciaprecio = $aux_promprecioxkilo - $aux_promvalorcosto;
            $aux_totaldiferenciaval = $aux_totalkilos * $aux_diferenciaprecio;
            $respuesta['tabla'] .= "
            </tbody>
            <tfoot>
                <tr>
                    <th colspan='7' style='text-align:right'>TOTALES</th>
                    <th style='text-align:right'>". number_format($aux_totalsubtotal, 0, ",", ".") ."</th>
                    <th style='text-align:right'>". number_format($aux_totalkilos, 2, ",", ".") ."</th>
                    <th></th>
                    <th style='text-align:right'>". number_format($aux_totaldiferenciakilos, 2, ",", ".") ."</th>
                    <th style='text-align:right'>". number_format($aux_promprecioxkilo, 2, ",", ".") ."</th>
                    <th style='text-align:right'>". number_format($aux_promvalorcosto, 2, ",", ".") ."</th>
                    <th style='text-align:right'>". number_format($aux_diferenciaprecio, 2, ",", ".") ."</th>
                    <th style='text-align:right'>". number_format($aux_totaldiferenciaval, 0, ",", ".") ."</th>
                </tr>
            </tfoot>

            </table>";

            //TOTALES Y GRAFICO
            $datas = consultaTgrafico($request);
            $respuesta['tablaT'] .= "<table id='tablacotizacion' name='tablacotizacion' class='table display AllDataTables table-hover table-condensed tablascons' data-page-length='50'>
            <thead>
                <tr>
                    <th>Materia Prima</th>
                    <th style='text-align:right'>Valor<br>Neto</th>
                    <th style='text-align:right'>Kilos<br>Entreg</th>
                    <th style='text-align:right'>Precio<br>Kilo</th>
                    <th style='text-align:right'>Preci<br>Costo</th>
                    <th style='text-align:right'>Dif<br>Precio</th>
                    <th style='text-align:right'>Dif<br>Valorizada</th>
                </tr>
            </thead>
            <tbody>";
            $i = 0;
            $aux_totalsubtotal = 0;
            $aux_totalkilos = 0;
            $aux_totalprecioxkilo = 0;
            $aux_totalvalorcosto = 0;
            $aux_totaldifprec = 0;
            $aux_totaldifval = 0;
            
            foreach ($datas as $data) {
                $respuesta['tablaT'] .= "
                <tr id='fila$i' name='fila$i'>
                    <td>$data->matprimdesc</td>
                    <td style='text-align:right'>".number_format($data->subtotal, 0, ",", ".") ."</td>
                    <td style='text-align:right'>".number_format($data->kilos, 0, ",", ".") ."</td>
                    <td style='text-align:right'>".number_format($data->precioxkilo, 2, ",", ".") ."</td>
                    <td style='text-align:right'>".number_format($data->valorcosto, 0, ",", ".") ."</td>
                    <td style='text-align:right'>".number_format($data->difprec, 2, ",", ".") ."</td>
                    <td style='text-align:right'>".number_format($data->difval, 0, ",", ".") ."</td>
                </tr>";
                $i++;
                $aux_totalsubtotal += $data->subtotal;
                $aux_totalkilos += $data->kilos;
                $aux_totalprecioxkilo += $data->precioxkilo;
                $aux_totalvalorcosto += $data->valorcosto;
                $aux_totaldifprec += $data->difprec;
                $aux_totaldifval += $data->difval;
            }
            $respuesta['tablaT'] .= "
            </tbody>
            <tfoot>
                <tr>
                    <th style='text-align:right'>TOTALES</th>
                    <th style='text-align:right'>". number_format($aux_totalsubtotal, 0, ",", ".") ."</th>
                    <th style='text-align:right'>". number_format($aux_totalkilos, 0, ",", ".") ."</th>
                    <th style='text-align:right'>". number_format($aux_totalprecioxkilo, 2, ",", ".") ."</th>
                    <th style='text-align:right'>". number_format($aux_totalvalorcosto, 0, ",", ".") ."</th>
                    <th style='text-align:right'>". number_format($aux_totaldifprec, 2, ",", ".") ."</th>
                    <th style='text-align:right'>". number_format($aux_totaldifval, 0, ",", ".") ."</th>
                </tr>
            </tfoot>

            </table>";

            $respuesta['matprimdesc'] = array_column($datas, 'matprimdesc');
            
            $respuesta['difvals'] = array_column($datas, 'difval');
            //dd($respuesta['difvals']);
            $i = 0;
            foreach($respuesta['difvals'] as &$difval){
                $difval = round($difval,2);
                $difval1 = round(($difval / $aux_totaldifval) * 100,2);
                $respuesta['matprimdesc'][$i] .= " " . number_format($difval1, 2, ",", ".") . "%";
                $i++;
            }

            return $respuesta;
        }
    }

    public function exportPdf(Request $request)
    {
        if($request->ajax()){
            $notaventas = consulta($request);
        }
        //dd($request);
        //dd(str_replace(".","",$request->rut));
        $datas = consulta($request);
        //dd($request);
        $aux_fdesde= $request->fechad;
        $aux_fhasta= $request->fechah;

        //$cotizaciones = consulta('','');
        $empresa = Empresa::orderBy('id')->get();
        $usuario = Usuario::findOrFail(auth()->id());
        
        //return armarReportehtml($request);
        if($datas){
            dd(getenv('APP_DEBUG'));
            if(getenv('APP_DEBUG')){
                return view('estadisticaventa.listado', compact('datas','empresa','usuario','aux_fdesde','aux_fhasta'));
            }

            //return view('notaventaconsulta.listado', compact('notaventas','empresa','usuario','aux_fdesde','aux_fhasta','nomvendedor','nombreAreaproduccion','nombreGiro','nombreTipoEntrega'));
            
            $pdf = PDF::loadView('estadisticaventa.listado', compact('datas','empresa','usuario','aux_fdesde','aux_fhasta'))->setPaper('a4', 'landscape');
            //return $pdf->download('ReporteMatPrimxKilo.pdf');
            //return $pdf->stream(str_pad($notaventa->id, 5, "0", STR_PAD_LEFT) .' - '. $notaventa->cliente->razonsocial . '.pdf');
            return $pdf->stream("ReporteMatPrimxKilo.pdf");
        }else{
            dd('Ningún dato disponible en esta consulta.');
        }
    }

    public function grafico(Request $request){
        //dd($request);
        $respuesta = array();
		$respuesta['exito'] = true;
		$respuesta['mensaje'] = "Código encontrado";
		$respuesta['tabla'] = "";

        if($request->ajax()){
            $datas = consultaTgrafico($request);

            $respuesta['tabla'] .= "<table id='tablacotizacion' name='tablacotizacion' class='table display AllDataTables table-hover table-condensed tablascons' data-page-length='50'>
			<thead>
				<tr>
					<th>Materia Prima</th>
                    <th style='text-align:right'>Valor<br>Neto</th>
					<th style='text-align:right'>Kilos<br>Entreg</th>
                    <th style='text-align:right'>Precio<br>Kilo</th>
                    <th style='text-align:right'>Preci<br>Costo</th>
                    <th style='text-align:right'>Dif<br>Precio</th>
                    <th style='text-align:right'>Dif<br>Valorizada</th>
                </tr>
            </thead>
            <tbody>";
            $i = 0;
            $aux_totalsubtotal = 0;
            $aux_totalkilos = 0;
            $aux_totalprecioxkilo = 0;
            $aux_totalvalorcosto = 0;
            $aux_totaldifprec = 0;
            $aux_totaldifval = 0;
            
            foreach ($datas as $data) {
                $respuesta['tabla'] .= "
                <tr id='fila$i' name='fila$i'>
                    <td>$data->matprimdesc</td>
                    <td style='text-align:right'>".number_format($data->subtotal, 0, ",", ".") ."</td>
                    <td style='text-align:right'>".number_format($data->kilos, 0, ",", ".") ."</td>
                    <td style='text-align:right'>".number_format($data->precioxkilo, 2, ",", ".") ."</td>
                    <td style='text-align:right'>".number_format($data->valorcosto, 0, ",", ".") ."</td>
                    <td style='text-align:right'>".number_format($data->difprec, 2, ",", ".") ."</td>
                    <td style='text-align:right'>".number_format($data->difval, 0, ",", ".") ."</td>
                </tr>";
                $i++;
                $aux_totalsubtotal += $data->subtotal;
                $aux_totalkilos += $data->kilos;
                $aux_totalprecioxkilo += $data->precioxkilo;
                $aux_totalvalorcosto += $data->valorcosto;
                $aux_totaldifprec += $data->difprec;
                $aux_totaldifval += $data->difval;
            }
            $respuesta['tabla'] .= "
            </tbody>
            <tfoot>
                <tr>
                    <th style='text-align:right'>TOTALES</th>
                    <th style='text-align:right'>". number_format($aux_totalsubtotal, 0, ",", ".") ."</th>
                    <th style='text-align:right'>". number_format($aux_totalkilos, 0, ",", ".") ."</th>
                    <th style='text-align:right'>". number_format($aux_totalprecioxkilo, 2, ",", ".") ."</th>
                    <th style='text-align:right'>". number_format($aux_totalvalorcosto, 0, ",", ".") ."</th>
                    <th style='text-align:right'>". number_format($aux_totaldifprec, 2, ",", ".") ."</th>
                    <th style='text-align:right'>". number_format($aux_totaldifval, 0, ",", ".") ."</th>
                </tr>
            </tfoot>

            </table>";

            $respuesta['matprimdesc'] = array_column($datas, 'matprimdesc');
            
            $respuesta['difvals'] = array_column($datas, 'difval');
            //dd($respuesta['difvals']);
            $i = 0;
            foreach($respuesta['difvals'] as &$difval){
                $difval = round(($difval / $aux_totaldifval) * 100,2); //round($difval,2);
                $difval1 = round(($difval / $aux_totaldifval) * 100,2);
                //$respuesta['matprimdesc'][$i] .= " " . number_format($difval1, 2, ",", ".") . "%";
                $i++;
            }


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
        $aux_condFecha = "estadisticaventa.fechadocumento>='$fechad' and estadisticaventa.fechadocumento<='$fechah'";
    }
    if(empty($request->producto)){
        $aux_condproducto = " true";
    }else{
        $aux_condproducto = "estadisticaventa.producto='$request->producto'";
    }
    if(empty($request->matprimdesc)){
        $aux_condmatprimdesc = " true";
    }else{
        $aux_condmatprimdesc = "estadisticaventa.matprimdesc='$request->matprimdesc'";
    }

    $sql = "SELECT *
            FROM estadisticaventa
            WHERE $aux_condFecha
            and $aux_condproducto
            and $aux_condmatprimdesc;";

    $datas = DB::select($sql);
    return $datas;
}

function consultaTgrafico($request){
    if(empty($request->fechad) or empty($request->fechah)){
        $aux_condFecha = " true";
    }else{
        $fecha = date_create_from_format('d/m/Y', $request->fechad);
        $fechad = date_format($fecha, 'Y-m-d');
        $fecha = date_create_from_format('d/m/Y', $request->fechah);
        $fechah = date_format($fecha, 'Y-m-d');
        $aux_condFecha = "estadisticaventa.fechadocumento>='$fechad' and estadisticaventa.fechadocumento<='$fechah'";
    }

    $sql = "SELECT matprimdesc,SUM(subtotal) AS subtotal,SUM(kilos) AS kilos,
            ROUND(SUM(subtotal)/SUM(kilos),2) AS precioxkilo,valorcosto,
            ROUND((SUM(subtotal)/SUM(kilos)),2)-valorcosto AS difprec,
            SUM(kilos)*(ROUND((SUM(subtotal)/SUM(kilos)),2)-valorcosto) AS difval
            FROM estadisticaventa
            WHERE $aux_condFecha
            GROUP BY estadisticaventa.matprimdesc,valorcosto;";
    //dd($sql);
    $datas = DB::select($sql);
    return $datas;
}