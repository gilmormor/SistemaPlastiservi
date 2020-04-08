<?php

namespace App\Http\Controllers;

use App\Models\CategoriaProd;
use App\Models\Cliente;
use App\Models\ClienteSucursal;
use App\Models\ClienteVendedor;
use App\Models\Empresa;
use App\Models\Giro;
use App\Models\NotaVentaDetalle;
use App\Models\Seguridad\Usuario;
use App\Models\Vendedor;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Support\Facades\DB;

class ProducxNotaVentaController extends Controller
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
        $giros = Giro::orderBy('id')->get();
        $categoriaprods = CategoriaProd::orderBy('id')->get();
        $vendedores = Vendedor::orderBy('id')->where('sta_activo',1)->get();

        return view('prodxnotaventa.index', compact('clientes','giros','categoriaprods','vendedores','vendedores1'));
    }

    
    public function reporte(Request $request){
        $respuesta = array();
		$respuesta['exito'] = true;
		$respuesta['mensaje'] = "Código encontrado";
		$respuesta['tabla'] = "";

        if($request->ajax()){
            $datas = consulta($request->fechad,$request->fechah,$request->categoriaprod_id,$request->giro_id,$request->rut,$request->vendedor_id);

            $respuesta['tabla'] .= "<table id='tablacotizacion' name='tablacotizacion' class='table display AllDataTables table-hover table-condensed tablascons'>
			<thead>
				<tr>
					<th>Descripción</th>
					<th>Diametro</th>
					<th>Long</th>
                    <th>Clase</th>
                    <th style='text-align:right'>Peso x Unidad</th>
                    <th style='text-align:right'>TU</th>
                    <th style='text-align:right'>Unid</th>
                    <th style='text-align:right'>Pesos</th>
                    <th style='text-align:right'>KG</th>
                    <th style='text-align:right'>Precio Prom Unit</th>
                    <th style='text-align:right'>Precio Kilo</th>
				</tr>
			</thead>
            <tbody>";
            $i = 0;
            $aux_totalkilos = 0;
            $totalsumsubtotal = 0;
            $totalsumcant = 0;
            foreach ($datas as $data) {
                $colorFila = 'background-color: #87CEEB;';
                $aux_totalkilos = $aux_totalkilos + $data->sumtotalkilos;
                $totalsumsubtotal += $data->sumsubtotal;
                $totalsumcant += $data->sumcant;
    
                $respuesta['tabla'] .= "
                <tr id='fila$i' name='fila$i' class='btn-accion-tabla tooltipsC'>
                    <td id='nombre$i' name='nombre$i'>$data->nombre</td>
                    <td id='diamextmm$i' name='diamextmm$i'>$data->diamextmm</td>
                    <td id='long$i' name='long$i'>$data->long</td>
                    <td id='cla_nombre$i' name='cla_nombre$i'>$data->cla_nombre</td>
                    <td id='peso$i' name='peso$i' style='text-align:right'>".number_format($data->peso, 2, ",", ".") ."</td>
                    <td id='tipounion$i' name='tipounion$i' style='text-align:right'>$data->tipounion</td>
                    <td id='sumcant$i' name='sumcant$i' style='text-align:right'>".number_format($data->sumcant, 2, ",", ".") ."</td>
                    <td id='subtotal$i' name='subtotal$i' style='text-align:right'>".number_format($data->sumsubtotal, 2, ",", ".") ."</td>
                    <td id='sumtotalkilos$i' name='sumtotalkilos$i' style='text-align:right'>".number_format($data->sumtotalkilos, 2, ",", ".") ."</td>
                    <td id='prompreciounit$i' name='prompreciounit$i' style='text-align:right'>".number_format($data->prompreciounit, 2, ",", ".") ."</td>
                    <td id='promprecioxkilo$i' name='promprecioxkilo$i' style='text-align:right'>".number_format($data->promprecioxkilo, 2, ",", ".") ."</td>
                </tr>";

                //dd($data->contacto);
            }
            if($totalsumcant==0){
                $totalsumcant = 1;
            }
            if($aux_totalkilos==0){
                $aux_totalkilos = 1;
            }
            $respuesta['tabla'] .= "
                </tbody>
                    <tfoot>
                        <tr>
                            <th>Total</th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th style='text-align:right'></th>
                            <th style='text-align:right'></th>
                            <th style='text-align:right'>". number_format($totalsumcant, 2, ",", ".") ."</th>
                            <th style='text-align:right'>". number_format($totalsumsubtotal, 2, ",", ".") ."</th>
                            <th style='text-align:right'>". number_format($aux_totalkilos, 2, ",", ".") ."</th>
                            <th style='text-align:right'>". number_format($totalsumsubtotal/$totalsumcant, 2, ",", ".") ."</th>
                            <th style='text-align:right'>". number_format($totalsumsubtotal/$aux_totalkilos, 2, ",", ".") ."</th>
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
        //dd($rut);
        $rut=str_replace("-","",$request->rut);
        $rut=str_replace(".","",$rut);
        if($request->ajax()){
            $notaventas = consulta($request->fechad,$request->fechah,$request->categoriaprod_id,$request->giro_id,$rut,$request->vendedor_id);
        }
        //dd($request);
        $notaventas = consulta($request->fechad,$request->fechah,$request->categoriaprod_id,$request->giro_id,$rut,$request->vendedor_id);
        $aux_fdesde= $request->fechad;
        $aux_fhasta= $request->fechah;

        //$cotizaciones = consulta('','');
        $empresa = Empresa::orderBy('id')->get();
        $usuario = Usuario::findOrFail(auth()->id());
        if($notaventas){
            //return view('prodxnotaventa.listado', compact('notaventas','empresa','usuario','aux_fdesde','aux_fhasta'));
        
            $pdf = PDF::loadView('prodxnotaventa.listado', compact('notaventas','empresa','usuario','aux_fdesde','aux_fhasta'));
            //return $pdf->download('cotizacion.pdf');
            return $pdf->stream();
        }else{
            dd('Ningún dato disponible en esta consulta.');
        }
    }
}


