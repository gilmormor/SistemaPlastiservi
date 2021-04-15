<?php

namespace App\Http\Controllers;

use App\Models\AreaProduccion;
use App\Models\Cliente;
use App\Models\ClienteSucursal;
use App\Models\ClienteVendedor;
use App\Models\Comuna;
use App\Models\Empresa;
use App\Models\Giro;
use App\Models\NotaVenta;
use App\Models\Seguridad\Usuario;
use App\Models\TipoEntrega;
use App\Models\Vendedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade as PDF;


class DespachoTempNotaVentaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('despacho-temp');
        $clientesArray = Cliente::clientesxUsuario();
        $clientes = $clientesArray['clientes'];
        $vendedor_id = $clientesArray['vendedor_id'];
        $sucurArray = $clientesArray['sucurArray'];

        $arrayvend = Vendedor::vendedores(); //Viene del modelo vendedores
        $vendedores1 = $arrayvend['vendedores'];
        $clientevendedorArray = $arrayvend['clientevendedorArray'];
        /*
        $sucurArray = $user->sucursales->pluck('id')->toArray();
        // Filtro solos los clientes que esten asignados a la sucursal y asignado al vendedor logueado
        $clientes = Cliente::select(['cliente.id','cliente.rut','cliente.razonsocial','cliente.direccion','cliente.telefono'])
        ->whereIn('cliente.id' , ClienteSucursal::select(['cliente_sucursal.cliente_id'])
                                ->whereIn('cliente_sucursal.sucursal_id', $sucurArray)
        ->pluck('cliente_sucursal.cliente_id')->toArray())
        ->whereIn('cliente.id',$clientevendedorArray)
        ->get();
        */
        $vendedores = Vendedor::orderBy('id')->where('sta_activo',1)->get();

        $giros = Giro::orderBy('id')->get();
        $areaproduccions = AreaProduccion::orderBy('id')->get();
        $tipoentregas = TipoEntrega::orderBy('id')->get();
        $comunas = NotaVenta::groupBy('comunaentrega_id')
                ->whereNotNull('visto')
                ->select([
                    'comunaentrega_id'
                    ])
                ->get();
        $fechaAct = date("d/m/Y");
        return view('despachoTemp.index', compact('clientes','vendedores','vendedores1','giros','areaproduccions','tipoentregas','comunas','fechaAct'));

    }

    public function reporte(Request $request){
        $respuesta = array();
		$respuesta['exito'] = false;
		$respuesta['mensaje'] = "Código no Existe";
        $respuesta['tabla'] = "";
        $respuesta['tabla2'] = "";
        $respuesta['tabla3'] = "";

        if($request->ajax()){
            $datas = consulta($request,1);
            //dd($consulta['datas1']);
            $respuesta['tabla'] .= "<table id='tablacotizacion' name='tablacotizacion' class='table display AllDataTables table-hover table-condensed tablascons' data-page-length='50'>
			<thead>
				<tr>
					<th>ID</th>
					<th>Fecha</th>
					<th>RUT</th>
                    <th>Razón Social</th>
                    <th class='tooltipsC' title='Orden de Compra'>OC</th>
                    <th style='text-align:right' class='tooltipsC' title='Total kg'>Total Kg</th>
                    <th class='tooltipsC' title='Nota de Venta'>NV</th>
                    <th class='tooltipsC' title='Tipo de Entrega'>TE</th>
                    <th class='tooltipsC' title='Leido'>Leido</th>
                    <th class='tooltipsC' title='Acción' style='text-align:center'>Acción</th>
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
                $colorFila = "";
                $aux_data_toggle = "";
                $aux_title = "";
                if(!empty($data->anulada)){
                    $colorFila = 'background-color: #87CEEB;';
                    $aux_data_toggle = "tooltip";
                    $aux_title = "Anulada Fecha:" . $data->anulada;
                }
    
                $rut = number_format( substr ( $data->rut, 0 , -1 ) , 0, "", ".") . '-' . substr ( $data->rut, strlen($data->rut) -1 , 1 );
                $prompvc = 0;
                if($data->pvckg!=0){
                    $prompvc = $data->pvcpesos / $data->pvckg;
                }
                $promcan = 0;
                if($data->cankg!=0){
                    $promcan = $data->canpesos / $data->cankg;
                }
                if($data->totalkilos>0){
                    $aux_prom = $data->subtotal / $data->totalkilos;
                }
                if(empty($data->inidespacho)){
                    $botoninidespacho = "<a id='initdespacho$i' name='initdespacho$i' class='btn btn-success btn-sm' onclick='inidespacho($data->id,$i)'>
                                            <span id='glypcnbtnInitdespacho$i' class='glyphicon glyphicon-play' style='bottom: 0px;top: 2px;' class='tooltipsC'></span>
                                        </a>";
                }else{
                    $botoninidespacho = "<a id='initdespacho$i' name='initdespacho$i' class='btn btn-warning btn-sm' onclick='findespacho($i)'>
                                            <span id='glypcnbtnInitdespacho$i' class='glyphicon glyphicon-stop' style='bottom: 0px;top: 2px;' class='tooltipsC'></span>
                                        </a>";
                }
                $aux_dbotonid = "";
                if(empty($data->inidespacho)){
                    $aux_dbotonid = "disabled";
                }
                if(empty($data->oc_file)){
                    $aux_enlaceoc = $data->oc_id;
                }else{
                    $aux_enlaceoc = "<a onclick='verpdf2(\"$data->oc_file\",2)'>$data->oc_id</a>";
                }
                $respuesta['tabla'] .= "
                <tr id='fila$i' name='fila$i' style='$colorFila' title='$aux_title' data-toggle='$aux_data_toggle' class='btn-accion-tabla tooltipsC'>
                    <td id='id$i' name='id$i'>$data->id</td>
                    <td id='fechahora$i' name='fechahora$i'>" . date('d-m-Y', strtotime($data->fechahora)) . "</td>
                    <td id='rut$i' name='rut$i'>$rut</td>
                    <td id='razonsocial$i' name='razonsocial$i'>$data->razonsocial</td>
                    <td id='oc_id$i' name='oc_id$i'>$aux_enlaceoc</td>
                    <td id='totalkilos$i' name='totalkilos$i' style='text-align:right'>".number_format($data->totalkilos, 2, ",", ".") ."</td>
                    <td>
                        <a class='btn-accion-tabla btn-sm' onclick='genpdfNV($data->id,1)' title='Nota de venta' data-toggle='tooltip'>
                            <i class='fa fa-fw fa-file-pdf-o'></i>                                    
                        </a>
                    </td>
                    <td>
                        <i class='fa fa-fw $data->icono tooltipsC' title='$data->nombre'></i>
                    </td>
                    <td id='visto$i' name='visto$i' style='text-align:left'>".date('d-m-Y', strtotime($data->visto)) ."</td>
                    <td style='text-align:center'>
                        $botoninidespacho | 
						<a id='guiadespacho$i' name='guiadespacho$i' class='btn btn-primary btn-sm $aux_dbotonid guiadespacho' onclick='guiadespacho($data->id,$i)' data-toggle='tooltip'><span class='glyphicon glyphicon-floppy-save' style='bottom: 0px;top: 2px;'></span></a>
                    </td>
                </tr>";
                $aux_Tpvckg += $data->pvckg;
                $aux_Tpvcpesos += $data->pvcpesos;
                $aux_Tcankg += $data->cankg;
                $aux_Tcanpesos += $data->canpesos;
                $aux_totalKG += $data->totalkilos;
                $aux_totalps += $data->subtotal;
                $i++;
    
                //dd($data->contacto);
            }

            $aux_promGeneral = 0;
            if($aux_totalKG>0){
                $aux_promGeneral = $aux_totalps / $aux_totalKG;
            }
/*
            $respuesta['tabla'] .= "
            </tbody>
            <tfoot>
                <tr>
                    <th colspan='5' style='text-align:left'>TOTAL</th>
                    <th style='text-align:right'>". number_format($aux_totalKG, 2, ",", ".") ."</th>
                    <th style='text-align:right'>". number_format($aux_totalps, 2, ",", ".") ."</th>
                    <th style='text-align:right'>". number_format($aux_promGeneral, 2, ",", ".") ."</th>
                    <th style='text-align:right'></th>
                </tr>
            </tfoot>

            </table>";
*/
            $respuesta['tabla'] .= "
            </tbody>
            </table>";

            //MOSTRAR LAS NOTAS DE VENTAS CON DESPACHO FINALIZADO
            $datas = consulta($request,2);
            $respuesta['tabla2'] .= "<table id='tablacotizacion' name='tablacotizacion' class='table display AllDataTables table-hover table-condensed tablascons' data-page-length='50'>
			<thead>
				<tr>
					<th>ID</th>
					<th>Fecha</th>
					<th>RUT</th>
                    <th>Razón Social</th>
                    <th class='tooltipsC' title='Orden de Compra'>OC</th>
                    <th style='text-align:right' class='tooltipsC' title='Total kg'>Total Kg</th>
                    <th class='tooltipsC' title='Nota de Venta'>NV</th>
                    <th class='tooltipsC' title='Tipo de Entrega'>TE</th>
                    <th class='tooltipsC' title='Leido'>Leido</th>
                    <th class='tooltipsC' title='Inicio'>Inicio</th>
                    <th class='tooltipsC' title='Fin'>Fin</th>
                    <th class='tooltipsC' title='Guia Despacho'>Guia Despacho</th>
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
                $colorFila = "";
                $aux_data_toggle = "";
                $aux_title = "";
                if(!empty($data->anulada)){
                    $colorFila = 'background-color: #87CEEB;';
                    $aux_data_toggle = "tooltip";
                    $aux_title = "Anulada Fecha:" . $data->anulada;
                }
                $rut = number_format( substr ( $data->rut, 0 , -1 ) , 0, "", ".") . '-' . substr ( $data->rut, strlen($data->rut) -1 , 1 );
                $prompvc = 0;
                if($data->pvckg!=0){
                    $prompvc = $data->pvcpesos / $data->pvckg;
                }
                $promcan = 0;
                if($data->cankg!=0){
                    $promcan = $data->canpesos / $data->cankg;
                }
                if($data->totalkilos>0){
                    $aux_prom = $data->subtotal / $data->totalkilos;
                }
                if(empty($data->inidespacho)){
                    $botoninidespacho = "<a id='initdespacho$i' name='initdespacho$i' class='btn btn-success btn-sm' onclick='inidespacho($data->id,$i)'>
                                            <span id='glypcnbtnInitdespacho$i' class='glyphicon glyphicon-play' style='bottom: 0px;top: 2px;' class='tooltipsC'></span>
                                        </a>";
                }else{
                    $botoninidespacho = "<a id='initdespacho$i' name='initdespacho$i' class='btn btn-warning btn-sm' onclick='findespacho($i)'>
                                            <span id='glypcnbtnInitdespacho$i' class='glyphicon glyphicon-stop' style='bottom: 0px;top: 2px;' class='tooltipsC'></span>
                                        </a>";
                }
                $aux_dbotonid = "";
                if(empty($data->inidespacho)){
                    $aux_dbotonid = "disabled";
                }  
                if(empty($data->oc_file)){
                    $aux_enlaceoc = $data->oc_id;
                }else{
                    $aux_enlaceoc = "<a onclick='verpdf2(\"$data->oc_file\",2)'>$data->oc_id</a>";
                }
                $respuesta['tabla2'] .= "
                <tr id='fila$i' name='fila$i' style='$colorFila' title='$aux_title' data-toggle='$aux_data_toggle' class='btn-accion-tabla tooltipsC'>
                    <td id='id$i' name='id$i'>$data->id</td>
                    <td id='fechahora$i' name='fechahora$i'>" . date('d-m-Y', strtotime($data->fechahora)) . "</td>
                    <td id='rut$i' name='rut$i'>$rut</td>
                    <td id='razonsocial$i' name='razonsocial$i'>$data->razonsocial</td>
                    <td id='oc_id$i' name='oc_id$i'>$aux_enlaceoc</td>
                    <td id='totalkilos$i' name='totalkilos$i' style='text-align:right'>".number_format($data->totalkilos, 2, ",", ".") ."</td>
                    <td>
                        <a class='btn-accion-tabla btn-sm' onclick='genpdfNV($data->id,1)' title='Nota de venta' data-toggle='tooltip'>
                            <i class='fa fa-fw fa-file-pdf-o'></i>                                    
                        </a>
                    </td>
                    <td>
                        <i class='fa fa-fw $data->icono tooltipsC' title='$data->nombre'></i>
                    </td>
                    <td id='visto$i' name='visto$i' style='text-align:left'>".date('d-m-Y', strtotime($data->visto)) ."</td>
                    <td style='text-align:left'>".date('d-m-Y', strtotime($data->inidespacho)) ."</td>
                    <td style='text-align:left'>".date('d-m-Y', strtotime($data->findespacho)) ."</td>
                    <td style='text-align:left'>$data->guiasdespacho</td>
                </tr>";
                $aux_Tpvckg += $data->pvckg;
                $aux_Tpvcpesos += $data->pvcpesos;
                $aux_Tcankg += $data->cankg;
                $aux_Tcanpesos += $data->canpesos;
                $aux_totalKG += $data->totalkilos;
                $aux_totalps += $data->subtotal;
                $i++;
    
                //dd($data->contacto);
            }

            $aux_promGeneral = 0;
            if($aux_totalKG>0){
                $aux_promGeneral = $aux_totalps / $aux_totalKG;
            }
