<?php

namespace App\Http\Controllers;

use App\Models\AreaProduccion;
use App\Models\Cliente;
use App\Models\ClienteSucursal;
use App\Models\ClienteVendedor;
use App\Models\Empresa;
use App\Models\Giro;
use App\Models\Seguridad\Usuario;
use App\Models\TipoEntrega;
use App\Models\Vendedor;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Support\Facades\DB;

class NotaVentaConsultaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('consulta-nota-de-venta');
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
        $fechaAct = date("d/m/Y");
        return view('notaventaconsulta.index', compact('clientes','vendedores','vendedores1','giros','areaproduccions','tipoentregas','fechaAct'));

    }

    public function reporte(Request $request){
        $respuesta = array();
		$respuesta['exito'] = false;
		$respuesta['mensaje'] = "Código no Existe";
		$respuesta['tabla'] = "";
        //dd($request);
        if($request->ajax()){
            $datas = consulta($request);
            $aux_colvistoth = "";
            if(auth()->id()==1 or auth()->id()==2 or auth()->id()==24){
                $aux_colvistoth = "<th class='tooltipsC' title='Leido'>Leido</th>";
            }
            $aux_colvistoth = "<th class='tooltipsC' title='Leido'>Leido</th>";
            $respuesta['tabla'] .= "<table id='tabla-data' name='tabla-data' class='table display AllDataTables table-hover table-condensed tablascons' data-page-length='50'>
			<thead>
				<tr>
					<th>ID</th>
					<th>Fecha</th>
					<th>RUT</th>
                    <th>Razón Social</th>
                    <th class='tooltipsC' title='Orden de Compra'>OC</th>
                    <th style='text-align:right' class='tooltipsC' title='Total kg'>Total Kg</th>
                    <th style='text-align:right' class='tooltipsC' title='Total Pesos'>Total $</th>
                    <th style='text-align:right' class='tooltipsC' title='Precio Promedio x Kg'>Prom</th>
                    <th class='tooltipsC' title='Nota de Venta'>NV</th>
                    <th class='tooltipsC' title='Precio x Kg'>$ x Kg</th>
                    $aux_colvistoth
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

                $aux_cantdesp = consultatotcantod($data->id);
                $aux_icodespachoNew = "";
                $aux_obsdespachoNew = "No ha iniciado el despacho";
                if($aux_cantdesp > 0){
                    $aux_icodespachoNew = "fa-star-o";
                    $aux_obsdespachoNew = "Inicio despacho";
                    $aux_obsdespacho = "";
                    if($data->cant == $aux_cantdesp){
                        $aux_icodespachoNew = " fa-star";
                        $aux_obsdespachoNew = "Fin despacho";
                    }
                }
                $respuesta['tabla'] .= "
                <tr id='fila$i' name='fila$i' style='$colorFila' title='$aux_title' data-toggle='$aux_data_toggle' class='btn-accion-tabla tooltipsC'>
                    <td id='id$i' name='id$i'>$data->id
                        <a class='btn-accion-tabla btn-sm tooltipsC' title='$aux_obsdespacho' data-toggle='tooltip'>
                            <i class='fa fa-fw $aux_icodespacho'></i>                                    
                        </a>
                        <a class='btn-accion-tabla btn-sm tooltipsC' onclick='listarorddespxNV($data->id)' title='$aux_obsdespachoNew' data-toggle='tooltip'>
                            <i class='fa fa-fw $aux_icodespachoNew text-danger'></i>                                    
                        </a>
                    </td>
                    <td id='fechahora$i' name='fechahora$i'>" . date('d-m-Y', strtotime($data->fechahora)) . "</td>
                    <td id='rut$i' name='rut$i'>$rut</td>
                    <td id='razonsocial$i' name='razonsocial$i'>$data->razonsocial</td>
                    <td id='oc_id$i' name='oc_id$i'>$aux_enlaceoc</a></td>
                    <td id='totalkilos$i' name='totalkilos$i' style='text-align:right'>".number_format($data->totalkilos, 2, ",", ".") ."</td>
                    <td id='totalps$i' name='totalps$i' style='text-align:right'>".number_format($data->subtotal, 2, ",", ".") ."</td>
                    <td id='prompvc$i' name='prompvc$i' style='text-align:right'>".number_format($aux_prom, 2, ",", ".") ."</td>
                    <td>
                        <!--<a href='" . route('exportPdf_notaventa', ['id' => $data->id,'stareport' => '1']) . "' class='btn-accion-tabla tooltipsC' title='Nota de Venta' target='_blank'>-->
                        <a class='btn-accion-tabla btn-sm' onclick='genpdfNV($data->id,1)' title='Nota de venta' data-toggle='tooltip'>
                            <i class='fa fa-fw fa-file-pdf-o'></i>                                    
                        </a>
                    </td>
                    <td>
                        <!--<a href='" . route('exportPdf_notaventa', ['id' => $data->id,'stareport' => '2']) . "' class='btn-accion-tabla tooltipsC' title='Precio x Kg' target='_blank'>-->
                        <a class='btn-accion-tabla btn-sm' onclick='genpdfNV($data->id,2)' title='Nota de venta' data-toggle='tooltip'>
                            <i class='fa fa-fw fa-file-pdf-o'></i>                                    
                        </a>
                    </td>
                    $aux_colvistotd
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
                    <th colspan='5' style='text-align:left'>TOTALES</th>
                    <th style='text-align:right'>". number_format($aux_totalKG, 2, ",", ".") ."</th>
                    <th style='text-align:right'>". number_format($aux_totalps, 2, ",", ".") ."</th>
                    <th style='text-align:right'>". number_format($aux_promGeneral, 2, ",", ".") ."</th>
                    <th style='text-align:right'></th>
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

    public function exportPdf(Request $request)
    {
        //dd($request);
        //$cotizaciones = Cotizacion::orderBy('id')->get();
        $rut=str_replace("-","",$request->rut);
        $rut=str_replace(".","",$rut);
        //dd($rut);
        if($request->ajax()){
            $notaventas = consulta($request);
        }
        //dd($request);
        //dd(str_replace(".","",$request->rut));
        $notaventas = consulta($request);
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
        if($notaventas){
            if(env('APP_DEBUG')){
                return view('notaventaconsulta.listado', compact('notaventas','empresa','usuario','aux_fdesde','aux_fhasta','nomvendedor','nombreAreaproduccion','nombreGiro','nombreTipoEntrega'));
            }

            //return view('notaventaconsulta.listado', compact('notaventas','empresa','usuario','aux_fdesde','aux_fhasta','nomvendedor','nombreAreaproduccion','nombreGiro','nombreTipoEntrega'));
            
            $pdf = PDF::loadView('notaventaconsulta.listado', compact('notaventas','empresa','usuario','aux_fdesde','aux_fhasta','nomvendedor','nombreAreaproduccion','nombreGiro','nombreTipoEntrega'));
            //return $pdf->download('cotizacion.pdf');
            //return $pdf->stream(str_pad($notaventa->id, 5, "0", STR_PAD_LEFT) .' - '. $notaventa->cliente->razonsocial . '.pdf');
            return $pdf->stream("ReporteNotasVenta.pdf");
        }else{
            dd('Ningún dato disponible en esta consulta.');
        }
    }
    
}

function consulta($request){
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
        $aux_rut = str_replace(".","",$request->rut);
        $aux_rut = str_replace("-","",$aux_rut);
        $aux_condrut = "cliente.rut='$aux_rut'";
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
            case 5:
                $aux_aprobstatus = "notaventa.findespacho IS NULL";
                break;
            case 6:
                $aux_aprobstatus = "notaventa.findespacho IS NOT NULL";
                break;
        }
        
    }

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
            and notaventa.deleted_at is null and notaventadetalle.deleted_at is null
            GROUP BY notaventadetalle.notaventa_id,notaventa.fechahora,notaventa.cliente_id,notaventa.comuna_id,notaventa.comunaentrega_id,
            notaventa.oc_id,notaventa.anulada,cliente.rut,cliente.razonsocial,aprobstatus,visto,oc_file,
            notaventa.inidespacho,notaventa.guiasdespacho,notaventa.findespacho;";
    $datas = DB::select($sql);
    return $datas;
}


