<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\ClienteSucursal;
use App\Models\ClienteVendedor;
use App\Models\Comuna;
use App\Models\Empresa;
use App\Models\Giro;
use App\Models\Seguridad\Usuario;
use App\Models\Vendedor;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportClientesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('reporte-clientes');
        $giros = Giro::orderBy('id')->get();
        $fechaAct = date("d/m/Y");
        $tablashtml['comunas'] = Comuna::selectcomunas();
        $tablashtml['vendedores'] = Vendedor::selectvendedores();

        return view('reportclientes.index',compact('giros','fechaAct','tablashtml'));
    }

    public function reporte(Request $request){
        $respuesta = array();
        $respuesta['exito'] = false;
        $respuesta['mensaje'] = "Código no Existe";
        $respuesta['tabla'] = "";
    
        if($request->ajax()){
            /*****CONSULTA POR PRODUCTO*****/
            //dd($request->bloqueado);
            $datas = consulta($request);
            $respuesta['tabla'] .= "<table id='tabla-data-listar' name='tabla-data-listar' class='table display AllDataTables table-hover table-condensed tablascons2' data-page-length='10'>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>RUT</th>
                    <th>Razón Social</th>";
            if($request->bloqueado == "1"){
                $respuesta['tabla'] .= "<th>Descbloq</th>";
            }else{
                $respuesta['tabla'] .= "<th>Dirección</th>";
            }
            
            $respuesta['tabla'] .= "<th>Comuna</th>
                </tr>
            </thead>
            <tbody>";

            foreach ($datas as $data) {
                $clientebloqueadodesc = "";
                if($data->clientebloqueadodesc){
                    $clientebloqueadodesc = 'Bloqueado: ' . $data->clientebloqueadodesc;
                }
                $respuesta['tabla'] .= "
                <tr class='tooltipsC' data-toggle='tooltip' title='$clientebloqueadodesc'>
                    <td>$data->id</td>
                    <td>$data->rut</td>
                    <td>$data->razonsocial</td>";

                    if($request->bloqueado == "1"){
                        $respuesta['tabla'] .= "<td>$data->clientebloqueadodesc</td>";
                    }else{
                        $respuesta['tabla'] .= "<td>$data->direccion</td>";
                    }
        
                $respuesta['tabla'] .= "
                    <td>$data->nombrecomuna</td>
                </tr>";
            }

            $respuesta['tabla'] .= "
                </tbody>
                </table>";
            //dd($respuesta['tabla3']);
            return $respuesta;
        }
        
    }

    
    public function exportPdf()
    {
        $datosv = array();
        $request = new Request();
        $request->fechad = $_GET["fechad"];
        $request->fechah = $_GET["fechah"];
        $request->rut = $_GET["rut"];
        $request->giro_id = $_GET["giro_id"];
        $request->comuna_id = $_GET["comuna_id"];
        $request->bloqueado = $_GET["bloqueado"];
        $request->vendedor_id = $_GET["vendedor_id"];
        

        $datas = consulta($request);
        $datosv['aux_fdesde'] = $request->fechad;

        //$aux_fdesde= $request->fechad;
        if(empty($request->fechad)){
            $datosv['aux_fdesde'] = '  /  /    ';
        }
        $datosv['aux_fhasta'] = $request->fechah;
        //$aux_fhasta= $request->fechah;

        $datosv['empresa'] = Empresa::orderBy('id')->get();
        $datosv['usuario'] = Usuario::findOrFail(auth()->id());
        //$empresa = Empresa::orderBy('id')->get();
        //$usuario = Usuario::findOrFail(auth()->id());
        
        $datosv['nomvendedor'] = "Todos";
        //$nomvendedor = "Todos";
        if(!empty($request->vendedor_id)){
            $vendedor = Vendedor::findOrFail($request->vendedor_id);
            $datosv['nomvendedor'] = $vendedor->persona->nombre . " " . $vendedor->persona->apellido;
            //$nomvendedor=$vendedor->persona->nombre . " " . $vendedor->persona->apellido;
        }
        $datosv['nombreGiro'] = "Todos";
        //$nombreGiro = "Todos";
        if($request->giro_id){
            $giro = Giro::findOrFail($request->giro_id);
            $datosv['nombreGiro'] = $nombreGiro=$giro->nombre;
            //$nombreGiro=$giro->nombre;
        }
        $datosv['bloqueado'] = "Clientes: Todos";
        //$bloqueado = "Clientes: Todos";
        if($request->bloqueado){
            $datosv['bloqueado'] = "Clientes: Activos";
            //$bloqueado = "Clientes: Activos";
            if($request->bloqueado=='1'){
                $datosv['bloqueado'] = "Clientes: Bloqueados";
                //$bloqueado = "Clientes: Bloqueados";
            }
        }
        
        //return armarReportehtml($request);
        //dd($datosv);
        if($datas){
            
            if(env('APP_DEBUG')){
                //return view('reportclientes.listado', compact('datas','empresa','usuario','aux_fdesde','aux_fhasta','nomvendedor','nombreAreaproduccion','nombreGiro','nombreTipoEntrega','aux_plazoentregad','aux_plazoentregah'));
                return view('reportclientes.listado', compact('datas','datosv','request'));
            }
            
            //return view('notaventaconsulta.listado', compact('notaventas','empresa','usuario','aux_fdesde','aux_fhasta','nomvendedor','nombreAreaproduccion','nombreGiro','nombreTipoEntrega'));
            
            //$pdf = PDF::loadView('reportclientes.listado', compact('datas','empresa','usuario','aux_fdesde','aux_fhasta','nomvendedor','nombreAreaproduccion','nombreGiro','nombreTipoEntrega','aux_plazoentregad','aux_plazoentregah'))->setPaper('a4', 'landscape');
            $pdf = PDF::loadView('reportclientes.listado', compact('datas','datosv','request'))->setPaper('a4', 'landscape');
            //return $pdf->download('cotizacion.pdf');
            //return $pdf->stream(str_pad($notaventa->id, 5, "0", STR_PAD_LEFT) .' - '. $notaventa->cliente->razonsocial . '.pdf');
            return $pdf->stream("ReporteClientes.pdf");
        }else{
            dd('Ningún dato disponible en esta consulta.');
        }
        
        
    }
}


