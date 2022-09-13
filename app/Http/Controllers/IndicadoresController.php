<?php

namespace App\Http\Controllers;

use App\Models\AreaProduccion;
use App\Models\CategoriaProd;
use App\Models\Cliente;
use App\Models\ClienteVendedor;
use App\Models\Comuna;
use App\Models\Empresa;
use App\Models\Giro;
use App\Models\Seguridad\Usuario;
use App\Models\Vendedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade as PDF;


class IndicadoresController extends Controller
{
    public function index()
    {
        can('indicador-nv-x-vendedor');
        $clientesArray = Cliente::clientesxUsuario();
        $clientes = $clientesArray['clientes'];
        $vendedor_id = $clientesArray['vendedor_id'];
        $sucurArray = $clientesArray['sucurArray'];

        
        $arrayvend = Vendedor::vendedores(); //Viene del modelo vendedores
        $vendedores1 = $arrayvend['vendedores'];
        $clientevendedorArray = $arrayvend['clientevendedorArray'];
        $giros = Giro::orderBy('id')->get();
        $categoriaprods = CategoriaProd::categoriasxUsuario();
        $vendedores = Vendedor::orderBy('id')->where('sta_activo',1)->get();
        $areaproduccions = AreaProduccion::orderBy('id')->get();
        $fechaServ = ['fecha1erDiaMes' => date("01/m/Y"),
                    'fechaAct' => date("d/m/Y")
                    ];
        $tablashtml['vendedores'] = Vendedor::selectvendedores();
        return view('nvindicadorxvendedor.index', compact('clientes','giros','categoriaprods','vendedores','vendedores1','areaproduccions','fechaServ','tablashtml'));
    }

    public function indexcomercial()
    {
        can('indicador-comercial');
        $clientesArray = Cliente::clientesxUsuario();
        $clientes = $clientesArray['clientes'];
        $vendedor_id = $clientesArray['vendedor_id'];
        $sucurArray = $clientesArray['sucurArray'];
        
        $arrayvend = Vendedor::vendedores(); //Viene del modelo vendedores
        $vendedores1 = $arrayvend['vendedores'];
        $clientevendedorArray = $arrayvend['clientevendedorArray'];
        $giros = Giro::orderBy('id')->get();
        $categoriaprods = CategoriaProd::categoriasxUsuario();
        $vendedores = Vendedor::orderBy('id')->where('sta_activo',1)->get();
        $areaproduccions = AreaProduccion::orderBy('id')->get();
        $fechaServ = ['fecha1erDiaMes' => date("01/m/Y"),
                    'fechaAct' => date("d/m/Y"),
                    'anno' => date('Y')
                    ];
        $tablashtml['vendedores'] = Vendedor::selectvendedores();
        return view('indicadorcomercial.index', compact('clientes','giros','categoriaprods','vendedores','vendedores1','areaproduccions','fechaServ','tablashtml'));
    }

    public function indexgestion()
    {
        can('indicador-gestion');
        $clientesArray = Cliente::clientesxUsuario();
        $clientes = $clientesArray['clientes'];
        $vendedor_id = $clientesArray['vendedor_id'];
        $sucurArray = $clientesArray['sucurArray'];

        
        $arrayvend = Vendedor::vendedores(); //Viene del modelo vendedores
        $vendedores1 = $arrayvend['vendedores'];
        $clientevendedorArray = $arrayvend['clientevendedorArray'];
        $giros = Giro::orderBy('id')->get();
        $categoriaprods = CategoriaProd::categoriasxUsuario();
        $vendedores = Vendedor::orderBy('id')->where('sta_activo',1)->get();
        $areaproduccions = AreaProduccion::orderBy('id')->get();
        $fechaServ = ['fecha1erDiaMes' => date("01/m/Y"),
                    'fechaAct' => date("d/m/Y"),
                    'anno' => date('Y')
                    ];
        $tablashtml['vendedores'] = Vendedor::selectvendedores();
        return view('indicadorgestion.index', compact('clientes','giros','categoriaprods','vendedores','vendedores1','areaproduccions','fechaServ','tablashtml'));
    }

    public function repkilosxtipoentrega(Request $request){
        can('listar-reporte-kilos-x-tipo-entrega');
        $giros = Giro::orderBy('id')->get();
        $categoriaprods = CategoriaProd::categoriasxUsuario();
        $areaproduccions = AreaProduccion::orderBy('id')->get();
        $tablashtml['vendedores'] = Vendedor::selectvendedores();

        $fechaServ = ['fecha1erDiaMes' => date("01/m/Y"),
                    'fechaAct' => date("d/m/Y"),
                    'anno' => date('Y')
                    ];
        return view('repkilosxtipoentrega.index', compact('giros','categoriaprods','areaproduccions','fechaServ','tablashtml'));
    }

    public function reporte(Request $request){
        //dd($request);
        $respuesta = array();
		$respuesta['exito'] = true;
		$respuesta['mensaje'] = "Código encontrado";
		$respuesta['tabla'] = "";
		$respuesta['tabladinero'] = "";
		$respuesta['tablaagruxproducto'] = "";
		$respuesta['tablaareaproduccion'] = "";

        if($request->ajax()){
            //dd($request->idcons);
            $aux_idcons = $request->idcons;
            $request['idcons'] = "1";
            $datasNV = consulta($request); //TODAS LAS NOTAS DE VENTA
            $request['idcons'] = "2";
            $datasFecFC = consultaODcerrada($request); //TODO LO FACTURADO POR FECHA DE FACTURA
            $request['idcons'] = "3";
            $datasFecNV = consultaODcerrada($request); //LO FACTURADO POR FECHA DE NOTA DE VENTA

            if($aux_idcons == "1"){
                $datas = $datasNV;
            }
            if($aux_idcons == "2"){
                $datas = $datasFecFC;
            }
            if($aux_idcons == "3"){
                $datas = $datasFecNV;
            }

            $respuesta['tabla'] .= "<table id='tablacotizacion' name='tablacotizacion' class='table display AllDataTables table-hover table-condensed tablascons' data-page-length='50'>
			<thead>
				<tr>
					<th>Productos</th>";
            $respuesta['tabladinero'] .= "<table id='tablacotizacion' name='tablacotizacion' class='table display AllDataTables table-hover table-condensed tablascons' data-page-length='50'>
            <thead>
                <tr>
                    <th>Productos</th>";
            foreach($datas['vendedores'] as $vendedor){
                $nombreven = $vendedor->nombre;
                $respuesta['tabla'] .= "
                        <th style='text-align:right' class='tooltipsC' title='$nombreven'>$nombreven</th>";
            }
            foreach($datas['vendedores'] as $vendedor){
                $nombreven = $vendedor->nombre;
                $respuesta['tabladinero'] .= "
                        <th style='text-align:right' class='tooltipsC hideshowdinero' title='$nombreven'>$nombreven</th>";
            }
            $respuesta['tabla'] .= "
                    <th style='text-align:right' class='tooltipsC' title='Total'>TOTAL</th>
                </tr>
            </thead>
            <tbody>";

            $respuesta['tabladinero'] .= "
                    <th style='text-align:right' class='tooltipsC hideshowdinero' title='Total'>TOTAL</th>";

            $sql = "SELECT areaproduccion.*
                FROM areaproduccion
                WHERE areaproduccion.id='$request->areaproduccion_id'
                and isnull(areaproduccion.deleted_at)
            ";
            $areaproduccion = DB::select($sql);
            if($areaproduccion[0]->stapromkg == "1"){
                $respuesta['tabladinero'] .= "
                    <th style='text-align:right' class='tooltipsC hskilos' title='Meta comercial KG'>Meta comercial KG</th>
                    <th style='text-align:right' class='tooltipsC hskilos' title='Total Kilos'>KG</th>
                    <th style='text-align:right' class='tooltipsC hskilos' title='Promedio x Kg'>Prom</th>";
            }

            $respuesta['tabladinero'] .= "
                </tr>
            </thead>
            <tbody>";
            $i = 0;
            $totalgeneralfilakg = 0;
            $totalgeneralDinero = 0;
            $precpromediofinal = 0;
            $totalmetacomercialkg = 0;
            foreach($datas['productos'] as $producto){
                $respuesta['tabla'] .= "
                    <tr id='fila$i' name='fila$i' class='btn-accion-tabla tooltipsC'>
                        <td id='producto$i' name='producto$i'>$producto->gru_nombre</td>";
                $respuesta['tabladinero'] .= "
                    <tr id='fila$i' name='fila$i' class='btn-accion-tabla tooltipsC'>
                        <td id='producto$i' name='producto$i'>$producto->gru_nombre</td>";

                foreach($datas['vendedores'] as $vendedor){
                    $aux_encontrado = false;
                    foreach($datas['totales'] as $total){
                        if($total->grupoprod_id == $producto->id and $total->persona_id==$vendedor->id){
                            $aux_encontrado = true;
                            //dd($total->subtotal);
                            $respuesta['tabla'] .= "<td id='vendedor$i' name='vendedor$i' style='text-align:right' data-order='$total->totalkilos'>" . number_format($total->totalkilos, 2, ",", ".") . "</td>";
                            $respuesta['tabladinero'] .= "<td id='vendedord$i' name='vendedord$i' style='text-align:right' class='hideshowdinero' data-order='$total->subtotal'>" . number_format($total->subtotal, 0, ",", ".") . "</td>";
                        } 
                    }
                    if($aux_encontrado==false){
                        $respuesta['tabla'] .= "<td id='vendedor$i' name='vendedor$i' style='text-align:right' data-order='0'>0.00</td>";
                        $respuesta['tabladinero'] .= "<td id='vendedordt$i' name='vendedordt$i' style='text-align:right' class='hideshowdinero' data-order='0'>0</td>";
                    }
                }
                
                $respuesta['tabla'] .= "
                    <td id='totalkilos$i' name='totalkilos$i' style='text-align:right'>" . number_format($producto->totalkilos, 2, ",", ".") . "</td>
                    </tr>";
                $aux_precpromkilo = 0;
                if($producto->totalkilos>0){
                    $aux_precpromkilo = $producto->subtotal/$producto->totalkilos;
                }
                $respuesta['tabladinero'] .= "
                        <td id='totalsubtotal$i' name='totalsubtotal$i' style='text-align:right' class='hideshowdinero' data-order='$producto->subtotal'>" . number_format($producto->subtotal, 0, ",", ".") . "</td>";
                if($areaproduccion[0]->stapromkg == "1"){
                    $respuesta['tabladinero'] .= "
                        <td style='text-align:right' data-order='$producto->metacomerkg' class=' hskilos'>" . number_format($producto->metacomerkg, 2, ",", ".") . "</td>
                        <td style='text-align:right' data-order='$producto->totalkilos' class=' hskilos'>" . number_format($producto->totalkilos, 2, ",", ".") . "</td>
                        <td style='text-align:right' data-order='$aux_precpromkilo' class=' hskilos'>" . number_format($aux_precpromkilo, 2, ",", ".") . "</td>";
                }
                $respuesta['tabladinero'] .= "
                    </tr>";
                $i++;
                $totalgeneralfilakg += $producto->totalkilos;
                $totalgeneralDinero += $producto->subtotal;
                $totalmetacomercialkg += $producto->metacomerkg;
            }
            if($totalgeneralfilakg > 0){
                $precpromediofinal = $totalgeneralDinero / $totalgeneralfilakg;
            }
            $respuesta['tabla'] .= "
            </tbody>
                <tfoot>
                    <tr>
                        <th>TOTAL KG</th>";
            $respuesta['tabladinero'] .= "
            </tbody>
                <tfoot>
                    <tr>
                        <th>TOTAL</th>";
            

            foreach($datas['vendedores'] as $vendedor){
                $respuesta['tabla'] .= "
                    <th style='text-align:right'>". number_format($vendedor->totalkilos, 2, ",", ".") ."</th>";
                $respuesta['tabladinero'] .= "
                    <th style='text-align:right' class='hideshowdinero'>". number_format($vendedor->subtotal, 0, ",", ".") ."</th>";
            }
            $respuesta['tabla'] .= "
                        <th style='text-align:right'>". number_format($totalgeneralfilakg, 2, ",", ".") ."</th>
                    </tr>
                </tfoot>
            </table>";
            $respuesta['tabladinero'] .= "
                        <th style='text-align:right' class='hideshowdinero'>". number_format($totalgeneralDinero, 0, ",", ".") ."</th>";
            if($areaproduccion[0]->stapromkg == "1"){
                $respuesta['tabladinero'] .= "
                    <th style='text-align:right' class=' hskilos'>". number_format($totalmetacomercialkg, 2, ",", ".") ."</th>
                    <th style='text-align:right' class=' hskilos'>". number_format($totalgeneralfilakg, 2, ",", ".") ."</th>
                    <th style='text-align:right' class=' hskilos'>". number_format($precpromediofinal, 2, ",", ".") ."</th>";
            }
            $respuesta['tabladinero'] .= "
                    </tr>
                </tfoot>
            </table>";

            $respuesta['nombre'] = array_column($datas['vendedores'], 'nombre');
            $respuesta['totalkilos'] = array_column($datas['vendedores'], 'totalkilos');
            $i = 0;
            foreach($respuesta['totalkilos'] as &$kilos){
                $kilos = round($kilos,2);
                $kilos1 = round(($kilos / $totalgeneralfilakg) * 100,2);
                $respuesta['nombre'][$i] .= " " . number_format($kilos1, 2, ",", ".") . "%";
                $i++;
            }
            $respuesta['nombredinero'] = array_column($datas['vendedores'], 'nombre');
            $respuesta['totaldinero'] = array_column($datas['vendedores'], 'subtotal');
            $i = 0;
            foreach($respuesta['totaldinero'] as &$subtotaldinero){
                $subtotaldinero = round($subtotaldinero,2);
                $subtotaldinero1 = 0;
                if($totalgeneralDinero > 0){
                    $subtotaldinero1 = round(($subtotaldinero / $totalgeneralDinero) * 100,2);
                }
                $respuesta['nombredinero'][$i] .= " " . number_format($subtotaldinero1, 2, ",", ".") . "%";
                $i++;
            }

            $i = 0;
            //$datasFecNV['vendedores'][$i] += [ "pendiente" => 3000000, "three" => 3 ];
            //$datasFecNV['vendedores'][$i]->pendientekg = 20000;
            //dd($datasFecNV['vendedores'][$i]);
            //array_push($datasFecNV['vendedores'][$i], [ "pendiente" => 3000000, "three" => 3 ]);
            //dd($datasFecFC['vendedores']);
            foreach($datasNV['vendedores'] as $datasNVs){
                $aux_totalfackg = 0;
                $aux_totalfacdin = 0;
                foreach($datasFecNV['vendedores'] as $datasFecNVs){
                    if($datasFecNVs->id == $datasNVs->id){
                        $aux_totalfackg = $datasFecNVs->totalkilos;
                        $aux_totalfacdin = $datasFecNVs->subtotal;    
                    }
                }
                $datasNV['vendedores'][$i]->pendientekg = $datasNVs->totalkilos - $aux_totalfackg;
                $datasNV['vendedores'][$i]->pendienteDinero = $datasNVs->subtotal - $aux_totalfacdin;
                $i++;
            }


            $respuesta['nombrebar'] = array_column($datasNV['vendedores'], 'nombre');
            $respuesta['totalkilosbarNV'] = array_column($datasNV['vendedores'], 'totalkilos');
            $respuesta['totalkilosbarFecFC'] = array_column($datasFecFC['vendedores'], 'totalkilos');
            $respuesta['totalkilosbarFecNV'] = array_column($datasFecNV['vendedores'], 'totalkilos');
            $respuesta['totalkilosbarNVPendiente'] = array_column($datasNV['vendedores'], 'pendientekg');

            $respuesta['totaldinerobarNV'] = array_column($datasNV['vendedores'], 'subtotal');
            $respuesta['totaldineroFecFC'] = array_column($datasFecFC['vendedores'], 'subtotal');
            $respuesta['totaldineroFecNV'] = array_column($datasFecNV['vendedores'], 'subtotal');
            $respuesta['totaldineroNVPendiente'] = array_column($datasNV['vendedores'], 'pendienteDinero');

            //TABLA TOTALES POR PRODUCTO
            $respuesta['tablaagruxproducto'] .= "<table id='tablaagruxproducto' name='tablaagruxproducto' class='table display AllDataTables table-hover table-condensed tablascons' data-page-length='50'>
                <thead>
                    <tr>
                        <th>Productos</th>
                        <th>Diametro</th>
                        <th>Longitud</th>
                        <th>Clase</th>
                        <th>PesoUnid</th>
                        <th>TU</th>
                        <th>Color</th>
                        <th style='text-align:right'>Unid</th>
                        <th style='text-align:right'>KG</th>
                        <th style='text-align:right'>Prom Unit</th>
                        <th style='text-align:right'>Prom Kilo</th>
                    </tr>
                </thead>
                <tbody>";
            $aux_sumpromkilo = 0;
            $totalgeneralfilakg = 0;
            foreach($datas['agruxproducto'] as $producto){
                $aux_promunit = 0;
                if($producto->cant>0){
                    $aux_promunit = $producto->subtotal/$producto->cant;
                }
                $aux_promkilo = 0;
                if($producto->totalkilos>0){
                    $aux_promkilo = $producto->subtotal/$producto->totalkilos;
                }
                $aux_sumpromkilo += $aux_promkilo;
                $respuesta['tablaagruxproducto'] .= "
                    <tr id='fila$i' name='fila$i' class='btn-accion-tabla tooltipsC'>
                        <td>$producto->nombre</td>
                        <td>$producto->diametro</td>
                        <td>$producto->long</td>
                        <td>$producto->cla_nombre</td>
                        <td>$producto->peso</td>
                        <td>$producto->tipounion</td>
                        <td>$producto->color</td>
                        <td style='text-align:right' data-order='$producto->cant' data-search='$producto->cant'>$producto->cant</td>
                        <td style='text-align:right' data-order='$producto->totalkilos' data-search='$producto->totalkilos'>" . number_format($producto->totalkilos, 2, ",", ".") . "</td>
                        <td style='text-align:right' data-order='$aux_promunit' data-search='$aux_promunit'>" . number_format($aux_promunit, 2, ",", ".") . "</td>
                        <td style='text-align:right' data-order='$aux_promkilo' data-search='$aux_promkilo'>" . number_format($aux_promkilo, 2, ",", ".") . "</td>
                    </tr>";
                    $totalgeneralfilakg += $producto->totalkilos;
            }
            $aux_promkilogen = 0;
            if(count($datas['agruxproducto']) > 0){
                $aux_promkilogen = $aux_sumpromkilo / count($datas['agruxproducto']);
            }
            $respuesta['tablaagruxproducto'] .= "
                </tbody>
                <tfoot>
                    <tr>
                        <th>TOTAL</th>
                        <th colspan='8' style='text-align:right'>". number_format($totalgeneralfilakg, 2, ",", ".") ."</th>
                        <th></th>
                        <th style='text-align:right'>". number_format($aux_promkilogen, 2, ",", ".") ."</th>
                    </tr>
                </tfoot>
            </table>";
            
            $respuesta['tablaagruxproductomargen'] = tablaAgruxProductoMargen($datas['agruxproducto']);

            $respuesta['productos'] = $datas['productos'];

            //TABLA POR AREA DE PRODUCCION
            $respuesta['tablaareaproduccion'] .= "<table id='tablacotizacion' name='tablacotizacion' class='table display AllDataTables table-hover table-condensed tablascons' data-page-length='50'>
			<thead>
				<tr>
					<th>Area Prod</th>
                    <th style='text-align:right'>Kg Facturado<br>al dia $request->fechah</th>
                    <th style='text-align:right'>Kg Facturado<br>Acumulado</th>
                    <th style='text-align:right'>$</th>
                    <th style='text-align:right'>Precio<br>Promedio Kg</th>
                </tr>
            </thead>
            <tbody>";
            $aux_totalkiloshoy = 0;
            $aux_totalkgfacacum = 0;
            $aux_totalmonto = 0;
            foreach($datas['areaproduccion'] as $areaproduccion){
                $aux_promkilo = 0;
                if($areaproduccion->totalkilos>0){
                    $aux_promkilo = $areaproduccion->subtotal/$areaproduccion->totalkilos;
                }
                $aux_kiloshoy = 0;
                foreach($datas['areaproduccionhoy'] as $areaproduccionhoy){
                    if($areaproduccionhoy->id == $areaproduccion->id){
                        $aux_kiloshoy = $areaproduccionhoy->totalkilos;
                    }  
                }
                $respuesta['tablaareaproduccion'] .= "
                    <tr id='fila$i' name='fila$i' class='btn-accion-tabla tooltipsC'>
                        <td data-order='$areaproduccion->id' >$areaproduccion->nombre</td>
                        <td style='text-align:right' data-order='$aux_kiloshoy' data-search='$aux_kiloshoy'>" . number_format($aux_kiloshoy, 2, ",", ".") . "</td>
                        <td style='text-align:right' data-order='$areaproduccion->totalkilos' data-search='$areaproduccion->totalkilos'>" . number_format($areaproduccion->totalkilos, 2, ",", ".") . "</td>
                        <td style='text-align:right' data-order='$areaproduccion->subtotal' data-search='$areaproduccion->subtotal'>" . number_format($areaproduccion->subtotal, 0, ",", ".") . "</td>
                        <td style='text-align:right' data-order='$aux_promkilo' data-search='$aux_promkilo'>" . number_format($aux_promkilo, 2, ",", ".") . "</td>
                    </tr>";
                        //$aux_totalfacdia += 0;
                $aux_totalkgfacacum += $areaproduccion->totalkilos;
                $aux_totalmonto += $areaproduccion->subtotal;
                $aux_totalkiloshoy += $aux_kiloshoy;    
            }
            $aux_promkilogen = 0;
            if($aux_totalkgfacacum > 0){
                $aux_promkilogen = $aux_totalmonto / $aux_totalkgfacacum;
            }
            $respuesta['tablaareaproduccion'] .= "
                </tbody>
                <tfoot>
                    <tr>
                        <th>TOTAL</th>
                        <th style='text-align:right'>". number_format($aux_totalkiloshoy, 2, ",", ".") ."</th>
                        <th style='text-align:right'>". number_format($aux_totalkgfacacum, 2, ",", ".") ."</th>
                        <th style='text-align:right'>". number_format($aux_totalmonto, 0, ",", ".") ."</th>
                        <!--<th style='text-align:right'>". number_format($aux_promkilogen, 2, ",", ".") ."</th>-->
                        <th></th>
                    </tr>
                </tfoot>
            </table>";

            $respuesta['vetasxmesmeses'] = array_column($datas['ventasxmes'], 'mes');
            $respuesta['ventasxmeskilos'] = array_column($datas['ventasxmes'], 'totalkilos');
            $respuesta['ventasxmesdinero'] = array_column($datas['ventasxmes'], 'subtotal');

            return $respuesta;
        }
    }

