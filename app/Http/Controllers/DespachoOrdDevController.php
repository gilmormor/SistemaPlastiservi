<?php

namespace App\Http\Controllers;

use App\Models\AreaProduccion;
use App\Models\Cliente;
use App\Models\ClienteVendedor;
use App\Models\Comuna;
use App\Models\Giro;
use App\Models\Seguridad\Usuario;
use App\Models\TipoEntrega;
use App\Models\Vendedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DespachoOrdDevController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-devolucion-orden-despacho');
        return view('despachoorddev.index');
    }

    
    public function despachoorddevpage(){
        $sql = "SELECT despachoorddev.id,DATE_FORMAT(despachoorddev.fechahora,'%d/%m/%Y %h:%i %p') as fechahora,
                cliente.razonsocial,despachoord_id,
                '' as pdfcot,
                despachoorddev.fechahora as fechahora_aaaammdd
            FROM despachoorddev inner join despachoord
            on despachoord.id = despachoorddev.despachoord_id and isnull(despachoord.deleted_at)
            and despachoord.id not in (select despachoordanul.despachoord_id from despachoordanul where isnull(despachoordanul.deleted_at))
            inner join notaventa
            on notaventa.id = despachoord.notaventa_id and isnull(notaventa.deleted_at) and isnull(notaventa.anulada)
            inner join cliente
            on cliente.id = notaventa.cliente_id and isnull(cliente.deleted_at)
            where isnull(despachoorddev.anulada) and isnull(despachoorddev.deleted_at)
            ORDER BY despachoorddev.id desc;";

        $datas = DB::select($sql);
        //dd($datas);

        return datatables($datas)->toJson();

        
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

    public function consultadespordfact(Request $request){
        $respuesta = cargadatos();
        $clientes = $respuesta['clientes'];
        $tablashtml['giros'] = $respuesta['giros'];
        $tablashtml['areaproduccions'] = $respuesta['areaproduccions'];
        $tablashtml['tipoentregas'] = $respuesta['tipoentregas'];
        $tablashtml['fechaServ'] = [
                    'fecha1erDiaMes' => $respuesta['fecha1erDiaMes'],
                    'fechaAct' => $respuesta['fechaAct']
                    ];

        $tablashtml['aux_verestado']='1'; //Mostrar todas los opciopnes de estado de OD

        $tablashtml['titulo'] = "Consultar Orden Despacho, Guia, Factura, cerrada";
        $tablashtml['comunas'] = Comuna::selectcomunas();
        $tablashtml['vendedores'] = Vendedor::selectvendedores();
        $tablashtml['rutacreardev'] = route('creardev_despachoorddev', ['id' => '0']);
        return view('despachoorddev.consulta', compact('clientes','tablashtml'));
    }

    public function reporte(Request $request){
        $respuesta = array();
        $respuesta['exito'] = false;
        $respuesta['mensaje'] = "Código no Existe";
        $respuesta['tabla'] = "";
    
        if($request->ajax()){
            $datas = consultaorddesp($request);
            return datatables($datas)->toJson();
        }
    }

    public function creardev(Request $request){
        dd(route('creardev_despachoorddev', ['id' => '0']));
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
    $aux_statusOD = "not isnull(despachoord.numfactura) and isnull(despachoordanul.id)";
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
            round(sum((notaventadetalle.preciounit * despachoorddet.cantdesp))*((notaventa.piva+100)/100)) AS subtotal,
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