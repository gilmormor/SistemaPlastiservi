<?php

namespace App\Http\Controllers;

use App\Models\AreaProduccion;
use App\Models\Cliente;
use App\Models\ClienteSucursal;
use App\Models\ClienteVendedor;
use App\Models\Comuna;
use App\Models\DespachoOrd;
use App\Models\DespachoOrdAnul;
use App\Models\DespachoOrdRec;
use App\Models\Empresa;
use App\Models\Giro;
use App\Models\Seguridad\Usuario;
use App\Models\TipoEntrega;
use App\Models\Vendedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade as PDF;

class ReportOrdDespController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('reporte-orden-de-despacho');
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
        return view('reportorddesp.index', compact('clientes','giros','areaproduccions','tipoentregas','comunas','fechaServ','tablashtml'));

    }

    public function reporte(Request $request){
        //dd($request);
        if($request->ajax()){
            $datas = consultaorddesp($request);
            return datatables($datas)->toJson();
        }
    }
    
    public function reporteanterior(Request $request){
        $respuesta = array();
        $respuesta['exito'] = false;
        $respuesta['mensaje'] = "Código no Existe";
        $respuesta['tabla'] = "";
        if($request->ajax()){
            $datas = consultaorddesp($request);
    
            $respuesta['tabla'] .= "<table id='tablacotizacion' name='tablacotizacion' class='table display AllDataTables table-hover table-condensed tablascons' data-page-length='50'>
            <thead>
                <tr>
                    <th>OD</th>
                    <th>Fecha</th>
                    <th class='tooltipsC' title='Fecha Estimada de Despacho'>Fecha ED</th>
                    <th>Razón Social</th>
                    <th class='tooltipsC' title='Orden de Despacho'>OD</th>
                    <th class='tooltipsC' title='Solicitud de Despacho'>SD</th>
                    <th class='tooltipsC' title='Orden de Compra'>OC</th>
                    <th class='tooltipsC' title='Nota de Venta'>NV</th>
                    <th>Comuna</th>
                    <th class='tooltipsC' title='Total Kg'>Total Kg</th>
                    <th class='tooltipsC' title='Tipo de Entrega'>TE</th>
                    <th class='tooltipsC' title='Orden Despacho'>Despacho</th>
                </tr>
            </thead>
            <tbody>";
    
            $i = 0;
            foreach ($datas as $data) {
                $rut = number_format( substr ( $data->rut, 0 , -1 ) , 0, "", ".") . '-' . substr ( $data->rut, strlen($data->rut) -1 , 1 );
                if(empty($data->oc_file)){
                    $aux_enlaceoc = $data->oc_id;
                }else{
                    $aux_enlaceoc = "<a onclick='verpdf2(\"$data->oc_file\",2)'>$data->oc_id</a>";
                }
                $ruta_nuevoOrdDesp = route('crearord_despachoord', ['id' => $data->id]);
                $aprguiadesp = "<i class='glyphicon glyphicon-floppy-save text-warning tooltipsC' title='Pendiente Aprobar'></i>";
                $imprOrdDesp = "";
                if($data->aprguiadesp){
                    $fechaaprob = date('d-m-Y h:i:s A', strtotime($data->aprguiadespfh));
                    $aprguiadesp = "<i class='glyphicon glyphicon-floppy-save text-primary tooltipsC' title='Fecha: $fechaaprob'></i>";
                    $imprOrdDesp = "<a class='btn-accion-tabla btn-sm tooltipsC' title='Orden de Despacho' onclick='genpdfOD($data->id,1)'>
                                $data->id
                                    </a>";
                }
                $listadosoldesp = "";
                /*
                $ordesp = DespachoOrd::orderBy('id')
                        ->where('despachosol_id',$data->id);
                if($ordesp->count() > 0){
                    $aux_cont = $ordesp->count();
                    $listadosoldesp = "<i class='glyphicon glyphicon-search text-primary tooltipsC' title='Orden Despacho: $aux_cont'></i>";
                }*/
                //dd($ordesp);

                /*$despachoord = DespachoOrd::findOrFail($data->id)
                                ->whereNull();*/

                $despachoordrecs = DespachoOrdRec::where('despachoord_id',$data->id)
                                    ->where('anulada',null)->get();
//                dd($despachoordrec);
                $aux_rechazos= "-";
                $aux_contrec = 0;
                foreach ($despachoordrecs as $despachoordrec){
                    $aux_rechazos = $aux_rechazos . "<a class='btn-accion-tabla btn-sm tooltipsC' title='Rechazo OD' onclick='genpdfODRec($despachoordrec->id,1)'>
                                        $despachoordrec->id
                                    </a>";
                    $aux_contrec++;
                }
                //$aux_rechazos = $aux_rechazos . ")";
                if($aux_contrec==0){
                    $aux_rechazos = "";
                }

                $aux_anulado= "";
                $despachoordanuls = DespachoOrdAnul::where("despachoord_id",$data->id)->get();
                foreach ($despachoordanuls as $despachoordanul){
                    $aux_anulado = "<a class='btn-accion-tabla tooltipsC' title='Anulada: " . date('d-m-Y h:i:s A', strtotime($despachoordanul->created_at))  ."'>
                                        <small class='label label-danger'>A</small>
                                    </a>";
                }

                //dd($aux_rechazos);
                $respuesta['tabla'] .= "
                <tr id='fila$i' name='fila$i' class='tooltipsC'>
                    <td id='id$i' name='id$i'>
                        $data->id $aux_anulado
                    </td>
                    <td id='fechahora$i' name='fechahora$i' data-order='$data->fechahora'>" . date('d-m-Y', strtotime($data->fechahora)) . "</td>
                    <td id='fechaestdesp$i' name='fechaestdesp$i' data-order='$data->fechaestdesp'>" . date('d-m-Y', strtotime($data->fechaestdesp)) . "</td>
                    <td id='razonsocial$i' name='razonsocial$i'>$data->razonsocial</td>
                    <td>
                        $imprOrdDesp $aux_rechazos
                    </td>
                    <td>
                        <a class='btn-accion-tabla btn-sm tooltipsC' title='Solicitud de Despacho' onclick='genpdfSD($data->despachosol_id,1)'>
                            </i>$data->despachosol_id
                        </a>
                    </td>
                    <td id='oc_id$i' name='oc_id$i'>$aux_enlaceoc</td>
                    <td>
                        <a class='btn-accion-tabla btn-sm tooltipsC' title='Nota de Venta' onclick='genpdfNV($data->notaventa_id,1)'>
                            $data->notaventa_id
                        </a>
                    </td>
                    <td id='comuna$i' name='comuna$i'>$data->comunanombre</td>
                    <td style='text-align:right'>".
                        number_format($data->totalkilos, 2, ",", ".") .
                    "</td>
                    <td>
                        <i class='fa fa-fw $data->icono tooltipsC' title='$data->tipentnombre'></i>
                    </td>
                    <td>
                        $aprguiadesp
                    </td>
                </tr>";
    
                //dd($data->contacto);
            }
    
            $respuesta['tabla'] .= "
            </tbody>
            </table>";
            return $respuesta;
        }
        
        return $respuesta;
    }

    public function exportPdf()
    {
        $request = new Request();
        $request->id = $_GET["id"];
        $request->fechad = $_GET["fechad"];
        $request->fechah = $_GET["fechah"];
        $request->fechaestdesp = $_GET["fechaestdesp"];
        $request->rut = $_GET["rut"];
        $request->vendedor_id = $_GET["vendedor_id"];
        $request->oc_id = $_GET["oc_id"];
        $request->giro_id = $_GET["giro_id"];
        $request->areaproduccion_id = $_GET["areaproduccion_id"];
        $request->tipoentrega_id = $_GET["tipoentrega_id"];
        $request->notaventa_id = $_GET["notaventa_id"];
        $request->aprobstatus = $_GET["aprobstatus"]; //explode ( ",", $_GET["aprobstatus"] );
        $request->comuna_id = $_GET["comuna_id"];
        $request->despachosol_id = $_GET["despachosol_id"];
        //dd($request);

        $datas = consultaorddesp($request);
        //dd($datas);

        //$totalareaprods = $this->consulta($request,2); //Totales Area de produccion
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
        if($datas){
            
            if(env('APP_DEBUG')){
                return view('reportorddesp.listado', compact('datas','totalareaprods','empresa','usuario','aux_fdesde','aux_fhasta','nomvendedor','nombreAreaproduccion','nombreGiro','nombreTipoEntrega','request'));
            }
            
            //return view('notaventaconsulta.listado', compact('notaventas','empresa','usuario','aux_fdesde','aux_fhasta','nomvendedor','nombreAreaproduccion','nombreGiro','nombreTipoEntrega'));
            
            $pdf = PDF::loadView('reportorddesp.listado', compact('datas','totalareaprods','empresa','usuario','aux_fdesde','aux_fhasta','nomvendedor','nombreAreaproduccion','nombreGiro','nombreTipoEntrega','request'));
            //return $pdf->download('cotizacion.pdf');
            //return $pdf->stream(str_pad($notaventa->id, 5, "0", STR_PAD_LEFT) .' - '. $notaventa->cliente->razonsocial . '.pdf');
            return $pdf->stream("ReporteOrdenDespacho.pdf");
        }else{
            dd('Ningún dato disponible en esta consulta.');
        }
    }

    public function totalizarRep(Request $request){
        //dd($request);
        $respuesta = array();
        if($request->ajax()){
            $datas = consultaorddesp($request);
            $aux_totalkg = 0;
            foreach ($datas as $data) {
                $aux_totalkg += $data->totalkilos;
            }
            $respuesta['aux_totalkg'] = $aux_totalkg;
            return $respuesta;
        }
    }

}


