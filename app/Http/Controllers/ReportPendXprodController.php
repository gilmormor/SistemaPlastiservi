<?php

namespace App\Http\Controllers;

use App\Models\AreaProduccion;
use App\Models\CategoriaGrupoValMes;
use App\Models\CategoriaProd;
use App\Models\ClienteVendedor;
use App\Models\Comuna;
use App\Models\Dte;
use App\Models\Empresa;
use App\Models\Giro;
use App\Models\InvBodega;
use App\Models\InvMov;
use App\Models\NotaVentaDetalle;
use App\Models\Producto;
use App\Models\Seguridad\Usuario;
use App\Models\Sucursal;
use App\Models\TipoEntrega;
use App\Models\Vendedor;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Support\Facades\DB;

class ReportPendXprodController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('reporte-pendiente-por-producto');
        $giros = Giro::orderBy('id')->get();
        $tipoentregas = TipoEntrega::orderBy('id')->get();
        $comunas = Comuna::orderBy('id')->get();
        $fechaAct = date("d/m/Y");
        $tablashtml['comunas'] = Comuna::selectcomunas();
        $tablashtml['vendedores'] = Vendedor::vendedores();
        $tablashtml['vendedores'] = $tablashtml['vendedores']['vendedores'];
        //dd($tablashtml['vendedores']);
        foreach ($tablashtml['vendedores'] as $vendedor){
            //dd($vendedor);
        }
        $tablashtml['categoriaprod'] = CategoriaProd::categoriasxUsuario();
        $user = Usuario::findOrFail(auth()->id());
        $tablashtml['sucurArray'] = $user->sucursales->pluck('id')->toArray(); //$clientesArray['sucurArray'];
        $tablashtml['sucursales'] = Sucursal::orderBy('id')->whereIn('sucursal.id', $tablashtml['sucurArray'])->get();
        //dd(count($tablashtml['sucursales']));
        $areaproduccions = AreaProduccion::areaproduccionxusuario();
        //dd($areaproduccions);
        //$areaproduccions = AreaProduccion::orderBy('id')->get();

        $selecmultprod = 1;
        return view('reportpendxprod.index', compact('giros','areaproduccions','tipoentregas','comunas','fechaAct','tablashtml'));

    }
    public function reportpendxprodpage(Request $request){
        
        
        //can('reporte-guia_despacho');
        //dd('entro');
        //$datas = GuiaDesp::reporteguiadesp($request);
        $aux_AgruOrd = "group by notaventadetalle.producto_id
                        order by notaventadetalle.producto_id";
        $datas = consulta($request,2,1,$aux_AgruOrd);
        if($request->producto_id == null){
            $arreglo_producto_id = array_map(function ($elemento) {
                return $elemento->producto_id;
            }, $datas);
            
            //dd($arreglo_resultante);
            //$request->producto_id = (implode ( ',' , $arreglo_producto_id));
            $request->merge(['producto_id' => implode ( ',' , $arreglo_producto_id)]);
        }
        //dd($request->producto_id);
        $aux_mesanno = CategoriaGrupoValMes::mesanno(date("Ym"));
        $request->merge(['mesanno' => $aux_mesanno]);
        $request->merge(['tipobodega' => "1,2"]);
        
        //dd($request);
        $datas = InvMov::stocksql($request,"producto.id");
        //dd($datas);
        $arreglo_ProdStock = [];
        foreach ($datas as $elemento) {
            $arreglo_ProdStock[$elemento->producto_id] = $elemento;
        }
        //dd($arreglo_ProdStock);
        /*
        $producto_id_array = [];
        foreach ($datas as $key => &$data) {
            $atributoProd = Producto::atributosProducto($data->producto_id);
            $producto = Producto::findOrFail($data->producto_id);
            $request1 = new Request();
            $request1["producto_id"] = $data->producto_id;
            //dd($producto->invbodegaproductos);
            $aux_invbodega_id = "";
            foreach ($producto->invbodegaproductos as $invbodegaproducto) {
                if($invbodegaproducto->invbodega->sucursal_id == $data->sucursal_id and $invbodegaproducto->invbodega->tipo == 2){
                    $aux_invbodega_id = $invbodegaproducto->invbodega_id; 
                }
            }
            $request1["invbodega_id"] = $aux_invbodega_id;
            $request1["tipo"] = 2;
            $stockbpt = 0;
            if(isset($invbodegaproducto)){
                $existencia =  $invbodegaproducto::existencia($request1);
                $stockbpt = $existencia["stock"]["cant"];    
            }
    
            $request1 = new Request();
            $request1["producto_id"] = $data->producto_id;
            //dd($producto->invbodegaproductos);
            $aux_invbodega_id = "";
            foreach ($producto->invbodegaproductos as $invbodegaproducto) {
                if($invbodegaproducto->invbodega->sucursal_id == $data->sucursal_id and $invbodegaproducto->invbodega->tipo == 1){
                    $aux_invbodega_id = $invbodegaproducto->invbodega_id; 
                }
            }
            $request1["invbodega_id"] = $aux_invbodega_id;
            $request1["tipo"] = 1;
            $picking = 0;
            if(isset($invbodegaproducto)){
                $existencia =  $invbodegaproducto::existencia($request1);
                $picking = $existencia["stock"]["cant"];    
            }

            $producto_id_array[$data->producto_id] = [
                "nombre" => $atributoProd["nombre"],
                "at_ancho" => $atributoProd["at_ancho"],
                "at_largo" => $atributoProd["at_largo"],
                "at_espesor" => $atributoProd["at_espesor"],
                "cla_nombre" => $atributoProd["cla_nombre"],
                "tipounion" => $atributoProd["tipounion"],
                "stockbpt" => $stockbpt,
                "picking" => $picking
            ];
        }
        */
        //dd($producto_id_array);
        $aux_AgruOrd = "";
        $datas = consulta($request,2,1,$aux_AgruOrd);
        //dd($datas);
        $total_sumapicking = 0;
        $total_sumacant = 0;
        $total_sumacantdesp = 0;
        $total_cantsaldo = 0;
        $total_kgpend = 0;
        $total_totalplata = 0;
        $total_precioxkilo = 0;

        foreach ($datas as &$data){
            $id = $data->producto_id;
            $atributoProd = Producto::atributosProducto($id);
            $data->nombre = $atributoProd["nombre"];
            $data->diametro = $atributoProd["at_ancho"];
            $data->long = $atributoProd["at_largo"];
            $data->at_espesor = $atributoProd["at_espesor"];
            $data->cla_nombre = $atributoProd["cla_nombre"];
            $data->tipounion = $atributoProd["tipounion"];
            $data->stockbpt = 0;
            $data->picking = 0;
            $aux_picking = 0;
            if(isset($arreglo_ProdStock[$id])){
                $data->stockbpt = $arreglo_ProdStock[$id]->stockBodProdTerm;
                //$data->picking = $arreglo_ProdStock[$id]->stockPiking;
                if($arreglo_ProdStock[$id]->stockPiking > 0){
                    $notaventadetalle = NotaVentaDetalle::findOrFail($data->id);
                    $detalles = $notaventadetalle->despachosoldets()->get();
                    $arrayBodegasPickings = InvBodega::llenarArrayBodegasPickingSolDesp($detalles);
                    foreach ($arrayBodegasPickings as $arrayBodegasPicking) {
                        $aux_picking += $arrayBodegasPicking["stock"];
                    }
                    $data->picking = $aux_picking;
                }
            }else{
                //$data->stockbpt = 0;
                //$data->picking = 0;
            }


            //SUMA TOTAL DE SOLICITADO
            /*************************/
            /* $sql = "SELECT cantsoldesp
            FROM vista_sumsoldespdet
            WHERE notaventadetalle_id=$data->id";
            $datasuma = DB::select($sql);
            if(empty($datasuma)){
                $sumacantsoldesp= 0;
            }else{
                $sumacantsoldesp= $datasuma[0]->cantsoldesp;
            } */
            /*************************/
            //SUMA TOTAL DESPACHADO
            /*************************/
            /* $sql = "SELECT cantdesp
                FROM vista_sumorddespxnvdetid
                WHERE notaventadetalle_id=$data->id";
            $datasumadesp = DB::select($sql);
            if(empty($datasumadesp)){
                $sumacantdesp= 0;
            }else{
                $sumacantdesp= $datasumadesp[0]->cantdesp;
            } */
            if(empty($data->oc_file)){
                $aux_enlaceoc = $data->oc_id;
            }else{
                $aux_enlaceoc = "<a class='btn-accion-tabla btn-sm tooltipsC' title='Orden de Compra' onclick='verpdf2(\"$data->oc_file\",2)'>$data->oc_id</a>";
            }

            //$aux_totalkg += $data->saldokg; // ($data->totalkilos - $data->kgsoldesp);
            //$aux_totalplata += $data->saldoplata; // ($data->subtotal - $data->subtotalsoldesp);
            $aux_cantsaldo = $data->cant-$data->cantdesp;
            $fila_cantdesp = number_format($data->cantdesp, 0, ",", ".");
            if($data->cantdesp>0){
                $fila_cantdesp = "<a class='btn-accion-tabla btn-sm tooltipsC' onclick='listarorddespxNV($data->notaventa_id,$data->producto_id)' title='Ver detalle despacho' data-toggle='tooltip'>"
                                . number_format($data->cantdesp, 0, ",", ".") .
                                "</a>";
            }
            $comuna = Comuna::findOrFail($data->comunaentrega_id);
            $producto = Producto::findOrFail($data->producto_id);
            //$notaventa = NotaVenta::findOrFail($data->notaventa_id);
            //$aux_subtotalplata = ($aux_cantsaldo * $data->peso) * $data->precioxkilo;
            $aux_subtotalplata = ($aux_cantsaldo) * $data->preciounit;


            $data->sumacantdesp = $data->cantdesp;
            $data->cantsaldo = $aux_cantsaldo;
            $data->kgpend = $aux_cantsaldo * $data->peso;
            $data->subtotalplata = round($aux_subtotalplata,0);

            $total_sumapicking += $data->picking;
            $total_sumacant += $data->cant;
            $total_sumacantdesp += $data->sumacantdesp;
            $total_cantsaldo += $data->cantsaldo;
            $total_kgpend += $data->kgpend;
            $total_totalplata += $data->subtotalplata;
            $total_precioxkilo += $data->precioxkilo;
        }
        //dd($datas);
        //$datas[]prueba = [];
        if(count($datas)>0){
            $aux_contreg = count($datas)>0 ? count($datas) : 1;
            $datas[0]->datosAdicionales = [
                'total_sumapicking' => $total_sumapicking,
                'total_sumacant' => $total_sumacant,
                'total_sumacantdesp' => $total_sumacantdesp,
                'total_cantsaldo' => $total_cantsaldo,
                'total_kgpend' => round($total_kgpend),
                'total_totalplata' => $total_totalplata,
                'prom_precioxkilo' => round($total_precioxkilo / $aux_contreg,2),
                'prom_totalplata' => round($total_totalplata / $aux_contreg,0)
            ];     
        }
        //dd($datas);
        return datatables($datas)->toJson();
        //return datatables($datas)->toJson();
    }

    public function exportPdf(Request $request)
    {
        $datas = Dte::reportdtefac($request);
        //dd($datas);

        $empresa = Empresa::orderBy('id')->get();
        $usuario = Usuario::findOrFail(auth()->id());
        if(!isset($request->sucursal_id) or empty($request->sucursal_id) or ($request->sucursal_id == "")){
            $request->merge(['sucursal_nombre' => "Todos"]);
        }else{
            $sucursal = Sucursal::findOrFail($request->sucursal_id);
            $aux_sucursalNombre = $sucursal->nombre;
            $request->merge(['sucursal_nombre' => $sucursal->nombre]);
        }
        if($datas){
            
            if(env('APP_DEBUG')){
                return view('reportdtefac.listado', compact('datas','empresa','usuario','request'));
            }
            
            //return view('notaventaconsulta.listado', compact('notaventas','empresa','usuario','aux_fdesde','aux_fhasta','nomvendedor','nombreAreaproduccion','nombreGiro','nombreTipoEntrega'));
            
            //$pdf = PDF::loadView('reportinvstockvend.listado', compact('datas','empresa','usuario','request'))->setPaper('a4', 'landscape');
            $pdf = PDF::loadView('reportdtefac.listado', compact('datas','empresa','usuario','request'));
            //$pdf = PDF::loadView('reportdtefac.listado', compact('datas','empresa','usuario','request'))->setPaper('a4', 'landscape');

            return $pdf->stream("reportdtefac.pdf");
        }else{
            dd('Ningún dato disponible en esta consulta.');
        } 
    }    
}