    public function reportecomercial(Request $request){
        //dd($request);
        $respuesta = array();
		$respuesta['exito'] = true;
		$respuesta['mensaje'] = "Código encontrado";
		$respuesta['tabla'] = "";
		$respuesta['tabladinero'] = "";
		$respuesta['tablaagruxproducto'] = "";
		$respuesta['tablaareaproduccion'] = "";

        if($request->ajax()){
            //dd($request->idcons);
            $aux_idcons = $request->idcons;
            $request['idcons'] = "1";
            $datasNV = consulta($request); //TODAS LAS NOTAS DE VENTA
            $request['idcons'] = "2";
            $datasFecFC = consultaODcerrada($request); //TODO LO FACTURADO POR FECHA DE FACTURA
            $request['idcons'] = "3";
            $datasFecNV = consultaODcerrada($request); //LO FACTURADO POR FECHA DE NOTA DE VENTA

            if($aux_idcons == "1"){
                $datas = $datasNV;
            }
            if($aux_idcons == "2"){
                $datas = $datasFecFC;
            }
            if($aux_idcons == "3"){
                $datas = $datasFecNV;
            }
            if(count($datas['totales']) <= 0){ //SI LA TABLA TOTALES VIENE VACIA NO ES NECESARIO HACER TODO LO DEMAS, ROMPO AQUI Y ENVIO REPUESTA VACIO
                return $respuesta;
            }
            $respuesta['tabla'] .= "<table id='tablacotizacion' name='tablacotizacion' class='table display AllDataTables table-hover table-condensed tablascons' data-page-length='50'>
			<thead>
				<tr>
					<th>Productos</th>";
            $respuesta['tabladinero'] .= "<table id='tablacotizacion' name='tablacotizacion' class='table display AllDataTables table-hover table-condensed tablascons' data-page-length='50'>
            <thead>
                <tr>
                    <th>Productos</th>";
            foreach($datas['vendedores'] as $vendedor){
                $nombreven = $vendedor->nombre;
                $respuesta['tabla'] .= "
                        <th style='text-align:right' class='tooltipsC' title='$nombreven'>$nombreven</th>";
            }
            foreach($datas['vendedores'] as $vendedor){
                $nombreven = $vendedor->nombre;
                $respuesta['tabladinero'] .= "
                        <th style='text-align:right' class='tooltipsC hideshowdinero' title='$nombreven'>$nombreven</th>";
            }
            $respuesta['tabla'] .= "
                    <th style='text-align:right' class='tooltipsC' title='Total'>TOTAL</th>
                </tr>
            </thead>
            <tbody>";

            $respuesta['tabladinero'] .= "
                    <th style='text-align:right' class='tooltipsC hideshowdinero' title='Total'>TOTAL</th>";

            $sql = "SELECT areaproduccion.*
                FROM areaproduccion
                WHERE areaproduccion.id='$request->areaproduccion_id'
                and isnull(areaproduccion.deleted_at)
            ";
            $areaproduccion = DB::select($sql);
            if($areaproduccion[0]->stapromkg == "1"){
                $respuesta['tabladinero'] .= "
                    <th style='text-align:right' class='tooltipsC hskilos' title='Meta comercial KG'>Meta comercial KG</th>
                    <th style='text-align:right' class='tooltipsC hskilos' title='Total Kilos'>KG</th>
                    <th style='text-align:right' class='tooltipsC hskilos' title='Promedio x Kg'>Prom</th>";
            }

            $respuesta['tabladinero'] .= "
                </tr>
            </thead>
            <tbody>";
            $i = 0;
            $totalgeneralfilakg = 0;
            $totalgeneralDinero = 0;
            $precpromediofinal = 0;
            $totalmetacomercialkg = 0;
            foreach($datas['productos'] as $producto){
                $respuesta['tabla'] .= "
                    <tr id='fila$i' name='fila$i' class='btn-accion-tabla tooltipsC'>
                        <td id='producto$i' name='producto$i'>$producto->gru_nombre</td>";
                $respuesta['tabladinero'] .= "
                    <tr id='fila$i' name='fila$i' class='btn-accion-tabla tooltipsC'>
                        <td id='producto$i' name='producto$i'>$producto->gru_nombre</td>";

                foreach($datas['vendedores'] as $vendedor){
                    $aux_encontrado = false;
                    foreach($datas['totales'] as $total){
                        if($total->grupoprod_id == $producto->id and $total->persona_id==$vendedor->id){
                            $aux_encontrado = true;
                            //dd($total->subtotal);
                            $respuesta['tabla'] .= "<td id='vendedor$i' name='vendedor$i' style='text-align:right' data-order='$total->totalkilos'>" . number_format($total->totalkilos, 2, ",", ".") . "</td>";
                            $respuesta['tabladinero'] .= "<td id='vendedord$i' name='vendedord$i' style='text-align:right' class='hideshowdinero' data-order='$total->subtotal'>" . number_format($total->subtotal, 0, ",", ".") . "</td>";
                        } 
                    }
                    if($aux_encontrado==false){
                        $respuesta['tabla'] .= "<td id='vendedor$i' name='vendedor$i' style='text-align:right' data-order='0'>0.00</td>";
                        $respuesta['tabladinero'] .= "<td id='vendedordt$i' name='vendedordt$i' style='text-align:right' class='hideshowdinero' data-order='0'>0</td>";
                    }
                }
                
                $respuesta['tabla'] .= "
                    <td id='totalkilos$i' name='totalkilos$i' style='text-align:right'>" . number_format($producto->totalkilos, 2, ",", ".") . "</td>
                    </tr>";
                $aux_precpromkilo = 0;
                if($producto->totalkilos>0){
                    $aux_precpromkilo = $producto->subtotal/$producto->totalkilos;
                }
                $respuesta['tabladinero'] .= "
                        <td id='totalsubtotal$i' name='totalsubtotal$i' style='text-align:right' class='hideshowdinero' data-order='$producto->subtotal'>" . number_format($producto->subtotal, 0, ",", ".") . "</td>";
                if($areaproduccion[0]->stapromkg == "1"){
                    $respuesta['tabladinero'] .= "
                        <td style='text-align:right' data-order='$producto->metacomerkg' class=' hskilos'>" . number_format($producto->metacomerkg, 2, ",", ".") . "</td>
                        <td style='text-align:right' data-order='$producto->totalkilos' class=' hskilos'>" . number_format($producto->totalkilos, 2, ",", ".") . "</td>
                        <td style='text-align:right' data-order='$aux_precpromkilo' class=' hskilos'>" . number_format($aux_precpromkilo, 2, ",", ".") . "</td>";
                }
                $respuesta['tabladinero'] .= "
                    </tr>";
                $i++;
                $totalgeneralfilakg += $producto->totalkilos;
                $totalgeneralDinero += $producto->subtotal;
                $totalmetacomercialkg += $producto->metacomerkg;
            }
            if($totalgeneralfilakg > 0){
                $precpromediofinal = $totalgeneralDinero / $totalgeneralfilakg;
            }
            $respuesta['tabla'] .= "
            </tbody>
                <tfoot>
                    <tr>
                        <th>TOTAL KG</th>";
            $respuesta['tabladinero'] .= "
            </tbody>
                <tfoot>
                    <tr>
                        <th>TOTAL</th>";
            

            foreach($datas['vendedores'] as $vendedor){
                $respuesta['tabla'] .= "
                    <th style='text-align:right'>". number_format($vendedor->totalkilos, 2, ",", ".") ."</th>";
                $respuesta['tabladinero'] .= "
                    <th style='text-align:right' class='hideshowdinero'>". number_format($vendedor->subtotal, 0, ",", ".") ."</th>";
            }
            $respuesta['tabla'] .= "
                        <th style='text-align:right'>". number_format($totalgeneralfilakg, 2, ",", ".") ."</th>
                    </tr>
                </tfoot>
            </table>";
            $respuesta['tabladinero'] .= "
                        <th style='text-align:right' class='hideshowdinero'>". number_format($totalgeneralDinero, 0, ",", ".") ."</th>";
            if($areaproduccion[0]->stapromkg == "1"){
                $respuesta['tabladinero'] .= "
                    <th style='text-align:right' class=' hskilos'>". number_format($totalmetacomercialkg, 2, ",", ".") ."</th>
                    <th style='text-align:right' class=' hskilos'>". number_format($totalgeneralfilakg, 2, ",", ".") ."</th>
                    <th style='text-align:right' class=' hskilos'>". number_format($precpromediofinal, 2, ",", ".") ."</th>";
            }
            $respuesta['tabladinero'] .= "
                    </tr>
                </tfoot>
            </table>";

            $respuesta['nombre'] = array_column($datas['vendedores'], 'nombre');
            $respuesta['totalkilos'] = array_column($datas['vendedores'], 'totalkilos');
            $i = 0;
            foreach($respuesta['totalkilos'] as &$kilos){
                $kilos = round($kilos,2);
                $kilos1 = 0;
                if($totalgeneralfilakg > 0){
                    $kilos1 = round(($kilos / $totalgeneralfilakg) * 100,2);
                }
                $respuesta['nombre'][$i] .= " " . number_format($kilos1, 2, ",", ".") . "%";
                $i++;
            }
            $respuesta['nombredinero'] = array_column($datas['vendedores'], 'nombre');
            $respuesta['totaldinero'] = array_column($datas['vendedores'], 'subtotal');
            $i = 0;
            foreach($respuesta['totaldinero'] as &$subtotaldinero){
                $subtotaldinero = round($subtotaldinero,2);
                $subtotaldinero1 = 0;
                if($totalgeneralDinero > 0){
                    $subtotaldinero1 = round(($subtotaldinero / $totalgeneralDinero) * 100,2);
                }
                $respuesta['nombredinero'][$i] .= " " . number_format($subtotaldinero1, 2, ",", ".") . "%";
                $i++;
            }

            $i = 0;
            //$datasFecNV['vendedores'][$i] += [ "pendiente" => 3000000, "three" => 3 ];
            //$datasFecNV['vendedores'][$i]->pendientekg = 20000;
            //dd($datasFecNV['vendedores'][$i]);
            //array_push($datasFecNV['vendedores'][$i], [ "pendiente" => 3000000, "three" => 3 ]);
            //dd($datasFecFC['vendedores']);
            foreach($datasNV['vendedores'] as $datasNVs){
                $aux_totalfackg = 0;
                $aux_totalfacdin = 0;
                foreach($datasFecNV['vendedores'] as $datasFecNVs){
                    if($datasFecNVs->id == $datasNVs->id){
                        $aux_totalfackg = $datasFecNVs->totalkilos;
                        $aux_totalfacdin = $datasFecNVs->subtotal;    
                    }
                }
                $datasNV['vendedores'][$i]->pendientekg = $datasNVs->totalkilos - $aux_totalfackg;
                $datasNV['vendedores'][$i]->pendienteDinero = $datasNVs->subtotal - $aux_totalfacdin;
                $i++;
            }


            $respuesta['nombrebar'] = array_column($datasNV['vendedores'], 'nombre');
            $respuesta['totalkilosbarNV'] = array_column($datasNV['vendedores'], 'totalkilos');
            $respuesta['totalkilosbarFecFC'] = array_column($datasFecFC['vendedores'], 'totalkilos');
            $respuesta['totalkilosbarFecNV'] = array_column($datasFecNV['vendedores'], 'totalkilos');
            $respuesta['totalkilosbarNVPendiente'] = array_column($datasNV['vendedores'], 'pendientekg');

            $respuesta['totaldinerobarNV'] = array_column($datasNV['vendedores'], 'subtotal');
            $respuesta['totaldineroFecFC'] = array_column($datasFecFC['vendedores'], 'subtotal');
            $respuesta['totaldineroFecNV'] = array_column($datasFecNV['vendedores'], 'subtotal');
            $respuesta['totaldineroNVPendiente'] = array_column($datasNV['vendedores'], 'pendienteDinero');

            //TABLA TOTALES POR PRODUCTO
            $respuesta['tablaagruxproducto'] .= "<table id='tablaagruxproducto' name='tablaagruxproducto' class='table display AllDataTables table-hover table-condensed tablascons' data-page-length='50'>
                <thead>
                    <tr>
                        <th>Productos</th>
                        <th>cod</th>
                        <th>Diametro</th>
                        <th>Longitud</th>
                        <th>Clase</th>
                        <th>PesoUnid</th>
                        <th>TU</th>
                        <th>Color</th>
                        <th style='text-align:right'>Unid</th>
                        <th style='text-align:right'>KG</th>
                        <th style='text-align:right'>Prom Unit</th>
                        <th style='text-align:right'>Prom Kilo</th>
                    </tr>
                </thead>
                <tbody>";
            $aux_sumpromkilo = 0;
            $totalgeneralfilakg = 0;
            foreach($datas['agruxproducto'] as $producto){
                $aux_promunit = 0;
                if($producto->cant>0){
                    $aux_promunit = $producto->subtotal/$producto->cant;
                }
                $aux_promkilo = 0;
                if($producto->totalkilos>0){
                    $aux_promkilo = $producto->subtotal/$producto->totalkilos;
                }
                $aux_sumpromkilo += $aux_promkilo;
                $respuesta['tablaagruxproducto'] .= "
                    <tr id='fila$i' name='fila$i' class='btn-accion-tabla tooltipsC'>
                        <td>$producto->nombre</td>
                        <td>$producto->producto_id</td>
                        <td>$producto->diametro</td>
                        <td>$producto->long</td>
                        <td>$producto->cla_nombre</td>
                        <td>$producto->peso</td>
                        <td>$producto->tipounion</td>
                        <td>$producto->color</td>
                        <td style='text-align:right' data-order='$producto->cant' data-search='$producto->cant'>$producto->cant</td>
                        <td style='text-align:right' data-order='$producto->totalkilos' data-search='$producto->totalkilos'>" . number_format($producto->totalkilos, 2, ",", ".") . "</td>
                        <td style='text-align:right' data-order='$aux_promunit' data-search='$aux_promunit'>" . number_format($aux_promunit, 2, ",", ".") . "</td>
                        <td style='text-align:right' data-order='$aux_promkilo' data-search='$aux_promkilo'>" . number_format($aux_promkilo, 2, ",", ".") . "</td>
                    </tr>";
                    $totalgeneralfilakg += $producto->totalkilos;
            }
            $aux_promkilogen = 0;
            if(count($datas['agruxproducto']) > 0){
                $aux_promkilogen = $aux_sumpromkilo / count($datas['agruxproducto']);
            }
            $respuesta['tablaagruxproducto'] .= "
                </tbody>
                <tfoot>
                    <tr>
                        <th>TOTAL</th>
                        <th colspan='9' style='text-align:right'>". number_format($totalgeneralfilakg, 2, ",", ".") ."</th>
                        <th></th>
                        <th style='text-align:right'>". number_format($aux_promkilogen, 2, ",", ".") ."</th>
                    </tr>
                </tfoot>
            </table>";
            
            $respuesta['tablaagruxproductomargen'] = tablaAgruxProductoMargen($datas['agruxproducto']);

            $respuesta['productos'] = $datas['productos'];

            //TABLA POR AREA DE PRODUCCION
            $respuesta['tablaareaproduccion'] .= "<table id='tablacotizacion' name='tablacotizacion' class='table display AllDataTables table-hover table-condensed tablascons' data-page-length='50'>
			<thead>
				<tr>
					<th>Area Prod</th>
                    <th style='text-align:right'>Kg Facturado<br>al dia $request->fechah</th>
                    <th style='text-align:right'>Kg Facturado<br>Acumulado</th>
                    <th style='text-align:right'>Precio<br>Promedio Kg</th>
                </tr>
            </thead>
            <tbody>";
            $aux_totalkiloshoy = 0;
            $aux_totalkgfacacum = 0;
            $aux_totalmonto = 0;
            foreach($datas['areaproduccion'] as $areaproduccion){
                $aux_promkilo = 0;
                if($areaproduccion->totalkilos > 0){
                    if($areaproduccion->totalkilos>0){
                        $aux_promkilo = $areaproduccion->subtotal/$areaproduccion->totalkilos;
                    }
                    $aux_kiloshoy = 0;
                    foreach($datas['areaproduccionhoy'] as $areaproduccionhoy){
                        if($areaproduccionhoy->id == $areaproduccion->id){
                            $aux_kiloshoy = $areaproduccionhoy->totalkilos;
                        }  
                    }
    
                    $respuesta['tablaareaproduccion'] .= "
                        <tr id='fila$i' name='fila$i' class='btn-accion-tabla tooltipsC'>
                            <td data-order='$areaproduccion->id' >$areaproduccion->nombre</td>
                            <td style='text-align:right' data-order='$aux_kiloshoy' data-search='$aux_kiloshoy'>" . number_format($aux_kiloshoy, 2, ",", ".") . "</td>
                            <td style='text-align:right' data-order='$areaproduccion->totalkilos' data-search='$areaproduccion->totalkilos'>" . number_format($areaproduccion->totalkilos, 2, ",", ".") . "</td>
                            <td style='text-align:right' data-order='$aux_promkilo' data-search='$aux_promkilo'>" . number_format($aux_promkilo, 2, ",", ".") . "</td>
                        </tr>";
                            //$aux_totalfacdia += 0;
                    $aux_totalkgfacacum += $areaproduccion->totalkilos;
                    $aux_totalmonto += $areaproduccion->subtotal;
                    $aux_totalkiloshoy += $aux_kiloshoy;    
                }
            }
            $aux_promkilogen = 0;
            if($aux_totalkgfacacum > 0){
                $aux_promkilogen = $aux_totalmonto / $aux_totalkgfacacum;
            }
            $respuesta['tablaareaproduccion'] .= "
                </tbody>
                <tfoot>
                    <tr>
                        <th>TOTAL</th>
                        <th style='text-align:right'>". number_format($aux_totalkiloshoy, 2, ",", ".") ."</th>
                        <th style='text-align:right'>". number_format($aux_totalkgfacacum, 2, ",", ".") ."</th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>";

            $respuesta['vetasxmesmeses'] = array_column($datas['ventasxmes'], 'mes');
            $respuesta['ventasxmeskilos'] = array_column($datas['ventasxmes'], 'totalkilos');
            $respuesta['ventasxmesdinero'] = array_column($datas['ventasxmes'], 'subtotal');



            /******Tabla Ventas Mensual por Unidad de Produccion***** */
            /******************************************************** */
            $respuesta['tablaventasmesap'] = "<table id='tablaventasmesareaprod' name='tablaventasmesareaprod' class='table display AllDataTables table-hover table-condensed tablascons' data-page-length='50'>
            <thead>
                <tr>
                    <th>Area Prod</th>";
            //dd($datas['ventasxmes']);
            foreach($datas['ventasxmes'] as $ventasxmes){
                $mes = $ventasxmes->mes;
                $respuesta['tablaventasmesap'] .= "
                        <th style='text-align:right' class='tooltipsC' title='$mes'>" . ucfirst($mes) . "</th>";
            }
            $respuesta['tablaventasmesap'] .= "
                </tr>
            </thead>
            <tbody>";
            foreach($datas['areaproduccion'] as $areaproduccion){
                if($areaproduccion->id != 3){
                    $respuesta['tablaventasmesap'] .= "
                    <tr class='btn-accion-tabla tooltipsC'>
                        <td data-order='$areaproduccion->id'>$areaproduccion->nombre</td>";
                    foreach($datas['ventasxmes'] as $ventasxmes){
                        $aux_valor = "0";
                        foreach($datas['ventasareaprodxmes'] as $ventasareaprodxmes){
                            if( ($ventasxmes->annomes == $ventasareaprodxmes->annomes) and ($areaproduccion->id == $ventasareaprodxmes->areaproduccion_id) ){
                                $aux_valor = number_format(round($ventasareaprodxmes->totalkilos,0), 0, ",", ".");
                                break;
                            }
                        }
                        $respuesta['tablaventasmesap'] .= "
                        <td style='text-align:right'>" . $aux_valor . "</td>";
                    }
                    $respuesta['tablaventasmesap'] .= "
                    </tr>";
                }
            }
            $respuesta['tablaventasmesap'] .= "
            </tbody>
            </table>";
            /*************************************** */
            //dd($respuesta['tablaventasmesap']);
            
            
            /* GRAFICO VENTAS MENSUAL POR AREA DE PRODUCCION*/
            /********************************************** */
            $array_ventasmesxareaprod = [];
            $array_vectorTemp[] = 'Mes';
            foreach($datas['areaproduccion'] as $areaproduccion){
                if($areaproduccion->id != 3){
                    $array_vectorTemp[] = $areaproduccion->nombre;
                }
            }
            $array_ventasmesxareaprod[] = $array_vectorTemp;
            $array_vectorTemp = [];
            foreach($datas['ventasxmes'] as $ventasxmes){
                $array_vectorTemp[] = ucfirst($ventasxmes->mes);
                $i = 1;
                foreach($datas['areaproduccion'] as $areaproduccion){
                    if($areaproduccion->id != 3){
                        $array_vectorTemp[] = 0.00;
                        foreach($datas['ventasareaprodxmes'] as $ventasareaprodxmes){
                            if($ventasxmes->annomes == $ventasareaprodxmes->annomes and $areaproduccion->id == $ventasareaprodxmes->areaproduccion_id ){
                                $array_vectorTemp[$i] = round($ventasareaprodxmes->totalkilos,0);
                                break;
                            }
                        }
                        $i++;    
                    }
                }
                $array_ventasmesxareaprod[] = $array_vectorTemp;
                $array_vectorTemp = [];
            }
            $respuesta['ventasmesxareaprod'] = $array_ventasmesxareaprod;
            //dd($respuesta['ventasmesxareaprod']);
            /********************************************** */

            /******Tabla Ventas Mensual PVC y precio promedio venta ***** */
            /************************************************************ */
            $respuesta['tablaventaPVC'] = "<table id='tablaventaPVC' name='tablaventaPVC' class='table display AllDataTables table-hover table-condensed tablascons' data-page-length='50'>
            <thead>
                <tr>
                    <th>Area Prod</th>";
            foreach($datas['ventasxmes'] as $ventasxmes){
                $mes = $ventasxmes->mes;
                $respuesta['tablaventaPVC'] .= "
                        <th style='text-align:right' class='tooltipsC' title='$mes'>" . ucfirst($mes) . "</th>";
            }
            $respuesta['tablaventaPVC'] .= "
                </tr>
            </thead>
            <tbody>";
            foreach($datas['areaproduccion'] as $areaproduccion){
                if($areaproduccion->id == 1){
                    $respuesta['tablaventaPVC'] .= "
                    <tr class='btn-accion-tabla tooltipsC'>
                        <td data-order='$areaproduccion->id'>$areaproduccion->nombre</td>";
                    foreach($datas['ventasxmes'] as $ventasxmes){
                        $aux_valor = "0";
                        foreach($datas['ventasareaprodxmes'] as $ventasareaprodxmes){
                            if(($ventasxmes->annomes == $ventasareaprodxmes->annomes) and ($areaproduccion->id == $ventasareaprodxmes->areaproduccion_id) ){
                                $aux_valor = number_format(round($ventasareaprodxmes->totalkilos,0), 0, ",", ".");
                                break;
                            }
                        }
                        $respuesta['tablaventaPVC'] .= "
                        <td style='text-align:right'>" . $aux_valor . "</td>";
                    }
                    $respuesta['tablaventaPVC'] .= "
                    </tr>";
                    $respuesta['tablaventaPVC'] .= "
                    <tr class='btn-accion-tabla tooltipsC'>
                        <td data-order='Precio Kg($)'>Precio Kg($)</td>";
                    foreach($datas['ventasxmes'] as $ventasxmes){
                        $aux_valor = "0";
                        foreach($datas['ventasareaprodxmes'] as $ventasareaprodxmes){
                            if(($ventasxmes->annomes == $ventasareaprodxmes->annomes) and ($areaproduccion->id == $ventasareaprodxmes->areaproduccion_id) ){
                                $aux_valor = 0;
                                if($ventasareaprodxmes->totalkilos > 0){
                                    $aux_valor = number_format(round($ventasareaprodxmes->subtotal / $ventasareaprodxmes->totalkilos,0), 0, ",", ".");
                                }
                                break;
                            }
                        }    
                        $respuesta['tablaventaPVC'] .= "
                        <td style='text-align:right'>" . $aux_valor . "</td>";
                    }
                    $respuesta['tablaventaPVC'] .= "
                    </tr>";


                }
            }
            $respuesta['tablaventaPVC'] .= "
            </tbody>
            </table>";
            /*************************************** */

            /* GRAFICO VENTAS MENSUAL PVC*/
            /********************************************** */
            $array_ventasmespvc = [];
            $array_vectorTemp[] = 'Mes';
            foreach($datas['areaproduccion'] as $areaproduccion){
                if($areaproduccion->id == 1){
                    $array_vectorTemp[] = $areaproduccion->nombre;
                }
            }
            $array_vectorTemp[] = 'Precio Kg($)';
            $array_ventasmespvc[] = $array_vectorTemp;
            $array_vectorTemp = [];
            foreach($datas['ventasxmes'] as $ventasxmes){
                $array_vectorTemp[] = ucfirst($ventasxmes->mes);
                $i = 1;
                foreach($datas['areaproduccion'] as $areaproduccion){
                    if($areaproduccion->id == 1){
                        $array_vectorTemp[] = 0.00;
                        foreach($datas['ventasareaprodxmes'] as $ventasareaprodxmes){
                            if($ventasxmes->annomes == $ventasareaprodxmes->annomes and $areaproduccion->id == $ventasareaprodxmes->areaproduccion_id ){
                                $array_vectorTemp[$i] = round($ventasareaprodxmes->totalkilos,0);
                                if($ventasareaprodxmes->totalkilos > 0){
                                    $array_vectorTemp[] = round($ventasareaprodxmes->subtotal / $ventasareaprodxmes->totalkilos,0);
                                }else{
                                    $array_vectorTemp[] = 0;
                                }
                                break;
                            }
                        }
                        $i++;    
                    }
                }
                $array_ventasmespvc[] = $array_vectorTemp;
                //$array_ventasmespvc[] = $array_vectorTemp1;
                $array_vectorTemp = [];
            }
            $respuesta['ventasmespvc'] = $array_ventasmespvc;
            //dd($respuesta['ventasmespvc']);
            /********************************************** */

            return $respuesta;
        }
    }

