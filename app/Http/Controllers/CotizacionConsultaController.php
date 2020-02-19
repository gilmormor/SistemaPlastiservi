<?php

namespace App\Http\Controllers;

use App\Models\ClienteVendedor;
use App\Models\Cotizacion;
use App\Models\Empresa;
use App\Models\Seguridad\Usuario;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade as PDF;


class CotizacionConsultaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('consulta-cotizacion');
        return view('cotizacionconsulta.index', compact('datas'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function reporte(Request $request){
        $respuesta = array();
		$respuesta['exito'] = false;
		$respuesta['mensaje'] = "Código no Existe";
		$respuesta['tabla'] = "";

        //$aux_fechad=DateTime::createFromFormat('d/m/Y', $request->fechad)->format('Y-m-d');
        //$aux_fechah=DateTime::createFromFormat('d/m/Y', $request->fechah)->format('Y-m-d');
        if($request->ajax()){
            $datas = consulta($request->fechad,$request->fechah);
            /*
            if(empty($request->fechad) or empty($request->fechah)){
                $aux_condFecha = " true";
            }else{
                $fecha = date_create_from_format('d/m/Y', $request->fechad);
                $fechad = date_format($fecha, 'Y-m-d')." 00:00:00";
                $fecha = date_create_from_format('d/m/Y', $request->fechah);
                $fechah = date_format($fecha, 'Y-m-d')." 23:59:59";
                $aux_condFecha = "cotizacion.fechahora>='$fechad' and cotizacion.fechahora<='$fechah'";
            }
            $user = Usuario::findOrFail(auth()->id());
            $idvendedor = $user->persona->vendedor->id;
            $sucurArray = $user->sucursales->pluck('id')->toArray();
            $sql = "SELECT cotizacion.*,if(isnull(cliente.razonsocial),clientetemp.rut,cliente.rut) as rut,
                if(isnull(cliente.razonsocial),clientetemp.razonsocial,cliente.razonsocial) as razonsocial,
                if(isnull(cliente.razonsocial),clientetemp.razonsocial,cliente.razonsocial) as razonsocial,
                    aprobstatus,aprobobs,aprobstatus,cliente_id,clientetemp_id,
                    (SELECT COUNT(*) 
                    FROM cotizaciondetalle 
                    WHERE cotizaciondetalle.cotizacion_id=cotizacion.id and 
                    cotizaciondetalle.precioxkilo < cotizaciondetalle.precioxkiloreal) AS contador
                FROM cotizacion left join cliente
                on cotizacion.cliente_id=cliente.id
                left join clientetemp
                on cotizacion.clientetemp_id=clientetemp.id
                where " . $aux_condFecha . " and cotizacion.vendedor_id=" . $idvendedor .
                " and cotizacion.deleted_at is null;";
            //dd("$sql");
            $datas = DB::select($sql);
            */
            $respuesta['tabla'] .= '<table id="tablacotizacion" name="tablacotizacion" class="table display AllDataTables responsive table-hover table-condensed tablas">
			<thead>
				<tr>
					<th>ID</th>
					<th>Fecha</th>
					<th>RUT</th>
                    <th>Razón Social</th>
                    <th>Estado</th>
                    <th>Total</th>
                    <th>PDF</th>
				</tr>
			</thead>
            <tbody>';
            $i = 0;
            $aux_nfila = 0;
            foreach ($datas as $data) {
                $aux_nfila++; 
                $colorFila = "";
                $aprobstatus = 1;
                $aux_mensaje = "";
                $aux_data_toggle = "";
                $aux_title = "";
                if($data->contador>0){
                    $colorFila = 'background-color: #87CEEB;';
                    $aprobstatus = 2;
                    $aux_data_toggle = "tooltip";
                    $aux_title = "Precio menor al valor en tabla";
                }
                if($data->aprobstatus==4){
                    $colorFila = 'background-color: #FFC6C6;';  //" style=background-color: #FFC6C6;  title=Rechazo por: $data->aprobobs data-toggle=tooltip"; //'background-color: #FFC6C6;'; 
                    $aux_data_toggle = "tooltip";
                    $aux_title = "Rechazado por: " . $data->aprobobs;
                }

                $rut = number_format( substr ( $data->rut, 0 , -1 ) , 0, "", ".") . '-' . substr ( $data->rut, strlen($data->rut) -1 , 1 );
                    $aux_mensaje= "";
                    $aux_icono = "";
                    $aux_color = "";
                if ($data->aprobstatus=='1'){
                    $aux_mensaje = "Aprobado Vendedor";
                    $aux_icono = "glyphicon glyphicon-thumbs-up";
                    $aux_color = "btn btn-success";
                }
                if ($data->aprobstatus=='2'){
                    $aux_mensaje= "Precio menor en Tabla";
                    $aux_icono = "glyphicon glyphicon-thumbs-down";
                    $aux_color = "btn btn-danger";
                }
                if ($data->aprobstatus=='3'){
                    $aux_mensaje= "Precio menor Aprobado por supervisor";
                    $aux_icono = "glyphicon glyphicon-thumbs-up";
                    $aux_color = "btn btn-success";
                }
                if(empty($data->cliente_id)){
                    $aux_mensaje = $aux_mensaje . " - Cliente Nuevo debe ser Validado";
                    $aux_icono = "glyphicon glyphicon-thumbs-down";
                    $aux_color = "btn btn-danger";
                }else{
                    if(!empty($data->clientetemp_id)){
                        $aux_mensaje= $aux_mensaje . " - Cliente Nuevo";
                        $aux_icono = "glyphicon glyphicon-thumbs-up";
                        $aux_color = "btn btn-success";
                    }
                }
                $respuesta['tabla'] .= "
                <tr id='fila$i' name='fila$i'  style='$colorFila' title='$aux_title' data-toggle='$aux_data_toggle'>
                    <td id='id$i' name='id$i'>$data->id</td>
                    <td id='fechahora$i' name='fechahora$i'>$data->fechahora</td>
                    <td id='contacto$i' name='contacto$i'>$rut</td>
                    <td id='contacto$i' name='contacto$i'>$data->razonsocial</td>
                    <td>
                        <a id='btnInicioServ1' name='btnInicioServ1' class='$aux_color btn-sm' data-toggle='tooltip' data-original-title='$aux_mensaje'>
                            <span id='glypcnbtnInicioServ1' class='$aux_icono' style='bottom: 0px;top: 2px;'></span>
                        </a>
                    </td>
                    <td class='textright' align='right' id='contacto$i' name='contacto$i'>".number_format($data->total, 2, ",", ".") ."</td>
                    <td>
                        <a href='" . route('exportPdf_cotizacion', ['id' => $data->id]) . "' class='btn-accion-tabla tooltipsC' title='PDF' target='_blank'>
                            <i class='fa fa-fw fa-file-pdf-o'></i>                                    
                        </a>
                    </td>

                </tr>";

                //dd($data->contacto);
            }
			$respuesta['tabla'] .= "</tbody>
			</table>";
            //dd($respuesta);
            //dd(compact('datas'));
            //dd($clientedirecs->get());
            //dd($datas->get());
            /*$cotizacion = Cotizacion::where('fechahora', '>=', $aux_fechad)
                                    ->where('fechahora', '<=', $aux_fechah);*/
            //echo json_encode($respuesta);
            return $respuesta;
            //return response()->json($respuesta);
        }
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
        //$cotizaciones = Cotizacion::orderBy('id')->get();
        if($request->ajax()){
            $cotizaciones = consulta($request->fechad,$request->fechah);
        }
        $cotizaciones = consulta($request->fechad,$request->fechah);
        $aux_fdesde= $request->fechad;
        $aux_fhasta= $request->fechah;

        //$cotizaciones = consulta('','');
        $empresa = Empresa::orderBy('id')->get();
        $usuario = Usuario::findOrFail(auth()->id());
        //return view('cotizacionconsulta.listado', compact('cotizaciones','empresa','usuario','aux_fdesde','aux_fhasta'));
        
        $pdf = PDF::loadView('cotizacionconsulta.listado', compact('cotizaciones','empresa','usuario','aux_fdesde','aux_fhasta'));
        //return $pdf->download('cotizacion.pdf');
        return $pdf->stream();
        
    }
}

function consulta($fdesde,$fhasta){
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
    if(empty($fdesde) or empty($fhasta)){
        $aux_condFecha = " true";
    }else{
        $fecha = date_create_from_format('d/m/Y', $fdesde);
        $fechad = date_format($fecha, 'Y-m-d')." 00:00:00";
        $fecha = date_create_from_format('d/m/Y', $fhasta);
        $fechah = date_format($fecha, 'Y-m-d')." 23:59:59";
        $aux_condFecha = "cotizacion.fechahora>='$fechad' and cotizacion.fechahora<='$fechah'";
    }

    $sql = "SELECT cotizacion.*,if(isnull(cliente.razonsocial),clientetemp.rut,cliente.rut) as rut,
            if(isnull(cliente.razonsocial),clientetemp.razonsocial,cliente.razonsocial) as razonsocial,
            aprobstatus,aprobobs,aprobstatus,cliente_id,clientetemp_id,
            (SELECT COUNT(*) 
            FROM cotizaciondetalle 
            WHERE cotizaciondetalle.cotizacion_id=cotizacion.id and 
            cotizaciondetalle.precioxkilo < cotizaciondetalle.precioxkiloreal) AS contador
        FROM cotizacion left join cliente
        on cotizacion.cliente_id=cliente.id
        left join clientetemp
        on cotizacion.clientetemp_id=clientetemp.id
        where " . $aux_condFecha . " and " . $vendedorcond .
        " and cotizacion.deleted_at is null;";
    //dd("$sql");
    $datas = DB::select($sql);
    return $datas;
}