/*
            $respuesta['tabla'] .= "
            </tbody>
            <tfoot>
                <tr>
                    <th colspan='5' style='text-align:left'>TOTAL</th>
                    <th style='text-align:right'>". number_format($aux_totalKG, 2, ",", ".") ."</th>
                    <th style='text-align:right'>". number_format($aux_totalps, 2, ",", ".") ."</th>
                    <th style='text-align:right'>". number_format($aux_promGeneral, 2, ",", ".") ."</th>
                    <th style='text-align:right'></th>
                </tr>
            </tfoot>

            </table>";
*/
            $respuesta['tabla2'] .= "
            </tbody>
            </table>";


            //MOSTRAR LAS NOTAS DE VENTAS
            $datas = consulta($request,3);
            $respuesta['tabla3'] .= "<table id='tablacotizacion' name='tablacotizacion' class='table display AllDataTables table-hover table-condensed tablascons' data-page-length='50'>
			<thead>
				<tr>
					<th>ID</th>
					<th>Fecha</th>
					<th>RUT</th>
                    <th>Razón Social</th>
                    <th class='tooltipsC' title='Orden de Compra'>OC</th>
                    <th style='text-align:right' class='tooltipsC' title='Total kg'>Total Kg</th>
                    <th class='tooltipsC' title='Nota de Venta'>NV</th>
                    <th class='tooltipsC' title='Tipo de Entrega'>TE</th>
                    <th class='tooltipsC' title='Leido'>Leido</th>
                    <th class='tooltipsC' title='Inicio despacho'>Inicio</th>
                    <th class='tooltipsC' title='Fin despacho'>Fin</th>
                    <th class='tooltipsC' title='Guia Despacho'>Guia Despacho</th>
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
                $colorFila = "";
                $aux_data_toggle = "";
                $aux_title = "";
                if(!empty($data->anulada)){
                    $colorFila = 'background-color: #87CEEB;';
                    $aux_data_toggle = "tooltip";
                    $aux_title = "Anulada Fecha:" . $data->anulada;
                }
                $rut = number_format( substr ( $data->rut, 0 , -1 ) , 0, "", ".") . '-' . substr ( $data->rut, strlen($data->rut) -1 , 1 );
                $prompvc = 0;
                if($data->pvckg!=0){
                    $prompvc = $data->pvcpesos / $data->pvckg;
                }
                $promcan = 0;
                if($data->cankg!=0){
                    $promcan = $data->canpesos / $data->cankg;
                }
                if($data->totalkilos>0){
                    $aux_prom = $data->subtotal / $data->totalkilos;
                }
                if(empty($data->inidespacho)){
                    $botoninidespacho = "<a id='initdespacho$i' name='initdespacho$i' class='btn btn-success btn-sm' onclick='inidespacho($data->id,$i)'>
                                            <span id='glypcnbtnInitdespacho$i' class='glyphicon glyphicon-play' style='bottom: 0px;top: 2px;' class='tooltipsC'></span>
                                        </a>";
                }else{
                    $botoninidespacho = "<a id='initdespacho$i' name='initdespacho$i' class='btn btn-warning btn-sm' onclick='findespacho($i)'>
                                            <span id='glypcnbtnInitdespacho$i' class='glyphicon glyphicon-stop' style='bottom: 0px;top: 2px;' class='tooltipsC'></span>
                                        </a>";
                }
                $aux_dbotonid = "";
                if(empty($data->inidespacho)){
                    $aux_dbotonid = "disabled";
                }
                if(empty($data->oc_file)){
                    $aux_enlaceoc = $data->oc_id;
                }else{
                    $aux_enlaceoc = "<a onclick='verpdf2(\"$data->oc_file\",2)'>$data->oc_id</a>";
                }
                $respuesta['tabla3'] .= "
                <tr id='fila$i' name='fila$i' style='$colorFila' title='$aux_title' data-toggle='$aux_data_toggle' class='btn-accion-tabla tooltipsC'>
                    <td id='id$i' name='id$i'>$data->id</td>
                    <td id='fechahora$i' name='fechahora$i'>" . date('d-m-Y', strtotime($data->fechahora)) . "</td>
                    <td id='rut$i' name='rut$i'>$rut</td>
                    <td id='razonsocial$i' name='razonsocial$i'>$data->razonsocial</td>
                    <td id='oc_id$i' name='oc_id$i'>$aux_enlaceoc</td>
                    <td id='totalkilos$i' name='totalkilos$i' style='text-align:right'>".number_format($data->totalkilos, 2, ",", ".") ."</td>
                    <td>
                        <a class='btn-accion-tabla btn-sm' onclick='genpdfNV($data->id,1)' title='Nota de venta' data-toggle='tooltip'>
                            <i class='fa fa-fw fa-file-pdf-o'></i>                                    
                        </a>
                    </td>
                    <td>
                        <i class='fa fa-fw $data->icono tooltipsC' title='$data->nombre'></i>
                    </td>
                    <td id='visto$i' name='visto$i' style='text-align:left'>".date('d-m-Y', strtotime($data->visto)) ."</td>
                    <td style='text-align:left'>". (empty($data->inidespacho) ? "" : date('d-m-Y', strtotime($data->inidespacho))) ."</td>
                    <td style='text-align:left'>". (empty($data->findespacho) ? "" : date('d-m-Y', strtotime($data->findespacho))) ."</td>
                    <td style='text-align:left'>$data->guiasdespacho</td>
                </tr>";
                $aux_Tpvckg += $data->pvckg;
                $aux_Tpvcpesos += $data->pvcpesos;
                $aux_Tcankg += $data->cankg;
                $aux_Tcanpesos += $data->canpesos;
                $aux_totalKG += $data->totalkilos;
                $aux_totalps += $data->subtotal;
                $i++;
    
                //dd($data->contacto);
            }

            $aux_promGeneral = 0;
            if($aux_totalKG>0){
                $aux_promGeneral = $aux_totalps / $aux_totalKG;
            }