    public function reportegestion(Request $request){
        //dd($request);
        $respuesta = array();
		$respuesta['exito'] = true;
		$respuesta['mensaje'] = "Código encontrado";
		$respuesta['tabla'] = "";
		$respuesta['tabladinero'] = "";
		$respuesta['metacomercial'] = "";
		$respuesta['tablaagruxproducto'] = "";
		$respuesta['tablaareaproduccion'] = "";

        if($request->ajax()){
            //dd($request->idcons);
            $aux_idcons = $request->idcons;
            $request['idcons'] = "1";
            $datasNV = consulta($request); //TODAS LAS NOTAS DE VENTA
            $request['idcons'] = "2";
            $datasFecFC = consultaODcerrada($request); //TODO LO FACTURADO POR FECHA DE FACTURA
            $request['idcons'] = "3";
            $datasFecNV = consultaODcerrada($request); //LO FACTURADO POR FECHA DE NOTA DE VENTA

            if($aux_idcons == "1"){
                $datas = $datasNV;
            }
            if($aux_idcons == "2"){
                $datas = $datasFecFC;
            }
            if($aux_idcons == "3"){
                $datas = $datasFecNV;
            }
            if(count($datas['totales']) <= 0){ //SI LA TABLA TOTALES VIENE VACIA NO ES NECESARIO HACER TODO LO DEMAS, ROMPO AQUI Y ENVIO REPUESTA VACIO
                return $respuesta;
            }
            $respuesta['tabladinero'] .= "<table id='tablacotizacion' name='tablacotizacion' class='table display AllDataTables table-hover table-condensed tablascons' data-page-length='50'>
            <thead>
                <tr>
                    <th>Productos</th>";
            foreach($datas['vendedores'] as $vendedor){
                $nombreven = $vendedor->nombre;
                $respuesta['tabladinero'] .= "
                        <th style='text-align:right' class='tooltipsC ' title='$nombreven'>$nombreven</th>";
            }

            $respuesta['tabladinero'] .= "
                    <th style='text-align:right' class='tooltipsC ' title='Total'>TOTAL</th>               
                </tr>
            </thead>
            <tbody>";
    

        
            $sql = "SELECT areaproduccion.*
                FROM areaproduccion
                WHERE areaproduccion.id='$request->areaproduccion_id'
                and isnull(areaproduccion.deleted_at)
            ";
            $areaproduccion = DB::select($sql);

            $respuesta['metacomercial'] .= "<table id='metacomercial' name='metacomercial' class='table display AllDataTables table-hover table-condensed tablascons' data-page-length='50'>
            <thead>
                <tr>
                    <th>Productos</th>";
            if($areaproduccion[0]->stapromkg == "1"){
                $respuesta['metacomercial'] .= "
                    <th style='text-align:right' class='tooltipsC hskilos' title='Meta comercial KG'>Meta comercial KG</th>
                    <th style='text-align:right' class='tooltipsC hskilos' title='Total Kilos'>KG</th>
                    <th style='text-align:right' class='tooltipsC hskilos' title='Promedio x Kg'>Prom</th>";
            }
            $respuesta['metacomercial'] .= "
                </tr>
            </thead>
            <tbody>";


            $i = 0;
            $totalgeneralfilakg = 0;
            $totalgeneralDinero = 0;
            $precpromediofinal = 0;
            $totalmetacomercialkg = 0;
            foreach($datas['productos'] as $producto){
                $respuesta['tabladinero'] .= "
                    <tr class='btn-accion-tabla tooltipsC'>
                        <td id='producto$i' name='producto$i'>$producto->gru_nombre</td>";

                $respuesta['metacomercial'] .= "
                    <tr class='btn-accion-tabla tooltipsC'>
                        <td id='producto$i' name='producto$i'>$producto->gru_nombre</td>";
    

                foreach($datas['vendedores'] as $vendedor){
                    $aux_encontrado = false;
                    foreach($datas['totales'] as $total){
                        if($total->grupoprod_id == $producto->id and $total->persona_id==$vendedor->id){
                            $aux_encontrado = true;
                            //dd($total->subtotal);
                            $respuesta['tabladinero'] .= "<td id='vendedord$i' name='vendedord$i' style='text-align:right' class='' data-order='$total->subtotal'>" . number_format($total->subtotal, 0, ",", ".") . "</td>";
                        } 
                    }
                    if($aux_encontrado==false){
                        $respuesta['tabladinero'] .= "<td id='vendedordt$i' name='vendedordt$i' style='text-align:right' class='' data-order='0'>0</td>";
                    }
                }
                
                $aux_precpromkilo = 0;
                if($producto->totalkilos>0){
                    $aux_precpromkilo = $producto->subtotal/$producto->totalkilos;
                }
                $respuesta['tabladinero'] .= "
                        <td id='totalsubtotal$i' name='totalsubtotal$i' style='text-align:right' class='' data-order='$producto->subtotal'>" . number_format($producto->subtotal, 0, ",", ".") . "</td>";
                if($areaproduccion[0]->stapromkg == "1"){
                    $respuesta['metacomercial'] .= "
                        <td style='text-align:right' data-order='$producto->metacomerkg' class=' hskilos'>" . number_format($producto->metacomerkg, 2, ",", ".") . "</td>
                        <td style='text-align:right' data-order='$producto->totalkilos' class=' hskilos'>" . number_format($producto->totalkilos, 2, ",", ".") . "</td>
                        <td style='text-align:right' data-order='$aux_precpromkilo' class=' hskilos'>" . number_format($aux_precpromkilo, 2, ",", ".") . "</td>";
                }
                $respuesta['tabladinero'] .= "
                    </tr>";
                $respuesta['metacomercial'] .= "
                    </tr>";
                $i++;
                $totalgeneralfilakg += $producto->totalkilos;
                $totalgeneralDinero += $producto->subtotal;
                $totalmetacomercialkg += $producto->metacomerkg;
            }
            if($totalgeneralfilakg > 0){
                $precpromediofinal = $totalgeneralDinero / $totalgeneralfilakg;
            }
            $respuesta['tabladinero'] .= "
            </tbody>
                <tfoot>
                    <tr>
                        <th>TOTAL $</th>";

            $respuesta['metacomercial'] .= "
            </tbody>
                <tfoot>
                    <tr>
                        <th>TOTAL</th>";
                        
            foreach($datas['vendedores'] as $vendedor){
                $respuesta['tabladinero'] .= "
                    <th style='text-align:right' class=''>". number_format($vendedor->subtotal, 0, ",", ".") ."</th>";
            }
            $respuesta['tabladinero'] .= "
                        <th style='text-align:right' class=''>". number_format($totalgeneralDinero, 0, ",", ".") ."</th>";
            if($areaproduccion[0]->stapromkg == "1"){
                $respuesta['metacomercial'] .= "
                    <th style='text-align:right' class=' hskilos'>". number_format($totalmetacomercialkg, 2, ",", ".") ."</th>
                    <th style='text-align:right' class=' hskilos'>". number_format($totalgeneralfilakg, 2, ",", ".") ."</th>
                    <th style='text-align:right' class=' hskilos'>". number_format($precpromediofinal, 2, ",", ".") ."</th>";
            }
            $respuesta['tabladinero'] .= "
                    </tr>
                </tfoot>
            </table>";

            $respuesta['metacomercial'] .= "
                    </tr>
                </tfoot>
            </table>";

            $respuesta['nombre'] = array_column($datas['vendedores'], 'nombre');
            $respuesta['totalkilos'] = array_column($datas['vendedores'], 'totalkilos');
            $i = 0;
            foreach($respuesta['totalkilos'] as &$kilos){
                $kilos = round($kilos,2);
                if($totalgeneralfilakg <= 0){
                    $totalgeneralfilakg = 1;
                }
                $kilos1 = round(($kilos / $totalgeneralfilakg) * 100,2);
                $respuesta['nombre'][$i] .= " " . number_format($kilos1, 2, ",", ".") . "%";
                $i++;
            }
            $respuesta['nombredinero'] = array_column($datas['vendedores'], 'nombre');
            $respuesta['totaldinero'] = array_column($datas['vendedores'], 'subtotal');
            $i = 0;
            foreach($respuesta['totaldinero'] as &$subtotaldinero){
                $subtotaldinero = round($subtotaldinero,2);
                $subtotaldinero1 = 0;
                if($totalgeneralDinero > 0){
                    $subtotaldinero1 = round(($subtotaldinero / $totalgeneralDinero) * 100,2);
                }
                $respuesta['nombredinero'][$i] .= " " . number_format($subtotaldinero1, 2, ",", ".") . "%";
                $i++;
            }

            $i = 0;
            foreach($datasNV['vendedores'] as $datasNVs){
                $aux_totalfackg = 0;
                $aux_totalfacdin = 0;
                foreach($datasFecNV['vendedores'] as $datasFecNVs){
                    if($datasFecNVs->id == $datasNVs->id){
                        $aux_totalfackg = $datasFecNVs->totalkilos;
                        $aux_totalfacdin = $datasFecNVs->subtotal;    
                    }
                }
                $datasNV['vendedores'][$i]->pendientekg = $datasNVs->totalkilos - $aux_totalfackg;
                $datasNV['vendedores'][$i]->pendienteDinero = $datasNVs->subtotal - $aux_totalfacdin;
                $i++;
            }


            $respuesta['nombrebar'] = array_column($datasNV['vendedores'], 'nombre');
            $respuesta['totalkilosbarNV'] = array_column($datasNV['vendedores'], 'totalkilos');
            $respuesta['totalkilosbarFecFC'] = array_column($datasFecFC['vendedores'], 'totalkilos');
            $respuesta['totalkilosbarFecNV'] = array_column($datasFecNV['vendedores'], 'totalkilos');
            $respuesta['totalkilosbarNVPendiente'] = array_column($datasNV['vendedores'], 'pendientekg');

            $respuesta['totaldinerobarNV'] = array_column($datasNV['vendedores'], 'subtotal');
            $respuesta['totaldineroFecFC'] = array_column($datasFecFC['vendedores'], 'subtotal');
            $respuesta['totaldineroFecNV'] = array_column($datasFecNV['vendedores'], 'subtotal');
            $respuesta['totaldineroNVPendiente'] = array_column($datasNV['vendedores'], 'pendienteDinero');
            
            $respuesta['tablaagruxproductomargen'] = tablaAgruxProductoMargen($datas['agruxproducto']);

            $respuesta['productos'] = $datas['productos'];

            //TABLA POR AREA DE PRODUCCION
            $respuesta['tablaareaproduccion'] .= "<table id='tablacotizacion' name='tablacotizacion' class='table display AllDataTables table-hover table-condensed tablascons' data-page-length='50'>
			<thead>
				<tr>
					<th>Area Prod</th>
                    <th style='text-align:right'>Kg Facturado<br>al dia $request->fechah</th>
                    <th style='text-align:right'>Kg Facturado<br>Acumulado</th>
                    <th style='text-align:right'>Neto $</th>
                    <th style='text-align:right'>Monto<br>con IVA</th>
                    <th style='text-align:right'>Precio<br>Promedio Kg</th>
                </tr>
            </thead>
            <tbody>";
            $aux_totalkiloshoy = 0;
            $aux_totalkgfacacum = 0;
            $aux_totalmonto = 0;
            $aux_totalmas_iva = 0;
            foreach($datas['areaproduccion'] as $areaproduccion){
                $aux_promkilo = 0;
                if($areaproduccion->totalkilos>0){
                    $aux_promkilo = $areaproduccion->subtotal/$areaproduccion->totalkilos;
                }
                $aux_kiloshoy = 0;
                foreach($datas['areaproduccionhoy'] as $areaproduccionhoy){
                    if($areaproduccionhoy->id == $areaproduccion->id){
                        $aux_kiloshoy = $areaproduccionhoy->totalkilos;
                    }  
                }

                $respuesta['tablaareaproduccion'] .= "
                    <tr id='fila$i' name='fila$i' class='btn-accion-tabla tooltipsC'>
                        <td data-order='$areaproduccion->id' >$areaproduccion->nombre</td>
                        <td style='text-align:right' data-order='$aux_kiloshoy' data-search='$aux_kiloshoy'>" . number_format($aux_kiloshoy, 2, ",", ".") . "</td>
                        <td style='text-align:right' data-order='$areaproduccion->totalkilos' data-search='$areaproduccion->totalkilos'>" . number_format($areaproduccion->totalkilos, 2, ",", ".") . "</td>
                        <td style='text-align:right' data-order='$areaproduccion->subtotal' data-search='$areaproduccion->subtotal'>" . number_format($areaproduccion->subtotal, 0, ",", ".") . "</td>
                        <td style='text-align:right' data-order='$areaproduccion->totalmas_iva' data-search='$areaproduccion->totalmas_iva'>" . number_format($areaproduccion->totalmas_iva, 0, ",", ".") . "</td>
                        <td style='text-align:right' data-order='$aux_promkilo' data-search='$aux_promkilo'>" . number_format($aux_promkilo, 2, ",", ".") . "</td>
                    </tr>";
                        //$aux_totalfacdia += 0;
                $aux_totalkgfacacum += $areaproduccion->totalkilos;
                $aux_totalmonto += $areaproduccion->subtotal;
                $aux_totalkiloshoy += $aux_kiloshoy;
                $aux_totalmas_iva += $areaproduccion->totalmas_iva;
            }
            $aux_promkilogen = 0;
            if($aux_totalkgfacacum > 0){
                $aux_promkilogen = $aux_totalmonto / $aux_totalkgfacacum;
            }
            $respuesta['tablaareaproduccion'] .= "
                </tbody>
                <tfoot>
                    <tr>
                        <th>TOTAL</th>
                        <th style='text-align:right'>". number_format($aux_totalkiloshoy, 2, ",", ".") ."</th>
                        <th style='text-align:right'>". number_format($aux_totalkgfacacum, 2, ",", ".") ."</th>
                        <th style='text-align:right'>". number_format($aux_totalmonto, 0, ",", ".") ."</th>
                        <th style='text-align:right'>". number_format($aux_totalmas_iva, 0, ",", ".") ."</th>
                        <!--<th style='text-align:right'>". number_format($aux_promkilogen, 2, ",", ".") ."</th>-->
                        <th></th>
                    </tr>
                </tfoot>
            </table>";

            $respuesta['vetasxmesmeses'] = array_column($datas['ventasxmes'], 'mes');
            $respuesta['ventasxmeskilos'] = array_column($datas['ventasxmes'], 'totalkilos');
            $respuesta['ventasxmesdinero'] = array_column($datas['ventasxmes'], 'subtotal');





            /******Tabla Ventas Mensual por Unidad de Produccion***** */
            /******************************************************** */
            $respuesta['tablaventasmesap'] = "<table id='tablaventasmesareaprod' name='tablaventasmesareaprod' class='table display AllDataTables table-hover table-condensed tablascons' data-page-length='50'>
            <thead>
                <tr>
                    <th>Area Prod</th>";
            foreach($datas['ventasxmes'] as $ventasxmes){
                $mes = $ventasxmes->mes;
                $respuesta['tablaventasmesap'] .= "
                        <th style='text-align:right' class='tooltipsC' title='" . ucfirst($mes) . "'>" . ucfirst($mes) . "</th>";
            }
            $respuesta['tablaventasmesap'] .= "
                </tr>
            </thead>
            <tbody>";
            foreach($datas['areaproduccion'] as $areaproduccion){
                $respuesta['tablaventasmesap'] .= "
                <tr class='btn-accion-tabla tooltipsC'>
                    <td data-order='$areaproduccion->id'>$areaproduccion->nombre</td>";
                foreach($datas['ventasxmes'] as $ventasxmes){
                    $aux_valor = "0";
                    foreach($datas['ventasareaprodxmes'] as $ventasareaprodxmes){
                        if(($ventasxmes->annomes == $ventasareaprodxmes->annomes) and ($areaproduccion->id == $ventasareaprodxmes->areaproduccion_id) ){
                            $aux_valor = number_format(round($ventasareaprodxmes->subtotal,0), 0, ",", ".");
                            break;
                        }
                    }
                    $respuesta['tablaventasmesap'] .= "
                    <td style='text-align:right'>" . $aux_valor . "</td>";
                }
                $respuesta['tablaventasmesap'] .= "
                </tr>";
            }
            $respuesta['tablaventasmesap'] .= "
            </tbody>
            </table>";
            /******************************************************** */

            /* GRAFICO VENTAS MENSUAL POR AREA DE PRODUCCION*/
            $array_ventasmesxareaprod = [];
            $array_vectorTemp[] = 'Mes';
            foreach($datas['areaproduccion'] as $areaproduccion){
                $array_vectorTemp[] = $areaproduccion->nombre;
            }
            $array_ventasmesxareaprod[] = $array_vectorTemp;
            $array_vectorTemp = [];
            foreach($datas['ventasxmes'] as $ventasxmes){
                $array_vectorTemp[] = ucfirst($ventasxmes->mes);
                $i = 1;
                foreach($datas['areaproduccion'] as $areaproduccion){
                    $array_vectorTemp[] = 0.00;
                    foreach($datas['ventasareaprodxmes'] as $ventasareaprodxmes){
                        if($ventasxmes->annomes == $ventasareaprodxmes->annomes and $areaproduccion->id == $ventasareaprodxmes->areaproduccion_id ){
                            $array_vectorTemp[$i] = $ventasareaprodxmes->subtotal;
                            break;
                        }
                    }
                    $i++;
                }
                $array_ventasmesxareaprod[] = $array_vectorTemp;
                $array_vectorTemp = [];
            }
            $respuesta['ventasmesxareaprod'] = $array_ventasmesxareaprod;
            

            return $respuesta;
        }
    }

