<?php

namespace App\Http\Controllers;

use App\Models\AreaProduccion;
use App\Models\Cliente;
use App\Models\ClienteBloqueado;
use App\Models\ClienteSucursal;
use App\Models\ClienteVendedor;
use App\Models\Comuna;
use App\Models\Giro;
use App\Models\Seguridad\Usuario;
use App\Models\TipoEntrega;
use App\Models\Vendedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportPendienteXProdController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Usuario::findOrFail(auth()->id());
        $sql= 'SELECT COUNT(*) AS contador
            FROM vendedor INNER JOIN persona
            ON vendedor.persona_id=persona.id
            INNER JOIN usuario 
            ON persona.usuario_id=usuario.id
            WHERE usuario.id=' . auth()->id();
        $counts = DB::select($sql);
        $vendedor_id = '0';
        if($counts[0]->contador>0){
            $vendedor_id=$user->persona->vendedor->id;
            $clientevendedorArray = ClienteVendedor::where('vendedor_id',$vendedor_id)->pluck('cliente_id')->toArray();
            $vendedores1 = Usuario::join('sucursal_usuario', function ($join) {
                $user = Usuario::findOrFail(auth()->id());
                $sucurArray = $user->sucursales->pluck('id')->toArray();
                $join->on('usuario.id', '=', 'sucursal_usuario.usuario_id')
                ->whereIn('sucursal_usuario.sucursal_id', $sucurArray);
                        })
                ->join('persona', 'usuario.id', '=', 'persona.usuario_id')
                ->join('vendedor', function ($join) {
                    $join->on('persona.id', '=', 'vendedor.persona_id')
                        ->where('vendedor.sta_activo', '=', 1);
                })
                ->select([
                    'vendedor.id',
                    'persona.nombre',
                    'persona.apellido'
                ])
                ->where('vendedor.id','=',$vendedor_id)
                ->get();
        }else{
            $clientevendedorArray = ClienteVendedor::pluck('cliente_id')->toArray();
            $vendedores1 = Usuario::join('sucursal_usuario', function ($join) {
                $user = Usuario::findOrFail(auth()->id());
                $sucurArray = $user->sucursales->pluck('id')->toArray();
                $join->on('usuario.id', '=', 'sucursal_usuario.usuario_id')
                ->whereIn('sucursal_usuario.sucursal_id', $sucurArray);
                        })
                ->join('persona', 'usuario.id', '=', 'persona.usuario_id')
                ->join('vendedor', function ($join) {
                    $join->on('persona.id', '=', 'vendedor.persona_id')
                        ->where('vendedor.sta_activo', '=', 1);
                })
                ->select([
                    'vendedor.id',
                    'persona.nombre',
                    'persona.apellido'
                ])
                ->get();
        }
        $sucurArray = $user->sucursales->pluck('id')->toArray();
        //* Filtro solos los clientes que esten asignados a la sucursal y asignado al vendedor logueado*/
        $clientes = Cliente::select(['cliente.id','cliente.rut','cliente.razonsocial','cliente.direccion','cliente.telefono'])
        ->whereIn('cliente.id' , ClienteSucursal::select(['cliente_sucursal.cliente_id'])
                                ->whereIn('cliente_sucursal.sucursal_id', $sucurArray)
        ->pluck('cliente_sucursal.cliente_id')->toArray())
        ->whereIn('cliente.id',$clientevendedorArray)
        ->get();
        $vendedores = Vendedor::orderBy('id')->where('sta_activo',1)->get();

        $giros = Giro::orderBy('id')->get();
        $areaproduccions = AreaProduccion::orderBy('id')->get();
        $tipoentregas = TipoEntrega::orderBy('id')->get();
        $comunas = Comuna::orderBy('id')->get();
        $fechaAct = date("d/m/Y");

        return view('reportpendientexprod.index', compact('clientes','vendedores','vendedores1','giros','areaproduccions','tipoentregas','comunas','fechaAct'));
    
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

}

