<?php

namespace App\Http\Controllers;

use App\Models\AreaProduccion;
use App\Models\Cliente;
use App\Models\Comuna;
use App\Models\DespachoSol;
use App\Models\Giro;
use App\Models\TipoEntrega;
use App\Models\Vendedor;
use Illuminate\Http\Request;

class ReportMovSolDespController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('movimiento-soldesp');
        $clientesArray = Cliente::clientesxUsuario();
        $clientes = $clientesArray['clientes'];
        $vendedor_id = $clientesArray['vendedor_id'];
        $sucurArray = $clientesArray['sucurArray'];
        
        $arrayvend = Vendedor::vendedores(); //Viene del modelo vendedores
        $clientevendedorArray = $arrayvend['clientevendedorArray'];

        $giros = Giro::orderBy('id')->get();
        $areaproduccions = AreaProduccion::orderBy('id')->get();
        $tipoentregas = TipoEntrega::orderBy('id')->get();
        $comunas = Comuna::orderBy('id')->get();
        $fechaServ = [
                    'fecha1erDiaMes' => date("01/m/Y"),
                    'fechaAct' => date("d/m/Y"),
                    ];
        $tablashtml['comunas'] = Comuna::selectcomunas();
        $tablashtml['vendedores'] = Vendedor::selectvendedores();
        return view('reportmovsoldesp.index', compact('clientes','giros','areaproduccions','tipoentregas','comunas','fechaServ','tablashtml'));

    }

    public function reporte(Request $request){
        $respuesta = array();
        $arreglo = array();
        $matriz = array();
        $respuesta['tabla'] = "";
        $despachosol = DespachoSol::find($request->despachosol_id);
        if($despachosol){
            $respuesta['tabla'] .= "<table id='tablacotizacion' name='tablacotizacion' class='table display AllDataTables table-hover table-condensed tablascons' data-page-length='50'>
            <thead>
                <tr>
                    <th>Prod/Doc</th>";
            array_push($arreglo, "Prod/Doc");
            foreach($despachosol->despachosoldets as $despachosoldet){
                $producto_id = $despachosoldet->notaventadetalle->producto->id;
                $producto_nombre = $despachosoldet->notaventadetalle->producto->nombre;
                $producto_nombre .= " " . $despachosoldet->notaventadetalle->producto->diamextpg;
                $producto_nombre .=  " " . $despachosoldet->notaventadetalle->producto->claseprod->cla_nombre;

                $respuesta['tabla'] .= "
                        <th style='text-align:right' class='tooltipsC' title='$producto_nombre'>$producto_id</th>";
                        array_push($arreglo, $producto_id . ' - ' . $producto_nombre);
            }
            array_push($matriz,$arreglo);
            $arreglo = array();
            $respuesta['tabla'] .= "
                </tr>
            </thead>
            <tbody>";
    
            $respuesta['tabla'] .= "
            <tr>
                <td style='text-align:left' data-order='$despachosol->fechahora'>SD-$request->despachosol_id</td>";
            array_push($arreglo, 'SD-' . $request->despachosol_id);
    
            foreach($despachosol->despachosoldets as $despachosoldet){
                if($despachosoldet->cantsoldesp>0){
                    $respuesta['tabla'] .= "
                        <td style='text-align:right'>$despachosoldet->cantsoldesp</td>";
                    array_push($arreglo, $despachosoldet->cantsoldesp);
                }
            }
            array_push($matriz,$arreglo);
            $respuesta['tabla'] .= "
            </tr>";
    
            foreach($despachosol->despachoords as $despachoord){
                $arreglo = array();
                $respuesta['tabla'] .= "
                    <tr>
                        <td data-order='$despachoord->fechahora' style='text-align:left'>OD-$despachoord->id</td>";
                array_push($arreglo, 'OD-' . $despachoord->id);
                foreach($despachosol->despachosoldets as $despachosoldet){
                    if($despachosoldet->cantsoldesp>0){
                        $producto_id = $despachosoldet->notaventadetalle->producto_id;
                        foreach($despachoord->despachoorddets as $despachoorddet){
                            $aux_cantdesp = "0";
                            if($despachoorddet->notaventadetalle->producto_id == $producto_id){
                                $aux_cantdesp = '-' . $despachoorddet->cantdesp;
                                break;
                            }
                        }
                        $respuesta['tabla'] .= "
                        <td style='text-align:right'>$aux_cantdesp</td>";
                        array_push($arreglo, $aux_cantdesp);
                    }
                }
                array_push($matriz,$arreglo);
                $respuesta['tabla'] .= "
                    </tr>";
                if($despachoord->despachoordrecs){
                    if($despachoord->despachoordrecs->aprobstatus==2 and is_null($despachoord->despachoordrecs->anulado)){
                        $arreglo = array();
                        $id = $despachoord->despachoordrecs->id;
                        $respuesta['tabla'] .= "
                            <tr>
                                <td style='text-align:left' data-order='$despachoord->fechahora'>OD-$despachoord->id R-$id</td>";
                        array_push($arreglo, 'OD-' . $despachoord->id . 'R-' . $id);
        
                        foreach($despachosol->despachosoldets as $despachosoldet){
                            if($despachosoldet->cantsoldesp>0){
                                $producto_id = $despachosoldet->notaventadetalle->producto_id;
                                foreach($despachoord->despachoordrecs->despachoordrecdets as $despachoordrecdet){
                                    $aux_cantrec = "0";
                                    if($despachoordrecdet->despachoorddet->notaventadetalle->producto_id == $producto_id){
                                        $aux_cantrec = $despachoordrecdet->cantrec;
                                        break;
                                    }
                                }
                                $respuesta['tabla'] .= "
                                    <td style='text-align:right'>$aux_cantrec</td>";
                                    array_push($arreglo, $aux_cantrec);
                            }
                        }
                        array_push($matriz,$arreglo);
                        $respuesta['tabla'] .= "
                            </tr>";
                    }

                }
            }
            $respuesta['tabla'] .= "
                </tbody>
                <tfoot>
                    <tr>
                        <th>TOTAL KG</th>
                    </tr>
                </tfoot>
            </table>";
            //array_push($matriz,$arreglo);
            $respuesta['tabla'] = "";
            $filas = count($arreglo);
            $colum = count($matriz);
            $respuesta['tabla'] .= "<table id='tablacotizacion' name='tablacotizacion' class='table display AllDataTables table-hover table-condensed tablascons' data-page-length='50'>
            <thead>
                <tr>";
            for( $i=0 ;$i < $colum ; $i++ ){
                $aux_valor = $matriz[$i][0];
                $aux_aling = 'left';
                if($i>0){
                    $aux_aling = 'right';
                }
                $respuesta['tabla'] .= "
                    <th style='text-align:$aux_aling' class='tooltipsC'> $aux_valor </th>";
            }
            $respuesta['tabla'] .= "
                    <th style='text-align:right' class='tooltipsC'>Saldo</th>
                </tr>
            </thead>
            <tbody>";
            for( $i=1 ;$i < $filas; $i++ ){
                $respuesta['tabla'] .= "
                    <tr>";
                $saldo = 0;
                for( $f=0 ;$f < $colum; $f++ ){
                    $aux_valor = $matriz[$f][$i];
                    $aux_aling = 'left';
                    if($f>0){
                        $saldo += $aux_valor;
                        $aux_aling = 'right';
                    }
                    $respuesta['tabla'] .= "
                        <td style='text-align:$aux_aling' data-order='$f'> $aux_valor </td>";
                }
                $respuesta['tabla'] .= "
                    <td style='text-align:right' data-order='$f'> $saldo </td>
                    </tr>";
            }
            $respuesta['tabla'] .= "
                </tbody>
                <!--
                <tfoot>
                    <tr>
                        <th>TOTAL KG</th>
                    </tr>
                </tfoot>
                -->
            </table>";
            //dd($respuesta['tabla']);
        }
        return $respuesta;
    }
}
