<?php

namespace App\Http\Controllers;

use App\Models\AreaProduccion;
use App\Models\Cliente;
use App\Models\ClienteVendedor;
use App\Models\Comuna;
use App\Models\Empresa;
use App\Models\Giro;
use App\Models\Seguridad\Usuario;
use App\Models\TipoEntrega;
use App\Models\Vendedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade as PDF;


class ReportOrdDespRecController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('reporte-rechazo-orden-despacho');
        $clientesArray = Cliente::clientesxUsuario();
        $clientes = $clientesArray['clientes'];
        $vendedor_id = $clientesArray['vendedor_id'];
        $sucurArray = $clientesArray['sucurArray'];
        
        $arrayvend = Vendedor::vendedores(); //Viene del modelo vendedores
        $clientevendedorArray = $arrayvend['clientevendedorArray'];

        $giros = Giro::orderBy('id')->get();
        $areaproduccions = AreaProduccion::areaproduccionxusuario();
        $tipoentregas = TipoEntrega::orderBy('id')->get();
        $comunas = Comuna::orderBy('id')->get();
        $fechaServ = [
                    'fecha1erDiaMes' => date("01/m/Y"),
                    'fechaAct' => date("d/m/Y"),
                    ];
        $tablashtml['comunas'] = Comuna::selectcomunas();
        $tablashtml['vendedores'] = Vendedor::selectvendedores();
        return view('reportorddesprec.index', compact('clientes','giros','areaproduccions','tipoentregas','comunas','fechaServ','tablashtml'));

    }

    public function reporte(Request $request){
        //dd($request);
        if($request->ajax()){
            $datas = consultaorddesprec($request);
            return datatables($datas)->toJson();
        }
    }

    public function totalizarRep(Request $request){
        //dd($request);
        $respuesta = array();
        if($request->ajax()){
            $datas = consultaorddesprec($request);
            $aux_totalkg = 0;
            $aux_totaldinero = 0;
            foreach ($datas as $data) {
                $aux_totalkg += $data->totalkilos;
                $aux_totaldinero += $data->subtotal;
            }
            $respuesta['aux_totalkg'] = $aux_totalkg;
            $respuesta['aux_totaldinero'] = $aux_totaldinero;
            return $respuesta;
        }
    }

    public function exportPdf()
    {
        $request = new Request();
        $request->id = $_GET["id"];
        $request->fechad = $_GET["fechad"];
        $request->fechah = $_GET["fechah"];
        $request->rut = $_GET["rut"];
        $request->vendedor_id = $_GET["vendedor_id"];
        $request->notaventa_id = $_GET["notaventa_id"];
        $request->oc_id = $_GET["oc_id"];
        $request->despachosol_id = $_GET["despachosol_id"];
        $request->despachoord_id = $_GET["despachoord_id"];
        $request->areaproduccion_id = $_GET["areaproduccion_id"];
        $request->tipoentrega_id = $_GET["tipoentrega_id"];
        $request->giro_id = $_GET["giro_id"];            
        $request->comuna_id = $_GET["comuna_id"];
        $request->aux_titulo = $_GET["aux_titulo"];
        $request->guiadespacho = $_GET["guiadespacho"];
        $request->numfactura = $_GET["numfactura"];
        $request->aprobstatus = $_GET["aprobstatus"];

        $datas = consultaorddesprec($request);
        //dd($datas);
        $aux_fdesde= $request->fechad;
        if(empty($request->fechad)){
            $aux_fdesde= '  /  /    ';
        }
        $aux_fhasta= $request->fechah;

        $empresa = Empresa::orderBy('id')->get();
        $usuario = Usuario::findOrFail(auth()->id());
        if($datas){
            //return view('despachosol.reportenotaventapendiente', compact('datas','empresa','usuario','aux_fdesde','aux_fhasta','nombreAreaproduccion','nombreGiro','request'));
            $pdf = PDF::loadView('reportorddesprec.listado', compact('datas','empresa','usuario','aux_fdesde','aux_fhasta','request'))->setPaper('a4', 'landscape');
            return $pdf->stream("reportenotaventapendiente.pdf");

        }else{
            dd('Ningún dato disponible en esta consulta.');
        } 
    }
}


