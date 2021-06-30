<?php

namespace App\Http\Controllers;

use App\Models\AreaProduccion;
use App\Models\CategoriaProd;
use App\Models\Cliente;
use App\Models\ClienteSucursal;
use App\Models\ClienteVendedor;
use App\Models\Empresa;
use App\Models\Giro;
use App\Models\Producto;
use App\Models\Seguridad\Usuario;
use App\Models\Vendedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade as PDF;

class NVIndicadorxVendController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
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
        /*
        $categoriaprods = CategoriaProd::join('categoriaprodsuc', function ($join) {
            $user = Usuario::findOrFail(auth()->id());
            $sucurArray = $user->sucursales->pluck('id')->toArray();
            $join->on('categoriaprod.id', '=', 'categoriaprodsuc.categoriaprod_id')
            ->whereIn('categoriaprodsuc.sucursal_id', $sucurArray);
                    })
            ->select([
                'categoriaprod.id',
                'categoriaprod.nombre',
                'categoriaprod.descripcion',
                'categoriaprod.precio',
                'categoriaprod.areaproduccion_id',
                'categoriaprod.sta_precioxkilo',
                'categoriaprod.unidadmedida_id',
                'categoriaprod.unidadmedidafact_id'
            ])
            ->get();
        */
        $vendedores = Vendedor::orderBy('id')->where('sta_activo',1)->get();
        $areaproduccions = AreaProduccion::orderBy('id')->get();
        $fechaServ = ['fecha1erDiaMes' => date("01/m/Y"),
                    'fechaAct' => date("d/m/Y")
                    ];
        return view('nvindicadorxvendedor.index', compact('clientes','giros','categoriaprods','vendedores','vendedores1','areaproduccions','fechaServ'));
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
                $subtotaldinero1 = round(($subtotaldinero / $totalgeneralDinero) * 100,2);
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
                        <th style='text-align:right'>". number_format($aux_promkilogen, 2, ",", ".") ."</th>
                    </tr>
                </tfoot>
            </table>";

            $respuesta['vetasxmesmeses'] = array_column($datas['ventasxmes'], 'mes');
            $respuesta['ventasxmeskilos'] = array_column($datas['ventasxmes'], 'totalkilos');
            $respuesta['ventasxmesdinero'] = array_column($datas['ventasxmes'], 'subtotal');

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
}