function consulta($fdesde,$fhasta,$categoriaprod_id,$giro_id,$rut,$vendedor_id1){
    if(empty($vendedor_id1)){
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
        $vendedorcond = "notaventa.vendedor_id='$vendedor_id1'";
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
    if(empty($categoriaprod_id)){
        $aux_condcategoriaprod_id = " true";
    }else{
        $aux_condcategoriaprod_id = "categoriaprod.id='$categoriaprod_id'";
    }
    if(empty($giro_id)){
        $aux_condgiro_id = " true";
    }else{
        $aux_condgiro_id = "cliente.giro_id='$giro_id'";
    }
    if(empty($rut)){
        $aux_condrut = " true";
    }else{
        $aux_condrut = "cliente.rut='$rut'";
    }


    $sql = "SELECT notaventadetalle.producto_id,categoriaprod.nombre,
    grupoprod.gru_nombre,
    producto.diamextmm,producto.long,claseprod.cla_nombre,
    producto.peso,producto.tipounion,
    sum(notaventadetalle.cant) AS sumcant,
    sum(notaventadetalle.totalkilos) AS sumtotalkilos,
    AVG(notaventadetalle.preciounit) AS prompreciounit,
    AVG(notaventadetalle.precioxkilo) AS promprecioxkilo,
    sum(notaventadetalle.subtotal) AS sumsubtotal
    FROM notaventadetalle INNER JOIN producto
    on notaventadetalle.producto_id=producto.id
    INNER JOIN notaventa
    ON notaventa.id=notaventadetalle.notaventa_id
    INNER JOIN cliente
    ON cliente.id=notaventa.cliente_id
    INNER JOIN grupoprod
    ON grupoprod.id = producto.grupoprod_id
    INNER JOIN claseprod
    ON claseprod.id=producto.claseprod_id
    INNER JOIN categoriaprod
    ON categoriaprod.id=producto.categoriaprod_id
    WHERE " . $vendedorcond .
    " and " . $aux_condFecha .
    " and " . $aux_condcategoriaprod_id .
    " and " . $aux_condgiro_id .
    " and " . $aux_condrut .
    " and notaventadetalle.deleted_at is null
    GROUP BY notaventadetalle.producto_id,categoriaprod.nombre,
    grupoprod.gru_nombre,producto.diamextmm,claseprod.cla_nombre,
    producto.long,producto.peso,producto.tipounion;";

    //" and " . $aux_condrut .

    $datas = DB::select($sql);
    return $datas;
}