function consulta($request,$aux_sql,$orden,$aux_AgruOrd){
    //dd($request);
    if($orden==1){
        $aux_orden = "notaventadetalle.notaventa_id desc";
    }else{
        $aux_orden = "notaventa.cliente_id";
    }
    //dd($request->vendedor_id);
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
    $user = Usuario::findOrFail(auth()->id());
    $sucurArray = $user->sucursales->pluck('id')->toArray();
    $sucurcadena = implode(",", $sucurArray);

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
/*
    if(empty($request->comuna_id)){
        $aux_condcomuna_id = " true";
    }else{
        $aux_condcomuna_id = "notaventa.comunaentrega_id='$request->comuna_id'";
    }
*/
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
/*
    if(empty($request->sucursal_id)){
        $aux_condsucursal_id = " true ";
    }else{
        if(is_array($request->sucursal_id)){
            $aux_sucursal = implode ( ',' , $request->sucursal_id);
        }else{
            $aux_sucursal = $request->sucursal_id;
        }
        $aux_condsucursal_id = " notaventa.sucursal_id in ($aux_sucursal) ";
    }
*/
    $user = Usuario::findOrFail(auth()->id());
    $sucurArray = implode ( ',' , $user->sucursales->pluck('id')->toArray());
    if(!isset($request->sucursal_id) or empty($request->sucursal_id)){
        $aux_condsucursal_id = "false";
        //$aux_condsucursal_id = " notaventa.sucursal_id in ($sucurArray) ";
        //$aux_condsucursal_id = " notaventa.sucursal_id in ($sucurArray)";
    }else{
        if(is_array($request->sucursal_id)){
            $aux_sucursal = implode ( ',' , $request->sucursal_id);
        }else{
            $aux_sucursal = $request->sucursal_id;
        }
        $aux_condsucursal_id = " (notaventa.sucursal_id in ($aux_sucursal) and notaventa.sucursal_id in ($sucurArray))";
    }


/*
    if(empty($request->plazoentrega)){
        $aux_condplazoentrega = " true";
    }else{
        $fecha = date_create_from_format('d/m/Y', $request->plazoentrega);
        $fechad = date_format($fecha, 'Y-m-d');
        $aux_condplazoentrega = "notaventa.plazoentrega='$fechad'";
    }
*/
    if(empty($request->plazoentregad) or empty($request->plazoentregah)){
        $aux_condplazoentrega = " true";
    }else{
        $fecha = date_create_from_format('d/m/Y', $request->plazoentregad);
        $plazoentregad = date_format($fecha, 'Y-m-d')." 00:00:00";
        $fecha = date_create_from_format('d/m/Y', $request->plazoentregah);
        $plazoentregah = date_format($fecha, 'Y-m-d')." 23:59:59";
        $aux_condplazoentrega = "notaventa.plazoentrega>='$plazoentregad' and notaventa.plazoentrega<='$plazoentregah'";
    }

    //dd($aux_condplazoentrega);

    $aux_condproducto_id = " true";
    if(!empty($request->producto_id)){
        /*
        $aux_condproducto_id = str_replace(".","",$request->producto_id);
        $aux_condproducto_id = str_replace("-","",$aux_condproducto_id);
        $aux_condproducto_id = "notaventadetalle.producto_id='$aux_condproducto_id'";
        */

        $aux_codprod = explode(",", $request->producto_id);
        $aux_codprod = implode ( ',' , $aux_codprod);
        $aux_condproducto_id = "notaventadetalle.producto_id in ($aux_codprod)";
    }
    if(empty($request->categoriaprod_id)){
        $aux_condcategoriaprod_id = " true";
    }else{

        if(is_array($request->categoriaprod_id)){
            $aux_categoriaprodid = implode ( ',' , $request->categoriaprod_id);
        }else{
            $aux_categoriaprodid = $request->categoriaprod_id;
        }
        $aux_condcategoriaprod_id = " producto.categoriaprod_id in ($aux_categoriaprodid) ";
    }
//dd($aux_condcategoriaprod_id);

    //$suma = DespachoSol::findOrFail(2)->despachosoldets->where('notaventadetalle_id',1);
    if($aux_sql==1){
        $sql = "SELECT notaventadetalle.id,notaventadetalle.notaventa_id as id,notaventa.fechahora,notaventa.cliente_id,
        notaventa.comuna_id,notaventa.comunaentrega_id,
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
        and $aux_condcategoriaprod_id
        and $aux_condsucursal_id
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
        $aux_campos = "";
        if($aux_AgruOrd == ""){
            $aux_campos = ",notaventa.fechahora,notaventa.cliente_id,
            notaventadetalle.cant,if(isnull(vista_sumorddespxnvdetid.cantdesp),0,vista_sumorddespxnvdetid.cantdesp) AS cantdesp,
            producto.nombre,cliente.razonsocial,notaventadetalle.id,
            notaventadetalle.notaventa_id,oc_file,
            producto.diametro,notaventa.oc_id,
            claseprod.cla_nombre,producto.long,
            if(producto.peso=0,notaventadetalle.totalkilos/notaventadetalle.cant,producto.peso) as peso,
            producto.tipounion,
            notaventadetalle.totalkilos,
            subtotal,notaventa.comunaentrega_id,notaventa.plazoentrega,
            notaventadetalle.preciounit,notaventadetalle.precioxkilo,
            comuna.nombre as comunanombre,acuerdotecnico.id as acuerdotecnico_id,
            '' as at_espesor,0 as stockbpt,0 as picking";
            //SE ORDENA DE FORMA DESENDENTE PARA ENVIAR EN EL 1er REGISTRO LOS TOTALES Y LLEGUEN A LA TABLA PARA PODER TOTALIZAR
            //SI SE CAMBIA O ELIMINA ESTE ORDEN NO VA A TOTALIZAR EN LA CONSULTA POR PANTALLA
            $aux_AgruOrd = "order by notaventadetalle.notaventa_id desc";

        }

        $sql = "SELECT notaventadetalle.producto_id $aux_campos ,notaventa.sucursal_id
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
        LEFT JOIN vista_sumorddespxnvdetid
        ON notaventadetalle.id=vista_sumorddespxnvdetid.notaventadetalle_id
        INNER JOIN comuna
        ON comuna.id=notaventa.comunaentrega_id
        LEFT JOIN acuerdotecnico
        ON notaventadetalle.producto_id = acuerdotecnico.producto_id
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
        and $aux_condproducto_id
        and $aux_condcategoriaprod_id
        and $aux_condsucursal_id
        AND isnull(notaventa.findespacho)
        AND isnull(notaventa.anulada)
        AND notaventadetalle.cant>if(isnull(vista_sumorddespxnvdetid.cantdesp),0,vista_sumorddespxnvdetid.cantdesp)
        AND isnull(notaventa.deleted_at) AND isnull(notaventadetalle.deleted_at)
        and notaventadetalle.notaventa_id not in (select notaventa_id from notaventacerrada where isnull(notaventacerrada.deleted_at))
        $aux_AgruOrd;";
    }
    $datas = DB::select($sql);

    return $datas;
    
}