    public function reportekilostipoentrega(Request $request){
        //dd($request);
        $respuesta = array();
		$respuesta['exito'] = true;
		$respuesta['mensaje'] = "Código encontrado";
		$respuesta['tabla'] = "";

        if($request->ajax()){
            //dd($request->idcons);
            $datas = consultakilostipoentrega($request); //TODAS LAS NOTAS DE VENTA

            $respuesta['tabla'] .= "<table id='tablacotizacion' name='tablacotizacion' class='table display AllDataTables table-hover table-condensed tablascons' data-page-length='50'>
			<thead>
				<tr>
                    <th>Tipo entrega</th>
                    <th>T</th>
                    <th style='text-align:right' class='tooltipsC' title='Total KG'>TOTAL Kg</th>
                </tr>
            </thead>
            <tbody>";
            $aux_totalkilos = 0;
            foreach($datas['kilosxtipoentrega'] as $kilosxtipoentrega){
                $respuesta['tabla'] .= "
                <tr>
                    <td>
                        $kilosxtipoentrega->nombre
                    </td>
                    <td><i class='fa $kilosxtipoentrega->icono'></i>
                    </td>
                    <td style='text-align:right' data-order='$kilosxtipoentrega->totalkilos' data-search='$kilosxtipoentrega->totalkilos'>" . number_format($kilosxtipoentrega->totalkilos, 2, ",", ".") . "</td>
                </tr>";
                $aux_totalkilos += $kilosxtipoentrega->totalkilos;
            }
            $respuesta['tabla'] .= "
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan='2'>TOTAL</th>
                        <th style='text-align:right'>". number_format($aux_totalkilos, 2, ",", ".") ."</th>
                    </tr>
                </tfoot>
            </table>";
            //dd($respuesta);
            return $respuesta;
        }
    }

