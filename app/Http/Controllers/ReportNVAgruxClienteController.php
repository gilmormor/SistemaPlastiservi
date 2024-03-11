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
use App\Models\NotaVenta;
use App\Models\NotaVentaDetalle;
use App\Models\Producto;
use App\Models\Seguridad\Usuario;
use App\Models\Sucursal;
use App\Models\TipoEntrega;
use App\Models\Vendedor;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Support\Facades\DB;

class ReportNVAgruxClienteController extends Controller
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
        return view('reportnvagruxcliente.index', compact('giros','areaproduccions','tipoentregas','comunas','fechaAct','tablashtml'));

    }
    public function reportnvagruxclientepage(Request $request){
        $request->merge(['agrupar' => "notaventa.cliente_id"]);
        $datas = NotaVenta::consulta($request,1);
        //dd($datas);

        return datatables($datas)->toJson();
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