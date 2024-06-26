<?php

namespace App\Http\Controllers;

use App\Models\AreaProduccion;
use App\Models\CategoriaProd;
use App\Models\Cliente;
use App\Models\ClienteSucursal;
use App\Models\ClienteVendedor;
use App\Models\Comuna;
use App\Models\Empresa;
use App\Models\Giro;
use App\Models\NotaVenta;
use App\Models\Producto;
use App\Models\Seguridad\Usuario;
use App\Models\Sucursal;
use App\Models\TipoEntrega;
use App\Models\Vendedor;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Support\Facades\DB;

class NotaVentaConsultaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('consulta-nota-de-venta');
        $giros = Giro::orderBy('id')->get();
        $areaproduccions =  AreaProduccion::areaproduccionxusuario();;
        $tipoentregas = TipoEntrega::orderBy('id')->get();
        $fechaServ = [
                    'fecha1erDiaMes' => date("01/m/Y"),
                    'fechaAct' => date("d/m/Y"),
                    ];
        $tablashtml['comunas'] = Comuna::selectcomunas();
        $tablashtml['vendedores'] = Vendedor::selectvendedores();
        $users = Usuario::findOrFail(auth()->id());
        $sucurArray = $users->sucursales->pluck('id')->toArray();
        $tablashtml['sucursales'] = Sucursal::orderBy('id')
        ->whereIn('sucursal.id', $sucurArray)
        ->get();
        return view('notaventaconsulta.index', compact('giros','areaproduccions','tipoentregas','fechaServ','tablashtml'));

    }

    public function reporte(Request $request){
        $respuesta = array();
		$respuesta['exito'] = false;
		$respuesta['mensaje'] = "Código no Existe";
		$respuesta['tabla'] = "";
        //dd($request);
        if($request->ajax()){
            $datas = NotaVenta::consulta($request,1);
            $aux_colvistoth = "";
            if(auth()->id()==1 or auth()->id()==2 or auth()->id()==24){
                $aux_colvistoth = "<th class='tooltipsC' title='Leido'>Leido</th>";
            }
            $aux_colvistoth = "<th class='tooltipsC' title='Leido'>Leido</th>";
            $respuesta['tabla'] .= "<table id='tabla-data' name='tabla-data' class='table display AllDataTables table-hover table-condensed tablascons' data-page-length='50'>
			<thead>
				<tr>
					<th>ID</th>
					<th class='tooltipsC' title='Fecha Hora Creacion'>Fecha</th>
					<th>RUT</th>
                    <th>Razón Social</th>
                    <th>Comuna</th>
                    <th class='tooltipsC' title='Orden de Compra'>OC</th>
                    <th style='text-align:right' class='tooltipsC' title='Total kg'>Total Kg</th>
                    <th style='text-align:right' class='tooltipsC' title='Total Pesos'>Total $</th>
                    <th style='text-align:right' class='tooltipsC' title='Precio Promedio x Kg'>Prom</th>
                    <th class='tooltipsC' title='Nota de Venta'>NV</th>
                    <th class='tooltipsC' title='Precio x Kg'>$ x Kg</th>
                    <!--$aux_colvistoth-->
				</tr>
			</thead>
            <tbody>";

            $i = 0;
            $aux_Tpvckg = 0;
            $aux_Tpvcpesos= 0;
            $aux_Tcankg = 0;
            $aux_Tcanpesos = 0;
            $aux_totalKG = 0;
            $aux_totalps = 0;
            $aux_prom = 0;
            foreach ($datas as $data) {
                $aux_cantdesp = NotaVenta::consultatotcantod($data->id);
                if(in_array('5',$request->aprobstatus)){
                    if($aux_cantdesp >= $data->cant){
                        continue;
                    }
                }
                if(in_array('6',$request->aprobstatus)){
                    if($data->cant != $aux_cantdesp){
                        continue;
                    }
                }
                $colorFila = "";
                $aux_data_toggle = "";
                $aux_title = "";
                /*
                if(!empty($data->anulada)){
                    $colorFila = 'background-color: #87CEEB;';
                    $aux_data_toggle = "tooltip";
                    $aux_title = "Anulada Fecha:" . $data->anulada;
                }
                */
                $rut = number_format( substr ( $data->rut, 0 , -1 ) , 0, "", ".") . '-' . substr ( $data->rut, strlen($data->rut) -1 , 1 );
                $prompvc = 0;
                $promcan = 0;
                $aux_prom = 0;
                if($data->pvckg!=0){
                    $prompvc = $data->pvcpesos / $data->pvckg;
                }
                if($data->cankg!=0){
                    $promcan = $data->canpesos / $data->cankg;
                }
                if($data->totalkilos>0){
                    $aux_prom = $data->subtotal / $data->totalkilos;
                }

                $Visto       = $data->visto;
                $checkVisto  = 'checked';
                if(empty($data->visto))
                    $checkVisto = ''; 

                $aux_colvistotd = "";
                if(empty($data->visto)){
                    $fechavisto = '';
                }else{
                    $fechavisto = 'Leido:' . date('d-m-Y h:i:s A', strtotime($data->visto));
                }
                
                $aux_colvistotd = "
                <td class='tooltipsC' style='text-align:center' class='tooltipsC' title='$fechavisto'>
                    <div class='checkbox'>
                        <label style='font-size: 1.2em'>";
                        if(!empty($data->anulada)){
                            $aux_colvistotd .= "<input type='checkbox' id='visto$i' name='visto$i' value='$Visto' $checkVisto disabled>";
                        }else{
                            if(auth()->id()==1 or auth()->id()==2 or auth()->id()==24){
                                $aux_colvistotd .= "<input type='checkbox' id='visto$i' name='visto$i' value='$Visto' $checkVisto onclick='visto($data->id,$i)'>";
                            }else{
                                $aux_colvistotd .= "<input type='checkbox' id='visto$i' name='visto$i' value='$Visto' $checkVisto disabled>";
                            }
                        }
                        $aux_colvistotd .= "<span class='cr'><i class='cr-icon fa fa-check'></i></span>
                        </label>
                    </div>
                </td>";
                if(empty($data->oc_file)){
                    $aux_enlaceoc = $data->oc_id;
                    /*
                    $aux_enlaceoc = "<button type='button' class='btn btn-block btn-primary'>$data->oc_id</button>";*/
                    $aux_enlaceoc = "<a class='btn btn-primary btn-xs tooltipsC' title='Subir OC' onclick='clicbotonactfileoc($i,$data->oc_id)'>
                                        <i class='fa fa-fw fa-cloud-upload'></i>
                                    </a>";
                }else{
                    $aux_enlaceoc = "<a onclick='verpdf2(\"$data->oc_file\",2)'>$data->oc_id</a>";
                }
                $aux_enlaceoc = "<a onclick='verpdf2(\"$data->oc_file\",2)'>$data->oc_id</a>";
                $aux_icono = "";
                $aux_ObsIcono = ""; //"No ha iniciado el despacho";
                if(!empty($data->inidespacho)){
                    $aux_icono = "fa-star-o";
                    $aux_ObsIcono = "Ini: " . date('d-m-Y', strtotime($data->inidespacho)) . " Guia: " . $data->guiasdespacho;
                }
                if(!empty($data->findespacho)){
                    $aux_icono = " fa-star";
                    $aux_ObsIcono = "Ini:" . date('d-m-Y', strtotime($data->inidespacho)) . " Fin:" . date('d-m-Y', strtotime($data->findespacho)) . " Guia: " . $data->guiasdespacho;
                }
                $aux_iconiInf = "";
                if(!empty($aux_icono)){
                    $aux_iconiInf = "<a class='btn-accion-tabla btn-sm tooltipsC' title='$aux_ObsIcono' data-toggle='tooltip'>
                                        <i class='fa fa-fw $aux_icono'></i>                                    
                                    </a>";
                }

                $aux_icodespachoNew = "";
                $aux_obsdespachoNew = "No ha iniciado el despacho";
                if($aux_cantdesp > 0){
                    $aux_icodespachoNew = "fa-star-o";
                    $aux_obsdespachoNew = "Inicio despacho, " . $aux_cantdesp . " / " . $data->cant;
                    $aux_obsdespacho = "";
                    //>= PORQUE EN SANTA ESTER SE PUEDE DESPACHAR MAS DE LO QUE DICE LA NOTA DE VENTA
                    if($aux_cantdesp >= $data->cant){
                        $aux_icodespachoNew = " fa-star";
                        $aux_obsdespachoNew = "Fin despacho";
                    }
                }
                $aux_iconiInf .= "<a class='btn-accion-tabla btn-sm tooltipsC' onclick='listarorddespxNV($data->id)' title='$aux_obsdespachoNew' data-toggle='tooltip'>
                                    <i class='fa fa-fw $aux_icodespachoNew text-aqua'></i>                                    
                                </a>";
                $comuna = Comuna::findOrFail($data->comunaentrega_id);

                if(!empty($data->anulada)){
                    $aux_fecanulada = date('d-m-Y h:i A', strtotime($data->anulada));
                    $aux_iconiInf .= "<a class='btn-accion-tabla btn-sm tooltipsC' title='Anulada: $aux_fecanulada' data-toggle='tooltip'>
                                        <span class='glyphicon glyphicon-remove text-danger'></span>
                                    </a>";
                }
                $notaventacerrada = NotaVenta::findOrFail($data->id)
                                    ->notaventacerradas;
                if(count($notaventacerrada)>0){
                    $aux_feccierre = date('d-m-Y h:i A', strtotime($notaventacerrada[0]->created_at));
                    $aux_iconiInf .= "<a class='btn-accion-tabla btn-sm tooltipsC' title='Cerrada: $aux_feccierre' data-toggle='tooltip'>
                                        <i class='fa fa-fw fa-archive'></i>
                                    </a>";
                }
                $respuesta['tabla'] .= "
                <tr id='fila$i' name='fila$i' style='$colorFila' title='$aux_title' data-toggle='$aux_data_toggle' class='btn-accion-tabla tooltipsC'>
                    <td id='id$i' name='id$i'>$data->id
                        $aux_iconiInf
                    </td>
                    <td style='font-size:12px' id='fechahora$i' name='fechahora$i' data-order='$data->fechahora'>" . date('d/m/Y H:i:s', strtotime($data->fechahora)) . "</td>
                    <td id='rut$i' name='rut$i'>$rut</td>
                    <td id='razonsocial$i' name='razonsocial$i' style='font-size:12px'>$data->razonsocial</td>
                    <td id='comuna$i' name='comuna$i'>$comuna->nombre</td>
                    <td id='oc_id$i' name='oc_id$i'>$aux_enlaceoc</a></td>
                    <td id='totalkilos$i' name='totalkilos$i' style='text-align:right' data-order='$data->totalkilos'>".number_format($data->totalkilos, 2, ",", ".") ."</td>
                    <td id='totalps$i' name='totalps$i' style='text-align:right' data-order='$data->subtotal'>".number_format($data->subtotal, 0, ",", ".") ."</td>
                    <td id='prompvc$i' name='prompvc$i' style='text-align:right' data-order='$aux_prom'>".number_format($aux_prom, 2, ",", ".") ."</td>
                    <td>
                        <!--<a href='" . route('exportPdf_notaventa', ['id' => $data->id,'stareport' => '1']) . "' class='btn-accion-tabla tooltipsC' title='Nota de Venta' target='_blank'>-->
                        <a class='btn-accion-tabla btn-sm tooltipsC' onclick='genpdfNV($data->id,1)' title='Nota de venta $data->id'>
                            <i class='fa fa-fw fa-file-pdf-o'></i>                                    
                        </a>
                    </td>
                    <td>
                        <!--<a href='" . route('exportPdf_notaventa', ['id' => $data->id,'stareport' => '2']) . "' class='btn-accion-tabla tooltipsC' title='Precio x Kg' target='_blank'>-->
                        <a class='btn-accion-tabla btn-sm tooltipsC' onclick='genpdfNV($data->id,2)' title='Nota de venta $data->id'>
                            <i class='fa fa-fw fa-file-pdf-o'></i>                                    
                        </a>
                    </td>
                    <!--$aux_colvistotd-->
                </tr>";

                if(empty($data->anulada)){
                    $aux_Tpvckg += $data->pvckg;
                    $aux_Tpvcpesos += $data->pvcpesos;
                    $aux_Tcankg += $data->cankg;
                    $aux_Tcanpesos += $data->canpesos;
                    $aux_totalKG += $data->totalkilos;
                    $aux_totalps += $data->subtotal;    
                }

    
                //dd($data->contacto);
            }

            $aux_promGeneral = 0;
            if($aux_totalKG>0){
                $aux_promGeneral = $aux_totalps / $aux_totalKG;
            }
            $respuesta['tabla'] .= "
            </tbody>
            <tfoot>
                <tr>
                    <th colspan='6' style='text-align:left'>TOTALES</th>
                    <th style='text-align:right'>". number_format($aux_totalKG, 2, ",", ".") ."</th>
                    <th style='text-align:right'>". number_format($aux_totalps, 0, ",", ".") ."</th>
                    <th style='text-align:right'>". number_format($aux_promGeneral, 2, ",", ".") ."</th>
                    <th style='text-align:right'></th>
                </tr>
            </tfoot>

            </table>";

            return $respuesta;
        }
    }
    public function exportPdf1(Request $request)
    {
        $rut=str_replace("-","",$request->rut);
        $rut=str_replace(".","",$rut);
        $notaventas = NotaVenta::consulta($request,1);
        $totalareaprods = NotaVenta::consulta($request,2); //Totales Area de produccion
        $aux_fdesde= $request->fechad;
        $aux_fhasta= $request->fechah;

        //$cotizaciones = consulta('','');
        $empresa = Empresa::orderBy('id')->get();
        $usuario = Usuario::findOrFail(auth()->id());
        $nomvendedor = "Todos";
        if(!empty($request->vendedor_id)){
            $vendedor = Vendedor::findOrFail($request->vendedor_id);
            $nomvendedor=$vendedor->persona->nombre . " " . $vendedor->persona->apellido;
        }
        $nombreAreaproduccion = "Todos";
        if($request->areaproduccion_id){
            $areaProduccion = AreaProduccion::findOrFail($request->areaproduccion_id);
            $nombreAreaproduccion=$areaProduccion->nombre;
        }
        $nombreGiro = "Todos";
        if($request->giro_id){
            $giro = Giro::findOrFail($request->giro_id);
            $nombreGiro=$giro->nombre;
        }
        $nombreTipoEntrega = "Todos";
        if($request->tipoentrega_id){
            $tipoentrega = TipoEntrega::findOrFail($request->tipoentrega_id);
            $nombreTipoEntrega=$tipoentrega->nombre;
        }
        
        //return armarReportehtml($request);
        if($notaventas){
            
            if(env('APP_DEBUG')){
                return view('notaventaconsulta.listado', compact('notaventas','totalareaprods','empresa','usuario','aux_fdesde','aux_fhasta','nomvendedor','nombreAreaproduccion','nombreGiro','nombreTipoEntrega','request'));
            }
            
            //return view('notaventaconsulta.listado', compact('notaventas','empresa','usuario','aux_fdesde','aux_fhasta','nomvendedor','nombreAreaproduccion','nombreGiro','nombreTipoEntrega'));
            
            $pdf = PDF::loadView('notaventaconsulta.listado', compact('notaventas','totalareaprods','empresa','usuario','aux_fdesde','aux_fhasta','nomvendedor','nombreAreaproduccion','nombreGiro','nombreTipoEntrega','request'));
            //return $pdf->download('cotizacion.pdf');
            //return $pdf->stream(str_pad($notaventa->id, 5, "0", STR_PAD_LEFT) .' - '. $notaventa->cliente->razonsocial . '.pdf');
            return $pdf->stream("ReporteNotasVenta.pdf");
        }else{
            dd('Ningún dato disponible en esta consulta.');
        }
    }

    public function exportPdf(Request $request)
    {
        //dd($request);
        /*
        $request = new Request();
        $request->fechad = $_GET["fechad"];
        $request->fechah = $_GET["fechah"];
        $request->rut = $_GET["rut"];
        $request->vendedor_id = $_GET["vendedor_id"];
        $request->oc_id = $_GET["oc_id"];
        $request->giro_id = $_GET["giro_id"];
        $request->areaproduccion_id = $_GET["areaproduccion_id"];
        $request->tipoentrega_id = $_GET["tipoentrega_id"];
        $request->notaventa_id = $_GET["notaventa_id"];
        $request->aprobstatus = explode ( ",", $_GET["aprobstatus"] );
        $request->producto_idM = $_GET["producto_idM"];
        $request->comuna_id = $_GET["comuna_id"];
        $request->sucursal_id = $_GET["sucursal_id"];
        */

        $notaventas = NotaVenta::consulta($request,1);
        $totalareaprods = NotaVenta::consulta($request,2); //Totales Area de produccion
        $aux_fdesde= $request->fechad;
        $aux_fhasta= $request->fechah;

        //$cotizaciones = consulta('','');
        $empresa = Empresa::orderBy('id')->get();
        $usuario = Usuario::findOrFail(auth()->id());
        $nomvendedor = "Todos";
        if(!empty($request->vendedor_id)){
            $vendedor = Vendedor::findOrFail($request->vendedor_id);
            $nomvendedor=$vendedor->persona->nombre . " " . $vendedor->persona->apellido;
        }
        $nombreAreaproduccion = "Todos";
        if($request->areaproduccion_id){
            $areaProduccion = AreaProduccion::findOrFail($request->areaproduccion_id);
            $nombreAreaproduccion=$areaProduccion->nombre;
        }
        $nombreGiro = "Todos";
        if($request->giro_id){
            $giro = Giro::findOrFail($request->giro_id);
            $nombreGiro=$giro->nombre;
        }
        $nombreTipoEntrega = "Todos";
        if($request->tipoentrega_id){
            $tipoentrega = TipoEntrega::findOrFail($request->tipoentrega_id);
            $nombreTipoEntrega=$tipoentrega->nombre;
        }
        $nombreSucursal = "Todas";
        if($request->sucursal_id){
            $sucursal = Sucursal::findOrFail($request->sucursal_id);
            $nombreSucursal=$sucursal->nombre;
        }
        //return armarReportehtml($request);
        if($notaventas){
            
            if(env('APP_DEBUG')){
                return view('notaventaconsulta.listado', compact('notaventas','totalareaprods','empresa','usuario','aux_fdesde','aux_fhasta','nomvendedor','nombreAreaproduccion','nombreGiro','nombreTipoEntrega','request','nombreSucursal'));
            }
            
            //return view('notaventaconsulta.listado', compact('notaventas','empresa','usuario','aux_fdesde','aux_fhasta','nomvendedor','nombreAreaproduccion','nombreGiro','nombreTipoEntrega'));
            
            $pdf = PDF::loadView('notaventaconsulta.listado', compact('notaventas','totalareaprods','empresa','usuario','aux_fdesde','aux_fhasta','nomvendedor','nombreAreaproduccion','nombreGiro','nombreTipoEntrega','request','nombreSucursal'));
            //return $pdf->download('cotizacion.pdf');
            //return $pdf->stream(str_pad($notaventa->id, 5, "0", STR_PAD_LEFT) .' - '. $notaventa->cliente->razonsocial . '.pdf');
            return $pdf->stream("ReporteNotasVenta.pdf");
        }else{
            dd('Ningún dato disponible en esta consulta.');
        }
        
    }  
}