    public function exportPdf(Request $request)
    {
        //$cotizaciones = Cotizacion::orderBy('id')->get();
        //dd($rut);
        $rut=str_replace("-","",$request->rut);
        $rut=str_replace(".","",$rut);
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
        $nombreCategoria = "Todos";
        if($request->categoriaprod_id){
            $categoriaprod = CategoriaProd::findOrFail($request->categoriaprod_id);
            $nombreCategoria=$categoriaprod->nombre;
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

        if($notaventas){
            //return view('prodxnotaventa.listado', compact('notaventas','empresa','usuario','aux_fdesde','aux_fhasta','nomvendedor','nombreCategoria','nombreAreaproduccion','nombreGiro'));
        
            $pdf = PDF::loadView('prodxnotaventa.listado', compact('notaventas','empresa','usuario','aux_fdesde','aux_fhasta','nomvendedor','nombreCategoria','nombreAreaproduccion','nombreGiro'));
            //return $pdf->download('cotizacion.pdf');
            return $pdf->stream("prueba");
        }else{
            dd('Ningún dato disponible en esta consulta.');
        }
    }

    public function comercialPdf(Request $request)
    {
        //$cotizaciones = Cotizacion::orderBy('id')->get();
        //dd($rut);
        $rut=str_replace("-","",$request->rut);
        $rut=str_replace(".","",$rut);
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
        $nombreCategoria = "Todos";
        if($request->categoriaprod_id){
            $categoriaprod = CategoriaProd::findOrFail($request->categoriaprod_id);
            $nombreCategoria=$categoriaprod->nombre;
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

        if($notaventas){
            //return view('prodxnotaventa.listado', compact('notaventas','empresa','usuario','aux_fdesde','aux_fhasta','nomvendedor','nombreCategoria','nombreAreaproduccion','nombreGiro'));
        
            $pdf = PDF::loadView('prodxnotaventa.listado', compact('notaventas','empresa','usuario','aux_fdesde','aux_fhasta','nomvendedor','nombreCategoria','nombreAreaproduccion','nombreGiro'));
            //return $pdf->download('cotizacion.pdf');
            return $pdf->stream("prueba");
        }else{
            dd('Ningún dato disponible en esta consulta.');
        }
    }



    public function exportPdfkg()
    {
        $request = new Request();
        $request->fechad = $_GET["fechad"];
        $request->fechah = $_GET["fechah"];
        $request->vendedor_id = $_GET["vendedor_id"];
        $request->giro_id = $_GET["giro_id"];
        $request->categoriaprod_id = $_GET["categoriaprod_id"];
        $request->areaproduccion_id = $_GET["areaproduccion_id"];
        $request->idcons = $_GET["idcons"];
        $request->statusact_id = $_GET["statusact_id"];
        $request->aux_titulo = $_GET["aux_titulo"];
        $request->numrep = $_GET["numrep"];

        //dd($request);

        if($request->idcons == "1"){
            $datas = consulta($request);
        }
        if($request->idcons == "2" or $request->idcons == "3"){
            $datas = consultaODcerrada($request);
        }

        //$datas = consulta($request);

        $aux_fdesde= $request->fechad;
        if(empty($request->fechad)){
            $aux_fdesde= '  /  /    ';
        }
        $aux_fhasta= $request->fechah;

        $aux_plazoentregad= $request->plazoentregad;
        if(empty($request->plazoentregad)){
            $aux_plazoentregad= '  /  /    ';
        }
        $aux_plazoentregah= $request->plazoentregah;

        //$cotizaciones = consulta('','');
        $empresa = Empresa::orderBy('id')->get();
        $usuario = Usuario::findOrFail(auth()->id());
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

        //return armarReportehtml($request);
        if($datas){
                if(env('APP_DEBUG')){
                    return view('nvindicadorxvendedor.listado', compact('datas','empresa','usuario','aux_fdesde','aux_fhasta','nombreAreaproduccion','nombreGiro','aux_plazoentregad','request'));
                }
                if($request->numrep=='7'){
                    $pdf = PDF::loadView('nvindicadorxvendedor.listado', compact('datas','empresa','usuario','aux_fdesde','aux_fhasta','nombreAreaproduccion','nombreGiro','aux_plazoentregad','request'))->setPaper('a4', 'landscape');
                    return $pdf->stream("KilosporVendedor.pdf");        
                }    
                $pdf = PDF::loadView('nvindicadorxvendedor.listado', compact('datas','empresa','usuario','aux_fdesde','aux_fhasta','nombreAreaproduccion','nombreGiro','aux_plazoentregad','request')); //->setPaper('a4', 'landscape');
                //return $pdf->download('cotizacion.pdf');
                //return $pdf->stream(str_pad($notaventa->id, 5, "0", STR_PAD_LEFT) .' - '. $notaventa->cliente->razonsocial . '.pdf');
                return $pdf->stream("KilosporVendedor.pdf");
            if($request->numrep=='2'){
                $pdf = PDF::loadView('nvindicadorxvendedor.listado', compact('datas','empresa','usuario','aux_fdesde','aux_fhasta','nombreAreaproduccion','nombreGiro','aux_plazoentregad','request')); //->setPaper('a4', 'landscape');
                return $pdf->stream("KilosporVendedor.pdf");    
            }
        }else{
            dd('Ningún dato disponible en esta consulta.');
        } 
    }

    public function comercialPdfkg()
    {
        $request = new Request();
        $request->fechad = $_GET["fechad"];
        $request->fechah = $_GET["fechah"];
        $request->vendedor_id = $_GET["vendedor_id"];
        $request->giro_id = $_GET["giro_id"];
        $request->categoriaprod_id = $_GET["categoriaprod_id"];
        $request->areaproduccion_id = $_GET["areaproduccion_id"];
        $request->idcons = $_GET["idcons"];
        $request->statusact_id = $_GET["statusact_id"];
        $request->aux_titulo = $_GET["aux_titulo"];
        $request->numrep = $_GET["numrep"];
        $request->anno = $_GET["anno"];

        //dd($request);
        if($request->idcons == "1"){
            $datas = consulta($request);
        }
        if($request->idcons == "2" or $request->idcons == "3"){
            $datas = consultaODcerrada($request);
        }

        //$datas = consulta($request);

        $aux_fdesde= $request->fechad;
        if(empty($request->fechad)){
            $aux_fdesde= '  /  /    ';
        }
        $aux_fhasta= $request->fechah;

        $aux_plazoentregad= $request->plazoentregad;
        if(empty($request->plazoentregad)){
            $aux_plazoentregad= '  /  /    ';
        }
        $aux_plazoentregah= $request->plazoentregah;

        //$cotizaciones = consulta('','');
        $empresa = Empresa::orderBy('id')->get();
        $usuario = Usuario::findOrFail(auth()->id());
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

        //return armarReportehtml($request);
        if($datas){
                if(env('APP_DEBUG')){
                    return view('indicadorcomercial.listado', compact('datas','empresa','usuario','aux_fdesde','aux_fhasta','nombreAreaproduccion','nombreGiro','aux_plazoentregad','request'));
                }
                if($request->numrep=='7'){
                    $pdf = PDF::loadView('indicadorcomercial.listado', compact('datas','empresa','usuario','aux_fdesde','aux_fhasta','nombreAreaproduccion','nombreGiro','aux_plazoentregad','request'))->setPaper('a4', 'landscape');
                    return $pdf->stream("KilosporVendedor.pdf");        
                }    
                $pdf = PDF::loadView('indicadorcomercial.listado', compact('datas','empresa','usuario','aux_fdesde','aux_fhasta','nombreAreaproduccion','nombreGiro','aux_plazoentregad','request')); //->setPaper('a4', 'landscape');
                //return $pdf->download('cotizacion.pdf');
                //return $pdf->stream(str_pad($notaventa->id, 5, "0", STR_PAD_LEFT) .' - '. $notaventa->cliente->razonsocial . '.pdf');
                return $pdf->stream("KilosporVendedor.pdf");
            if($request->numrep=='2'){
                $pdf = PDF::loadView('indicadorcomercial.listado', compact('datas','empresa','usuario','aux_fdesde','aux_fhasta','nombreAreaproduccion','nombreGiro','aux_plazoentregad','request')); //->setPaper('a4', 'landscape');
                return $pdf->stream("KilosporVendedor.pdf");    
            }
        }else{
            dd('Ningún dato disponible en esta consulta.');
        } 
    }

    public function gestionPdfkg()
    {
        //dd(session('grafico2'));
        $request = new Request();
        $request->fechad = $_GET["fechad"];
        $request->fechah = $_GET["fechah"];
        $request->vendedor_id = $_GET["vendedor_id"];
        $request->giro_id = $_GET["giro_id"];
        $request->categoriaprod_id = $_GET["categoriaprod_id"];
        $request->areaproduccion_id = $_GET["areaproduccion_id"];
        $request->idcons = $_GET["idcons"];
        $request->statusact_id = $_GET["statusact_id"];
        $request->aux_titulo = $_GET["aux_titulo"];
        $request->anno = $_GET["anno"];
        $request->numrep = $_GET["numrep"];

        //dd($request);

        if($request->idcons == "1"){
            $datas = consulta($request);
        }
        if($request->idcons == "2" or $request->idcons == "3"){
            $datas = consultaODcerrada($request);
        }

        //$datas = consulta($request);

        $aux_fdesde= $request->fechad;
        if(empty($request->fechad)){
            $aux_fdesde= '  /  /    ';
        }
        $aux_fhasta= $request->fechah;

        $aux_plazoentregad= $request->plazoentregad;
        if(empty($request->plazoentregad)){
            $aux_plazoentregad= '  /  /    ';
        }
        $aux_plazoentregah= $request->plazoentregah;

        //$cotizaciones = consulta('','');
        $empresa = Empresa::orderBy('id')->get();
        $usuario = Usuario::findOrFail(auth()->id());
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

        //return armarReportehtml($request);
        if($datas){
                if(env('APP_DEBUG')){
                    return view('indicadorgestion.listado', compact('datas','empresa','usuario','aux_fdesde','aux_fhasta','nombreAreaproduccion','nombreGiro','aux_plazoentregad','request'));
                }
                if($request->numrep=='7'){
                    $pdf = PDF::loadView('indicadorgestion.listado', compact('datas','empresa','usuario','aux_fdesde','aux_fhasta','nombreAreaproduccion','nombreGiro','aux_plazoentregad','request'))->setPaper('a4', 'landscape');
                    return $pdf->stream("KilosporVendedor.pdf");        
                }    
                $pdf = PDF::loadView('indicadorgestion.listado', compact('datas','empresa','usuario','aux_fdesde','aux_fhasta','nombreAreaproduccion','nombreGiro','aux_plazoentregad','request')); //->setPaper('a4', 'landscape');
                //return $pdf->download('cotizacion.pdf');
                //return $pdf->stream(str_pad($notaventa->id, 5, "0", STR_PAD_LEFT) .' - '. $notaventa->cliente->razonsocial . '.pdf');
                return $pdf->stream("KilosporVendedor.pdf");
            if($request->numrep=='2'){
                $pdf = PDF::loadView('indicadorgestion.listado', compact('datas','empresa','usuario','aux_fdesde','aux_fhasta','nombreAreaproduccion','nombreGiro','aux_plazoentregad','request')); //->setPaper('a4', 'landscape');
                return $pdf->stream("KilosporVendedor.pdf");    
            }
        }else{
            dd('Ningún dato disponible en esta consulta.');
        } 
    }

    public function imagengrafico(Request $request){
        /*
        $img = $request['base64'];
        $img = str_replace('data:image/png;base64,', '', $img);
        $fileData = base64_decode($img);
        $file_name = $request['filename'] . '.png';
        Storage::disk('public')->put("imagenes/charts/$file_name", $fileData);
        */
        session(['grafico' => $request['base64']]);
        session(['grafico1' => $request['base64b1']]);
        session(['grafico2' => $request['base64b2']]);
        return "";
    }

    public function reportekilostipoentregapdf()
    {
        //dd(session('grafico2'));
        $request = new Request();
        $request->fechad = $_GET["fechad"];
        $request->fechah = $_GET["fechah"];
        $request->vendedor_id = $_GET["vendedor_id"];
        $request->giro_id = $_GET["giro_id"];
        $request->categoriaprod_id = $_GET["categoriaprod_id"];
        $request->areaproduccion_id = $_GET["areaproduccion_id"];
        $request->idcons = $_GET["idcons"];
        $request->statusact_id = $_GET["statusact_id"];
        $request->aux_titulo = $_GET["aux_titulo"];

        //dd($request);

        $datas = consultakilostipoentrega($request);

        //$datas = consulta($request);

        $aux_fdesde= $request->fechad;
        if(empty($request->fechad)){
            $aux_fdesde= '  /  /    ';
        }
        $aux_fhasta= $request->fechah;

        $aux_plazoentregad= $request->plazoentregad;
        if(empty($request->plazoentregad)){
            $aux_plazoentregad= '  /  /    ';
        }
        $aux_plazoentregah= $request->plazoentregah;

        //$cotizaciones = consulta('','');
        $empresa = Empresa::orderBy('id')->get();
        $usuario = Usuario::findOrFail(auth()->id());
        $aux_areaproduccion = implode ( ',' , json_decode($request->areaproduccion_id));
        if(empty($aux_areaproduccion )){
            $areaprodcond = " true ";
        }else{
            $areaprodcond = " areaproduccion.id in ($aux_areaproduccion) ";
        }

        $sql = "SELECT nombre
        FROM areaproduccion 
        where $areaprodcond
        ORDER BY id;";
        //dd($sql);
        $datas_areaproduccion = DB::select($sql);

        
        $nombreAreaproduccion = "";
        /*
        dd($request->areaproduccion_id);
        if($request->areaproduccion_id){
            $areaProduccion = AreaProduccion::findOrFail($request->areaproduccion_id);
            $nombreAreaproduccion=$areaProduccion->nombre;
        }
        */
        $nombreGiro = "Todos";
        if($request->giro_id){
            $giro = Giro::findOrFail($request->giro_id);
            $nombreGiro=$giro->nombre;
        }
        //dd($datas);

        //return armarReportehtml($request);
        if($datas){
                //return view('repkilosxtipoentrega.listado', compact('datas','empresa','usuario','aux_fdesde','aux_fhasta','nombreAreaproduccion','nombreGiro','aux_plazoentregad','request'));
                $pdf = PDF::loadView('repkilosxtipoentrega.listado', compact('datas','empresa','usuario','aux_fdesde','aux_fhasta','nombreAreaproduccion','nombreGiro','aux_plazoentregad','request')); //->setPaper('a4', 'landscape');
                return $pdf->stream("repkilosxtipoentrega.pdf");
        }else{
            dd('Ningún dato disponible en esta consulta.');
        } 
    }
}




function consulta($request){
    $array = array('apellido', 'email', 'teléfono');
    $separado_por_comas = implode(",", $array);
    //dd(json_decode($request->vendedor_id));
    //$aux_vendedor = implode ( ',' , json_decode($request->vendedor_id));
    //dd($aux_vendedor);
    $respuesta = array();
    $respuesta['exito'] = true;
    $respuesta['mensaje'] = "Código encontrado";
    $respuesta['productos'] = "";
    $respuesta['vendedores'] = "";
    $respuesta['agruxproducto'] = "";
/*
    if(empty($aux_vendedor )){
            $vendedorcond = " true ";
    }else{
        $vendedorcond = " notaventa.vendedor_id in ($aux_vendedor) ";
    }
*/
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
        if(is_array($request->vendedor_id)){
            $aux_vendedorid = implode ( ',' , $request->vendedor_id);
        }else{
            $aux_vendedorid = $request->vendedor_id;
        }
        $vendedorcond = " notaventa.vendedor_id in ($aux_vendedorid) ";

        //$vendedorcond = "notaventa.vendedor_id='$request->vendedor_id'";
    }
    //dd($vendedorcond);
    if(empty($request->fechad) or empty($request->fechah)){
        $aux_condFecha = " true";
        $annomes = date("Y") . date("m");
        /*CONSULTAR FECHA DE HOY SI LOS CAMPOS DE FECHA ESTAN VACIOS $aux_condFechahoy*/
        $fechadhoy = date('Y-m-d')." 00:00:00";
        $fechahhoy = date('Y-m-d')." 23:59:59";
        $aux_condFechahoy = "notaventa.fechahora>='$fechadhoy' and notaventa.fechahora<='$fechahhoy'";
    
    }else{
        $fecha = date_create_from_format('d/m/Y', $request->fechad);
        $fechad = date_format($fecha, 'Y-m-d')." 00:00:00";
        $fecha = date_create_from_format('d/m/Y', $request->fechah);
        $fechah = date_format($fecha, 'Y-m-d')." 23:59:59";
        $aux_condFecha = "notaventa.fechahora>='$fechad' and notaventa.fechahora<='$fechah'";
        $annomes = date_format($fecha, 'Ym');
    }

    if(empty($request->fechah)){
        /*CONSULTAR FECHA DE HOY SI EL CAMPO FECHA HASTA ESTA VACIO $aux_condFechahoy*/
        $fechahoy = date('Y-m-d');
        $aux_condFechahoy = "date_format(notaventa.fechahora,'%Y-%m-%d')='$fechahoy'";
    
    }else{
        /*DE LO CONTRARIO TOMO LA FECHA DE $request->fechah */
        $fecha = date_create_from_format('d/m/Y', $request->fechah);
        $fechahoy = date_format($fecha, 'Y-m-d');
        $aux_condFechahoy = "date_format(notaventa.fechahora,'%Y-%m-%d')='$fechahoy'";
    }

    if(empty($request->categoriaprod_id)){
        $aux_condcategoriaprod_id = " true";
    }else{
        $aux_condcategoriaprod_id = "categoriaprod.id='$request->categoriaprod_id'";
    }
    if(empty($request->giro_id)){
        $aux_condgiro_id = " true";
    }else{
        $aux_condgiro_id = "cliente.giro_id='$request->giro_id'";
    }

    if(empty($request->areaproduccion_id)){
        $aux_condareaproduccion_id = " true";
    }else{
        $aux_condareaproduccion_id = "categoriaprod.areaproduccion_id='$request->areaproduccion_id'";
    }

    switch ($request->statusact_id) {
        case 1:
            $aux_condstatusact_id = "notaventa.id not in (select notaventa_id from notaventacerrada where isnull(notaventacerrada.deleted_at))";
            break;
        case 2:
            $aux_condstatusact_id = "notaventa.id in (select notaventa_id from notaventacerrada where isnull(notaventacerrada.deleted_at))";
            break;
        case 3:
            $aux_condstatusact_id = " true";
            break;
    }

    /*CONDICION PARA CONSULTAR TODO EL AÑO SELECCIONADO PARA REPORTES DE TODO EL AÑO PARA LOS GRAFICOS ETC*/
    if(empty($request->anno)){
        $aux_condanno = " false ";
    }else{
        $aux_condanno = "date_format(notaventa.fechahora,'%Y')='$request->anno'";
        if(!empty($request->fechah)){
            $fecha = date_create_from_format('d/m/Y', $request->fechah);
            $fechah = date_format($fecha, 'Y-m-d')." 23:59:59";
            $aux_condanno .= " and notaventa.fechahora<='$fechah'";
        }
    }

    $sql = "SELECT grupoprod.id,grupoprod.gru_nombre,
    sum(notaventadetalle.totalkilos) AS totalkilos,
    sum(notaventadetalle.subtotal) AS subtotal,
    round(sum((notaventadetalle.subtotal))*((notaventa.piva+100)/100)) AS totalmas_iva,
    categoriagrupovalmes.metacomerkg,categoriagrupovalmes.costo
    FROM notaventadetalle INNER JOIN producto
    ON notaventadetalle.producto_id=producto.id and isnull(producto.deleted_at)
    INNER JOIN categoriaprod
    ON producto.categoriaprod_id=categoriaprod.id and isnull(categoriaprod.deleted_at)
    INNER JOIN grupoprod
    ON producto.grupoprod_id=grupoprod.id and isnull(grupoprod.deleted_at)
    INNER JOIN notaventa
    ON notaventadetalle.notaventa_id=notaventa.id and isnull(notaventa.deleted_at)
    INNER JOIN cliente
    ON notaventa.cliente_id=cliente.id and isnull(cliente.deleted_at)
    LEFT JOIN categoriagrupovalmes
    ON grupoprod.id=categoriagrupovalmes.grupoprod_id and categoriagrupovalmes.annomes='$annomes' 
        and isnull(categoriagrupovalmes.deleted_at)
    WHERE $aux_condFecha
    and $vendedorcond
    and $aux_condcategoriaprod_id
    and $aux_condgiro_id
    and $aux_condareaproduccion_id
    and $aux_condstatusact_id
    and isnull(notaventa.anulada)
    and isnull(notaventadetalle.deleted_at) 
    GROUP BY grupoprod.id,grupoprod.gru_nombre;";
    //dd($sql);
    //" and " . $aux_condrut .

    $datas = DB::select($sql);
    //dd($datas);
    $respuesta['productos'] = $datas;

    $sql = "SELECT persona.id,persona.nombre,
    ROUND(sum(notaventadetalle.totalkilos),2) AS totalkilos,
    sum(notaventadetalle.subtotal) AS subtotal,
    round(sum((notaventadetalle.subtotal))*((notaventa.piva+100)/100)) AS totalmas_iva
    FROM notaventadetalle INNER JOIN producto
    ON notaventadetalle.producto_id=producto.id and isnull(producto.deleted_at)
    INNER JOIN categoriaprod
    ON producto.categoriaprod_id=categoriaprod.id and isnull(categoriaprod.deleted_at)
    INNER JOIN grupoprod
    ON producto.grupoprod_id=grupoprod.id and isnull(grupoprod.deleted_at)
    INNER JOIN notaventa 
    ON notaventadetalle.notaventa_id=notaventa.id and isnull(notaventa.deleted_at)
    INNER JOIN vendedor 
    ON notaventa.vendedor_id=vendedor.id and isnull(vendedor.deleted_at)
    INNER JOIN persona
    ON vendedor.persona_id=persona.id and isnull(persona.deleted_at)
    INNER JOIN cliente
    ON notaventa.cliente_id=cliente.id and isnull(cliente.deleted_at)
    WHERE $aux_condFecha
    and $vendedorcond
    and $aux_condcategoriaprod_id
    and $aux_condgiro_id
    and $aux_condareaproduccion_id
    and $aux_condstatusact_id
    and isnull(notaventa.anulada)
    and isnull(notaventadetalle.deleted_at)
    GROUP BY persona.id,persona.nombre;";

    $datas = DB::select($sql);
    $respuesta['vendedores'] = $datas;


    $sql = "SELECT grupoprod.id as grupoprod_id,grupoprod.gru_nombre,persona.id as persona_id,persona.nombre,
    sum(notaventadetalle.totalkilos) AS totalkilos,
    sum(notaventadetalle.subtotal) AS subtotal,
    round(sum((notaventadetalle.subtotal))*((notaventa.piva+100)/100)) AS totalmas_iva
    FROM notaventadetalle INNER JOIN producto
    ON notaventadetalle.producto_id=producto.id
    INNER JOIN categoriaprod
    ON producto.categoriaprod_id=categoriaprod.id and isnull(categoriaprod.deleted_at)
    INNER JOIN grupoprod
    ON producto.grupoprod_id=grupoprod.id and isnull(grupoprod.deleted_at)
    INNER JOIN notaventa 
    ON notaventadetalle.notaventa_id=notaventa.id and isnull(notaventa.deleted_at)
    INNER JOIN vendedor 
    ON notaventa.vendedor_id=vendedor.id and isnull(vendedor.deleted_at)
    INNER JOIN persona
    ON vendedor.persona_id=persona.id and isnull(persona.deleted_at)
    INNER JOIN cliente
    ON notaventa.cliente_id=cliente.id and isnull(cliente.deleted_at)
    WHERE $aux_condFecha
    and $vendedorcond
    and $aux_condcategoriaprod_id
    and $aux_condgiro_id
    and $aux_condareaproduccion_id
    and $aux_condstatusact_id
    and isnull(notaventa.anulada)
    and isnull(notaventadetalle.deleted_at)
    GROUP BY grupoprod.id,grupoprod.gru_nombre,persona.id,persona.nombre;";

    $datas = DB::select($sql);
    $respuesta['totales'] = $datas;
    //dd($respuesta['totales']);

    $sql = "SELECT producto.id as producto_id,categoriaprod.nombre,claseprod.cla_nombre,
    producto.long,producto.diametro,
    producto.tipounion,notaventadetalle.peso,color.nombre as color,
    categoriagrupovalmes.metacomerkg,categoriagrupovalmes.costo,
    grupoprod.id as gru_id,grupoprod.gru_nombre,
    sum(notaventadetalle.cant) AS cant,
    sum(notaventadetalle.totalkilos) AS totalkilos,
    sum(notaventadetalle.subtotal) AS subtotal,
    round(sum((notaventadetalle.subtotal))*((notaventa.piva+100)/100)) AS totalmas_iva
    FROM notaventadetalle INNER JOIN producto
    ON notaventadetalle.producto_id=producto.id
    INNER JOIN categoriaprod
    ON producto.categoriaprod_id=categoriaprod.id and isnull(categoriaprod.deleted_at)
    INNER JOIN claseprod
    ON producto.claseprod_id=claseprod.id and isnull(claseprod.deleted_at)
    INNER JOIN notaventa 
    ON notaventadetalle.notaventa_id=notaventa.id and isnull(notaventa.deleted_at)
    INNER JOIN cliente
    ON notaventa.cliente_id=cliente.id and isnull(cliente.deleted_at)
    LEFT JOIN color
    ON producto.color_id=color.id and isnull(color.deleted_at)
    INNER JOIN grupoprod
    ON producto.grupoprod_id=grupoprod.id and isnull(grupoprod.deleted_at)
    LEFT JOIN categoriagrupovalmes
    ON grupoprod.id=categoriagrupovalmes.grupoprod_id and categoriagrupovalmes.annomes='$annomes' and isnull(categoriagrupovalmes.deleted_at)
    WHERE $aux_condFecha
    and $vendedorcond
    and $aux_condcategoriaprod_id
    and $aux_condgiro_id
    and $aux_condareaproduccion_id
    and $aux_condstatusact_id
    and isnull(notaventa.anulada)
    and isnull(notaventadetalle.deleted_at)
    GROUP BY producto.id;";
    //dd($sql);
    //" and " . $aux_condrut .

    $datas = DB::select($sql);
    $respuesta['agruxproducto'] = $datas;

    $sql = "SELECT areaproduccion.id,areaproduccion.nombre,
    sum(notaventadetalle.totalkilos) AS totalkilos,
    sum(notaventadetalle.subtotal) AS subtotal,
    round(sum((notaventadetalle.subtotal))*((notaventa.piva+100)/100)) AS totalmas_iva
    FROM notaventadetalle INNER JOIN producto
    ON notaventadetalle.producto_id=producto.id
    INNER JOIN categoriaprod
    ON producto.categoriaprod_id=categoriaprod.id and isnull(categoriaprod.deleted_at)
    INNER JOIN notaventa 
    ON notaventadetalle.notaventa_id=notaventa.id and isnull(notaventa.deleted_at)
    INNER JOIN areaproduccion
    ON categoriaprod.areaproduccion_id = areaproduccion.id and isnull(areaproduccion.deleted_at)
    WHERE $aux_condFecha
    and $vendedorcond
    and $aux_condcategoriaprod_id
    and $aux_condgiro_id
    and $aux_condstatusact_id
    and areaproduccion.stapromkg=1
    and isnull(notaventa.anulada)
    and isnull(notaventadetalle.deleted_at)
    GROUP BY categoriaprod.areaproduccion_id
    ORDER BY categoriaprod.areaproduccion_id;";
    //dd($sql);
    //" and " . $aux_condrut .

    $datas = DB::select($sql);
    $respuesta['areaproduccion'] = $datas;
    //dd($respuesta['areaproduccion']);

    $sql = "SELECT areaproduccion.id,areaproduccion.nombre,
    sum(notaventadetalle.totalkilos) AS totalkilos,
    sum(notaventadetalle.subtotal) AS subtotal,
    round(sum((notaventadetalle.subtotal))*((notaventa.piva+100)/100)) AS totalmas_iva
    FROM notaventadetalle INNER JOIN producto
    ON notaventadetalle.producto_id=producto.id
    INNER JOIN categoriaprod
    ON producto.categoriaprod_id=categoriaprod.id and isnull(categoriaprod.deleted_at)
    INNER JOIN notaventa 
    ON notaventadetalle.notaventa_id=notaventa.id and isnull(notaventa.deleted_at)
    INNER JOIN areaproduccion
    ON categoriaprod.areaproduccion_id = areaproduccion.id and isnull(areaproduccion.deleted_at)
    WHERE $aux_condFechahoy
    and $vendedorcond
    and $aux_condcategoriaprod_id
    and $aux_condgiro_id
    and $aux_condstatusact_id
    and areaproduccion.stapromkg=1
    and isnull(notaventa.anulada)
    and isnull(notaventadetalle.deleted_at)
    GROUP BY categoriaprod.areaproduccion_id
    ORDER BY categoriaprod.areaproduccion_id;";
    //dd($sql);
    //" and " . $aux_condrut .

    $datas = DB::select($sql);
    $respuesta['areaproduccionhoy'] = $datas;
    //dd($respuesta['areaproduccionhoy']);

    $sql = " SET lc_time_names = 'es_ES';";
    $datas = DB::select($sql);

    $sql = "SELECT date_format(notaventa.fechahora,'%Y%m') AS annomes,
    MONTHNAME(notaventa.fechahora) AS mes,
    MONTH(notaventa.fechahora) AS nummes,
    sum(notaventadetalle.cant) AS cant,
    sum(notaventadetalle.totalkilos) AS totalkilos,
    sum(notaventadetalle.subtotal) AS subtotal,
    round(sum((notaventadetalle.subtotal))*((notaventa.piva+100)/100)) AS totalmas_iva
    FROM notaventadetalle INNER JOIN producto
    ON notaventadetalle.producto_id=producto.id
    INNER JOIN categoriaprod
    ON producto.categoriaprod_id=categoriaprod.id and isnull(categoriaprod.deleted_at)
    INNER JOIN claseprod
    ON producto.claseprod_id=claseprod.id and isnull(claseprod.deleted_at)
    INNER JOIN notaventa 
    ON notaventadetalle.notaventa_id=notaventa.id and isnull(notaventa.deleted_at)
    INNER JOIN cliente
    ON notaventa.cliente_id=cliente.id and isnull(cliente.deleted_at)
    INNER JOIN grupoprod
    ON producto.grupoprod_id=grupoprod.id and isnull(grupoprod.deleted_at)
    WHERE $aux_condanno
    and $vendedorcond
    and $aux_condcategoriaprod_id
    and $aux_condgiro_id
    and $aux_condareaproduccion_id
    and $aux_condstatusact_id
    and isnull(notaventa.anulada)
    and isnull(notaventadetalle.deleted_at)
    GROUP BY date_format(notaventa.fechahora,'%Y%m');";
    //dd($sql);
    //" and " . $aux_condrut .

    $datas = DB::select($sql);
    $respuesta['ventasxmes'] = $datas;


    $sql = "SELECT date_format(notaventa.fechahora,'%Y%m') AS annomes,
    categoriaprod.areaproduccion_id,areaproduccion.nombre,
    MONTHNAME(notaventa.fechahora) AS mes,
    MONTH(notaventa.fechahora) AS nummes,
    sum(notaventadetalle.cant) AS cant,
    sum(notaventadetalle.totalkilos) AS totalkilos,
    sum(notaventadetalle.subtotal) AS subtotal,
    round(sum((notaventadetalle.subtotal))*((notaventa.piva+100)/100)) AS totalmas_iva
    FROM notaventadetalle INNER JOIN producto
    ON notaventadetalle.producto_id=producto.id
    INNER JOIN categoriaprod
    ON producto.categoriaprod_id=categoriaprod.id and isnull(categoriaprod.deleted_at)
    INNER JOIN claseprod
    ON producto.claseprod_id=claseprod.id and isnull(claseprod.deleted_at)
    INNER JOIN notaventa 
    ON notaventadetalle.notaventa_id=notaventa.id and isnull(notaventa.deleted_at)
    INNER JOIN cliente
    ON notaventa.cliente_id=cliente.id and isnull(cliente.deleted_at)
    INNER JOIN grupoprod
    ON producto.grupoprod_id=grupoprod.id and isnull(grupoprod.deleted_at)
    INNER JOIN areaproduccion
    ON categoriaprod.areaproduccion_id=areaproduccion.id and isnull(areaproduccion.deleted_at)
    WHERE $aux_condanno
    and $vendedorcond
    and $aux_condcategoriaprod_id
    and $aux_condgiro_id
    and $aux_condstatusact_id
    and notaventa.id not in (select notaventa_id from notaventacerrada where isnull(notaventacerrada.deleted_at))
    and isnull(notaventa.anulada)
    and isnull(notaventadetalle.deleted_at)
    GROUP BY categoriaprod.areaproduccion_id,date_format(notaventa.fechahora,'%Y%m')
    ORDER BY date_format(notaventa.fechahora,'%Y%m'),categoriaprod.areaproduccion_id;";
    //dd($sql);
    //" and " . $aux_condrut .

    $datas = DB::select($sql);
    $respuesta['ventasareaprodxmes'] = $datas;



    return $respuesta;
}

function consultaODcerrada($request){
    $array = array('apellido', 'email', 'teléfono');
    $separado_por_comas = implode(",", $array);
    //dd(json_decode($request->vendedor_id));
    //$aux_vendedor = implode ( ',' , json_decode($request->vendedor_id));
    //dd($aux_vendedor);
    $respuesta = array();
    $respuesta['exito'] = true;
    $respuesta['mensaje'] = "Código encontrado";
    $respuesta['productos'] = "";
    $respuesta['vendedores'] = "";
/*
    if(empty($aux_vendedor )){
        $vendedorcond = " true ";
    }else{
        $vendedorcond = " notaventa.vendedor_id in ($aux_vendedor) ";
    }
*/
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
        if(is_array($request->vendedor_id)){
            $aux_vendedorid = implode ( ',' , $request->vendedor_id);
        }else{
            $aux_vendedorid = $request->vendedor_id;
        }
        $vendedorcond = " notaventa.vendedor_id in ($aux_vendedorid) ";

        //$vendedorcond = "notaventa.vendedor_id='$request->vendedor_id'";
    }