function consultaorddesprec($request){
    $user = Usuario::findOrFail(auth()->id());
    if(empty($request->vendedor_id)){
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
    }

    if(empty($request->id)){
        $aux_condid = " true";
    }else{
        $aux_condid = "despachoordrec.id='$request->id'";
    }

    if(empty($request->fechad) or empty($request->fechah)){
        $aux_condFecha = " true";
    }else{
        $fecha = date_create_from_format('d/m/Y', $request->fechad);
        $fechad = date_format($fecha, 'Y-m-d')." 00:00:00";
        $fecha = date_create_from_format('d/m/Y', $request->fechah);
        $fechah = date_format($fecha, 'Y-m-d')." 23:59:59";
        $aux_condFecha = "despachoordrec.fechahora>='$fechad' and despachoordrec.fechahora<='$fechah'";
    }

    if(!empty($request->id) or !empty($request->notaventa_id) or !empty($request->despachosol_id) or !empty($request->despachoord_id) or !empty($request->oc_id) or !empty($request->guiadespacho) or !empty($request->numfactura)){
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

    if(empty($request->despachoord_id)){
        $aux_conddespachoord_id = " true";
    }else{
        $aux_conddespachoord_id = "despachoordrec.despachoord_id='$request->despachoord_id'";
    }

    if(empty($request->despachosol_id)){
        $aux_conddespachosol_id = " true";
    }else{
        $aux_conddespachosol_id = "despachoord.despachosol_id='$request->despachosol_id'";
    }

    if(empty($request->guiadespacho)){
        $aux_condguiadespacho = " true";
    }else{
        $aux_condguiadespacho = "despachoord.guiadespacho='$request->guiadespacho'";
    }
    if(empty($request->numfactura)){
        $aux_condnumfactura = " true";
    }else{
        $aux_condnumfactura = "despachoord.numfactura='$request->numfactura'";
    }

    if(empty($request->aprobstatus)){
        $aux_condaprobstatus = " true";
    }else{
        $aux_condaprobstatus = "despachoordrec.aprobstatus='$request->aprobstatus'";
        if($request->aprobstatus==4){
            $aux_condaprobstatus = "isnull(despachoordrec.anulada)";
        }
    }
    

    $aux_condaprobord = "true";

    //$suma = despachoord::findOrFail(2)->despachoorddets->where('notaventadetalle_id',1);

    $sql = "SELECT despachoordrec.id,despachoord.notaventa_id,despachoord.despachosol_id,despachoordrec.despachoord_id,
            despachoordrec.fechahora,cliente.rut,cliente.razonsocial,notaventa.oc_id,notaventa.oc_file,
            comuna.nombre as comunanombre,despachoord.fechaestdesp,
            sum(despachoordrecdet.cantrec * (notaventadetalle.totalkilos / notaventadetalle.cant)) AS totalkilos,
            round(sum((notaventadetalle.preciounit * despachoordrecdet.cantrec))*((notaventa.piva+100)/100)) AS subtotal,
            despachoord.aprguiadesp,despachoord.aprguiadespfh,
            despachoord.guiadespacho,despachoord.guiadespachofec,despachoord.numfactura,despachoord.fechafactura,
            despachoordanul.id as despachoordanul_id,
            despachoordrecmotivo.nombre as recmotivonombre,
            if(isnull(despachoordrec.anulada),'','A') as sta_anulada,
            despachoordrec.anulada,despachoordrec.documento_id,despachoordrec.documento_file,
            despachoordrec.aprobstatus,despachoordrec.aprobobs
            FROM despachoordrec inner join despachoordrecdet
            on despachoordrec.id = despachoordrecdet.despachoordrec_id AND isnull(despachoordrec.deleted_at)  AND isnull(despachoordrecdet.deleted_at)
            inner join despachoord 
            on despachoord.id = despachoordrec.despachoord_id AND isnull(despachoord.deleted_at)
            INNER JOIN despachoorddet
            ON despachoorddet.id = despachoordrecdet.despachoorddet_id AND isnull(despachoorddet.deleted_at)
            INNER JOIN notaventa
            ON notaventa.id=despachoord.notaventa_id AND isnull(notaventa.deleted_at) AND isnull(notaventa.anulada)
            INNER JOIN notaventadetalle
            ON despachoorddet.notaventadetalle_id=notaventadetalle.id AND isnull(notaventadetalle.deleted_at)
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
            inner join despachoordrecmotivo
            on despachoordrecmotivo.id = despachoordrec.despachoordrecmotivo_id
            LEFT JOIN despachoordanul
            ON despachoordanul.despachoord_id=despachoord.id
            LEFT JOIN vista_sumrecorddespdet
            ON vista_sumrecorddespdet.despachoorddet_id=despachoorddet.id
            WHERE $vendedorcond
            and $aux_condid
            and $aux_condFecha
            and $aux_condrut
            and $aux_condoc_id
            and $aux_condgiro_id
            and $aux_condareaproduccion_id
            and $aux_condtipoentrega_id
            and $aux_condnotaventa_id
            and $aux_condcomuna_id
            and $aux_condaprobord
            and $aux_conddespachoord_id
            and $aux_conddespachosol_id
            and $aux_condguiadespacho
            and $aux_condnumfactura
            and $aux_condaprobstatus
            GROUP BY despachoordrec.id
            ORDER BY despachoordrec.id desc;";
            
            //Linea en comentario para poder mostrar todos los registros incluso las notas de venta que  que fueron cerradas de manera forzada
            //and notaventa.id not in (select notaventa_id from notaventacerrada where isnull(notaventacerrada.deleted_at))

            //and despachoord.id not in (SELECT despachoord_id from despachoordanul where isnull(deleted_at))

    //dd($sql);
    $datas = DB::select($sql);

    return $datas;
}