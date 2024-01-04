<?php

namespace App\Http\Controllers;

use App\Models\AreaProduccion;
use App\Models\Cliente;
use App\Models\ClienteSucursal;
use App\Models\ClienteVendedor;
use App\Models\Comuna;
use App\Models\DespachoOrd;
use App\Models\Giro;
use App\Models\Seguridad\Usuario;
use App\Models\TipoEntrega;
use App\Models\Vendedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportOrdDespCerradaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
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
        $areaproduccions =  AreaProduccion::areaproduccionxusuario();
        $tipoentregas = TipoEntrega::orderBy('id')->get();
        $comunas = Comuna::orderBy('id')->get();
        $fechaServ = [
                    'fecha1erDiaMes' => date("01/m/Y"),
                    'fechaAct' => date("d/m/Y"),
                    ];


        return view('reportorddespcerrada.index', compact('clientes','vendedores','vendedores1','giros','areaproduccions','tipoentregas','comunas','fechaServ'));

    }

    public function reporte(Request $request){
        $respuesta = array();
		$respuesta['exito'] = false;
		$respuesta['mensaje'] = "Código no Existe";
		$respuesta['tabla'] = "";
        if($request->ajax()){
            $datas = consulta($request);
            $aux_colvistoth = "";
            if(auth()->id()==1 or auth()->id()==2 or auth()->id()==24){
                $aux_colvistoth = "<th class='tooltipsC' title='Leido'>Leido</th>";
            }
            $aux_colvistoth = "<th class='tooltipsC' title='Leido'>Leido</th>";

            $respuesta['tabla'] .= "<table id='tablacotizacion' name='tablacotizacion' class='table display AllDataTables table-hover table-condensed tablascons' data-page-length='50'>
			<thead>
				<tr>
                    <th class='tooltipsC' title='Nota de Venta'>NV</th>
                    <th>Fecha</th>
					<th>RUT</th>
                    <th>Razón Social</th>
                    <th class='tooltipsC' title='Precio x Kg'>$ x Kg</th>
                    <th class='tooltipsC' title='Orden de Compra'>OC</th>
                    <th style='text-align:right' class='tooltipsC' title='Total kg'>Total Kg</th>
                    <th style='text-align:right' class='tooltipsC' title='Total Pesos'>Total $</th>
                    <th style='text-align:right' class='tooltipsC' title='Precio Promedio x Kg'>Prom</th>
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
                if(!empty($data->anulada)){
                    $colorFila = 'background-color: #87CEEB;';
                    $aux_data_toggle = "tooltip";
                    $aux_title = "Anulada Fecha:" . $data->anulada;
                }
    
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
                            if(auth()->id()==1 or auth()->id()==2 or auth()->id()==24){
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
                $aux_icodespacho = "";
                $aux_obsdespacho = "No ha iniciado el despacho";
                if(!empty($data->inidespacho)){
                    $aux_icodespacho = "fa-star-o";
                    $aux_obsdespacho = "Ini: " . date('d-m-Y', strtotime($data->inidespacho)) . " Guia: " . $data->guiasdespacho;
                }
                if(!empty($data->findespacho)){
                    $aux_icodespacho = " fa-star";
                    $aux_obsdespacho = "Ini:" . date('d-m-Y', strtotime($data->inidespacho)) . " Fin:" . date('d-m-Y', strtotime($data->findespacho)) . " Guia: " . $data->guiasdespacho;
                }
                $respuesta['tabla'] .= "
                <tr id='fila$i' name='fila$i' style='$colorFila' title='$aux_title' data-toggle='$aux_data_toggle' class='btn-accion-tabla tooltipsC'>
                    <td id='id$i' name='id$i'>
                        <a class='btn-accion-tabla btn-sm tooltipsC' onclick='genpdfNV($data->id,1)' title='Nota de Venta'>
                            $data->id
                        </a>
                        <a class='btn-accion-tabla btn-sm tooltipsC' title='$aux_obsdespacho' data-toggle='tooltip'>
                            <i class='fa fa-fw $aux_icodespacho'></i>                                    
                        </a>
                    </td>
                    <td id='fechahora$i' name='fechahora$i'>" . date('d-m-Y', strtotime($data->fechahora)) . "</td>
                    <td id='rut$i' name='rut$i'>$rut</td>
                    <td id='razonsocial$i' name='razonsocial$i'>$data->razonsocial</td>
                    <td>
                        <a class='btn-accion-tabla btn-sm' onclick='genpdfNV($data->id,2)' title='Nota de venta' data-toggle='tooltip'>
                            <i class='fa fa-fw fa-file-pdf-o'></i>                                    
                        </a>
                    </td>
                    <td id='oc_id$i' name='oc_id$i'>$aux_enlaceoc</a></td>
                    <td id='totalkilos$i' name='totalkilos$i' style='text-align:right'>".number_format($data->totalkilos, 2, ",", ".") ."</td>
                    <td id='totalps$i' name='totalps$i' style='text-align:right'>".number_format($data->subtotal, 2, ",", ".") ."</td>
                    <td id='prompvc$i' name='prompvc$i' style='text-align:right'>".number_format($aux_prom, 2, ",", ".") ."</td>
                </tr>";

                if(empty($data->anulada)){
                    $aux_Tpvckg += $data->pvckg;
                    $aux_Tpvcpesos += $data->pvcpesos;
                    $aux_Tcankg += $data->cankg;
                    $aux_Tcanpesos += $data->canpesos;
                    $aux_totalKG += $data->totalkilos;
                    $aux_totalps += $data->subtotal;    
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
                    <th colspan='6' style='text-align:left'>TOTAL</th>
                    <th style='text-align:right'>". number_format($aux_totalKG, 2, ",", ".") ."</th>
                    <th style='text-align:right'>". number_format($aux_totalps, 2, ",", ".") ."</th>
                    <th style='text-align:right'>". number_format($aux_promGeneral, 2, ",", ".") ."</th>
                </tr>
            </tfoot>
            </table>";
            return $respuesta;
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


function consulta($request){
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

    $aux_aprobstatus = "";
    //dd($request->aprobstatus);
    if(is_array($request->aprobstatus)){
        if(!empty($request->aprobstatus)){
            if(in_array('1',$request->aprobstatus)){
                $aux_aprobstatus = " notaventa.aprobstatus='0'";
            }
            if(in_array('2',$request->aprobstatus)){
                
                $aux_aprobstatus = " notaventa.aprobstatus='2'";
            }
            if(in_array('3',$request->aprobstatus)){
                $aux_aprobstatus = " (notaventa.aprobstatus='1' or notaventa.aprobstatus='3')";
            }
            if(in_array('4',$request->aprobstatus)){
                $aux_aprobstatus = " notaventa.aprobstatus='4'";
            }
            if(in_array('7',$request->aprobstatus)){
                $aux_aprobstatus = " notaventa.id in (SELECT notaventa_id
                                                        FROM notaventacerrada
                                                        WHERE ISNULL(notaventacerrada.deleted_at))";
            }
            if(in_array('8',$request->aprobstatus)){
                $aux_aprobstatus .= " !isnull(notaventa.anulada)";
            }
        }
    }else{
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
                case 5:
                    $aux_aprobstatus = "notaventa.findespacho IS NULL";
                    break;
                case 6:
                    $aux_aprobstatus = "notaventa.findespacho IS NOT NULL";
                    break;
            }
        }
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
    $sucurArray = implode ( ',' , $user->sucursales->pluck('id')->toArray());
    $aux_condsucursal_id = " notaventa.sucursal_id in ($sucurArray) ";


    $sql = "SELECT notaventadetalle.notaventa_id as id,notaventa.fechahora,notaventa.cliente_id,notaventa.comuna_id,notaventa.comunaentrega_id,
            notaventa.oc_id,notaventa.anulada,cliente.rut,cliente.razonsocial,aprobstatus,visto,oc_file,
            sum(notaventadetalle.cant) AS cant,sum(notaventadetalle.precioxkilo) AS precioxkilo,
            sum(notaventadetalle.totalkilos) AS totalkilos,sum(notaventadetalle.subtotal) AS subtotal,
            sum(if(areaproduccion.id=1,notaventadetalle.totalkilos,0)) AS pvckg,
            sum(if(areaproduccion.id=2,notaventadetalle.totalkilos,0)) AS cankg,
            sum(if(areaproduccion.id=1,notaventadetalle.subtotal,0)) AS pvcpesos,
            sum(if(areaproduccion.id=2,notaventadetalle.subtotal,0)) AS canpesos,
            sum(notaventadetalle.subtotal) AS totalps,
            notaventa.inidespacho,notaventa.guiasdespacho,notaventa.findespacho
            FROM notaventa INNER JOIN notaventadetalle
            ON notaventa.id=notaventadetalle.notaventa_id
            INNER JOIN producto
            ON notaventadetalle.producto_id=producto.id
            INNER JOIN categoriaprod
            ON categoriaprod.id=producto.categoriaprod_id
            INNER JOIN areaproduccion
            ON areaproduccion.id=categoriaprod.areaproduccion_id
            INNER JOIN cliente
            ON cliente.id=notaventa.cliente_id
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
            and $aux_condsucursal_id
            and notaventa.deleted_at is null and notaventadetalle.deleted_at is null
            GROUP BY notaventadetalle.notaventa_id,notaventa.fechahora,notaventa.cliente_id,notaventa.comuna_id,notaventa.comunaentrega_id,
            notaventa.oc_id,notaventa.anulada,cliente.rut,cliente.razonsocial,aprobstatus,visto,oc_file,
            notaventa.inidespacho,notaventa.guiasdespacho,notaventa.findespacho;";
    //dd("$sql");
    $datas = DB::select($sql);
    return $datas;
}