    //dd($vendedorcond);
    if(empty($request->fechad) or empty($request->fechah)){
        $aux_condFecha = " true";
        $annomes = date("Y") . date("m");
    }else{
        if($request->idcons == "2"){
            $fecha = date_create_from_format('d/m/Y', $request->fechad);
            $fechad = date_format($fecha, 'Y-m-d'); //." 00:00:00";
            $fecha = date_create_from_format('d/m/Y', $request->fechah);
            $fechah = date_format($fecha, 'Y-m-d'); //." 23:59:59";
            $aux_condFecha = "despachoord.fechafactura>='$fechad' and despachoord.fechafactura<='$fechah'";
        }else{
            $fecha = date_create_from_format('d/m/Y', $request->fechad);
            $fechad = date_format($fecha, 'Y-m-d')." 00:00:00";
            $fecha = date_create_from_format('d/m/Y', $request->fechah);
            $fechah = date_format($fecha, 'Y-m-d')." 23:59:59";
            $aux_condFecha = "notaventa.fechahora>='$fechad' and notaventa.fechahora<='$fechah'";
        }
        $annomes = date_format($fecha, 'Ym');
    }

    if($request->idcons == "2"){
        $auxVarcampoFecha = "despachoord.fechafactura";
    }else{
        $auxVarcampoFecha = "notaventa.fechahora";
    }

    
    if(empty($request->fechah)){
        /*CONSULTAR FECHA DE HOY SI EL CAMPO FECHA HASTA ESTA VACIO $aux_condFechahoy*/
        $fechahoy = date('Y-m-d');
        $aux_condFechahoy = "date_format($auxVarcampoFecha,'%Y-%m-%d')='$fechahoy'";
    
    }else{
        /*DE LO CONTRARIO TOMO LA FECHA DE $request->fechah */
        $fecha = date_create_from_format('d/m/Y', $request->fechah);
        $fechahoy = date_format($fecha, 'Y-m-d');
        $aux_condFechahoy = "date_format($auxVarcampoFecha,'%Y-%m-%d')='$fechahoy'";
    }

    if(empty($request->categoriaprod_id)){
        $aux_condcategoriaprod_id = " true";
    }else{
        $aux_condcategoriaprod_id = "categoriaprod.id='$request->categoriaprod_id'";
    }
    if(empty($request->giro_id)){
        $aux_condgiro_id = " true";
    }else{
        $aux_condgiro_id = "cliente.giro_id='$request->giro_id'";
    }

    if(empty($request->areaproduccion_id)){
        $aux_condareaproduccion_id = " true";
    }else{
        $aux_condareaproduccion_id = "categoriaprod.areaproduccion_id='$request->areaproduccion_id'";
    }

    switch ($request->statusact_id) {
        case 1:
            $aux_condstatusact_id = "notaventa.id not in (select notaventa_id from notaventacerrada where isnull(notaventacerrada.deleted_at))";
            break;
        case 2:
            $aux_condstatusact_id = "notaventa.id in (select notaventa_id from notaventacerrada where isnull(notaventacerrada.deleted_at))";
            break;
        case 3:
            $aux_condstatusact_id = " true";
            break;
    }

    /*CONDICION PARA CONSULTAR TODO EL AÑO SELECCIONADO PARA REPORTES DE TODO EL AÑO PARA LOS GRAFICOS ETC*/
    if(empty($request->anno)){
        $aux_condanno = " false ";
    }else{
        $aux_condanno = "date_format(despachoord.fechafactura,'%Y')='$request->anno'";
        if(!empty($request->fechah)){
            $fecha = date_create_from_format('d/m/Y', $request->fechah);
            $fechah = date_format($fecha, 'Y-m-d')." 23:59:59";
            $aux_condanno .= " and despachoord.fechafactura<='$fechah' ";
        }
    }

    //dd($aux_condanno);

