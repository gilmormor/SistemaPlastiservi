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
        $fechaServ = ['fecha1erDiaMes' => date("01/m/Y"),
                    'fechaAct' => date("d/m/Y")
                    ];
        return view('nvindicadorxvendedor.index', compact('clientes','giros','categoriaprods','vendedores','vendedores1','areaproduccions','fechaServ'));
    }

    public function reporte(Request $request){
        //dd($request);
        $respuesta = array();
		$respuesta['exito'] = true;
		$respuesta['mensaje'] = "Código encontrado";
		$respuesta['tabla'] = "";

        if($request->ajax()){
            if($request->idcons == "1"){
                $datas = consulta($request);
            }
            if($request->idcons == "2" or $request->idcons == "3"){
                $datas = consultaODcerrada($request);
            }
            //dd($respuesta['totalkilos']);

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
                        <th>TOTAL KG</th>";

            foreach($datas['vendedores'] as $vendedor){
                $respuesta['tabla'] .= "
                    <th style='text-align:right'>". number_format($vendedor->totalkilos, 2, ",", ".") ."</th>";
            }

            $respuesta['tabla'] .= "
                        <th style='text-align:right'>". number_format($totalgeneral, 2, ",", ".") ."</th>
                    </tr>
                </tfoot>
            </table>";
            $respuesta['nombre'] = array_column($datas['vendedores'], 'nombre');
            $respuesta['totalkilos'] = array_column($datas['vendedores'], 'totalkilos');
            $i = 0;
            foreach($respuesta['totalkilos'] as &$kilos){
                $kilos = round($kilos,2);
                $kilos1 = round(($kilos / $totalgeneral) * 100,2);
                $respuesta['nombre'][$i] .= " " . number_format($kilos1, 2, ",", ".") . "%";
                $i++;
            }

            return $respuesta;
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
    $array = array('apellido', 'email', 'teléfono');
    $separado_por_comas = implode(",", $array);
    //dd(json_decode($request->vendedor_id));
    $aux_vendedor = implode ( ',' , json_decode($request->vendedor_id));
    //dd($aux_vendedor);
    $respuesta = array();
    $respuesta['exito'] = true;
    $respuesta['mensaje'] = "Código encontrado";
    $respuesta['productos'] = "";
    $respuesta['vendedores'] = "";

    if(empty($aux_vendedor )){
            $vendedorcond = " true ";
    }else{
        $vendedorcond = " notaventa.vendedor_id in ($aux_vendedor) ";
    }
    //dd($vendedorcond);
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

    switch ($request->statusact_id) {
        case 1:
            $aux_condstatusact_id = "notaventa.id not in (select notaventa_id from notaventacerrada where isnull(notaventacerrada.deleted_at))";
            break;
        case 2:
            $aux_condstatusact_id = "notaventa.id in (select notaventa_id from notaventacerrada where isnull(notaventacerrada.deleted_at))";
            break;
        case 3:
            $aux_condstatusact_id = " true";
            break;
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
    and notaventa.anulada is null
    and $aux_condstatusact_id
    and notaventadetalle.deleted_at is null and notaventa.deleted_at is null
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
    and notaventa.anulada is null
    and $aux_condstatusact_id
    and notaventadetalle.deleted_at is null and notaventa.deleted_at is null
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
    and notaventa.anulada is null
    and $aux_condstatusact_id
    and notaventadetalle.deleted_at is null and notaventa.deleted_at is null
    GROUP BY grupoprod.id,grupoprod.gru_nombre,persona.id,persona.nombre;";

    $datas = DB::select($sql);
    $respuesta['totales'] = $datas;
    //dd($respuesta['totales']);
    return $respuesta;
}

function consultaODcerrada($request){
    $array = array('apellido', 'email', 'teléfono');
    $separado_por_comas = implode(",", $array);
    //dd(json_decode($request->vendedor_id));
    $aux_vendedor = implode ( ',' , json_decode($request->vendedor_id));
    //dd($aux_vendedor);
    $respuesta = array();
    $respuesta['exito'] = true;
    $respuesta['mensaje'] = "Código encontrado";
    $respuesta['productos'] = "";
    $respuesta['vendedores'] = "";

    if(empty($aux_vendedor )){
            $vendedorcond = " true ";
    }else{
        $vendedorcond = " notaventa.vendedor_id in ($aux_vendedor) ";
    }
    //dd($vendedorcond);
    if(empty($request->fechad) or empty($request->fechah)){
        $aux_condFecha = " true";
    }else{
        if($request->idcons == "2"){
            $fecha = date_create_from_format('d/m/Y', $request->fechad);
            $fechad = date_format($fecha, 'Y-m-d'); //." 00:00:00";
            $fecha = date_create_from_format('d/m/Y', $request->fechah);
            $fechah = date_format($fecha, 'Y-m-d'); //." 23:59:59";
            $aux_condFecha = "despachoord.fechafactura>='$fechad' and despachoord.fechafactura<='$fechah'";    
        }else{
            $fecha = date_create_from_format('d/m/Y', $request->fechad);
            $fechad = date_format($fecha, 'Y-m-d')." 00:00:00";
            $fecha = date_create_from_format('d/m/Y', $request->fechah);
            $fechah = date_format($fecha, 'Y-m-d')." 23:59:59";
            $aux_condFecha = "notaventa.fechahora>='$fechad' and notaventa.fechahora<='$fechah'";    
        }
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

    switch ($request->statusact_id) {
        case 1:
            $aux_condstatusact_id = "notaventa.id not in (select notaventa_id from notaventacerrada where isnull(notaventacerrada.deleted_at))";
            break;
        case 2:
            $aux_condstatusact_id = "notaventa.id in (select notaventa_id from notaventacerrada where isnull(notaventacerrada.deleted_at))";
            break;
        case 3:
            $aux_condstatusact_id = " true";
            break;
    }


    $sql = "SELECT grupoprod.id,grupoprod.gru_nombre,
    sum((notaventadetalle.totalkilos/notaventadetalle.cant) * cantdesp) AS totalkilos
    FROM despachoorddet INNER JOIN notaventadetalle 
    ON despachoorddet.notaventadetalle_id=notaventadetalle.id
    INNER JOIN despachoord
    ON despachoorddet.despachoord_id=despachoord.id
    INNER JOIN producto
    ON notaventadetalle.producto_id=producto.id
    INNER JOIN categoriaprod
    ON producto.categoriaprod_id=categoriaprod.id
    INNER JOIN grupoprod
    ON producto.grupoprod_id=grupoprod.id
    INNER JOIN notaventa 
    ON notaventadetalle.notaventa_id=notaventa.id
    INNER JOIN cliente
    ON notaventa.cliente_id=cliente.id
    WHERE (despachoord.guiadespacho IS NOT NULL AND despachoord.numfactura IS NOT NULL)
    and $aux_condFecha
    and $vendedorcond
    and $aux_condcategoriaprod_id
    and $aux_condgiro_id
    and $aux_condareaproduccion_id
    and notaventa.anulada is null
    and $aux_condstatusact_id
    and despachoord.deleted_at is null 
    and notaventadetalle.deleted_at is null 
    and notaventa.deleted_at is null
    GROUP BY grupoprod.id,grupoprod.gru_nombre;";
    //dd($sql);
    //" and " . $aux_condrut .

    $datas = DB::select($sql);
    $respuesta['productos'] = $datas;

    $sql = "SELECT persona.id,persona.nombre,
    sum((notaventadetalle.totalkilos/notaventadetalle.cant) * cantdesp) AS totalkilos
    FROM despachoorddet INNER JOIN notaventadetalle 
    ON despachoorddet.notaventadetalle_id=notaventadetalle.id
    INNER JOIN despachoord
    ON despachoorddet.despachoord_id=despachoord.id
    INNER JOIN producto
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
    WHERE (despachoord.guiadespacho IS NOT NULL AND despachoord.numfactura IS NOT NULL)
    and $aux_condFecha
    and $vendedorcond
    and $aux_condcategoriaprod_id
    and $aux_condgiro_id
    and $aux_condareaproduccion_id
    and notaventa.anulada is null
    and $aux_condstatusact_id
    and despachoord.deleted_at is null 
    and notaventadetalle.deleted_at is null 
    and notaventa.deleted_at is null
    GROUP BY persona.id,persona.nombre;";

    $datas = DB::select($sql);
    $respuesta['vendedores'] = $datas;


    $sql = "SELECT grupoprod.id as grupoprod_id,grupoprod.gru_nombre,persona.id as persona_id,persona.nombre,
    sum((notaventadetalle.totalkilos/notaventadetalle.cant) * cantdesp) AS totalkilos
    FROM despachoorddet INNER JOIN notaventadetalle 
    ON despachoorddet.notaventadetalle_id=notaventadetalle.id
    INNER JOIN despachoord
    ON despachoorddet.despachoord_id=despachoord.id
    INNER JOIN producto
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
    WHERE (despachoord.guiadespacho IS NOT NULL AND despachoord.numfactura IS NOT NULL)
    and $aux_condFecha
    and $vendedorcond
    and $aux_condcategoriaprod_id
    and $aux_condgiro_id
    and $aux_condareaproduccion_id
    and notaventa.anulada is null
    and $aux_condstatusact_id
    and despachoord.deleted_at is null 
    and notaventadetalle.deleted_at is null 
    and notaventa.deleted_at is null
    GROUP BY grupoprod.id,grupoprod.gru_nombre,persona.id,persona.nombre;";

    $datas = DB::select($sql);
    $respuesta['totales'] = $datas;
    //dd($respuesta['totales']);
    return $respuesta;
}