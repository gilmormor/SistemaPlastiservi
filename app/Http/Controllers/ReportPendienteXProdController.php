<?php

namespace App\Http\Controllers;

use App\Models\AreaProduccion;
use App\Models\CategoriaProd;
use App\Models\Cliente;
use App\Models\ClienteBloqueado;
use App\Models\ClienteSucursal;
use App\Models\ClienteVendedor;
use App\Models\Comuna;
use App\Models\Empresa;
use App\Models\Giro;
use App\Models\Producto;
use App\Models\Seguridad\Usuario;
use App\Models\TipoEntrega;
use App\Models\Vendedor;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use stdClass;

class ReportPendienteXProdController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('reporte-pendiente-por-producto');
        $clientesArray = Cliente::clientesxUsuario();
        $clientes = $clientesArray['clientes'];
        $vendedor_id = $clientesArray['vendedor_id'];
        $sucurArray = $clientesArray['sucurArray'];

        $arrayvend = Vendedor::vendedores(); //Viene del modelo vendedores
        $vendedores1 = $arrayvend['vendedores'];
        $clientevendedorArray = $arrayvend['clientevendedorArray'];
        $vendedores = Vendedor::orderBy('id')->where('sta_activo',1)->get();

        $giros = Giro::orderBy('id')->get();
        $areaproduccions = AreaProduccion::orderBy('id')->get();
        $tipoentregas = TipoEntrega::orderBy('id')->get();
        $comunas = Comuna::orderBy('id')->get();
        $fechaAct = date("d/m/Y");
        $productos = Producto::productosxUsuario();
        $selecmultprod = 1;
        $tablashtml['comunas'] = Comuna::selectcomunas();
        $tablashtml['vendedores'] = Vendedor::selectvendedores();
        $tablashtml['categoriaprod'] = CategoriaProd::categoriasxUsuario();
        return view('reportpendientexprod.index', compact('clientes','vendedores','vendedores1','giros','areaproduccions','tipoentregas','comunas','fechaAct','productos','selecmultprod','tablashtml'));
    
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
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
        $respuesta = reporte1($request);
        return $respuesta;
    }

    public function exportPdf()
    {
        $request = new Request();
        $request->fechad = $_GET["fechad"];
        $request->fechah = $_GET["fechah"];
        $request->plazoentregad = $_GET["plazoentregad"];
        $request->plazoentregah = $_GET["plazoentregah"];
        $request->rut = $_GET["rut"];
        $request->vendedor_id = $_GET["vendedor_id"];
        $request->oc_id = $_GET["oc_id"];
        $request->areaproduccion_id = $_GET["areaproduccion_id"];
        $request->tipoentrega_id = $_GET["tipoentrega_id"];
        $request->notaventa_id = $_GET["notaventa_id"];
        $request->aprobstatus = $_GET["aprobstatus"];
        $request->giro_id = $_GET["giro_id"];
        $request->comuna_id = $_GET["comuna_id"];
        $request->producto_id = $_GET["producto_id"];
        $request->vendedor_id = $_GET["vendedor_id"];
        $request->categoriaprod_id = $_GET["categoriaprod_id"];
        $datas = consulta($request,2,1);

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
                return view('reportpendientexprod.listado', compact('datas','empresa','usuario','aux_fdesde','aux_fhasta','nomvendedor','nombreAreaproduccion','nombreGiro','nombreTipoEntrega','aux_plazoentregad','aux_plazoentregah'));
            }
            
            //return view('notaventaconsulta.listado', compact('notaventas','empresa','usuario','aux_fdesde','aux_fhasta','nomvendedor','nombreAreaproduccion','nombreGiro','nombreTipoEntrega'));
            
            $pdf = PDF::loadView('reportpendientexprod.listado', compact('datas','empresa','usuario','aux_fdesde','aux_fhasta','nomvendedor','nombreAreaproduccion','nombreGiro','nombreTipoEntrega','aux_plazoentregad','aux_plazoentregah'))->setPaper('a4', 'landscape');
            //return $pdf->download('cotizacion.pdf');
            //return $pdf->stream(str_pad($notaventa->id, 5, "0", STR_PAD_LEFT) .' - '. $notaventa->cliente->razonsocial . '.pdf');
            return $pdf->stream("ReportePendienteXProducto.pdf");
        }else{
            dd('Ningún dato disponible en esta consulta.');
        } 
    }
}