function reporte1($request){
    $respuesta = array();
    $respuesta['exito'] = false;
    $respuesta['mensaje'] = "Código no Existe";
    $respuesta['tabla'] = "";
    $respuesta['tabla2'] = "";
    $respuesta['tabla3'] = "";

    if($request->ajax()){
        /*
        $datas = consulta($request,1,1);
        $aux_colvistoth = "";
        if(auth()->id()==1 or auth()->id()==2){
            $aux_colvistoth = "<th class='tooltipsC' title='Leido'>Leido</th>";
        }
        $aux_colvistoth = "<th class='tooltipsC' title='Leido'>Leido</th>";

        $respuesta['tabla'] .= "<table id='tabla-data-listar' name='tabla-data-listar' class='table display AllDataTables table-hover table-condensed tablascons' data-page-length='50'>
        <thead>
            <tr>
                <th>ID</th>
                <th>Fecha</th>
                <th>Razón Social</th>
                <th class='tooltipsC' title='Orden de Compra'>OC</th>
                <th class='tooltipsC' title='Nota de Venta'>NV</th>
                <th class='tooltipsC' title='Precio x Kg'>$ x Kg</th>
                <th>Comuna</th>
                <th style='text-align:right' class='tooltipsC' title='Kg Pendiente'>Kg Pend</th>
                <th style='text-align:right' class='tooltipsC' title='$ Pendiente'>$ Pend</th>
                <!--<th style='text-align:right' class='tooltipsC' title='Precio Promedio x Kg'>Prom</th>-->
                <th class='tooltipsC' title='Solicitud Despacho'>Despacho</th>
            </tr>
        </thead>
        <tbody>";

        $i = 0;
        $aux_Tpvckg = 0;
        $aux_Tpvcpesos= 0;
        $aux_Tcankg = 0;
        $aux_Tcanpesos = 0;
        $aux_totalKG = 0;
        $aux_totalps = 0;
        $aux_prom = 0;
        foreach ($datas as $data) {
            $colorFila = "";
            $aux_data_toggle = "";
            $aux_title = "";

            $rut = number_format( substr ( $data->rut, 0 , -1 ) , 0, "", ".") . '-' . substr ( $data->rut, strlen($data->rut) -1 , 1 );
            $prompvc = 0;
            $promcan = 0;
            $aux_prom = 0;
            if($data->pvckg!=0){
                $prompvc = $data->pvcpesos / $data->pvckg;
            }
            if($data->cankg!=0){
                $promcan = $data->canpesos / $data->cankg;
            }
            if($data->totalkilos>0){
                $aux_prom = $data->subtotal / $data->totalkilos;
            }

            $Visto       = $data->visto;
            $checkVisto  = 'checked';
            if(empty($data->visto))
                $checkVisto = '';

            $aux_colvistotd = "";
            if(empty($data->visto)){
                $fechavisto = '';
            }else{
                $fechavisto = 'Leido:' . date('d-m-Y h:i:s A', strtotime($data->visto));
            }
            
            $aux_colvistotd = "
            <td class='tooltipsC' style='text-align:center' class='tooltipsC' title='$fechavisto'>
                <div class='checkbox'>
                    <label style='font-size: 1.2em'>";
                    if(!empty($data->anulada)){
                        $aux_colvistotd .= "<input type='checkbox' id='visto$i' name='visto$i' value='$Visto' $checkVisto disabled>";
                    }else{
                        if(auth()->id()==1 or auth()->id()==2){
                            $aux_colvistotd .= "<input type='checkbox' id='visto$i' name='visto$i' value='$Visto' $checkVisto onclick='visto($data->id,$i)'>";
                        }else{
                            $aux_colvistotd .= "<input type='checkbox' id='visto$i' name='visto$i' value='$Visto' $checkVisto disabled>";
                        }
                    }
                    $aux_colvistotd .= "<span class='cr'><i class='cr-icon fa fa-check'></i></span>
                    </label>
                </div>
            </td>";
            if(empty($data->oc_file)){
                $aux_enlaceoc = $data->oc_id;
            }else{
                $aux_enlaceoc = "<a onclick='verpdf2(\"$data->oc_file\",2)'>$data->oc_id</a>";
            }
            $nuevoSolDesp = "<a class='btn-accion-tabla btn-sm tooltipsC' title='Vista Previa SD' onclick='pdfSolDespPrev($data->id,2)'>
                                <i class='fa fa-fw fa-file-pdf-o'></i>                                    
                            </a>";
            $clibloq = ClienteBloqueado::where("cliente_id" , "=" ,$data->cliente_id)->get();
            if(count($clibloq) > 0){
                $aux_descbloq = $clibloq[0]->descripcion;
                $nuevoSolDesp .= "<a class='btn-accion-tabla tooltipsC' title='Cliente Bloqueado: $aux_descbloq'>
                                    <i class='fa fa-fw fa-ban text-danger'></i>
                                </a>";
            }else{
                $ruta_nuevoSolDesp = route('crearsol_despachosol', ['id' => $data->id]);
                $nuevoSolDesp .= "<a href='$ruta_nuevoSolDesp' class='btn-accion-tabla tooltipsC' title='Hacer solicitud despacho: $data->tipentnombre'>
                    <i class='fa fa-fw $data->icono'></i>
                    </a>";
            }
            if(!empty($data->anulada)){
                $colorFila = 'background-color: #87CEEB;';
                $aux_data_toggle = "tooltip";
                $aux_title = "Anulada Fecha:" . $data->anulada;
                $nuevoSolDesp = "";
            }
            //dd($ruta_nuevoSolDesp);
            $respuesta['tabla'] .= "
            <tr id='fila$i' name='fila$i' style='$colorFila' title='$aux_title' data-toggle='$aux_data_toggle' class='btn-accion-tabla tooltipsC'>
                <td id='id$i' name='id$i'>$data->id</td>
                <td id='fechahora$i' name='fechahora$i'>" . date('d-m-Y', strtotime($data->fechahora)) . "</td>
                <td id='razonsocial$i' name='razonsocial$i'>$data->razonsocial</td>
                <td id='oc_id$i' name='oc_id$i'>$aux_enlaceoc</td>
                <td>
                    <!--<a href='" . route('exportPdf_notaventa', ['id' => $data->id,'stareport' => '1']) . "' class='btn-accion-tabla tooltipsC' title='Nota de Venta' target='_blank'>-->
                    <a class='btn-accion-tabla btn-sm tooltipsC' title='Nota de Venta' onclick='genpdfNV($data->id,1)'>
                        <i class='fa fa-fw fa-file-pdf-o'></i>$data->id
                    </a>
                </td>
                <td>
                    <!--<a href='" . route('exportPdf_notaventa', ['id' => $data->id,'stareport' => '2']) . "' class='btn-accion-tabla tooltipsC' title='Precio x Kg' target='_blank'>-->
                    <a class='btn-accion-tabla btn-sm tooltipsC' title='Precio x Kg' onclick='genpdfNV($data->id,2)'>
                        <i class='fa fa-fw fa-file-pdf-o'></i>                                    
                    </a>
                </td>
                <td>$data->comunanombre</td>
                <td id='totalkilos$i' name='totalkilos$i' style='text-align:right'>".number_format($data->totalkilos - $data->totalkgsoldesp, 2, ",", ".") ."</td>
                <td id='totalps$i' name='totalps$i' style='text-align:right'>".number_format($data->subtotal - $data->totalsubtotalsoldesp, 2, ",", ".") ."</td>
                <!--<td id='prompvc$i' name='prompvc$i' style='text-align:right'>".number_format($aux_prom, 2, ",", ".") ."</td>-->
                <td>
                    $nuevoSolDesp
                </td>
            </tr>";

            if(empty($data->anulada)){
                $aux_Tpvckg += $data->pvckg;
                $aux_Tpvcpesos += $data->pvcpesos;
                $aux_Tcankg += $data->cankg;
                $aux_Tcanpesos += $data->canpesos;
                $aux_totalKG += ($data->totalkilos - $data->totalkgsoldesp);
                $aux_totalps += ($data->subtotal - $data->totalsubtotalsoldesp);    
            }


            //dd($data->contacto);
        }

        $aux_promGeneral = 0;
        if($aux_totalKG>0){
            $aux_promGeneral = $aux_totalps / $aux_totalKG;
        }
        $respuesta['tabla'] .= "
        </tbody>
        <tfoot>
            <tr>
                <th colspan='7' style='text-align:left'>TOTAL</th>
                <th style='text-align:right'>". number_format($aux_totalKG, 2, ",", ".") ."</th>
                <th style='text-align:right'>". number_format($aux_totalps, 2, ",", ".") ."</th>
                <!--<th style='text-align:right'>". number_format($aux_promGeneral, 2, ",", ".") ."</th>-->
                <th style='text-align:right'></th>
            </tr>
        </tfoot>

        </table>";
        */

        /*****CONSULTA AGRUPADO POR CLIENTE******/
        /*
        $datas = consulta($request,1,2);
        if($datas){
            $aux_clienteid = $datas[0]->cliente_id;

        }

        $respuesta['tabla2'] .= "<table id='tabla-data-listar' name='tabla-data-listar' class='table display AllDataTables table-hover table-condensed tablascons2' data-page-length='50'>
        <thead>
            <tr>
                <th>Razón Social</th>
                <th>Comuna</th>
                <th style='text-align:right' class='tooltipsC' title='Kg Pendiente'>Kg Pend</th>
                <th style='text-align:right' class='tooltipsC' title='$ Pendiente'>$ Pend</th>
            </tr>
        </thead>
        <tbody>";

        $aux_kgpend = 0;
        $aux_platapend = 0;
        $razonsocial = "";
        $aux_comuna  = "";
        $aux_totalkg = 0;
        $aux_totalplata = 0;
        foreach ($datas as $data) {
            if($data->cliente_id!=$aux_clienteid){
                $respuesta['tabla2'] .= "
                <tr>
                    <td>$razonsocial</td>
                    <td>$aux_comuna</td>
                    <td style='text-align:right'>".number_format($aux_kgpend, 2, ",", ".") ."</td>
                    <td style='text-align:right'>".number_format($aux_platapend, 2, ",", ".") ."</td>
                </tr>";
                $aux_kgpend = 0;
                $aux_platapend = 0;
            }
            $aux_kgpend += ($data->totalkilos - $data->totalkgsoldesp);
            $aux_platapend += ($data->subtotal - $data->totalsubtotalsoldesp);
            $aux_totalkg += ($data->totalkilos - $data->totalkgsoldesp);
            $aux_totalplata += ($data->subtotal - $data->totalsubtotalsoldesp);
            $razonsocial = $data->razonsocial;
            $aux_comuna  = $data->comunanombre;

        }
        $respuesta['tabla2'] .= "
            <tr>
                <td>$razonsocial</td>
                <td>$aux_comuna</td>
                <td style='text-align:right'>".number_format($aux_kgpend, 2, ",", ".") ."</td>
                <td style='text-align:right'>".number_format($aux_platapend, 2, ",", ".") ."</td>
            </tr>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan='2' style='text-align:left'>TOTALES</th>
                    <th style='text-align:right'>". number_format($aux_totalkg, 2, ",", ".") ."</th>
                    <th style='text-align:right'>". number_format($aux_totalplata, 2, ",", ".") ."</th>
                </tr>
            </tfoot>

            </table>";
        */

        /*****CONSULTA AGRUPADO POR PRODUCTO*****/
        $datas = consulta($request,2,1);
        $respuesta['tabla3'] .= "<table id='tabla-data-listar' name='tabla-data-listar' class='table display AllDataTables table-hover table-condensed tablascons2' data-page-length='50'>
        <thead>
            <tr>
                <th>Razón Social</th>
                <th>OC</th>
                <th>NV</th>
                <th>Descripción</th>
                <th>Diametro</th>
                <th>Clase</th>
                <th>Largo</th>
                <th>Peso</th>
                <th>TU</th>
                <th style='text-align:right' class='tooltipsC' title='Cantidad Pendiente'>Cant Pend</th>
                <th style='text-align:right' class='tooltipsC' title='Kg Pendiente'>Kg Pend</th>
                <th style='text-align:right' class='tooltipsC' title='$ Pendiente'>$ Pend</th>
            </tr>
        </thead>
        <tbody>";
        $aux_totalkg = 0;
        $aux_totalplata = 0;
        foreach ($datas as $data) {
            if($data->saldoplata>0){
                if(empty($data->oc_file)){
                    $aux_enlaceoc = $data->oc_id;
                }else{
                    $aux_enlaceoc = "<a onclick='verpdf2(\"$data->oc_file\",2)'>$data->oc_id</a>";
                }
    
                $aux_totalkg += $data->saldokg; // ($data->totalkilos - $data->kgsoldesp);
                $aux_totalplata += $data->saldoplata; // ($data->subtotal - $data->subtotalsoldesp);    
                $respuesta['tabla3'] .= "
                <tr>
                    <td>$data->razonsocial</td>
                    <td>$aux_enlaceoc</td>
                    <td>
                        <a class='btn-accion-tabla btn-sm tooltipsC' title='Nota de Venta' onclick='genpdfNV($data->notaventa_id,1)'>
                            <i class='fa fa-fw fa-file-pdf-o'></i>$data->notaventa_id
                        </a>
                    </td>
                    <td>$data->nombre</td>
                    <td>$data->diametro</td>
                    <td>$data->cla_nombre</td>
                    <td>$data->long</td>
                    <td>$data->peso</td>
                    <td>$data->tipounion</td>
                    <td style='text-align:right'>$data->saldocant</td>
                    <td style='text-align:right'>".number_format($data->saldokg, 2, ",", ".") ."</td>
                    <td style='text-align:right'>".number_format($data->saldoplata, 2, ",", ".") ."</td>
                </tr>";    
            }
        }
        $respuesta['tabla3'] .= "
            </tbody>
            <tfoot>
                <tr>
                    <th colspan='7' style='text-align:left'>TOTALES</th>
                    <th style='text-align:right'>". number_format($aux_totalkg, 2, ",", ".") ."</th>
                    <th style='text-align:right'>". number_format($aux_totalplata, 2, ",", ".") ."</th>
                </tr>
            </tfoot>
            </table>";
        //dd($respuesta['tabla3']);
        return $respuesta;
    }
}