    $sql = "SELECT grupoprod.id,grupoprod.gru_nombre,
    sum((notaventadetalle.totalkilos/notaventadetalle.cant) * despachoorddet.cantdesp) AS totalkilos,
    sum((notaventadetalle.preciounit * despachoorddet.cantdesp)) AS subtotal,
    round(sum((notaventadetalle.preciounit * despachoorddet.cantdesp))*((notaventa.piva+100)/100)) AS totalmas_iva,
    categoriagrupovalmes.metacomerkg,categoriagrupovalmes.costo
    FROM despachoorddet INNER JOIN notaventadetalle 
    ON despachoorddet.notaventadetalle_id=notaventadetalle.id and isnull(notaventadetalle.deleted_at) 
    INNER JOIN despachoord
    ON despachoorddet.despachoord_id=despachoord.id and isnull(despachoord.deleted_at) 
    INNER JOIN producto
    ON notaventadetalle.producto_id=producto.id and isnull(producto.deleted_at) 
    INNER JOIN categoriaprod
    ON producto.categoriaprod_id=categoriaprod.id and isnull(categoriaprod.deleted_at) 
    INNER JOIN grupoprod
    ON producto.grupoprod_id=grupoprod.id and isnull(grupoprod.deleted_at) 
    INNER JOIN notaventa 
    ON notaventadetalle.notaventa_id=notaventa.id and isnull(notaventa.deleted_at)
    INNER JOIN cliente
    ON notaventa.cliente_id=cliente.id and isnull(cliente.deleted_at)
    LEFT JOIN categoriagrupovalmes
    ON grupoprod.id=categoriagrupovalmes.grupoprod_id and categoriagrupovalmes.annomes='$annomes' and isnull(categoriagrupovalmes.deleted_at)
    WHERE (despachoord.guiadespacho IS NOT NULL AND despachoord.numfactura IS NOT NULL)
    and $aux_condFecha
    and $vendedorcond
    and $aux_condcategoriaprod_id
    and $aux_condgiro_id
    and $aux_condareaproduccion_id
    and $aux_condstatusact_id
    and isnull(notaventa.anulada)
    and isnull(despachoorddet.deleted_at) 
    and despachoord.id not in (SELECT despachoord_id FROM despachoordanul where isnull(despachoordanul.deleted_at))
    GROUP BY grupoprod.id,grupoprod.gru_nombre;";
    //dd($sql);
    //" and " . $aux_condrut .

    $datas = DB::select($sql);
    $respuesta['productos'] = $datas;

    $sql = "SELECT persona.id,persona.nombre,
    ROUND(sum((notaventadetalle.totalkilos/notaventadetalle.cant) * despachoorddet.cantdesp),2) AS totalkilos,
    sum((notaventadetalle.preciounit * despachoorddet.cantdesp)) AS subtotal,
    round(sum((notaventadetalle.preciounit * despachoorddet.cantdesp))*((notaventa.piva+100)/100)) AS totalmas_iva
    FROM despachoorddet INNER JOIN notaventadetalle 
    ON despachoorddet.notaventadetalle_id=notaventadetalle.id and isnull(notaventadetalle.deleted_at)
    INNER JOIN despachoord
    ON despachoorddet.despachoord_id=despachoord.id and isnull(despachoord.deleted_at)
    INNER JOIN producto
    ON notaventadetalle.producto_id=producto.id and isnull(producto.deleted_at)
    INNER JOIN categoriaprod
    ON producto.categoriaprod_id=categoriaprod.id and isnull(categoriaprod.deleted_at)
    INNER JOIN grupoprod
    ON producto.grupoprod_id=grupoprod.id and isnull(grupoprod.deleted_at)
    INNER JOIN notaventa 
    ON notaventadetalle.notaventa_id=notaventa.id and isnull(notaventa.deleted_at)
    INNER JOIN vendedor 
    ON notaventa.vendedor_id=vendedor.id and isnull(vendedor.deleted_at)
    INNER JOIN persona
    ON vendedor.persona_id=persona.id and isnull(persona.deleted_at)
    INNER JOIN cliente
    ON notaventa.cliente_id=cliente.id and isnull(cliente.deleted_at)
    WHERE (despachoord.guiadespacho IS NOT NULL AND despachoord.numfactura IS NOT NULL)
    and $aux_condFecha
    and $vendedorcond
    and $aux_condcategoriaprod_id
    and $aux_condgiro_id
    and $aux_condareaproduccion_id
    and $aux_condstatusact_id
    and isnull(notaventa.anulada)
    and isnull(despachoorddet.deleted_at)
    and despachoord.id not in (SELECT despachoord_id FROM despachoordanul where isnull(despachoordanul.deleted_at))
    GROUP BY persona.id,persona.nombre;";

    $datas = DB::select($sql);
    $respuesta['vendedores'] = $datas;


    $sql = "SELECT grupoprod.id as grupoprod_id,grupoprod.gru_nombre,persona.id as persona_id,persona.nombre,
    sum((notaventadetalle.totalkilos/notaventadetalle.cant) * despachoorddet.cantdesp) AS totalkilos,
    sum((notaventadetalle.preciounit * despachoorddet.cantdesp)) AS subtotal,
    round(sum((notaventadetalle.preciounit * despachoorddet.cantdesp))*((notaventa.piva+100)/100)) AS totalmas_iva
    FROM despachoorddet INNER JOIN notaventadetalle 
    ON despachoorddet.notaventadetalle_id=notaventadetalle.id and isnull(notaventadetalle.deleted_at) 
    INNER JOIN despachoord
    ON despachoorddet.despachoord_id=despachoord.id and isnull(despachoord.deleted_at) 
    INNER JOIN producto
    ON notaventadetalle.producto_id=producto.id and isnull(producto.deleted_at) 
    INNER JOIN categoriaprod
    ON producto.categoriaprod_id=categoriaprod.id and isnull(categoriaprod.deleted_at) 
    INNER JOIN grupoprod
    ON producto.grupoprod_id=grupoprod.id and isnull(grupoprod.deleted_at) 
    INNER JOIN notaventa 
    ON notaventadetalle.notaventa_id=notaventa.id and isnull(notaventa.deleted_at)
    INNER JOIN vendedor 
    ON notaventa.vendedor_id=vendedor.id and isnull(vendedor.deleted_at)
    INNER JOIN persona
    ON vendedor.persona_id=persona.id and isnull(persona.deleted_at)
    INNER JOIN cliente
    ON notaventa.cliente_id=cliente.id and isnull(cliente.deleted_at)
    WHERE (despachoord.guiadespacho IS NOT NULL AND despachoord.numfactura IS NOT NULL)
    and $aux_condFecha
    and $vendedorcond
    and $aux_condcategoriaprod_id
    and $aux_condgiro_id
    and $aux_condareaproduccion_id
    and isnull(notaventa.anulada)
    and isnull(despachoorddet.deleted_at)
    and $aux_condstatusact_id
    and despachoord.id not in (SELECT despachoord_id FROM despachoordanul where isnull(despachoordanul.deleted_at))
    GROUP BY grupoprod.id,grupoprod.gru_nombre,persona.id,persona.nombre;";

    $datas = DB::select($sql);
    $respuesta['totales'] = $datas;
    //dd($respuesta['totales']);
/*
    $sql = "SELECT producto.id as producto_id,categoriaprod.nombre,claseprod.cla_nombre,
    producto.long,producto.diametro,
    producto.tipounion,notaventadetalle.peso,color.nombre as color,
    categoriagrupovalmes.metacomerkg,categoriagrupovalmes.costo,
    grupoprod.id as gru_id,grupoprod.gru_nombre,
    sum(despachoorddet.cantdesp) AS cant,
    sum((notaventadetalle.totalkilos/notaventadetalle.cant) * despachoorddet.cantdesp) AS totalkilos,
    sum((notaventadetalle.preciounit * despachoorddet.cantdesp)) AS subtotal,
    round(sum((notaventadetalle.preciounit * despachoorddet.cantdesp))*((notaventa.piva+100)/100)) AS totalmas_iva
    FROM despachoorddet INNER JOIN notaventadetalle 
    ON despachoorddet.notaventadetalle_id=notaventadetalle.id and isnull(notaventadetalle.deleted_at) 
    INNER JOIN despachoord
    ON despachoorddet.despachoord_id=despachoord.id and isnull(despachoord.deleted_at) 
    INNER JOIN producto
    ON notaventadetalle.producto_id=producto.id and isnull(producto.deleted_at) 
    INNER JOIN categoriaprod
    ON producto.categoriaprod_id=categoriaprod.id and isnull(categoriaprod.deleted_at) 
    INNER JOIN claseprod
    ON producto.claseprod_id=claseprod.id and isnull(claseprod.deleted_at) 
    INNER JOIN notaventa 
    ON notaventadetalle.notaventa_id=notaventa.id and isnull(notaventa.deleted_at)
    INNER JOIN cliente
    ON notaventa.cliente_id=cliente.id and isnull(cliente.deleted_at)
    LEFT JOIN color
    ON producto.color_id=color.id and isnull(color.deleted_at)
    INNER JOIN grupoprod
    ON producto.grupoprod_id=grupoprod.id and isnull(grupoprod.deleted_at)
    LEFT JOIN categoriagrupovalmes
    ON grupoprod.id=categoriagrupovalmes.grupoprod_id and categoriagrupovalmes.annomes='$annomes' and isnull(categoriagrupovalmes.deleted_at)
    WHERE (despachoord.guiadespacho IS NOT NULL AND despachoord.numfactura IS NOT NULL)
    and $aux_condFecha
    and $vendedorcond
    and $aux_condcategoriaprod_id
    and $aux_condgiro_id
    and $aux_condareaproduccion_id
    and $aux_condstatusact_id
    and isnull(notaventa.anulada)
    and isnull(despachoorddet.deleted_at)
    and despachoord.id not in (SELECT despachoord_id FROM despachoordanul where isnull(despachoordanul.deleted_at))
    GROUP BY producto.id;";
*/
    $sql = "SELECT producto.id as producto_id,categoriaprod.nombre,claseprod.cla_nombre,
    producto.long,producto.diametro,
    producto.tipounion,notaventadetalle.peso,color.nombre as color,
    categoriagrupovalmes.metacomerkg,categoriagrupovalmes.costo,
    grupoprod.id as gru_id,grupoprod.gru_nombre,
    sum(vista_despachoorddet.cantdesp) AS cant,
    sum((notaventadetalle.totalkilos/notaventadetalle.cant) * vista_despachoorddet.cantdesp) AS totalkilos,
    sum((notaventadetalle.preciounit * vista_despachoorddet.cantdesp)) AS subtotal,
    round(sum((notaventadetalle.preciounit * vista_despachoorddet.cantdesp))*((notaventa.piva+100)/100)) AS totalmas_iva
    FROM vista_despachoorddet INNER JOIN notaventadetalle 
    ON vista_despachoorddet.notaventadetalle_id=notaventadetalle.id and isnull(notaventadetalle.deleted_at) 
    INNER JOIN despachoord
    ON vista_despachoorddet.despachoord_id=despachoord.id and isnull(despachoord.deleted_at) 
    INNER JOIN producto
    ON notaventadetalle.producto_id=producto.id and isnull(producto.deleted_at) 
    INNER JOIN categoriaprod
    ON producto.categoriaprod_id=categoriaprod.id and isnull(categoriaprod.deleted_at) 
    INNER JOIN claseprod
    ON producto.claseprod_id=claseprod.id and isnull(claseprod.deleted_at) 
    INNER JOIN notaventa 
    ON notaventadetalle.notaventa_id=notaventa.id and isnull(notaventa.deleted_at)
    INNER JOIN cliente
    ON notaventa.cliente_id=cliente.id and isnull(cliente.deleted_at)
    LEFT JOIN color
    ON producto.color_id=color.id and isnull(color.deleted_at)
    INNER JOIN grupoprod
    ON producto.grupoprod_id=grupoprod.id and isnull(grupoprod.deleted_at)
    LEFT JOIN categoriagrupovalmes
    ON grupoprod.id=categoriagrupovalmes.grupoprod_id and categoriagrupovalmes.annomes='$annomes' and isnull(categoriagrupovalmes.deleted_at)
    WHERE (despachoord.guiadespacho IS NOT NULL AND despachoord.numfactura IS NOT NULL)
    and $aux_condFecha
    and $vendedorcond
    and $aux_condcategoriaprod_id
    and $aux_condgiro_id
    and $aux_condareaproduccion_id
    and $aux_condstatusact_id
    and isnull(notaventa.anulada)
    and isnull(vista_despachoorddet.deleted_at)
    and despachoord.id not in (SELECT despachoord_id FROM despachoordanul where isnull(despachoordanul.deleted_at))
    GROUP BY producto.id;";
/*
    CONSULTA DE LOS RECHAZOS, LA VOY A DEJAR AQUI PARA INCLUIRLA DESPUES EN LA CONSULTA 20/01/2022
    $sql = "SELECT producto.id as producto_id,categoriaprod.nombre,claseprod.cla_nombre,
    producto.long,producto.diametro,
    producto.tipounion,notaventadetalle.peso,color.nombre as color,
    categoriagrupovalmes.metacomerkg,categoriagrupovalmes.costo,
    grupoprod.id as gru_id,grupoprod.gru_nombre,
    SUM(despachoordrecdet.cantrec) AS cant,
    sum((notaventadetalle.totalkilos/notaventadetalle.cant) * despachoordrecdet.cantrec) AS totalkilos,
    sum((notaventadetalle.preciounit * despachoordrecdet.cantrec)) AS subtotal,
    round(sum((notaventadetalle.preciounit * despachoordrecdet.cantrec))*((notaventa.piva+100)/100)) AS totalmas_iva
    FROM despachoordrec inner join despachoordrecdet
    ON despachoordrec.id=despachoordrecdet.despachoordrec_id and isnull(despachoordrec.deleted_at)  and isnull(despachoordrecdet.deleted_at) 
    INNER JOIN despachoorddet
    ON despachoordrecdet.despachoorddet_id=despachoorddet.id and isnull(despachoorddet.deleted_at) 
    INNER JOIN notaventadetalle
    on despachoorddet.notaventadetalle_id=notaventadetalle.id and isnull(notaventadetalle.deleted_at) 
    INNER JOIN producto
    ON notaventadetalle.producto_id=producto.id and isnull(producto.deleted_at) 
    INNER JOIN categoriaprod
    ON producto.categoriaprod_id=categoriaprod.id and isnull(categoriaprod.deleted_at)
    INNER JOIN claseprod
    ON producto.claseprod_id=claseprod.id and isnull(claseprod.deleted_at) 
    INNER JOIN notaventa 
    ON notaventadetalle.notaventa_id=notaventa.id and isnull(notaventa.deleted_at)
    INNER JOIN cliente
    ON notaventa.cliente_id=cliente.id and isnull(cliente.deleted_at)
    LEFT JOIN color
    ON producto.color_id=color.id and isnull(color.deleted_at)
    INNER JOIN grupoprod
    ON producto.grupoprod_id=grupoprod.id and isnull(grupoprod.deleted_at)
    LEFT JOIN categoriagrupovalmes
    ON grupoprod.id=categoriagrupovalmes.grupoprod_id and categoriagrupovalmes.annomes='$annomes' and isnull(categoriagrupovalmes.deleted_at)
    group BY notaventadetalle.producto_id;";
*/
    //dd($sql);
    $datas = DB::select($sql);
    $respuesta['agruxproducto'] = $datas;

    $sql = "SELECT areaproduccion.id,areaproduccion.nombre,categoriagrupovalmes.costo,
    sum((notaventadetalle.totalkilos/notaventadetalle.cant) * despachoorddet.cantdesp) AS totalkilos,
    sum((notaventadetalle.preciounit * despachoorddet.cantdesp)) AS subtotal,
    round(sum((notaventadetalle.preciounit * despachoorddet.cantdesp))*((notaventa.piva+100)/100)) AS totalmas_iva
    FROM despachoorddet INNER JOIN notaventadetalle 
    ON despachoorddet.notaventadetalle_id=notaventadetalle.id and isnull(notaventadetalle.deleted_at) 
    INNER JOIN despachoord
    ON despachoorddet.despachoord_id=despachoord.id and isnull(despachoord.deleted_at)
    INNER JOIN producto
    ON notaventadetalle.producto_id=producto.id and isnull(producto.deleted_at) 
    INNER JOIN categoriaprod
    ON producto.categoriaprod_id=categoriaprod.id and isnull(categoriaprod.deleted_at)
    INNER JOIN notaventa 
    ON notaventadetalle.notaventa_id=notaventa.id and isnull(notaventa.deleted_at)
    INNER JOIN areaproduccion
    ON categoriaprod.areaproduccion_id = areaproduccion.id and isnull(areaproduccion.deleted_at)
    INNER JOIN grupoprod
    ON producto.grupoprod_id=grupoprod.id and isnull(grupoprod.deleted_at)
    LEFT JOIN categoriagrupovalmes
    ON grupoprod.id=categoriagrupovalmes.grupoprod_id and categoriagrupovalmes.annomes='$annomes' and isnull(categoriagrupovalmes.deleted_at)
    WHERE (despachoord.guiadespacho IS NOT NULL AND despachoord.numfactura IS NOT NULL)
    and $aux_condFecha
    and $vendedorcond
    and $aux_condcategoriaprod_id
    and $aux_condgiro_id
    and $aux_condstatusact_id
    and areaproduccion.stapromkg=1
    and isnull(notaventa.anulada)
    and isnull(despachoorddet.deleted_at)
    and despachoord.id not in (SELECT despachoord_id FROM despachoordanul where isnull(despachoordanul.deleted_at))
    GROUP BY categoriaprod.areaproduccion_id
    ORDER BY categoriaprod.areaproduccion_id;";
    //dd($sql);
    $datas = DB::select($sql);
    $respuesta['areaproduccion'] = $datas;

