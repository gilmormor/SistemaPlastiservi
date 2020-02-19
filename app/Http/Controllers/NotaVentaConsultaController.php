<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\ClienteSucursal;
use App\Models\ClienteVendedor;
use App\Models\Empresa;
use App\Models\Seguridad\Usuario;
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
        can('consulta-cotizacion');
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
        }else{
            $clientevendedorArray = ClienteVendedor::pluck('cliente_id')->toArray();
        }
        $sucurArray = $user->sucursales->pluck('id')->toArray();
        //* Filtro solos los clientes que esten asignados a la sucursal y asignado al vendedor logueado*/
        $clientes = Cliente::select(['cliente.id','cliente.rut','cliente.razonsocial','cliente.direccion','cliente.telefono'])
        ->whereIn('cliente.id' , ClienteSucursal::select(['cliente_sucursal.cliente_id'])
                                ->whereIn('cliente_sucursal.sucursal_id', $sucurArray)
        ->pluck('cliente_sucursal.cliente_id')->toArray())
        ->whereIn('cliente.id',$clientevendedorArray)
        ->get();

        return view('notaventaconsulta.index', compact('clientes'));

    }

    public function reporte(Request $request){
        $respuesta = array();
		$respuesta['exito'] = false;
		$respuesta['mensaje'] = "Código no Existe";
		$respuesta['tabla'] = "";

        if($request->ajax()){
            $datas = consulta($request->fechad,$request->fechah,$request->rut);

            $respuesta['tabla'] .= "<table id='tablacotizacion' name='tablacotizacion' class='table display AllDataTables table-hover table-condensed tablascons'>
			<thead>
				<tr>
					<th>ID</th>
					<th>Fecha</th>
					<th>RUT</th>
                    <th>Razón Social</th>
                    <th style='text-align:right'>PVC Kg</th>
                    <th style='text-align:right'>PVC $</th>
                    <th style='text-align:right'>Cañeria Kg</th>
                    <th style='text-align:right'>Cañeria $</th>
                    <th style='text-align:right'>Total Kg</th>
                    <th style='text-align:right'>Total $</th>
                    <th>PDF</th>
				</tr>
			</thead>
            <tbody>";
            $i = 0;
            $aux_totalKG = 0;
            $aux_totalps = 0;
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
                $respuesta['tabla'] .= "
                <tr id='fila$i' name='fila$i' style='$colorFila' title='$aux_title' data-toggle='$aux_data_toggle' class='btn-accion-tabla tooltipsC'>
                    <td id='id$i' name='id$i'>$data->id</td>
                    <td id='fechahora$i' name='fechahora$i'>$data->fechahora</td>
                    <td id='rut$i' name='rut$i'>$rut</td>
                    <td id='razonsocial$i' name='razonsocial$i'>$data->razonsocial</td>
                    <td id='pvckg$i' name='pvckg$i' style='text-align:right'>".number_format($data->pvckg, 2, ",", ".") ."</td>
                    <td id='pvcpesos$i' name='pvcpesos$i' style='text-align:right'>".number_format($data->pvcpesos, 2, ",", ".") ."</td>
                    <td id='cankg$i' name='cankg$i' style='text-align:right'>".number_format($data->cankg, 2, ",", ".") ."</td>
                    <td id='canpesos$i' name='canpesos$i' style='text-align:right'>".number_format($data->canpesos, 2, ",", ".") ."</td>
                    <td id='totalkilos$i' name='totalkilos$i' style='text-align:right'>".number_format($data->totalkilos, 2, ",", ".") ."</td>
                    <td id='totalps$i' name='totalps$i' style='text-align:right'>".number_format($data->totalps, 2, ",", ".") ."</td>
                    <td>
                        <a href='" . route('exportPdf_notaventa', ['id' => $data->id]) . "' class='btn-accion-tabla tooltipsC' title='PDF' target='_blank'>
                            <i class='fa fa-fw fa-file-pdf-o'></i>                                    
                        </a>
                    </td>
                </tr>";
                $aux_totalKG += $data->totalkilos;
                $aux_totalps += $data->totalps;
    
                //dd($data->contacto);
            }
            $respuesta['tabla'] .= "
            </tbody>
            <tfoot>
                <tr>
                    <th colspan='8' style='text-align:left'>TOTAL</th>
                    <th style='text-align:right'>". number_format($aux_totalKG, 2, ",", ".") ."</th>
                    <th style='text-align:right'>". number_format($aux_totalps, 2, ",", ".") ."</th>
                    <th style='text-align:right'></th>
                </tr>
            </tfoot>

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
        //$cotizaciones = Cotizacion::orderBy('id')->get();
        $rut=str_replace("-","",$request->rut);
        $rut=str_replace(".","",$rut);
        //dd($rut);
        if($request->ajax()){
            $notaventas = consulta($request->fechad,$request->fechah,$rut);
        }
        //dd($request);
        $notaventas = consulta($request->fechad,$request->fechah,$rut);
        $aux_fdesde= $request->fechad;
        $aux_fhasta= $request->fechah;

        //$cotizaciones = consulta('','');
        $empresa = Empresa::orderBy('id')->get();
        $usuario = Usuario::findOrFail(auth()->id());
        if($notaventas){
            //return view('notaventaconsulta.listado', compact('notaventas','empresa','usuario','aux_fdesde','aux_fhasta'));
        
            $pdf = PDF::loadView('notaventaconsulta.listado', compact('notaventas','empresa','usuario','aux_fdesde','aux_fhasta'));
            //return $pdf->download('cotizacion.pdf');
            return $pdf->stream();
        }else{
            dd('Ningún dato disponible en esta consulta.');
        }
    }

    
}

function consulta($fdesde,$fhasta,$rut){
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
        $aux_condFecha = "notaventa.fechahora>='$fechad' and notaventa.fechahora<='$fechah'";
    }
    if(empty($rut)){
        $aux_condrut = " true";
    }else{
        $aux_condrut = "cliente.rut='$rut'";
    }
    $sql = "SELECT notaventadetalle.notaventa_id as id,notaventa.fechahora,notaventa.cliente_id,notaventa.comuna_id,notaventa.comunaentrega_id,
            notaventa.oc_id,notaventa.anulada,cliente.rut,cliente.razonsocial,
            sum(notaventadetalle.cant) AS cant,sum(notaventadetalle.precioxkilo) AS precioxkilo,
            sum(notaventadetalle.totalkilos) AS totalkilos,sum(notaventadetalle.subtotal) AS subtotal,
            sum(if(areaproduccion.id=1,notaventadetalle.totalkilos,0)) AS pvckg,
            sum(if(areaproduccion.id=2,notaventadetalle.totalkilos,0)) AS cankg,
            sum(if(areaproduccion.id=1,notaventadetalle.subtotal,0)) AS pvcpesos,
            sum(if(areaproduccion.id=2,notaventadetalle.subtotal,0)) AS canpesos,
            sum(notaventadetalle.totalkilos) AS totalkilos,
            sum(notaventadetalle.subtotal) AS totalps
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
            WHERE " . $vendedorcond .
            " and " . $aux_condFecha .
            " and " . $aux_condrut .
            " and notaventa.deleted_at is null
            GROUP BY notaventadetalle.notaventa_id,notaventa.fechahora,notaventa.cliente_id,notaventa.comuna_id,notaventa.comunaentrega_id,
            notaventa.oc_id,notaventa.anulada,cliente.rut,cliente.razonsocial;";
    //dd("$sql");
    $datas = DB::select($sql);
    return $datas;
}