function reporte1($request){
    $respuesta = array();
    $respuesta['exito'] = false;
    $respuesta['mensaje'] = "Código no Existe";
    $respuesta['tabla'] = "";
    $respuesta['tabla2'] = "";
    $respuesta['tabla3'] = "";

    if($request->ajax()){
        /*****CONSULTA POR PRODUCTO*****/
        $datas = consulta($request,2,1);
        $respuesta['tabla3'] .= "<table id='tabla-data-listar' name='tabla-data-listar' class='table display AllDataTables table-hover table-condensed tablascons2' data-page-length='50'>
        <thead>
            <tr>
                <th>NV</th>
                <th>OC</th>
                <th>Fecha</th>
                <th>Plazo<br>Entrega</th>
                <th>Razón Social</th>
                <th>Comuna</th>
                <th class='tooltipsC' title='Código Producto'>Cod</th>
                <th>Descripción</th>
                <th>Diametro</th>
                <th>Clase</th>
                <th>Largo</th>
                <th>Peso</th>
                <th>TU</th>
                <th style='text-align:right'>Cant</th>
                <th style='text-align:right' class='tooltipsC' title='Cantidad Despachada'>Cant<br>Desp</th>
                <th style='text-align:right' class='tooltipsC' title='Cantidad Pendiente'>Cant<br>Pend</th>
                <th style='text-align:right' class='tooltipsC' title='Kilos Pendiente'>Kilos<br>Pend</th>
                <th style='text-align:right' class='tooltipsC' title='Precio por Kilo'>Precio<br>Kilo</th>
                <th style='text-align:right' class='tooltipsC' title='Dinero'>$</th>
            </tr>
        </thead>
        <tbody>";
        $aux_totalcant = 0;
        $aux_totalcantdesp = 0;
        $aux_totalcantsol = 0;
        $aux_totalkilos = 0;
        $aux_totalkilosdesp = 0;
        $aux_totalcantpend = 0;
        $aux_totalkilospend = 0;
        $aux_totalplata = 0;
        $aux_totalprecio = 0;
        $i = 0;
        foreach ($datas as $data) {
            //SUMA TOTAL DE SOLICITADO
            /*************************/
            $sql = "SELECT cantsoldesp
            FROM vista_sumsoldespdet
            WHERE notaventadetalle_id=$data->id";
            $datasuma = DB::select($sql);
            if(empty($datasuma)){
                $sumacantsoldesp= 0;
            }else{
                $sumacantsoldesp= $datasuma[0]->cantsoldesp;
            }
            /*************************/
            //SUMA TOTAL DESPACHADO
            /*************************/
            $sql = "SELECT cantdesp
                FROM vista_sumorddespxnvdetid
                WHERE notaventadetalle_id=$data->id";
            $datasumadesp = DB::select($sql);
            if(empty($datasumadesp)){
                $sumacantdesp= 0;
            }else{
                $sumacantdesp= $datasumadesp[0]->cantdesp;
            }
            if(empty($data->oc_file)){
                $aux_enlaceoc = $data->oc_id;
            }else{
                $aux_enlaceoc = "<a class='btn-accion-tabla btn-sm tooltipsC' title='Orden de Compra' onclick='verpdf2(\"$data->oc_file\",2)'>$data->oc_id</a>";
            }

            //$aux_totalkg += $data->saldokg; // ($data->totalkilos - $data->kgsoldesp);
            //$aux_totalplata += $data->saldoplata; // ($data->subtotal - $data->subtotalsoldesp);
            $aux_cantsaldo = $data->cant-$sumacantdesp;
            $fila_cantdesp = number_format($sumacantdesp, 0, ",", ".");
            if($sumacantdesp>0){
                $fila_cantdesp = "<a class='btn-accion-tabla btn-sm tooltipsC' onclick='listarorddespxNV($data->notaventa_id,$data->producto_id)' title='Ver detalle despacho' data-toggle='tooltip'>"
                                . number_format($sumacantdesp, 0, ",", ".") .
                                "</a>";
            }
            $comuna = Comuna::findOrFail($data->comunaentrega_id);
            $producto = Producto::findOrFail($data->producto_id);
            $aux_subtotalplata = ($aux_cantsaldo * $data->peso) * $data->precioxkilo;

            $respuesta['tabla3'] .= "
            <tr>
                <td>
                    <a class='btn-accion-tabla btn-sm tooltipsC' title='Nota de Venta' onclick='genpdfNV($data->notaventa_id,1)'>
                        $data->notaventa_id
                    </a>
                </td>
                <td>$aux_enlaceoc</td>
                <td data-order='$data->fechahora'>" . date('d-m-Y', strtotime($data->fechahora)) . "</td>
                <td data-order='$data->plazoentrega'>" . date('d-m-Y', strtotime($data->plazoentrega)) . "</td>
                <td>$data->razonsocial</td>
                <td>$comuna->nombre</td>
                <td>$data->producto_id</td>
                <td>$data->nombre</td>
                <td>$producto->diametro</td>
                <td>$data->cla_nombre</td>
                <td>$data->long</td>
                <td>$data->peso</td>
                <td>$data->tipounion</td>
                <td style='text-align:right' data-order='" . $data->cant . "'>". number_format($data->cant, 0, ",", ".") ."</td>
                <td style='text-align:right' data-order='$sumacantdesp'>
                    $fila_cantdesp
                </td>
                <td style='text-align:right' data-order='" . $aux_cantsaldo . "'>". number_format($aux_cantsaldo, 0, ",", ".") ."</td>
                <td style='text-align:right' data-order='" . $aux_cantsaldo * $data->peso . "'>". number_format($aux_cantsaldo * $data->peso, 2, ",", ".") ."</td>
                <td style='text-align:right' data-order='" . $data->precioxkilo . "'>". number_format($data->precioxkilo, 2, ",", ".") ."</td>
                <td style='text-align:right' data-order='" . $aux_subtotalplata . "'>". number_format($aux_subtotalplata, 2, ",", ".") ."</td>
            </tr>";
            $aux_totalcant += $data->cant;
            $aux_totalcantdesp += $sumacantdesp;
            //$aux_totalcantsol += $sumacantsoldesp;
            $aux_totalkilos += $data->totalkilos;
            $aux_totalkilosdesp += ($sumacantdesp * $data->peso);
            $aux_totalcantpend += $aux_cantsaldo;    
            $aux_totalkilospend += ($aux_cantsaldo * $data->peso);
            $aux_totalplata += $aux_subtotalplata;
            $aux_totalprecio += $data->precioxkilo;
            $i++;
        }
        $aux_totalkilospend = round($aux_totalkilospend,2);
        $aux_promprecioxkilo = round($aux_totalprecio/$i,2);

        $respuesta['tabla3'] .= "
            </tbody>
            <tfoot>
                <tr>
                    <th colspan='13' style='text-align:right'>TOTALES</th>
                    <th style='text-align:right' class='tooltipsC' title='Cantidad'>". number_format($aux_totalcant, 0, ",", ".") ."</th>
                    <th style='text-align:right' class='tooltipsC' title='Cantidad Despachada'>". number_format($aux_totalcantdesp, 0, ",", ".") ."</th>
                    <th style='text-align:right' class='tooltipsC' title='Cantidad Pendiente'>". number_format($aux_totalcantpend, 0, ",", ".") ."</th>
                    <th style='text-align:right' class='tooltipsC' title='Kg Pendientes'>". number_format($aux_totalkilospend, 2, ",", ".") ."</th>
                    <th style='text-align:right'></th>
                    <th style='text-align:right' class='tooltipsC' title='Total $'>". number_format($aux_totalplata, 2, ",", ".") ."</th>
                </tr>
                <tr>
                    <th colspan='13' style='text-align:right'>PROMEDIO</th>
                    <th colspan='4' style='text-align:right'></th>
                    <th style='text-align:right' class='tooltipsC' title='Precio Kg Promedio'>". number_format($aux_promprecioxkilo, 2, ",", ".") ."</th>
                    <th style='text-align:right' class='tooltipsC' title='Total $ (Precio promedio)'>". number_format($aux_totalkilospend * $aux_promprecioxkilo, 2, ",", ".") ."</th>
                </tr>
            </tfoot>

        </table>";
        //dd($respuesta['tabla3']);
        return $respuesta;
    }
}