function armarReportehtml($request){
    //$cotizaciones = Cotizacion::orderBy('id')->get();
    $rut=str_replace("-","",$request->rut);
    $rut=str_replace(".","",$rut);
    //dd($rut);
    $notaventas = consulta($request,1);
    $aux_fdesde= $request->fechad;
    $aux_fhasta= $request->fechah;

    //$cotizaciones = consulta('','');
    $empresa = Empresa::orderBy('id')->get();
    $usuario = Usuario::findOrFail(auth()->id());
    $nomvendedor = "Todos";
    if(!empty($request->vendedor_id)){
        $vendedor = Vendedor::findOrFail($request->vendedor_id);
        $nomvendedor=$vendedor->persona->nombre . " " . $vendedor->persona->apellido;
    }
    $nombreAreaproduccion = "Todos";
    if($request->areaproduccion_id){
        $areaProduccion = AreaProduccion::findOrFail($request->areaproduccion_id);
        $nombreAreaproduccion=$areaProduccion->nombre;
    }
    $nombreGiro = "Todos";
    if($request->giro_id){
        $giro = Giro::findOrFail($request->giro_id);
        $nombreGiro=$giro->nombre;
    }
    $nombreTipoEntrega = "Todos";
    if($request->tipoentrega_id){
        $tipoentrega = TipoEntrega::findOrFail($request->tipoentrega_id);
        $nombreTipoEntrega=$tipoentrega->nombre;
    }

    $respuesta = array();
    $respuesta['exito'] = false;
    $respuesta['mensaje'] = "Código no Existe";
    $respuesta['tabla'] = "";
    $theme = '';
    $ruta_logo = asset("assets/$theme/dist/img/LOGO-PLASTISERVI.png");
    $respuesta['tabla'] .= "
    <br>
    <br>
    <div id='page_pdf'>
        <table id='factura_head'>
            <tr>
                <td class='logo_factura'>
                    <div>
                        <img src='$ruta_logo' style='max-width:1200%;width:auto;height:auto;'>
                        <p>$empresa[0]['nombre']</p>					
                        <p>RUT: $empresa[0]['rut']</p>
                    </div>
                </td>
                <td class='info_empresa'>
                </td>
                <td class='info_factura'>
                    <div class='round'>
                        <span class='h3'>Reporte Nota de Venta</span>
                        <p>Fecha: date('d-m-Y h:i:s A')</p>
                        <p>Area Producción: $nombreAreaproduccion</p>
                        <p>Vendedor: $nomvendedor </p>
                        <p>Giro: $nombreGiro </p>
                        <p>Tipo Entrega: $nombreTipoEntrega </p>
                        <p>Desde: $aux_fdesde Hasta: $aux_fhasta</p>
                    </div>
                </td>
            </tr>
        </table>
    
        <div class='round'>
            <table id='factura_detalle'>
                    <thead>
                        <tr>
                            <th style='text-align:left'>#</th>
                            <th style='text-align:left'>NV ID</th>
                            <th class='textcenter'>Fecha</th>
                            <th class='textleft'>Razón Social</th>
                            <th style='text-align:right'>Total Kg</th>
                            <th style='text-align:right'>Total $</th>
                            <th style='text-align:right'>Prom</th>
                        </tr>
                    </thead>
                    <tbody id='detalle_productos'>";
                    $i=0;
                    $aux_totalKG = 0;
                    $aux_totalps = 0;
                    foreach($notaventas as $notaventa){
                        if(empty($notaventa->anulada)){
                            $i++;
                            $aux_totalKG += $notaventa->totalkilos;
                            $aux_totalps += $notaventa->totalps;
                        }
                        $rut = number_format( substr ( $notaventa->rut, 0 , -1 ) , 0, '', '.') . '-' . substr ( $notaventa->rut, strlen($notaventa->rut) -1 , 1 );
                        $colorFila = '';
                        $aux_data_toggle = '';
                        $aux_title = '';
                        if(!empty($notaventa->anulada)){
                            $colorFila = 'background-color: #87CEEB;';
                            $aux_data_toggle = 'tooltip';
                            $aux_title = 'Anulada Fecha:' . $notaventa->anulada;
                        }
                        $aux_prom = 0;
                        if($notaventa->totalkilos>0){
                            $aux_prom = $notaventa->subtotal / $notaventa->totalkilos;
                        }
                        $respuesta['tabla'] .= "
                        <tr style='$colorFila' title='$aux_title' data-toggle='$aux_data_toggle' class='btn-accion-tabla tooltipsC'>
                            <td>$i</td>
                            <td>$notaventa->id</td>
                            <td style='text-align:center'>date('d-m-Y', strtotime($notaventa->fechahora))</td>
                            <td>$notaventa->razonsocial</td>
                            <td style='text-align:right'>number_format($notaventa->totalkilos, 2, ',', '.')</td>
                            <td style='text-align:right'>number_format($notaventa->totalps, 2, ',', '.')</td>
                            <td style='text-align:right'>number_format($aux_prom, 2, ',', '.')</td>
                        </tr>";
                    }
                    $respuesta['tabla'] .= "
                    </tbody>";
                    $aux_promGeneral = 0;
                    if($aux_totalKG>0){
                        $aux_promGeneral = $aux_totalps / $aux_totalKG;
                    }

                    $respuesta['tabla'] .= "
                                    <tfoot id='detalle_totales'>
                                        <tr class='headt'>
                                            <b>
                                            <td colspan='4' class='textright'><span>TOTALES</span></td>
                                            <td class='textright'><span>number_format($aux_totalKG, 2, ',', '.')</span></td>
                                            <td class='textright'><span>number_format($aux_totalps, 2, ',', '.')</span></td>
                                            <td class='textright'><span>number_format($aux_promGeneral, 2, ',', '.')</span></td>
                                            </b>
                                    </tfoot>
                            </table>
                        </div>
    </div>";
    return $respuesta['tabla'];
}