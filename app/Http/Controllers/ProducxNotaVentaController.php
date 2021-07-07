<?php

namespace App\Http\Controllers;

use App\Models\AreaProduccion;
use App\Models\CategoriaProd;
use App\Models\Cliente;
use App\Models\ClienteSucursal;
use App\Models\ClienteVendedor;
use App\Models\Empresa;
use App\Models\Giro;
use App\Models\NotaVentaDetalle;
use App\Models\Producto;
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
        can('reporte-productos-x-nota-venta');
        $clientesArray = Cliente::clientesxUsuario();
        $clientes = $clientesArray['clientes'];
        $vendedor_id = $clientesArray['vendedor_id'];
        $sucurArray = $clientesArray['sucurArray'];

        
        $arrayvend = Vendedor::vendedores(); //Viene del modelo vendedores
        $vendedores1 = $arrayvend['vendedores'];
        $clientevendedorArray = $arrayvend['clientevendedorArray'];
        $giros = Giro::orderBy('id')->get();
        $categoriaprods = CategoriaProd::categoriasxUsuario();

        $vendedores = Vendedor::orderBy('id')->where('sta_activo',1)->get();
        $areaproduccions = AreaProduccion::orderBy('id')->get();
        $fechaServ = [
                    'fechaAct' => date("d/m/Y"),
                    'fecha1erDiaMes' => date("01/m/Y")
                    ];
        return view('prodxnotaventa.index', compact('clientes','giros','categoriaprods','vendedores','vendedores1','areaproduccions','fechaServ'));
    }

    
    public function reporte(Request $request){
        $respuesta = array();
		$respuesta['exito'] = true;
		$respuesta['mensaje'] = "Código encontrado";
		$respuesta['tabla'] = "";

        if($request->ajax()){
            $datas = consulta($request);

            $respuesta['tabla'] .= "<table id='tablacotizacion' name='tablacotizacion' class='table display AllDataTables table-hover table-condensed tablascons' data-page-length='50'>
			<thead>
				<tr>
					<th>Descripción</th>
					<th>Diametro</th>
                    <th>Clase</th>
					<th>Long</th>
                    <th style='text-align:right' class='tooltipsC' title='Peso x Unidad'>Peso x Unidad</th>
                    <th style='text-align:right' class='tooltipsC' title='Tipo de Unión'>U</th>
                    <th style='text-align:right' class='tooltipsC' title='Precio $'>$</th>
                    <th style='text-align:right' class='tooltipsC' title='Precio promedio por Unidad'>Precio Prom Unit</th>
                    <th style='text-align:right' class='tooltipsC' title='Precio promedio por Kg'>Precio Prom Kilo</th>
                    <th style='text-align:right' class='tooltipsC' title='Unidades'>Unid</th>
                    <th style='text-align:right' class='tooltipsC' title='Total Kg'>Total KG</th>
                    <th style='text-align:right' class='tooltipsC' title='%'>%</th>
				</tr>
			</thead>
            <tbody>";
            $i = 0;
            $aux_totalkilos = 0;
            $totalsumsubtotal = 0;
            $totalsumcant = 0;
            $aux_totalkilosP = 0;
            $aux_totalporcenkg = 0;
            foreach ($datas as $data) {
                $aux_totalkilosP += $data->sumtotalkilos;
            }
            foreach ($datas as $data) {
                $colorFila = 'background-color: #87CEEB;';
                $aux_totalkilos = $aux_totalkilos + $data->sumtotalkilos;
                $totalsumsubtotal += $data->sumsubtotal;
                $totalsumcant += $data->sumcant;
                
                $producto = Producto::findOrFail($data->producto_id);
                $porcentajeKg = ($data->sumtotalkilos * 100) / $aux_totalkilosP;
                $aux_totalporcenkg += $porcentajeKg;
                $respuesta['tabla'] .= "
                <tr id='fila$i' name='fila$i' class='btn-accion-tabla tooltipsC'>
                    <td id='nombre$i' name='nombre$i'>$data->nombre</td>
                    <td id='diamextmm$i' name='diamextmm$i'>$producto->diametro</td>
                    <td id='cla_nombre$i' name='cla_nombre$i'>$data->cla_nombre</td>
                    <td id='long$i' name='long$i'>$data->long</td>
                    <td id='peso$i' name='peso$i' style='text-align:right'>".number_format($data->peso, 2, ",", ".") ."</td>
                    <td id='tipounion$i' name='tipounion$i' style='text-align:right'>$data->tipounion</td>
                    <td id='subtotal$i' name='subtotal$i' style='text-align:right'>".number_format($data->sumsubtotal, 2, ",", ".") ."</td>
                    <td id='prompreciounit$i' name='prompreciounit$i' style='text-align:right'>".number_format($data->prompreciounit, 2, ",", ".") ."</td>
                    <td id='promprecioxkilo$i' name='promprecioxkilo$i' style='text-align:right'>".number_format($data->promprecioxkilo, 2, ",", ".") ."</td>
                    <td id='sumcant$i' name='sumcant$i' style='text-align:right'>".number_format($data->sumcant, 0, ",", ".") ."</td>
                    <td id='sumtotalkilos$i' name='sumtotalkilos$i' style='text-align:right'>".number_format($data->sumtotalkilos, 2, ",", ".") ."</td>
                    <td id='aux_procent$i' name='aux_procent$i' style='text-align:right'>".number_format($porcentajeKg, 2, ",", ".") ."</td>
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
                            <th style='text-align:right'>". number_format($totalsumsubtotal, 2, ",", ".") ."</th>
                            <th style='text-align:right'>". number_format($totalsumsubtotal/$totalsumcant, 2, ",", ".") ."</th>
                            <th style='text-align:right'>". number_format($totalsumsubtotal/$aux_totalkilos, 2, ",", ".") ."</th>
                            <th style='text-align:right'>". number_format($totalsumcant, 0, ",", ".") ."</th>
                            <th style='text-align:right'>". number_format($aux_totalkilos, 2, ",", ".") ."</th>
                            <th style='text-align:right'>". number_format($aux_totalporcenkg, 2, ",", ".") ."</th>
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

    public function exportPdf(Request $request)
    {
        //$cotizaciones = Cotizacion::orderBy('id')->get();
        //dd($rut);
        $rut=str_replace("-","",$request->rut);
        $rut=str_replace(".","",$rut);
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

        if($notaventas){
            //return view('prodxnotaventa.listado', compact('notaventas','empresa','usuario','aux_fdesde','aux_fhasta','nomvendedor','nombreCategoria','nombreAreaproduccion','nombreGiro'));
        
            $pdf = PDF::loadView('prodxnotaventa.listado', compact('notaventas','empresa','usuario','aux_fdesde','aux_fhasta','nomvendedor','nombreCategoria','nombreAreaproduccion','nombreGiro'));
            //return $pdf->download('cotizacion.pdf');
            return $pdf->stream("prueba");
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
    if(empty($request->categoriaprod_id)){
        $aux_condcategoriaprod_id = " true";
    }else{
        $aux_condcategoriaprod_id = "categoriaprod.id='$request->categoriaprod_id'";
    }
    if(empty($request->giro_id)){
        $aux_condgiro_id = " true";
    }else{
        $aux_condgiro_id = "cliente.giro_id='$request->giro_id'";
    }
    if(empty($request->rut)){
        $aux_condrut = " true";
    }else{
        $aux_condrut = "cliente.rut='$request->rut'";
    }

    if(empty($request->areaproduccion_id)){
        $aux_condareaproduccion_id = " true";
    }else{
        $aux_condareaproduccion_id = "categoriaprod.areaproduccion_id='$request->areaproduccion_id'";
    }


    $sql = "SELECT notaventadetalle.producto_id,categoriaprod.nombre,
    grupoprod.gru_nombre,
    producto.diamextmm,producto.diametro,producto.long,claseprod.cla_nombre,
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
    WHERE $vendedorcond
    and $aux_condFecha
    and $aux_condcategoriaprod_id
    and $aux_condgiro_id
    and $aux_condrut
    and $aux_condareaproduccion_id
    and notaventa.id not in (select notaventa_id from notaventacerrada where isnull(notaventacerrada.deleted_at))
    and isnull(notaventa.anulada)
    and isnull(notaventa.deleted_at) and isnull(notaventadetalle.deleted_at)
    GROUP BY notaventadetalle.producto_id,categoriaprod.nombre,
    grupoprod.gru_nombre,producto.diamextmm,producto.diametro,claseprod.cla_nombre,
    producto.long,producto.peso,producto.tipounion;";

    //" and " . $aux_condrut .
    $datas = DB::select($sql);
    return $datas;
}