function consulta($request,$aux_sql,$orden){
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
        $vendedorcond = "notaventa.vendedor_id='$request->vendedor_id'";
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

    if(empty($request->comuna_id)){
        $aux_condcomuna_id = " true";
    }else{
        $aux_condcomuna_id = "notaventa.comunaentrega_id='$request->comuna_id'";
    }

    if(empty($request->plazoentrega)){
        $aux_condplazoentrega = " true";
    }else{
        $fecha = date_create_from_format('d/m/Y', $request->plazoentrega);
        $fechad = date_format($fecha, 'Y-m-d');
        $aux_condplazoentrega = "notaventa.plazoentrega='$fechad'";
    }
    //dd($aux_condplazoentrega);

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
        $sql = "SELECT notaventadetalle.producto_id,producto.nombre,cliente.razonsocial,notaventa.id,notaventadetalle.notaventa_id,
        if(categoriaprod.unidadmedida_id=3,producto.diamextpg,producto.diamextmm) AS diametro,notaventa.oc_id,
        claseprod.cla_nombre,producto.long,producto.peso,producto.tipounion,
        cant,cantsoldesp,
        totalkilos,
        subtotal,
        kgsoldesp,subtotalsoldesp,
        sum(cant-if(isnull(cantsoldesp),0,cantsoldesp)) as saldocant,
        sum(totalkilos-if(isnull(kgsoldesp),0,kgsoldesp)) as saldokg,
        sum(subtotal-if(isnull(subtotalsoldesp),0,subtotalsoldesp)) as saldoplata
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
        LEFT JOIN vista_sumsoldespdet
        ON vista_sumsoldespdet.notaventadetalle_id=notaventadetalle.id
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
        AND isnull(notaventa.findespacho)
        AND isnull(notaventa.anulada)
        AND isnull(notaventa.deleted_at) AND isnull(notaventadetalle.deleted_at)
        GROUP BY notaventadetalle.producto_id
        ORDER BY producto.nombre,producto.peso;";
    }
    $datas = DB::select($sql);
    return $datas;
}