function consulta($request,$aux_sql,$orden){
    //dd($request);
    if($orden==1){
        $aux_orden = "notaventadetalle.notaventa_id desc";
    }else{
        $aux_orden = "notaventa.cliente_id";
    }
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


/*
    if(empty($request->plazoentrega)){
        $aux_condplazoentrega = " true";
    }else{
        $fecha = date_create_from_format('d/m/Y', $request->plazoentrega);
        $fechad = date_format($fecha, 'Y-m-d');
        $aux_condplazoentrega = "notaventa.plazoentrega='$fechad'";
    }
*/
    if(empty($request->plazoentregad) or empty($request->plazoentregah)){
        $aux_condplazoentrega = " true";
    }else{
        $fecha = date_create_from_format('d/m/Y', $request->plazoentregad);
        $plazoentregad = date_format($fecha, 'Y-m-d')." 00:00:00";
        $fecha = date_create_from_format('d/m/Y', $request->plazoentregah);
        $plazoentregah = date_format($fecha, 'Y-m-d')." 23:59:59";
        $aux_condplazoentrega = "notaventa.plazoentrega>='$plazoentregad' and notaventa.plazoentrega<='$plazoentregah'";
    }

    //dd($aux_condplazoentrega);

    $aux_condproducto_id = " true";
    if(!empty($request->producto_id)){
        /*
        $aux_condproducto_id = str_replace(".","",$request->producto_id);
        $aux_condproducto_id = str_replace("-","",$aux_condproducto_id);
        $aux_condproducto_id = "notaventadetalle.producto_id='$aux_condproducto_id'";
        */

        $aux_codprod = explode(",", $request->producto_id);
        $aux_codprod = implode ( ',' , $aux_codprod);
        $aux_condproducto_id = "notaventadetalle.producto_id in ($aux_codprod)";
    }
    if(empty($request->categoriaprod_id)){
        $aux_condcategoriaprod_id = " true";
    }else{

        if(is_array($request->categoriaprod_id)){
            $aux_categoriaprodid = implode ( ',' , $request->categoriaprod_id);
        }else{
            $aux_categoriaprodid = $request->categoriaprod_id;
        }
        $aux_condcategoriaprod_id = " producto.categoriaprod_id in ($aux_categoriaprodid) ";
    }
//dd($aux_condcategoriaprod_id);

    //$suma = DespachoSol::findOrFail(2)->despachosoldets->where('notaventadetalle_id',1);
    if($aux_sql==1){
        $sql = "SELECT notaventadetalle.notaventa_id as id,notaventa.fechahora,notaventa.cliente_id,notaventa.comuna_id,notaventa.comunaentrega_id,
        notaventa.oc_id,notaventa.anulada,cliente.rut,cliente.razonsocial,aprobstatus,visto,oc_file,
        comuna.nombre as comunanombre,
        vista_notaventatotales.cant,
        vista_notaventatotales.precioxkilo,
        vista_notaventatotales.totalkilos,
        vista_notaventatotales.subtotal,
        sum(if(areaproduccion.id=1,notaventadetalle.totalkilos,0)) AS pvckg,
        sum(if(areaproduccion.id=2,notaventadetalle.totalkilos,0)) AS cankg,
        sum(if(areaproduccion.id=1,notaventadetalle.subtotal,0)) AS pvcpesos,
        sum(if(areaproduccion.id=2,notaventadetalle.subtotal,0)) AS canpesos,
        sum(notaventadetalle.subtotal) AS totalps,
        (SELECT sum(kgsoldesp) as kgsoldesp
                FROM vista_sumsoldespdet
                WHERE notaventa_id=notaventa.id) as totalkgsoldesp,
        (SELECT sum(subtotalsoldesp) as subtotalsoldesp
                FROM vista_sumsoldespdet
                WHERE notaventa_id=notaventa.id) as totalsubtotalsoldesp,
        notaventa.inidespacho,notaventa.guiasdespacho,notaventa.findespacho,
        tipoentrega.nombre as tipentnombre,tipoentrega.icono
        FROM notaventa INNER JOIN notaventadetalle
        ON notaventa.id=notaventadetalle.notaventa_id and 
        if((SELECT cantsoldesp
                FROM vista_sumsoldespdet
                WHERE notaventadetalle_id=notaventadetalle.id
                ) >= notaventadetalle.cant,false,true)
        INNER JOIN producto
        ON notaventadetalle.producto_id=producto.id
        INNER JOIN categoriaprod
        ON categoriaprod.id=producto.categoriaprod_id
        INNER JOIN areaproduccion
        ON areaproduccion.id=categoriaprod.areaproduccion_id
        INNER JOIN cliente
        ON cliente.id=notaventa.cliente_id
        INNER JOIN comuna
        ON comuna.id=notaventa.comunaentrega_id
        INNER JOIN tipoentrega
        ON tipoentrega.id=notaventa.tipoentrega_id
        INNER JOIN vista_notaventatotales
        ON notaventa.id=vista_notaventatotales.id
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
        and $aux_condplazoentrega
        and $aux_condcategoriaprod_id
        and notaventa.anulada is null
        and notaventa.findespacho is null
        and notaventa.deleted_at is null and notaventadetalle.deleted_at is null
        and notaventa.id not in (select notaventa_id from notaventacerrada where isnull(notaventacerrada.deleted_at))
        GROUP BY notaventadetalle.notaventa_id,notaventa.fechahora,notaventa.cliente_id,notaventa.comuna_id,notaventa.comunaentrega_id,
        notaventa.oc_id,notaventa.anulada,cliente.rut,cliente.razonsocial,aprobstatus,visto,oc_file,
        notaventa.inidespacho,notaventa.guiasdespacho,notaventa.findespacho
        ORDER BY $aux_orden;";
    }

    if($aux_sql==2){
        $sql = "SELECT notaventa.fechahora,notaventadetalle.producto_id,
        notaventadetalle.cant,if(isnull(vista_sumorddespxnvdetid.cantdesp),0,vista_sumorddespxnvdetid.cantdesp) AS cantdesp,
        producto.nombre,cliente.razonsocial,notaventadetalle.id,
        notaventadetalle.notaventa_id,oc_file,
        producto.diametro,notaventa.oc_id,
        claseprod.cla_nombre,producto.long,producto.peso,producto.tipounion,
        notaventadetalle.totalkilos,
        subtotal,notaventa.comunaentrega_id,notaventa.plazoentrega,
        notaventadetalle.precioxkilo
        FROM notaventadetalle INNER JOIN notaventa
        ON notaventadetalle.notaventa_id=notaventa.id
        INNER JOIN producto
        ON notaventadetalle.producto_id=producto.id
        INNER JOIN claseprod
        ON producto.claseprod_id=claseprod.id
        INNER JOIN categoriaprod
        ON producto.categoriaprod_id=categoriaprod.id
        INNER JOIN cliente
        ON cliente.id=notaventa.cliente_id
        LEFT JOIN vista_sumorddespxnvdetid
        ON notaventadetalle.id=vista_sumorddespxnvdetid.notaventadetalle_id
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
        and $aux_condplazoentrega
        and $aux_condproducto_id
        and $aux_condcategoriaprod_id
        AND isnull(notaventa.findespacho)
        AND isnull(notaventa.anulada)
        AND notaventadetalle.cant>if(isnull(vista_sumorddespxnvdetid.cantdesp),0,vista_sumorddespxnvdetid.cantdesp)
        AND isnull(notaventa.deleted_at) AND isnull(notaventadetalle.deleted_at)
        and notaventadetalle.notaventa_id not in (select notaventa_id from notaventacerrada where isnull(notaventacerrada.deleted_at));";
    }
    $datas = DB::select($sql);
    return $datas;
    
}