/*
            $respuesta['tabla'] .= "
            </tbody>
            <tfoot>
                <tr>
                    <th colspan='5' style='text-align:left'>TOTAL</th>
                    <th style='text-align:right'>". number_format($aux_totalKG, 2, ",", ".") ."</th>
                    <th style='text-align:right'>". number_format($aux_totalps, 2, ",", ".") ."</th>
                    <th style='text-align:right'>". number_format($aux_promGeneral, 2, ",", ".") ."</th>
                    <th style='text-align:right'></th>
                </tr>
            </tfoot>

            </table>";
*/
            $respuesta['tabla3'] .= "
            </tbody>
            </table>";

            return $respuesta;
        }
    }

    public function exportPdf(Request $request)
    {
        //$cotizaciones = Cotizacion::orderBy('id')->get();
        $rut=str_replace("-","",$request->rut);
        $rut=str_replace(".","",$rut);
        //dd($rut);
        if($request->ajax()){
            $notaventas = consulta($request);
        }
        //dd($request);
        $notaventas = consulta($request);
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

        if($notaventas){
            //return view('notaventaconsulta.listado', compact('notaventas','empresa','usuario','aux_fdesde','aux_fhasta','nomvendedor','nombreAreaproduccion','nombreGiro','nombreTipoEntrega'));
        
            $pdf = PDF::loadView('notaventaconsulta.listado', compact('notaventas','empresa','usuario','aux_fdesde','aux_fhasta','nomvendedor','nombreAreaproduccion','nombreGiro','nombreTipoEntrega'));
            //return $pdf->download('cotizacion.pdf');
            return $pdf->stream();
        }else{
            dd('Ningún dato disponible en esta consulta.');
        }
    }

    
    public function despachotempconsulta()
    {
        can('consulta-despacho');
        $clientesArray = Cliente::clientesxUsuario();
        $clientes = $clientesArray['clientes'];
        $vendedor_id = $clientesArray['vendedor_id'];
        $sucurArray = $clientesArray['sucurArray'];

        $arrayvend = Vendedor::vendedores(); //Viene del modelo vendedores
        $vendedores1 = $arrayvend['vendedores'];
        $clientevendedorArray = $arrayvend['clientevendedorArray'];
        /*
        $sucurArray = $user->sucursales->pluck('id')->toArray();
        // Filtro solos los clientes que esten asignados a la sucursal y asignado al vendedor logueado
        $clientes = Cliente::select(['cliente.id','cliente.rut','cliente.razonsocial','cliente.direccion','cliente.telefono'])
        ->whereIn('cliente.id' , ClienteSucursal::select(['cliente_sucursal.cliente_id'])
                                ->whereIn('cliente_sucursal.sucursal_id', $sucurArray)
        ->pluck('cliente_sucursal.cliente_id')->toArray())
        ->whereIn('cliente.id',$clientevendedorArray)
        ->get();
        */
        $vendedores = Vendedor::orderBy('id')->where('sta_activo',1)->get();

        $giros = Giro::orderBy('id')->get();
        $areaproduccions = AreaProduccion::orderBy('id')->get();
        $tipoentregas = TipoEntrega::orderBy('id')->get();
        $comunas = NotaVenta::groupBy('comunaentrega_id')
                ->whereNotNull('visto')
                ->select([
                    'comunaentrega_id'
                    ])
                ->get();
        $fechaAct = date("d/m/Y");
        return view('despachoTempconsulta.index', compact('clientes','vendedores','vendedores1','giros','areaproduccions','tipoentregas','comunas','fechaAct'));

    }
    
}