function consultaorddesp($request){
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

    if(empty($request->fechad) or empty($request->fechah)){
        $aux_condFecha = " true";
    }else{
        $fecha = date_create_from_format('d/m/Y', $request->fechad);
        $fechad = date_format($fecha, 'Y-m-d')." 00:00:00";
        $fecha = date_create_from_format('d/m/Y', $request->fechah);
        $fechah = date_format($fecha, 'Y-m-d')." 23:59:59";
        $aux_condFecha = "despachoord.fechahora>='$fechad' and despachoord.fechahora<='$fechah'";
    }
    if(!empty($request->id) or !empty($request->oc_id) or !empty($request->notaventa_id) or !empty($request->despachosol_id)){
        $aux_condFecha = " true";
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
/*
    if(empty($request->comuna_id)){
        $aux_condcomuna_id = " true";
    }else{
        $aux_condcomuna_id = "notaventa.comunaentrega_id='$request->comuna_id'";
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
        $aux_condcomuna_id = " notaventa.comunaentrega_id in ($aux_comuna) ";
    }


    if(empty($request->id)){
        $aux_condid = " true";
    }else{
        $aux_condid = "despachoord.id='$request->id'";
    }

       
    if(empty($request->despachosol_id)){
        $aux_conddespachosol_id = " true";
    }else{
        $aux_conddespachosol_id = "despachoord.despachosol_id='$request->despachosol_id'";
    }

    $aux_condaprobord = "true";

    if(empty($request->fechaestdesp)){
        $aux_condfechaestdesp = " true";
    }else{
        $fecha = date_create_from_format('d/m/Y', $request->fechaestdesp);
        $fechad = date_format($fecha, 'Y-m-d');
        $aux_condfechaestdesp = "despachoord.fechaestdesp='$fechad'";
    }
    //$suma = despachoord::findOrFail(2)->despachoorddets->where('notaventadetalle_id',1);
    /*
    $sql = "SELECT despachoord.id,despachoord.despachosol_id,despachoord.fechahora,cliente.rut,cliente.razonsocial,notaventa.oc_id,notaventa.oc_file,
            comuna.nombre as comunanombre,
            despachoord.notaventa_id,despachoord.fechaestdesp,
            sum(despachoorddet.cantdesp * (notaventadetalle.totalkilos / notaventadetalle.cant)) AS totalkilos,
            despachoord.aprguiadesp,despachoord.aprguiadespfh,
            tipoentrega.nombre as tipentnombre,tipoentrega.icono
            FROM despachoord INNER JOIN despachoorddet
            ON despachoord.id=despachoorddet.despachoord_id
            INNER JOIN notaventa
            ON notaventa.id=despachoord.notaventa_id
            INNER JOIN notaventadetalle
            ON despachoorddet.notaventadetalle_id=notaventadetalle.id
            INNER JOIN producto
            ON notaventadetalle.producto_id=producto.id
            INNER JOIN categoriaprod
            ON categoriaprod.id=producto.categoriaprod_id
            INNER JOIN areaproduccion
            ON areaproduccion.id=categoriaprod.areaproduccion_id
            INNER JOIN cliente
            ON cliente.id=notaventa.cliente_id
            INNER JOIN comuna
            ON comuna.id=despachoord.comunaentrega_id
            INNER JOIN tipoentrega
            ON tipoentrega.id=despachoord.tipoentrega_id
            WHERE $vendedorcond
            and $aux_condFecha
            and $aux_condrut
            and $aux_condoc_id
            and $aux_condgiro_id
            and $aux_condareaproduccion_id
            and $aux_condtipoentrega_id
            and $aux_condnotaventa_id
            and $aux_aprobstatus
            and $aux_condcomuna_id
            and $aux_condaprobord
            and $aux_condid
            and $aux_conddespachosol_id
            and $aux_condfechaestdesp
            and despachoord.deleted_at is null AND notaventa.deleted_at is null AND notaventadetalle.deleted_at is null
            GROUP BY despachoord.id desc;";
*/
    $sql = "SELECT despachoord.id,despachoord.despachosol_id,despachoord.fechahora,cliente.rut,cliente.razonsocial,notaventa.oc_id,notaventa.oc_file,
            comuna.nombre as comunanombre,
            despachoord.notaventa_id,despachoord.fechaestdesp,
            sum(if(isnull(despachoordanul.created_at),despachoorddet.cantdesp * (notaventadetalle.totalkilos / notaventadetalle.cant),0.00)) AS totalkilos,
            despachoord.aprguiadesp,despachoord.aprguiadespfh,
            tipoentrega.nombre as tipentnombre,tipoentrega.icono,
            despachoordanul.created_at AS despachoordanul_fechahora,
            GROUP_CONCAT(despachoordrec.id) AS despachoordrec_id,despachoordrec.fechahora AS despachoordrec_fechahora,
            despachoorddet.despachoord_id
            FROM despachoord INNER JOIN despachoorddet
            ON despachoord.id=despachoorddet.despachoord_id AND isnull(despachoord.deleted_at) AND isnull(despachoorddet.deleted_at)
            INNER JOIN notaventa
            ON notaventa.id=despachoord.notaventa_id AND isnull(notaventa.deleted_at)
            INNER JOIN notaventadetalle
            ON despachoorddet.notaventadetalle_id=notaventadetalle.id AND isnull(notaventadetalle.deleted_at)
            INNER JOIN producto
            ON notaventadetalle.producto_id=producto.id AND isnull(producto.deleted_at)
            INNER JOIN categoriaprod
            ON categoriaprod.id=producto.categoriaprod_id AND isnull(categoriaprod.deleted_at)
            INNER JOIN areaproduccion
            ON areaproduccion.id=categoriaprod.areaproduccion_id AND isnull(areaproduccion.deleted_at)
            INNER JOIN cliente
            ON cliente.id=notaventa.cliente_id AND isnull(cliente.deleted_at)
            INNER JOIN comuna
            ON comuna.id=despachoord.comunaentrega_id AND isnull(comuna.deleted_at)
            INNER JOIN tipoentrega
            ON tipoentrega.id=despachoord.tipoentrega_id AND isnull(tipoentrega.deleted_at)
            LEFT JOIN despachoordanul
            ON despachoord.id=despachoordanul.despachoord_id AND isnull(despachoordanul.deleted_at)
            LEFT JOIN despachoordrec
            ON despachoord.id=despachoordrec.despachoord_id AND despachoordrec.aprobstatus=2 AND isnull(despachoordrec.anulada) AND isnull(despachoordrec.deleted_at)
            WHERE $vendedorcond
            and $aux_condFecha
            and $aux_condrut
            and $aux_condoc_id
            and $aux_condgiro_id
            and $aux_condareaproduccion_id
            and $aux_condtipoentrega_id
            and $aux_condnotaventa_id
            and $aux_aprobstatus
            and $aux_condcomuna_id
            and $aux_condaprobord
            and $aux_condid
            and $aux_conddespachosol_id
            and $aux_condfechaestdesp
            GROUP BY despachoord.id
            ORDER BY despachoord.id asc,despachoordrec.id asc;";

    //dd($sql);
    $datas = DB::select($sql);

    return $datas;
}