    $sql = "SELECT areaproduccion.id,areaproduccion.nombre,
    sum((notaventadetalle.totalkilos/notaventadetalle.cant) * despachoorddet.cantdesp) AS totalkilos,
    sum((notaventadetalle.preciounit * despachoorddet.cantdesp)) AS subtotal,
    round(sum((notaventadetalle.preciounit * despachoorddet.cantdesp))*((notaventa.piva+100)/100)) AS totalmas_iva
    FROM despachoorddet INNER JOIN notaventadetalle 
    ON despachoorddet.notaventadetalle_id=notaventadetalle.id and isnull(notaventadetalle.deleted_at) 
    INNER JOIN despachoord
    ON despachoorddet.despachoord_id=despachoord.id and isnull(despachoord.deleted_at) 
    INNER JOIN producto
    ON notaventadetalle.producto_id=producto.id and isnull(producto.deleted_at)
    INNER JOIN categoriaprod
    ON producto.categoriaprod_id=categoriaprod.id and isnull(categoriaprod.deleted_at)
    INNER JOIN notaventa 
    ON notaventadetalle.notaventa_id=notaventa.id and isnull(notaventa.deleted_at)
    INNER JOIN areaproduccion
    ON categoriaprod.areaproduccion_id = areaproduccion.id and isnull(areaproduccion.deleted_at)
    WHERE (despachoord.guiadespacho IS NOT NULL AND despachoord.numfactura IS NOT NULL)
    and $aux_condFechahoy
    and $vendedorcond
    and $aux_condcategoriaprod_id
    and $aux_condgiro_id
    and $aux_condstatusact_id
    and areaproduccion.stapromkg=1
    and isnull(notaventa.anulada)
    and isnull(despachoorddet.deleted_at)
    and despachoord.id not in (SELECT despachoord_id FROM despachoordanul where isnull(despachoordanul.deleted_at))
    GROUP BY categoriaprod.areaproduccion_id
    ORDER BY categoriaprod.areaproduccion_id;";

    $datas = DB::select($sql);
    $respuesta['areaproduccionhoy'] = $datas;

    $sql = " SET lc_time_names = 'es_ES';";
    $datas = DB::select($sql);

    $sql = "SELECT date_format(despachoord.fechafactura,'%Y%m') AS annomes,
    MONTHNAME(despachoord.fechafactura) AS mes,
    sum(despachoorddet.cantdesp) AS cant,
    sum((notaventadetalle.totalkilos/notaventadetalle.cant) * despachoorddet.cantdesp) AS totalkilos,
    sum((notaventadetalle.preciounit * despachoorddet.cantdesp)) AS subtotal,
    round(sum((notaventadetalle.preciounit * despachoorddet.cantdesp))*((notaventa.piva+100)/100)) AS totalmas_iva
    FROM despachoorddet INNER JOIN notaventadetalle 
    ON despachoorddet.notaventadetalle_id=notaventadetalle.id and isnull(notaventadetalle.deleted_at) 
    INNER JOIN despachoord
    ON despachoorddet.despachoord_id=despachoord.id and isnull(despachoord.deleted_at) 
    INNER JOIN producto
    ON notaventadetalle.producto_id=producto.id and isnull(producto.deleted_at) 
    INNER JOIN categoriaprod
    ON producto.categoriaprod_id=categoriaprod.id and isnull(categoriaprod.deleted_at) 
    INNER JOIN claseprod
    ON producto.claseprod_id=claseprod.id and isnull(claseprod.deleted_at) 
    INNER JOIN notaventa 
    ON notaventadetalle.notaventa_id=notaventa.id and isnull(notaventa.deleted_at)
    INNER JOIN cliente
    ON notaventa.cliente_id=cliente.id and isnull(cliente.deleted_at)
    INNER JOIN grupoprod
    ON producto.grupoprod_id=grupoprod.id and isnull(grupoprod.deleted_at)
    WHERE (despachoord.guiadespacho IS NOT NULL AND despachoord.numfactura IS NOT NULL)
    and $aux_condanno
    and $vendedorcond
    and $aux_condcategoriaprod_id
    and $aux_condgiro_id
    and $aux_condareaproduccion_id
    and $aux_condstatusact_id
    and isnull(notaventa.anulada)
    and isnull(despachoorddet.deleted_at)
    and despachoord.id not in (SELECT despachoord_id FROM despachoordanul where isnull(despachoordanul.deleted_at))
    GROUP BY date_format(despachoord.fechafactura,'%Y%m');";

    $datas = DB::select($sql);
    $respuesta['ventasxmes'] = $datas;

    $sql = "SELECT date_format(despachoord.fechafactura,'%Y%m') AS annomes,
    categoriaprod.areaproduccion_id,areaproduccion.nombre,
    MONTHNAME(despachoord.fechafactura) AS mes,
    MONTH(despachoord.fechafactura) AS nummes,
    sum(despachoorddet.cantdesp) AS cant,
    sum((notaventadetalle.totalkilos/notaventadetalle.cant) * despachoorddet.cantdesp) AS totalkilos,
    sum((notaventadetalle.preciounit * despachoorddet.cantdesp)) AS subtotal,
    round(sum((notaventadetalle.preciounit * despachoorddet.cantdesp))*((notaventa.piva+100)/100)) AS totalmas_iva
    FROM despachoorddet INNER JOIN notaventadetalle 
    ON despachoorddet.notaventadetalle_id=notaventadetalle.id and isnull(notaventadetalle.deleted_at) 
    INNER JOIN despachoord
    ON despachoorddet.despachoord_id=despachoord.id and isnull(despachoord.deleted_at) 
    INNER JOIN producto
    ON notaventadetalle.producto_id=producto.id and isnull(producto.deleted_at) 
    INNER JOIN categoriaprod
    ON producto.categoriaprod_id=categoriaprod.id and isnull(categoriaprod.deleted_at) 
    INNER JOIN notaventa 
    ON notaventadetalle.notaventa_id=notaventa.id and isnull(notaventa.deleted_at)
    INNER JOIN areaproduccion
    ON categoriaprod.areaproduccion_id=areaproduccion.id and isnull(areaproduccion.deleted_at)
    WHERE (despachoord.guiadespacho IS NOT NULL AND despachoord.numfactura IS NOT NULL)
    and $aux_condanno
    and $vendedorcond
    and $aux_condcategoriaprod_id
    and $aux_condgiro_id
    and $aux_condstatusact_id
    and notaventa.id not in (select notaventa_id from notaventacerrada where isnull(notaventacerrada.deleted_at))
    and isnull(notaventa.anulada)
    and isnull(despachoorddet.deleted_at)
    and despachoord.id not in (SELECT despachoord_id FROM despachoordanul where isnull(despachoordanul.deleted_at))
    GROUP BY categoriaprod.areaproduccion_id,date_format(despachoord.fechafactura,'%Y%m')
    ORDER BY date_format(despachoord.fechafactura,'%Y%m'),categoriaprod.areaproduccion_id;";

    //dd($sql);
    $datas = DB::select($sql);
    $respuesta['ventasareaprodxmes'] = $datas;

    return $respuesta;
}

function tablaAgruxProductoMargen($datas){
    //TABLA TOTALES POR PRODUCTO
    $tabla = "<table id='tablaagruxproductoMargen' name='tablaagruxproductoMargen' class='table display AllDataTables table-hover table-condensed tablascons' data-page-length='50'>
        <thead>
            <tr>
                <th>Productos</th>
                <th>Cod</th>
                <th>Diametro</th>
                <th>Long</th>
                <th>Clase</th>
                <th>Peso Unid</th>
                <th>TU</th>
                <th>Color</th>
                <th style='text-align:right'>Unid</th>
                <th style='text-align:right'>KG</th>
                <th style='text-align:right'>Prom Unit</th>
                <th style='text-align:right'>Prom Kilo</th>
                <th style='text-align:right'>Ventas $</th>
                <th style='text-align:right'>Costo</br>formula Kg</th>
                <th style='text-align:right'>Margen</br>Aporte</th>
                <th style='text-align:right'>Margen</br>venta</th>
                <th style='text-align:right'>Prom</br>Grupo</th>
            </tr>
        </thead>
        <tbody>";

    $aux_sumpromkilo = 0;
    $totalgeneralfilakg = 0;
    $aux_totalsubtotal = 0;
    $aux_totalmargenVenta = 0;
    $sum_grupo = 0;
    $sum_KgGrupo = 0;
    $i = 0;
    foreach($datas as $producto){
        $aux_promunit = 0;
        if($producto->cant>0){
            $aux_promunit = $producto->subtotal/$producto->cant;
        }
        $aux_preciopromkilo = 0;
        if($producto->totalkilos>0){
            $aux_preciopromkilo = $producto->subtotal/$producto->totalkilos;
        }
        $aux_sumpromkilo += $aux_preciopromkilo;
        $aux_margenAporte = $aux_preciopromkilo - $producto->costo;
        $aux_margenVenta = $producto->totalkilos * $aux_margenAporte;
        $sum_grupo += $producto->subtotal;
        $sum_KgGrupo += $producto->totalkilos;
        $tabla .= "
            <tr class='btn-accion-tabla tooltipsC'>
                <td>$producto->nombre</td>
                <td>$producto->producto_id</td>
                <td>$producto->diametro</td>
                <td>$producto->long</td>
                <td>$producto->cla_nombre</td>
                <td>$producto->peso</td>
                <td>$producto->tipounion</td>
                <td>$producto->color</td>
                <td style='text-align:right' data-order='$producto->cant' data-search='$producto->cant'>$producto->cant</td>
                <td style='text-align:right' data-order='$producto->totalkilos' data-search='$producto->totalkilos'>" . number_format($producto->totalkilos, 2, ",", ".") . "</td>
                <td style='text-align:right' data-order='$aux_promunit' data-search='$aux_promunit'>" . number_format($aux_promunit, 2, ",", ".") . "</td>
                <td style='text-align:right' data-order='$aux_preciopromkilo' data-search='$aux_preciopromkilo'>" . number_format($aux_preciopromkilo, 2, ",", ".") . "</td>
                <td style='text-align:right' data-order='$producto->subtotal' data-search='$producto->subtotal'>" . number_format($producto->subtotal, 0, ",", ".") . "</td>
                <td style='text-align:right' data-order='$producto->costo' data-search='$producto->costo'>" . number_format($producto->costo, 0, ",", ".") . "</td>
                <td style='text-align:right' data-order='$aux_margenAporte' data-search='$aux_margenAporte'>" . number_format($aux_margenAporte, 0, ",", ".") . "</td>
                <td style='text-align:right' data-order='$aux_margenVenta' data-search='$aux_margenVenta'>" . number_format($aux_margenVenta, 0, ",", ".") . "</td>";

        //dd(count($datas));
        if( (count($datas) == ($i +1)) or ($producto->gru_id != $datas[$i + 1]->gru_id)){
            if($sum_KgGrupo<=0){
                $sum_KgGrupo = 1;
            }
            $aux_promgrup = ($sum_grupo / $sum_KgGrupo);
            $tabla .= "<td style='text-align:right' data-order='$aux_promgrup' data-search='$aux_promgrup' class='tooltipsC' title='$producto->gru_nombre'><b>" . number_format($aux_promgrup, 0, ",", ".") . "</b></td>";
            $sum_grupo = 0;
            $sum_KgGrupo = 0;        
        }else{
            $tabla .= "<td style='text-align:right' data-order='' data-search=''></td>";
        }
        $tabla .= "</tr>";
        $totalgeneralfilakg += $producto->totalkilos;
        $aux_totalsubtotal += $producto->subtotal;
        $aux_totalmargenVenta += $aux_margenVenta;
        $i++;
    }
    $aux_prom1 = 0;
    if($totalgeneralfilakg > 0){
        $aux_prom1 = $aux_totalsubtotal/$totalgeneralfilakg;
    }
    $aux_prom2 = 0;
    if($aux_totalsubtotal > 0){
        $aux_prom2 = $aux_totalmargenVenta/$aux_totalsubtotal;
    }
    $tabla .= "
        </tbody>
        <tfoot>
            <tr>
                <th>TOTAL</th>
                <th colspan='9' style='text-align:right'>". number_format($totalgeneralfilakg, 2, ",", ".") ."</th>
                <th></th>
                <th style='text-align:right'>". number_format($aux_prom1, 2, ",", ".") ."</th>
                <th style='text-align:right'>". number_format($aux_totalsubtotal, 0, ",", ".") ."</th>
                <th style='text-align:right'></th>
                <th style='text-align:right'>". number_format(($aux_prom2)*100, 0, ",", ".") ."%</th>
                <th style='text-align:right'>". number_format($aux_totalmargenVenta, 0, ",", ".") ."</th>
                <th style='text-align:right'></th>
            </tr>
        </tfoot>
    </table>";
    return $tabla;
}

function consultakilostipoentrega($request){
    //$aux_vendedor = implode ( ',' , json_decode($request->vendedor_id));
    $aux_areaproduccion = implode ( ',' , json_decode($request->areaproduccion_id));
    $respuesta = array();
    $respuesta['exito'] = true;
    $respuesta['mensaje'] = "Código encontrado";
    $respuesta['productos'] = "";
    $respuesta['vendedores'] = "";
/*
    if(empty($aux_vendedor )){
        $vendedorcond = " true ";
    }else{
        $vendedorcond = " notaventa.vendedor_id in ($aux_vendedor) ";
    }
*/
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
        if(is_array($request->vendedor_id)){
            $aux_vendedorid = implode ( ',' , $request->vendedor_id);
        }else{
            $aux_vendedorid = $request->vendedor_id;
        }
        $vendedorcond = " notaventa.vendedor_id in ($aux_vendedorid) ";

        //$vendedorcond = "notaventa.vendedor_id='$request->vendedor_id'";
    }
    //dd($vendedorcond);
    if(empty($request->fechad) or empty($request->fechah)){
        $aux_condFecha = " true";
    }else{
        if($request->idcons == "2"){
            $fecha = date_create_from_format('d/m/Y', $request->fechad);
            $fechad = date_format($fecha, 'Y-m-d'); //." 00:00:00";
            $fecha = date_create_from_format('d/m/Y', $request->fechah);
            $fechah = date_format($fecha, 'Y-m-d'); //." 23:59:59";
            $aux_condFecha = "despachoord.fechafactura>='$fechad' and despachoord.fechafactura<='$fechah'";
        }else{
            $fecha = date_create_from_format('d/m/Y', $request->fechad);
            $fechad = date_format($fecha, 'Y-m-d')." 00:00:00";
            $fecha = date_create_from_format('d/m/Y', $request->fechah);
            $fechah = date_format($fecha, 'Y-m-d')." 23:59:59";
            $aux_condFecha = "notaventa.fechahora>='$fechad' and notaventa.fechahora<='$fechah'";
        }
    }

    if(empty($request->categoriaprod_id)){
        $aux_condcategoriaprod_id = " true";
    }else{
        $aux_condcategoriaprod_id = "categoriaprod.id='$request->categoriaprod_id'";
    }
    if(empty($request->giro_id)){
        $aux_condgiro_id = " true";
    }else{
        $aux_condgiro_id = "cliente.giro_id='$request->giro_id'";
    }

    if(empty($aux_areaproduccion )){
        $aux_condareaproduccion_id = " true ";
    }else{
        $aux_condareaproduccion_id = " categoriaprod.areaproduccion_id in ($aux_areaproduccion) ";
    }

    switch ($request->statusact_id) {
        case 1:
            $aux_condstatusact_id = "notaventa.id not in (select notaventa_id from notaventacerrada where isnull(notaventacerrada.deleted_at))";
            break;
        case 2:
            $aux_condstatusact_id = "notaventa.id in (select notaventa_id from notaventacerrada where isnull(notaventacerrada.deleted_at))";
            break;
        case 3:
            $aux_condstatusact_id = " true";
            break;
    }

    $sql = "SELECT despachoord.tipoentrega_id,tipoentrega.nombre,tipoentrega.icono,
    sum((notaventadetalle.totalkilos/notaventadetalle.cant) * despachoorddet.cantdesp) AS totalkilos,
    sum((notaventadetalle.preciounit * despachoorddet.cantdesp)) AS subtotal,
    round(sum((notaventadetalle.preciounit * despachoorddet.cantdesp))*((notaventa.piva+100)/100)) AS totalmas_iva
    FROM despachoorddet INNER JOIN notaventadetalle 
    ON despachoorddet.notaventadetalle_id=notaventadetalle.id and isnull(notaventadetalle.deleted_at) 
    INNER JOIN despachoord
    ON despachoorddet.despachoord_id=despachoord.id and isnull(despachoord.deleted_at)
    INNER JOIN producto
    ON notaventadetalle.producto_id=producto.id and isnull(producto.deleted_at) 
    INNER JOIN categoriaprod
    ON producto.categoriaprod_id=categoriaprod.id and isnull(categoriaprod.deleted_at)
    INNER JOIN notaventa 
    ON notaventadetalle.notaventa_id=notaventa.id and isnull(notaventa.deleted_at)
    INNER JOIN areaproduccion
    ON categoriaprod.areaproduccion_id = areaproduccion.id and isnull(areaproduccion.deleted_at)
    INNER JOIN grupoprod
    ON producto.grupoprod_id=grupoprod.id and isnull(grupoprod.deleted_at)
    inner join tipoentrega
    on despachoord.tipoentrega_id=tipoentrega.id
    WHERE (despachoord.guiadespacho IS NOT NULL AND despachoord.numfactura IS NOT NULL)
    and $aux_condFecha
    and $vendedorcond
    and $aux_condcategoriaprod_id
    and $aux_condgiro_id
    and $aux_condstatusact_id
    and $aux_condareaproduccion_id
    and areaproduccion.stapromkg=1
    and isnull(notaventa.anulada)
    and isnull(despachoorddet.deleted_at)
    and despachoord.id not in (SELECT despachoord_id FROM despachoordanul where isnull(despachoordanul.deleted_at))
    GROUP BY despachoord.tipoentrega_id
    ORDER BY despachoord.tipoentrega_id;";
    //dd($sql);
    $datas = DB::select($sql);
    $respuesta['kilosxtipoentrega'] = $datas;
    //dd($respuesta['kilosxtipoentrega']);

    return $respuesta;
}