function consulta($request,$band){
    if(empty($request->vendedor_id)){
        $user = Usuario::findOrFail(auth()->id());
        $sql= 'SELECT COUNT(*) AS contador
            FROM vendedor INNER JOIN persona
            ON vendedor.persona_id=persona.id
            INNER JOIN usuario 
            ON persona.usuario_id=usuario.id
            WHERE usuario.id=' . auth()->id();
        $counts = DB::select($sql);
        if($counts[0]->contador>0){
            $vendedor_id=$user->persona->vendedor->id;
            $vendedorcond = "notaventa.vendedor_id=" . $vendedor_id ;
            $clientevendedorArray = ClienteVendedor::where('vendedor_id',$vendedor_id)->pluck('cliente_id')->toArray();
            $sucurArray = $user->sucursales->pluck('id')->toArray();
        }else{
            $vendedorcond = " true ";
            $clientevendedorArray = ClienteVendedor::pluck('cliente_id')->toArray();
        }
    }else{
        $vendedorcond = "notaventa.vendedor_id='$request->vendedor_id'";
    }

    if(empty($request->fechad) or empty($request->fechah)){
        $aux_condFecha = " true";
    }else{
        $fecha = date_create_from_format('d/m/Y', $request->fechad);
        $fechad = date_format($fecha, 'Y-m-d')." 00:00:00";
        $fecha = date_create_from_format('d/m/Y', $request->fechah);
        $fechah = date_format($fecha, 'Y-m-d')." 23:59:59";
        $aux_condFecha = "notaventa.fechahora>='$fechad' and notaventa.fechahora<='$fechah'";
    }
    if(empty($request->rut)){
        $aux_condrut = " true";
    }else{
        $aux_condrut = "cliente.rut='$request->rut'";
    }
    if(empty($request->oc_id)){
        $aux_condoc_id = " true";
    }else{
        $aux_condoc_id = "notaventa.oc_id='$request->oc_id'";
    }
    if(empty($request->giro_id)){
        $aux_condgiro_id = " true";
    }else{
        $aux_condgiro_id = "notaventa.giro_id='$request->giro_id'";
    }
    if(empty($request->areaproduccion_id)){
        $aux_condareaproduccion_id = " true";
    }else{
        $aux_condareaproduccion_id = "categoriaprod.areaproduccion_id='$request->areaproduccion_id'";
    }
    if(empty($request->tipoentrega_id)){
        $aux_condtipoentrega_id = " true";
    }else{
        $aux_condtipoentrega_id = "notaventa.tipoentrega_id='$request->tipoentrega_id'";
    }
    if(empty($request->notaventa_id)){
        $aux_condnotaventa_id = " true";
    }else{
        $aux_condnotaventa_id = "notaventa.id='$request->notaventa_id'";
    }

    if(empty($request->aprobstatus)){
        $aux_aprobstatus = " true";
    }else{
        switch ($request->aprobstatus) {
            case 1:
                $aux_aprobstatus = "notaventa.aprobstatus='0'";
                break;
            case 2:
                $aux_aprobstatus = "notaventa.aprobstatus='$request->aprobstatus'";
                break;    
            case 3:
                $aux_aprobstatus = "(notaventa.aprobstatus='1' or notaventa.aprobstatus='3')";
                break;
            case 4:
                $aux_aprobstatus = "notaventa.aprobstatus='$request->aprobstatus'";
                break;
        }        
    }
    if($band==1){
        $aux_tipoconsulta = " findespacho is null";
    }else{
        $aux_tipoconsulta = " !(findespacho is null)";
    }

    switch ($band) {
        case 1:
            $aux_tipoconsulta = " findespacho is null";
            break;
        case 2:
            $aux_tipoconsulta = " !(findespacho is null)";
            break;    
        case 3:
            $aux_tipoconsulta = " true";
            break;
    }
    $comunaentrega_id = implode ( ',' , json_decode($request->comunaentrega_id));
    if(empty($comunaentrega_id )){
        $comunacond = " true ";
    }else{
        $comunacond = " notaventa.comunaentrega_id in ($comunaentrega_id) ";
    }

    $sql = "SELECT notaventadetalle.notaventa_id as id,notaventa.fechahora,notaventa.cliente_id,notaventa.comuna_id,notaventa.comunaentrega_id,
            notaventa.oc_id,notaventa.anulada,cliente.rut,cliente.razonsocial,aprobstatus,visto,oc_file,inidespacho,findespacho,guiasdespacho,
            tipoentrega.nombre,tipoentrega.icono,
            sum(notaventadetalle.cant) AS cant,sum(notaventadetalle.precioxkilo) AS precioxkilo,
            sum(notaventadetalle.totalkilos) AS totalkilos,sum(notaventadetalle.subtotal) AS subtotal,
            sum(if(areaproduccion.id=1,notaventadetalle.totalkilos,0)) AS pvckg,
            sum(if(areaproduccion.id=2,notaventadetalle.totalkilos,0)) AS cankg,
            sum(if(areaproduccion.id=1,notaventadetalle.subtotal,0)) AS pvcpesos,
            sum(if(areaproduccion.id=2,notaventadetalle.subtotal,0)) AS canpesos,
            sum(notaventadetalle.subtotal) AS totalps
            FROM notaventa INNER JOIN notaventadetalle
            ON notaventa.id=notaventadetalle.notaventa_id
            INNER JOIN producto
            ON notaventadetalle.producto_id=producto.id
            INNER JOIN categoriaprod
            ON categoriaprod.id=producto.categoriaprod_id
            INNER JOIN areaproduccion
            ON areaproduccion.id=categoriaprod.areaproduccion_id
            INNER JOIN cliente
            ON cliente.id=notaventa.cliente_id
            INNER JOIN tipoentrega
            ON tipoentrega.id=notaventa.tipoentrega_id
            WHERE $vendedorcond
            and $aux_condFecha
            and $aux_condrut
            and $aux_condoc_id
            and $aux_condgiro_id
            and $aux_condareaproduccion_id
            and $aux_condtipoentrega_id
            and $aux_condnotaventa_id
            and $aux_aprobstatus
            and !(visto is null) 
            and $aux_tipoconsulta
            and $comunacond
            and notaventa.anulada is null
            and notaventa.deleted_at is null and notaventadetalle.deleted_at is null
            GROUP BY notaventadetalle.notaventa_id,notaventa.fechahora,notaventa.cliente_id,notaventa.comuna_id,notaventa.comunaentrega_id,
            notaventa.oc_id,notaventa.anulada,cliente.rut,cliente.razonsocial,aprobstatus,visto,oc_file,inidespacho,findespacho,guiasdespacho,
            tipoentrega.nombre,tipoentrega.icono;";
    //dd("$sql");
    $datas = DB::select($sql);
    return $datas;
}
