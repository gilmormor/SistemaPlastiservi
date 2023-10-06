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

class FacturaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-factura');
        return view('factura.index');
    }

    public function facturapage(){
        $datas = consultaindex();
        return datatables($datas)->toJson();
    }

    public function listarguiadesp()
    {
        can('listar-orden-despacho');
        $clientesArray = Cliente::clientesxUsuario();
        $clientes = $clientesArray['clientes'];
        $vendedor_id = $clientesArray['vendedor_id'];
        $sucurArray = $clientesArray['sucurArray'];

        $giros = Giro::orderBy('id')->get();
        $areaproduccions = AreaProduccion::orderBy('id')->get();
        $tipoentregas = TipoEntrega::orderBy('id')->get();
        $fechaAct = date("d/m/Y");
        $tablashtml['comunas'] = Comuna::selectcomunas();
        $tablashtml['vendedores'] = Vendedor::selectvendedores();

        return view('factura.listarguiadesp', compact('giros','areaproduccions','tipoentregas','fechaAct','tablashtml'));
    }

    public function listarguiadesppage(Request $request){
        $datas = consultalistarguiadesppage($request);
        //dd($datas);
        return datatables($datas)->toJson();
    }



    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function crear()
    {
        can('crear-factura');
        $vendedor = Vendedor::vendedores();
        $tablas['vendedores'] = $vendedor['vendedores'];
        //dd($tablas);
        return view('factura.crear',compact('tablas'));
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

    public function totalizarindex(){
        $respuesta = array();
        $datas = consultaindex();
        $kgtotal = 0;
        //$aux_totaldinero = 0;
        foreach ($datas as $data) {
            $kgtotal += $data->kgtotal;
            //$aux_totaldinero += $data->subtotal;
        }
        $respuesta['kgtotal'] = $kgtotal;
        //$respuesta['aux_totaldinero'] = $aux_totaldinero;
        return $respuesta;
    }
}


function consultaindex(){
    $user = Usuario::findOrFail(auth()->id());
    $sucurArray = $user->sucursales->pluck('id')->toArray();
    $sucurcadena = implode(",", $sucurArray);

    $sql ="SELECT factura.id,factura.fechahora,cliente.razonsocial,
        comuna.nombre as cmnarecep,factura.kgtotal,
        factura.tipoentrega_id,tipoentrega.nombre as tipoentrega_nombre,tipoentrega.icono,
        clientebloqueado.descripcion as clientebloqueado_descripcion,
        '' as oc_id,'' as oc_file,'' as notaventa_id,'' as despachosol_id,'' as despachoord_id,'' as guiadesp_id,
        factura.updated_at
        FROM factura INNER JOIN cliente
        ON factura.cliente_id  = cliente.id AND ISNULL(factura.deleted_at) AND ISNULL(cliente.deleted_at)
        inner join comuna
        ON cliente.comunap_id = comuna.id AND ISNULL(comuna.deleted_at)
        INNER JOIN tipoentrega
        ON factura.tipoentrega_id  = tipoentrega.id AND ISNULL(tipoentrega.deleted_at)
        LEFT JOIN clientebloqueado
        ON factura.cliente_id = clientebloqueado.cliente_id AND ISNULL(clientebloqueado.deleted_at)
        WHERE factura.id NOT IN (SELECT facturaanul.factura_id FROM facturaanul WHERE ISNULL(facturaanul.deleted_at))
        AND factura.sucursal_id in ($sucurcadena)
        AND ISNULL(factura.nrodocto)
        AND ISNULL(factura.fchemis)
        ORDER BY factura.id desc;";

    return DB::select($sql);
}

function consultalistarguiadesppage($request){
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
        $vendedorcond = "notaventa.vendedor_id='$request->vendedor_id'";
    }

    if(empty($request->fechad) or empty($request->fechah)){
        $aux_condFecha = " true";
    }else{
        $fecha = date_create_from_format('d/m/Y', $request->fechad);
        $fechad = date_format($fecha, 'Y-m-d');
        $fecha = date_create_from_format('d/m/Y', $request->fechah);
        $fechah = date_format($fecha, 'Y-m-d');
        $aux_condFecha = "guiadesp.fchemis>='$fechad' and guiadesp.fchemis<='$fechah'";
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
        $aux_condcomuna_id = " true";
    }else{
        $aux_condcomuna_id = "notaventa.comunaentrega_id='$request->comuna_id'";
    }
    $user = Usuario::findOrFail(auth()->id());
    $sucurArray = $user->sucursales->pluck('id')->toArray();
    $sucurcadena = implode(",", $sucurArray);

    $sql = "SELECT guiadesp.id,guiadesp.nrodocto,guiadesp.fchemis,guiadesp.despachoord_id,notaventa.cotizacion_id,despachoord.despachosol_id,
    guiadesp.fechahora,despachoord.fechaestdesp,
    cliente.razonsocial,notaventa.oc_id,notaventa.oc_file,despachoord.notaventa_id,
    '' as notaventaxk,comuna.nombre as comuna_nombre,
    tipoentrega.nombre as tipoentrega_nombre,tipoentrega.icono,clientebloqueado.descripcion as clientebloqueado_descripcion,
    guiadesp.kgtotal as aux_totalkg,
    guiadesp.mnttotal as subtotal,
    guiadesp.updated_at,'' as rutacrear
    FROM guiadesp INNER JOIN despachoord
    ON guiadesp.despachoord_id = despachoord.id AND ISNULL(guiadesp.deleted_at) AND ISNULL(despachoord.deleted_at)
    INNER JOIN notaventa
    ON notaventa.id = guiadesp.notaventa_id and isnull(notaventa.deleted_at)
    INNER JOIN cliente
    ON cliente.id = notaventa.cliente_id AND isnull(cliente.deleted_at)
    INNER JOIN comuna
    ON comuna.id = despachoord.comunaentrega_id AND isnull(comuna.deleted_at)
    INNER JOIN tipoentrega
    ON tipoentrega.id = despachoord.tipoentrega_id AND ISNULL(tipoentrega.deleted_at)
    LEFT JOIN clientebloqueado
    ON clientebloqueado.cliente_id = notaventa.cliente_id AND ISNULL(clientebloqueado.deleted_at)
    WHERE $vendedorcond
    and $aux_condFecha
    and $aux_condrut
    and $aux_condoc_id
    and $aux_condgiro_id
    and $aux_condtipoentrega_id
    and $aux_condnotaventa_id
    and $aux_condcomuna_id
    AND guiadesp.sucursal_id in ($sucurcadena)
    AND NOT ISNULL(guiadesp.nrodocto)
    AND NOT ISNULL(guiadesp.fchemis)
    AND guiadesp.id NOT IN (SELECT guiadespanul.guiadesp_id FROM guiadespanul WHERE ISNULL(guiadespanul.deleted_at))
    AND isnull(despachoord.numfactura)
    order BY guiadesp.nrodocto;";

    //dd($sql);
    $arrays = DB::select($sql);
    $i = 0;
    foreach ($arrays as $array) {
        $arrays[$i]->rutacrear = route('crear_factura', ['id' => $array->id]);
        $i++;
    }
    return $arrays;
}