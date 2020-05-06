<?php

namespace App\Http\Controllers;

use App\Models\AreaProduccion;
use App\Models\CategoriaProd;
use App\Models\Cliente;
use App\Models\ClienteSucursal;
use App\Models\ClienteVendedor;
use App\Models\Empresa;
use App\Models\Giro;
use App\Models\Producto;
use App\Models\Seguridad\Usuario;
use App\Models\Vendedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade as PDF;


class NVIndicadorxVendController extends Controller
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
        $categoriaprods = CategoriaProd::join('categoriaprodsuc', function ($join) {
            $user = Usuario::findOrFail(auth()->id());
            $sucurArray = $user->sucursales->pluck('id')->toArray();
            $join->on('categoriaprod.id', '=', 'categoriaprodsuc.categoriaprod_id')
            ->whereIn('categoriaprodsuc.sucursal_id', $sucurArray);
                    })
            ->select([
                'categoriaprod.id',
                'categoriaprod.nombre',
                'categoriaprod.descripcion',
                'categoriaprod.precio',
                'categoriaprod.areaproduccion_id',
                'categoriaprod.sta_precioxkilo',
                'categoriaprod.unidadmedida_id',
                'categoriaprod.unidadmedidafact_id'
            ])
            ->get();

        $vendedores = Vendedor::orderBy('id')->where('sta_activo',1)->get();
        $areaproduccions = AreaProduccion::orderBy('id')->get();

        return view('nvindicadorxvendedor.index', compact('clientes','giros','categoriaprods','vendedores','vendedores1','areaproduccions'));
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
					<th>Productos</th>";

            foreach($datas['vendedores'] as $vendedor){
                $nombreven = $vendedor->nombre;
                $respuesta['tabla'] .= "
                        <th style='text-align:right' class='tooltipsC' title='$nombreven'>$nombreven</th>";
            }
            $respuesta['tabla'] .= "
                    <th style='text-align:right' class='tooltipsC' title='Total'>TOTAL</th>
                </tr>
            </thead>
            <tbody>";
            $i = 0;
            $totalgeneral = 0;
            foreach($datas['productos'] as $producto){
                $respuesta['tabla'] .= "
                    <tr id='fila$i' name='fila$i' class='btn-accion-tabla tooltipsC'>
                        <td id='producto$i' name='producto$i'>$producto->gru_nombre</td>";
                foreach($datas['vendedores'] as $vendedor){
                    $aux_encontrado = false;
                    foreach($datas['totales'] as $total){
                        if($total->grupoprod_id == $producto->id and $total->persona_id==$vendedor->id){
                            $aux_encontrado = true;
                            $respuesta['tabla'] .= "<td id='vendedor$i' name='vendedor$i' style='text-align:right'>" . number_format($total->totalkilos, 2, ",", ".") . "</td>";
                        } 
                    }
                    if($aux_encontrado==false){
                        $respuesta['tabla'] .= "<td id='vendedor$i' name='vendedor$i' style='text-align:right'>0.00</td>";
                    }
                }
                
                $respuesta['tabla'] .= "
                    <td id='totalkilos$i' name='totalkilos$i' style='text-align:right'>" . number_format($producto->totalkilos, 2, ",", ".") . "</td>
                    </tr>";
                $i++;
                $totalgeneral += $producto->totalkilos;

            }
            $respuesta['tabla'] .= "
            </tbody>
                <tfoot>
                    <tr>
                        <th>Total</th>";

            foreach($datas['vendedores'] as $vendedor){
                $respuesta['tabla'] .= "
                    <th style='text-align:right'>". number_format($vendedor->totalkilos, 2, ",", ".") ."</th>";
            }

            $respuesta['tabla'] .= "
                        <th style='text-align:right'>". number_format($totalgeneral, 2, ",", ".") ."</th>
                    </tr>
                </tfoot>
            </table>";

            return $respuesta;

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
                $aum_uniMed = '';
                if ($producto->categoriaprod->unidadmedida_id==3){
                    $aum_uniMed = $producto->diamextpg;
                }
                else{
                    $aum_uniMed = $producto->diamextmm . 'mm';
                }
                $porcentajeKg = ($data->sumtotalkilos * 100) / $aux_totalkilosP;
                $aux_totalporcenkg += $porcentajeKg;
                $respuesta['tabla'] .= "
                <tr id='fila$i' name='fila$i' class='btn-accion-tabla tooltipsC'>
                    <td id='nombre$i' name='nombre$i'>$data->nombre</td>
                    <td id='diamextmm$i' name='diamextmm$i'>$aum_uniMed</td>
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

    $respuesta = array();
    $respuesta['exito'] = true;
    $respuesta['mensaje'] = "Código encontrado";
    $respuesta['productos'] = "";
    $respuesta['vendedores'] = "";

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

    if(empty($request->areaproduccion_id)){
        $aux_condareaproduccion_id = " true";
    }else{
        $aux_condareaproduccion_id = "categoriaprod.areaproduccion_id='$request->areaproduccion_id'";
    }


    $sql = "SELECT grupoprod.id,grupoprod.gru_nombre,
    sum(notaventadetalle.totalkilos) AS totalkilos
    FROM notaventadetalle INNER JOIN producto
    ON notaventadetalle.producto_id=producto.id
    INNER JOIN categoriaprod
    ON producto.categoriaprod_id=categoriaprod.id
    INNER JOIN grupoprod
    ON producto.grupoprod_id=grupoprod.id
    INNER JOIN notaventa 
    ON notaventadetalle.notaventa_id=notaventa.id
    INNER JOIN cliente
    ON notaventa.cliente_id=cliente.id
    WHERE $aux_condFecha
    and $vendedorcond
    and $aux_condcategoriaprod_id
    and $aux_condgiro_id
    and $aux_condareaproduccion_id
    and notaventadetalle.deleted_at is null
    GROUP BY grupoprod.id,grupoprod.gru_nombre;";
    //dd($sql);
    //" and " . $aux_condrut .

    $datas = DB::select($sql);
    $respuesta['productos'] = $datas;

    $sql = "SELECT persona.id,persona.nombre,
    sum(notaventadetalle.totalkilos) AS totalkilos
    FROM notaventadetalle INNER JOIN producto
    ON notaventadetalle.producto_id=producto.id
    INNER JOIN categoriaprod
    ON producto.categoriaprod_id=categoriaprod.id
    INNER JOIN grupoprod
    ON producto.grupoprod_id=grupoprod.id
    INNER JOIN notaventa 
    ON notaventadetalle.notaventa_id=notaventa.id
    INNER JOIN vendedor 
    ON notaventa.vendedor_id=vendedor.id
    INNER JOIN persona
    ON vendedor.persona_id=persona.id
    WHERE $aux_condFecha
    and $vendedorcond
    and $aux_condcategoriaprod_id
    and $aux_condgiro_id
    and $aux_condareaproduccion_id
    and notaventadetalle.deleted_at is null
    GROUP BY persona.id,persona.nombre;";

    $datas = DB::select($sql);
    $respuesta['vendedores'] = $datas;


    $sql = "SELECT grupoprod.id as grupoprod_id,grupoprod.gru_nombre,persona.id as persona_id,persona.nombre,
    sum(notaventadetalle.totalkilos) AS totalkilos
    FROM notaventadetalle INNER JOIN producto
    ON notaventadetalle.producto_id=producto.id
    INNER JOIN categoriaprod
    ON producto.categoriaprod_id=categoriaprod.id
    INNER JOIN grupoprod
    ON producto.grupoprod_id=grupoprod.id
    INNER JOIN notaventa 
    ON notaventadetalle.notaventa_id=notaventa.id
    INNER JOIN vendedor 
    ON notaventa.vendedor_id=vendedor.id
    INNER JOIN persona
    ON vendedor.persona_id=persona.id
    WHERE $aux_condFecha
    and $vendedorcond
    and $aux_condcategoriaprod_id
    and $aux_condgiro_id
    and $aux_condareaproduccion_id
    and notaventadetalle.deleted_at is null
    GROUP BY grupoprod.id,grupoprod.gru_nombre,persona.id,persona.nombre;";

    $datas = DB::select($sql);
    $respuesta['totales'] = $datas;
    //dd($respuesta['totales']);
    return $respuesta;
}