function consulta($request){
    //dd($request);
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
            $vendedorcond = "cliente_vendedor.vendedor_id=" . $vendedor_id ;
        }else{
            $vendedorcond = " true ";
        }
    }else{
        if(is_array($request->vendedor_id)){
            $aux_vendedorid = implode ( ',' , $request->vendedor_id);
        }else{
            $aux_vendedorid = $request->vendedor_id;
        }
        $vendedorcond = " cliente_vendedor.vendedor_id in ($aux_vendedorid) ";

        //$vendedorcond = "cliente_vendedor.vendedor_id='$request->vendedor_id'";
    }


    if(empty($request->fechad) or empty($request->fechah)){
        $aux_condFecha = " true";
    }else{
        $fecha = date_create_from_format('d/m/Y', $request->fechad);
        $fechad = date_format($fecha, 'Y-m-d')." 00:00:00";
        $fecha = date_create_from_format('d/m/Y', $request->fechah);
        $fechah = date_format($fecha, 'Y-m-d')." 23:59:59";
        $aux_condFecha = "cliente.created_at>='$fechad' and cliente.created_at<='$fechah'";
    }
    if(empty($request->rut)){
        $aux_condrut = " true";
    }else{
        $aux_condrut = "cliente.rut='$request->rut'";
    }
    if(empty($request->giro_id)){
        $aux_condgiro_id = " true";
    }else{
        $aux_condgiro_id = "cliente.giro_id='$request->giro_id'";
    }
/*
    if(empty($request->comuna_id)){
        $aux_condcomuna_id = " true";
    }else{
        $aux_condcomuna_id = "cliente.comunap_id='$request->comuna_id'";
    }
*/
    if(empty($request->comuna_id)){
        $aux_condcomuna_id = " true ";
    }else{
        if(is_array($request->comuna_id)){
            $aux_comuna = implode ( ',' , $request->comuna_id);
        }else{
            $aux_comuna = $request->comuna_id;
        }
        $aux_condcomuna_id = " cliente.comunap_id in ($aux_comuna) ";
    }

    if(empty($request->bloqueado)){
        $aux_bloqueado = " true";
    }else{
        if($request->bloqueado=='1'){
            $aux_bloqueado = " cliente.id in ";
        }else{
            $aux_bloqueado = " cliente.id not in ";
        }
        $aux_bloqueado .= " (SELECT cliente_id from clientebloqueado where isnull(clientebloqueado.deleted_at))";
    }

    $sql = "SELECT cliente.*,comuna.nombre as nombrecomuna, clientebloqueado.descripcion as clientebloqueadodesc
    FROM cliente inner join comuna
    on cliente.comunap_id=comuna.id
    inner join cliente_vendedor
    on cliente.id=cliente_vendedor.cliente_id
    left join clientebloqueado
    on cliente.id=clientebloqueado.cliente_id and isnull(clientebloqueado.deleted_at)
    WHERE $vendedorcond
    and $aux_condFecha
    and $aux_condrut
    and $aux_condgiro_id
    and $aux_condcomuna_id
    and $aux_bloqueado
    AND isnull(cliente.deleted_at);";

    $datas = DB::select($sql);
    return $datas;
}