function consultatotcantod($id){
    $sql = "SELECT notaventa_id,sum(cantdesp) AS cantdesp 
    FROM despachoord JOIN despachoorddet 
    ON despachoord.id = despachoorddet.despachoord_id
    WHERE NOT(despachoord.id IN (SELECT despachoordanul.despachoord_id FROM despachoordanul))
    and despachoord.numfactura is not null
    and despachoord.notaventa_id=$id
    and isnull(despachoord.deleted_at) and isnull(despachoorddet.deleted_at)
    group by despachoord.notaventa_id;";
    //dd("$sql");
    $datas = DB::select($sql);
    $aux_cant = 0;
    if($datas){
        $aux_cant = $datas[0]->cantdesp;
    }
    return $aux_cant;
}


function armarReportehtml($request){
    //$cotizaciones = Cotizacion::orderBy('id')->get();
    $rut=str_replace("-","",$request->rut);
    $rut=str_replace(".","",$rut);
    //dd($rut);
    if($request->ajax()){
        $notaventas = consulta($request);
    }
    //dd($request);
    $notaventas = consulta($request);
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

    $respuesta = array();
    $respuesta['exito'] = false;
    $respuesta['mensaje'] = "Código no Existe";
    $respuesta['tabla'] = "";
    $theme = '';
    $ruta_logo = asset("assets/$theme/dist/img/LOGO-PLASTISERVI.png");
    $respuesta['tabla'] .= "
    <br>
    <br>
    <div id='page_pdf'>
        <table id='factura_head'>
            <tr>
                <td class='logo_factura'>
                    <div>
                        <img src='$ruta_logo' style='max-width:1200%;width:auto;height:auto;'>
                        <p>$empresa[0]['nombre']</p>					
                        <p>RUT: $empresa[0]['rut']</p>
                    </div>
                </td>
                <td class='info_empresa'>
                </td>
                <td class='info_factura'>
                    <div class='round'>
                        <span class='h3'>Reporte Nota de Venta</span>
                        <p>Fecha: date('d-m-Y h:i:s A')</p>
                        <p>Area Producción: $nombreAreaproduccion</p>
                        <p>Vendedor: $nomvendedor </p>
                        <p>Giro: $nombreGiro </p>
                        <p>Tipo Entrega: $nombreTipoEntrega </p>
                        <p>Desde: $aux_fdesde Hasta: $aux_fhasta</p>
                    </div>
                </td>
            </tr>
        </table>
    
        <div class='round'>
            <table id='factura_detalle'>
                    <thead>
                        <tr>
                            <th style='text-align:left'>#</th>
                            <th style='text-align:left'>NV ID</th>
                            <th class='textcenter'>Fecha</th>
                            <th class='textleft'>Razón Social</th>
                            <th style='text-align:right'>Total Kg</th>
                            <th style='text-align:right'>Total $</th>
                            <th style='text-align:right'>Prom</th>
                        </tr>
                    </thead>
                    <tbody id='detalle_productos'>";
                    $i=0;
                    $aux_totalKG = 0;
                    $aux_totalps = 0;
                    foreach($notaventas as $notaventa){
                        if(empty($notaventa->anulada)){
                            $i++;
                            $aux_totalKG += $notaventa->totalkilos;
                            $aux_totalps += $notaventa->totalps;
                        }
                        $rut = number_format( substr ( $notaventa->rut, 0 , -1 ) , 0, '', '.') . '-' . substr ( $notaventa->rut, strlen($notaventa->rut) -1 , 1 );
                        $colorFila = '';
                        $aux_data_toggle = '';
                        $aux_title = '';
                        if(!empty($notaventa->anulada)){
                            $colorFila = 'background-color: #87CEEB;';
                            $aux_data_toggle = 'tooltip';
                            $aux_title = 'Anulada Fecha:' . $notaventa->anulada;
                        }
                        $aux_prom = 0;
                        if($notaventa->totalkilos>0){
                            $aux_prom = $notaventa->subtotal / $notaventa->totalkilos;
                        }
                        $respuesta['tabla'] .= "
                        <tr style='$colorFila' title='$aux_title' data-toggle='$aux_data_toggle' class='btn-accion-tabla tooltipsC'>
                            <td>$i</td>
                            <td>$notaventa->id</td>
                            <td style='text-align:center'>date('d-m-Y', strtotime($notaventa->fechahora))</td>
                            <td>$notaventa->razonsocial</td>
                            <td style='text-align:right'>number_format($notaventa->totalkilos, 2, ',', '.')</td>
                            <td style='text-align:right'>number_format($notaventa->totalps, 2, ',', '.')</td>
                            <td style='text-align:right'>number_format($aux_prom, 2, ',', '.')</td>
                        </tr>";
                    }
                    $respuesta['tabla'] .= "
                    </tbody>";
                    $aux_promGeneral = 0;
                    if($aux_totalKG>0){
                        $aux_promGeneral = $aux_totalps / $aux_totalKG;
                    }
    
                    $respuesta['tabla'] .= "
                                    <tfoot id='detalle_totales'>
                                        <tr class='headt'>
                                            <b>
                                            <td colspan='4' class='textright'><span>TOTALES</span></td>
                                            <td class='textright'><span>number_format($aux_totalKG, 2, ',', '.')</span></td>
                                            <td class='textright'><span>number_format($aux_totalps, 2, ',', '.')</span></td>
                                            <td class='textright'><span>number_format($aux_promGeneral, 2, ',', '.')</span></td>
                                            </b>
                                    </tfoot>
                            </table>
                        </div>
    </div>";
    return $respuesta['tabla'];
}