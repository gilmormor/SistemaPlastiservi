<?php

namespace App\Http\Controllers;

use App\Models\AreaProduccion;
use App\Models\CategoriaProd;
use App\Models\Cliente;
use App\Models\ClienteSucursal;
use App\Models\ClienteVendedor;
use App\Models\Comuna;
use App\Models\DespachoOrd;
use App\Models\Empresa;
use App\Models\Giro;
use App\Models\Seguridad\Usuario;
use App\Models\TipoEntrega;
use App\Models\Vendedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade as PDF;


class ReportOrdDespGuiaFactController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //can('listar-despacho-consulta-guia-factura-cerradas');
        can('reporte-orden-despacho,-guia,-factura,-cerrada');
        $respuesta = cargadatos();
        $clientes = $respuesta['clientes'];
        $vendedores = $respuesta['vendedores'];
        $vendedores1 = $respuesta['vendedores1'];
        $giros = $respuesta['giros'];
        $areaproduccions = $respuesta['areaproduccions'];
        $tipoentregas = $respuesta['tipoentregas'];
        $comunas = $respuesta['comunas'];
        //$fechaAct = $respuesta['fechaAct'];
        $fechaServ = [
                    'fecha1erDiaMes' => $respuesta['fecha1erDiaMes'],
                    'fechaAct' => $respuesta['fechaAct']
                    ];

        $aux_verestado='1'; //Mostrar todas los opciopnes de estado de OD

        $titulo = "Consultar Orden Despacho, Guia, Factura, cerrada";

        return view('reportorddespguiafact.index', compact('clientes','vendedores','vendedores1','giros','areaproduccions','tipoentregas','comunas','fechaServ','aux_verestado','titulo'));

    }

    public function index2()
    {
        can('listar-despacho-consulta-cerradas');
        $respuesta = cargadatos();
        $clientes = $respuesta['clientes'];
        $vendedores = $respuesta['vendedores'];
        $vendedores1 = $respuesta['vendedores1'];
        $giros = $respuesta['giros'];
        $areaproduccions = $respuesta['areaproduccions'];
        $tipoentregas = $respuesta['tipoentregas'];
        $comunas = $respuesta['comunas'];
        $fechaAct = $respuesta['fechaAct'];

        $aux_verestado='2'; //Mostrar solo opcion orddesp cerradas
        $titulo = "Consultar Orden Despacho Cerradas";

        return view('reportorddespguiafact.index', compact('clientes','vendedores','vendedores1','giros','areaproduccions','tipoentregas','comunas','fechaAct','aux_verestado','titulo'));

    }

    public function indexupdateguiafact()
    {
        can('listar-despacho-cerradas-edit-guia-fact');
        $respuesta = cargadatos();
        $clientes = $respuesta['clientes'];
        $vendedores = $respuesta['vendedores'];
        $vendedores1 = $respuesta['vendedores1'];
        $giros = $respuesta['giros'];
        $areaproduccions = $respuesta['areaproduccions'];
        $tipoentregas = $respuesta['tipoentregas'];
        $comunas = $respuesta['comunas'];
        $fechaAct = $respuesta['fechaAct'];

        $aux_verestado='3'; //3 muestra boton de editar Num Guia y Num Fact 
        $titulo = "Editar Número Guia o Factura";

        return view('reportorddespguiafact.index', compact('clientes','vendedores','vendedores1','giros','areaproduccions','tipoentregas','comunas','fechaAct','aux_verestado','titulo'));

    }

    public function reporte(Request $request){
        $respuesta = array();
        $respuesta['exito'] = false;
        $respuesta['mensaje'] = "Código no Existe";
        $respuesta['tabla'] = "";
    
        if($request->ajax()){
            $datas = consultaorddesp($request);

            $encabezadoGF = "
                <th class='tooltipsC' title='Num Guia'>NumGuia</th>
                <th class='tooltipsC' title='Fecha Guia'>F Guia</th>
                <th class='tooltipsC' title='Num Factura'>NumFact</th>
                <th class='tooltipsC' title='Fecha Factura'>F Fact</th>";
            if ($request->statusOD >= 1 and $request->statusOD <=3){
                $encabezadoGF = "";
            }
            $encabezadoeditarguiafac = "";

            if($request->aux_verestado == '3'){
                $encabezadoeditarguiafac = "<th class='width70 tooltipsC' title='Editar Guia Factura'>Edit</th>";
            }
    
            $respuesta['tabla'] .= "<table id='tablacotizacion' name='tablacotizacion' class='table display AllDataTables table-hover table-condensed tablascons' data-page-length='50'>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Fecha</th>
                    <th class='tooltipsC' title='Fecha Estimada de Despacho'>Fecha ED</th>
                    <th>Razón Social</th>
                    <th class='tooltipsC' title='Orden de Despacho'>OD</th>
                    <th class='tooltipsC' title='Solicitud de Despacho'>SD</th>
                    <th class='tooltipsC' title='Orden de Compra'>OC</th>
                    <th class='tooltipsC' title='Nota de Venta'>NV</th>
                    <th>Comuna</th>
                    <th class='tooltipsC' title='Total Kg'>Total Kg</th>
                    <th class='tooltipsC' title='Subtotal $'>$</th>
                    $encabezadoGF
                    $encabezadoeditarguiafac
                </tr>
            </thead>
            <tbody>";
    
            $i = 0;
            $aux_totalkilos = 0;
            $totalsumsubtotal = 0;
            foreach ($datas as $data) {
                $rut = number_format( substr ( $data->rut, 0 , -1 ) , 0, "", ".") . '-' . substr ( $data->rut, strlen($data->rut) -1 , 1 );
                if(empty($data->oc_file)){
                    $aux_enlaceoc = $data->oc_id;
                }else{
                    $aux_enlaceoc = "<a onclick='verpdf2(\"$data->oc_file\",2)'>$data->oc_id</a>";
                }
                $ruta_nuevoOrdDesp = route('crearord_despachoord', ['id' => $data->id]);
                $aprguiadesp = "<i class='glyphicon glyphicon-floppy-save text-warning tooltipsC' title='Pendiente Aprobar'></i>";
                if($data->aprguiadesp){
                    $fechaaprob = date('d-m-Y h:i:s A', strtotime($data->aprguiadespfh));
                    $aprguiadesp = "<i class='glyphicon glyphicon-floppy-save text-primary tooltipsC' title='Fecha: $fechaaprob'></i>";
                }
                $listadosoldesp = "";
                $aux_enlaceOD = "";
                if ($data->aprguiadesp == 1){
                    $aux_enlaceOD = "<a class='btn-accion-tabla btn-sm tooltipsC' title='Orden de Despacho' onclick='genpdfOD($data->id,1)'>
                                        <i class='fa fa-fw fa-file-pdf-o'></i>$data->id
                                    </a>";
                }
                if ($data->despachoordanul_id != null){
                    $aux_enlaceOD = "<small class='label pull-left bg-red'>Anulado</small>";
                }

                $aux_fechaguia = $data->guiadespachofec == null ? "" : date('d-m-Y', strtotime($data->guiadespachofec));

                $detalleGF = "
                    <td>$data->guiadespacho</td>
                    <td data-order='$data->guiadespachofec'>$aux_fechaguia</td>
                    <td>$data->numfactura</td>
                    <td data-order='$data->fechafactura'>" . date('d-m-Y', strtotime($data->fechafactura)) . "</td>";
                    
                if ($request->statusOD >= 1 and $request->statusOD <=3){
                    $detalleGF = "";
                }

                $detalleeditarguiafac = "";
                if($request->aux_verestado == '3'){
                    $detalleeditarguiafac = "
                        <td>
                            <a class='btn btn-primary btn-xs tooltipsC' title='Editar Guia' onclick='guiadesp($i,$data->id,2)'>
                                <i class='fa fa-fw fa-pencil'></i>
                            </a>
                            <a class='btn btn-primary btn-xs tooltipsC' title='Editar Fact' onclick='numfactura($i,$data->id,2)'>
                                <i class='fa fa-fw fa-pencil'></i>
                            </a>
                        </td>
                    ";
                }
    

                $respuesta['tabla'] .= "
                <tr id='fila$i' name='fila$i' class='btn-accion-tabla tooltipsC'>
                    <td>$data->id</td>
                    <td data-order='$data->fechahora'>" . date('d-m-Y', strtotime($data->fechahora)) . "</td>
                    <td data-order='$data->fechaestdesp'>" . date('d-m-Y', strtotime($data->fechaestdesp)) . "</td>
                    <td>$data->razonsocial</td>
                    <td>$aux_enlaceOD</td>
                    <td>
                        <a class='btn-accion-tabla btn-sm tooltipsC' title='Solicitud de Despacho' onclick='genpdfSD($data->despachosol_id,1)'>
                            <i class='fa fa-fw fa-file-pdf-o'></i>$data->despachosol_id
                        </a>
                    </td>
                    <td>$aux_enlaceoc</td>
                    <td>
                        <a class='btn-accion-tabla btn-sm tooltipsC' title='Nota de Venta' onclick='genpdfNV($data->notaventa_id,1)'>
                            <i class='fa fa-fw fa-file-pdf-o'></i>$data->notaventa_id
                        </a>
                    </td>
                    <td>$data->comunanombre</td>
                    <td style='text-align:right' data-order='$data->totalkilos'>".
                        number_format($data->totalkilos, 2, ",", ".") .
                    "</td>
                    <td style='text-align:right' data-order='$data->subtotal'>".
                        number_format($data->subtotal, 0, ",", ".") .
                    "</td>
                    $detalleGF
                    $detalleeditarguiafac
                </tr>";
                $i++;
                $aux_totalkilos += $data->totalkilos;
                $totalsumsubtotal += $data->subtotal;
            }
            $respuesta['tabla'] .= "
            </tbody>
                <tfoot>
                    <tr>
                        <th style='text-align:right' colspan='9'>TOTAL</th>
                        <th style='text-align:right'>". number_format($aux_totalkilos, 2, ",", ".") ."</th>
                        <th style='text-align:right'>". number_format($totalsumsubtotal, 0, ",", ".") ."</th>
                        <th colspan='4'></th>
                    </tr>
                </tfoot>
            </table>";
            return $respuesta;
        }
        
        return $respuesta;
    }

    public function exportPdf(Request $request)
    {
        //dd($request);
        $datas = consultaorddesp($request);
        

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

        if($datas){
            $pdf = PDF::loadView('reportorddespguiafact.listado', compact('datas','empresa','usuario','request'))->setPaper('a4', 'landscape');;
            //return $pdf->download('cotizacion.pdf');
            return $pdf->stream("prueba");
        }else{
            dd('Ningún dato disponible en esta consulta.');
        }
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
}