function consulta($request){
    $array = array('apellido', 'email', 'teléfono');
    $separado_por_comas = implode(",", $array);
    //dd(json_decode($request->vendedor_id));
    $aux_vendedor = implode ( ',' , json_decode($request->vendedor_id));
    //dd($aux_vendedor);
    $respuesta = array();
    $respuesta['exito'] = true;
    $respuesta['mensaje'] = "Código encontrado";
    $respuesta['productos'] = "";
    $respuesta['vendedores'] = "";
    $respuesta['agruxproducto'] = "";

    if(empty($aux_vendedor )){
            $vendedorcond = " true ";
    }else{
        $vendedorcond = " notaventa.vendedor_id in ($aux_vendedor) ";
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

    $sql = "SELECT grupoprod.id,grupoprod.gru_nombre,
    sum(notaventadetalle.totalkilos) AS totalkilos,
    sum(notaventadetalle.subtotal) AS subtotal,
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
    sum(notaventadetalle.subtotal) AS subtotal
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
    sum(notaventadetalle.subtotal) AS subtotal
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

    $sql = "SELECT categoriaprod.nombre,claseprod.cla_nombre,
    producto.long,producto.diametro,
    producto.tipounion,notaventadetalle.peso,color.nombre as color,
    categoriagrupovalmes.metacomerkg,categoriagrupovalmes.costo,
    grupoprod.id as gru_id,grupoprod.gru_nombre,
    sum(notaventadetalle.cant) AS cant,
    sum(notaventadetalle.totalkilos) AS totalkilos,
    sum(notaventadetalle.subtotal) AS subtotal
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
    sum(notaventadetalle.subtotal) AS subtotal
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
    sum(notaventadetalle.subtotal) AS subtotal
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
    sum(notaventadetalle.cant) AS cant,
    sum(notaventadetalle.totalkilos) AS totalkilos,
    sum(notaventadetalle.subtotal) AS subtotal
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
    WHERE $aux_condFecha
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


    return $respuesta;
}

function consultaODcerrada($request){
    $array = array('apellido', 'email', 'teléfono');
    $separado_por_comas = implode(",", $array);
    //dd(json_decode($request->vendedor_id));
    $aux_vendedor = implode ( ',' , json_decode($request->vendedor_id));
    //dd($aux_vendedor);
    $respuesta = array();
    $respuesta['exito'] = true;
    $respuesta['mensaje'] = "Código encontrado";
    $respuesta['productos'] = "";
    $respuesta['vendedores'] = "";

    if(empty($aux_vendedor )){
            $vendedorcond = " true ";
    }else{
        $vendedorcond = " notaventa.vendedor_id in ($aux_vendedor) ";
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


    $sql = "SELECT grupoprod.id,grupoprod.gru_nombre,
    sum((notaventadetalle.totalkilos/notaventadetalle.cant) * despachoorddet.cantdesp) AS totalkilos,
    sum((notaventadetalle.preciounit * despachoorddet.cantdesp)) AS subtotal,
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
    sum((notaventadetalle.preciounit * despachoorddet.cantdesp)) AS subtotal
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
    sum((notaventadetalle.preciounit * despachoorddet.cantdesp)) AS subtotal
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

    $sql = "SELECT categoriaprod.nombre,claseprod.cla_nombre,
    producto.long,producto.diametro,
    producto.tipounion,notaventadetalle.peso,color.nombre as color,
    categoriagrupovalmes.metacomerkg,categoriagrupovalmes.costo,
    grupoprod.id as gru_id,grupoprod.gru_nombre,
    sum(despachoorddet.cantdesp) AS cant,
    sum((notaventadetalle.totalkilos/notaventadetalle.cant) * despachoorddet.cantdesp) AS totalkilos,
    sum((notaventadetalle.preciounit * despachoorddet.cantdesp)) AS subtotal
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
    GROUP BY producto.id,grupoprod.id;";

    //dd($sql);
    $datas = DB::select($sql);
    $respuesta['agruxproducto'] = $datas;

    
    $sql = "SELECT areaproduccion.id,areaproduccion.nombre,
    sum((notaventadetalle.totalkilos/notaventadetalle.cant) * despachoorddet.cantdesp) AS totalkilos,
    sum((notaventadetalle.preciounit * despachoorddet.cantdesp)) AS subtotal
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

    $datas = DB::select($sql);
    $respuesta['areaproduccion'] = $datas;

    $sql = "SELECT areaproduccion.id,areaproduccion.nombre,
    sum((notaventadetalle.totalkilos/notaventadetalle.cant) * despachoorddet.cantdesp) AS totalkilos,
    sum((notaventadetalle.preciounit * despachoorddet.cantdesp)) AS subtotal
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

    $sql = "SELECT date_format(notaventa.fechahora,'%Y%m') AS annomes,
    MONTHNAME(notaventa.fechahora) AS mes,
    sum(despachoorddet.cantdesp) AS cant,
    sum((notaventadetalle.totalkilos/notaventadetalle.cant) * despachoorddet.cantdesp) AS totalkilos,
    sum((notaventadetalle.preciounit * despachoorddet.cantdesp)) AS subtotal
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
    and $aux_condFecha
    and $vendedorcond
    and $aux_condcategoriaprod_id
    and $aux_condgiro_id
    and $aux_condareaproduccion_id
    and $aux_condstatusact_id
    and isnull(notaventa.anulada)
    and isnull(despachoorddet.deleted_at)
    and despachoord.id not in (SELECT despachoord_id FROM despachoordanul where isnull(despachoordanul.deleted_at))
    GROUP BY date_format(notaventa.fechahora,'%Y%m');";

    //dd($sql);
    $datas = DB::select($sql);
    $respuesta['ventasxmes'] = $datas;

    return $respuesta;
}

function tablaAgruxProductoMargen($datas){
    //TABLA TOTALES POR PRODUCTO
    $tabla = "<table id='tablaagruxproductoMargen' name='tablaagruxproductoMargen' class='table display AllDataTables table-hover table-condensed tablascons' data-page-length='50'>
        <thead>
            <tr>
                <th>Productos</th>
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
        $aux_promkilo = 0;
        if($producto->totalkilos>0){
            $aux_promkilo = $producto->subtotal/$producto->totalkilos;
        }
        $aux_sumpromkilo += $aux_promkilo;
        $aux_margenAporte = $aux_promkilo - $producto->costo;
        $aux_margenVenta = $aux_promkilo * $aux_margenAporte;
        $sum_grupo += $producto->subtotal;
        $sum_KgGrupo += $producto->totalkilos;
        $tabla .= "
            <tr class='btn-accion-tabla tooltipsC'>
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
    $tabla .= "
        </tbody>
        <tfoot>
            <tr>
                <th>TOTAL</th>
                <th colspan='8' style='text-align:right'>". number_format($totalgeneralfilakg, 2, ",", ".") ."</th>
                <th></th>
                <th style='text-align:right'>". number_format($aux_totalsubtotal/$totalgeneralfilakg, 2, ",", ".") ."</th>
                <th style='text-align:right'>". number_format($aux_totalsubtotal, 0, ",", ".") ."</th>
                <th style='text-align:right'></th>
                <th style='text-align:right'>". number_format(($aux_totalmargenVenta/$aux_totalsubtotal)*100, 0, ",", ".") ."%</th>
                <th style='text-align:right'>". number_format($aux_totalmargenVenta, 0, ",", ".") ."</th>
                <th style='text-align:right'></th>
            </tr>
        </tfoot>
    </table>";
    return $tabla;
}