function cargadatos(){
    $respuesta = array();
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
    $comunas = Comuna::orderBy('id')->get();

    $respuesta['clientes'] = $clientes;
    $respuesta['vendedores'] = $vendedores;
    $respuesta['vendedores1'] = $vendedores1;
    $respuesta['giros'] = $giros;
    $respuesta['areaproduccions'] = $areaproduccions;
    $respuesta['tipoentregas'] = $tipoentregas;
    $respuesta['comunas'] = $comunas;
    $respuesta['fecha1erDiaMes'] = date("01/m/Y");
    $respuesta['fechaAct'] = date("d/m/Y");

    return $respuesta;
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
        $vendedorcond = "notaventa.vendedor_id='$request->vendedor_id'";
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

    if(empty($request->fechadfac) or empty($request->fechahfac)){
        $aux_condFechaFac = " true";
    }else{
        $fecha = date_create_from_format('d/m/Y', $request->fechadfac);
        $fechadfac = date_format($fecha, 'Y-m-d');
        $fecha = date_create_from_format('d/m/Y', $request->fechahfac);
        $fechahfac = date_format($fecha, 'Y-m-d');
        $aux_condFechaFac = "despachoord.fechafactura>='$fechadfac' and despachoord.fechafactura<='$fechahfac'";
    }

    if(empty($request->fechaestdesp)){
        $aux_condFechaED = " true";
    }else{
        $fecha = date_create_from_format('d/m/Y', $request->fechaestdesp);
        $fechaestdesp = date_format($fecha, 'Y-m-d');
        $aux_condFechaED = "despachoord.fechaestdesp ='$fechaestdesp'";
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

    if(empty($request->statusOD)){
        $aux_statusOD = " true";
    }else{
        switch ($request->statusOD) {
            case 1: //Emitidas
                $aux_statusOD = "isnull(despachoord.aprguiadesp) and isnull(despachoordanul.id)";
                break;
            case 2: //Anuladas
                $aux_statusOD = "isnull(despachoord.aprguiadesp) and not isnull(despachoordanul.id)";
                break;
            case 3: //Esperando por guia
                $aux_statusOD = "despachoord.aprguiadesp=1 and isnull(despachoord.guiadespacho) and isnull(despachoordanul.id)";
                break;    
            case 4: //Esperando por Factura
                $aux_statusOD = "not isnull(despachoord.guiadespacho) and isnull(despachoord.numfactura) and isnull(despachoordanul.id)";
                break;
            case 5: //Cerradas
                $aux_statusOD = "not isnull(despachoord.numfactura) and isnull(despachoordanul.id)";
                break;
        }
        
    }

    if(empty($request->comuna_id)){
        $aux_condcomuna_id = " true";
    }else{
        $aux_condcomuna_id = "notaventa.comunaentrega_id='$request->comuna_id'";
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

    $aux_condaprobord = "true";

    //$suma = despachoord::findOrFail(2)->despachoorddets->where('notaventadetalle_id',1);

    $sql = "SELECT despachoord.id,despachoord.despachosol_id,despachoord.fechahora,cliente.rut,
            cliente.razonsocial,notaventa.oc_id,notaventa.oc_file,
            comuna.nombre as comunanombre,
            despachoord.notaventa_id,despachoord.fechaestdesp,
            sum(despachoorddet.cantdesp * (notaventadetalle.totalkilos / notaventadetalle.cant)) AS totalkilos,
            sum((notaventadetalle.preciounit * despachoorddet.cantdesp)) AS subtotal,
            despachoord.aprguiadesp,despachoord.aprguiadespfh,
            despachoord.guiadespacho,despachoord.guiadespachofec,despachoord.numfactura,despachoord.fechafactura,
            despachoordanul.id as despachoordanul_id
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
            LEFT JOIN despachoordanul
            ON despachoordanul.despachoord_id=despachoord.id
            WHERE $vendedorcond
            and $aux_condFecha
            and $aux_condFechaFac
            and $aux_condFechaED
            and $aux_condrut
            and $aux_condoc_id
            and $aux_condgiro_id
            and $aux_condareaproduccion_id
            and $aux_condtipoentrega_id
            and $aux_condnotaventa_id
            and $aux_statusOD
            and $aux_condcomuna_id
            and $aux_condaprobord
            and $aux_condid
            and $aux_conddespachosol_id
            and $aux_condguiadespacho
            and $aux_condnumfactura
            and isnull(despachoord.deleted_at) AND isnull(notaventa.deleted_at) AND isnull(notaventadetalle.deleted_at)
            GROUP BY despachoord.id;";
            
            //Linea en comentario para poder mostrar todos los registros incluso las notas de venta que  que fueron cerradas de manera forzada
            //and notaventa.id not in (select notaventa_id from notaventacerrada where isnull(notaventacerrada.deleted_at))

            //and despachoord.id not in (SELECT despachoord_id from despachoordanul where isnull(deleted_at))

    //dd($sql);
    $datas = DB::select($sql);

    return $datas;
}