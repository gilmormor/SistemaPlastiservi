<?php

namespace App\Models;

use App\Models\Seguridad\Usuario;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class EtapaProd extends Model
{
    use SoftDeletes;
    protected $table = "etapaprod";
    protected $fillable = [
        'nombre',
        'desc',
        'usuario_id',
        'usuariodel_id'
    ];

    public static function reportot($request){
        $user = Usuario::findOrFail(auth()->id());

        $aux_condvendedor_id = " true";
        if(isset($user->persona->vendedor)){
            $aux_vendedor_id = $user->persona->vendedor->id;
            $aux_condvendedor_id = "ot.vendedor_id = $aux_vendedor_id";
        }
    
        if(!isset($request->fechad) or !isset($request->fechah) or empty($request->fechad) or empty($request->fechah)){
            $aux_condFecha = " true";
        }else{
            $fecha = date_create_from_format('d/m/Y', $request->fechad);
            $fechad = date_format($fecha, 'Y-m-d');
            $fecha = date_create_from_format('d/m/Y', $request->fechah);
            $fechah = date_format($fecha, 'Y-m-d');
            $aux_condFecha = "ot.fechahora>='$fechad' and ot.fechahora<='$fechah'";
        }
        if(!isset($request->rut) or empty($request->rut)){
            $aux_condrut = " true";
        }else{
            $aux_condrut = "cliente.rut='$request->rut'";
        }
        if(!isset($request->oc_id) or empty($request->oc_id)){
            $aux_condoc_id = " true";
        }else{
            $aux_condoc_id = "otoc.oc_id='$request->oc_id'";
            $aux_condFecha = " true";
        }
        if(!isset($request->notaventa_id) or empty($request->notaventa_id)){
            $aux_condnotaventa_id = " true";
        }else{
            $aux_condnotaventa_id = "otnotaventa.notaventa_id='$request->notaventa_id'";
            $aux_condFecha = " true";
        }
    
        if(!isset($request->comuna_id) or empty($request->comuna_id)){
            $aux_condcomuna_id = " true";
        }else{
            $aux_condcomuna_id = "cliente.comunap_id='$request->comuna_id'";
        }
    
        if(!isset($request->dtenotnull) or empty($request->dtenotnull)){
            $aux_conddtenotnull = " true";
        }else{
            $aux_conddtenotnull = "ot.id NOT IN (SELECT otanul.ot_id FROM otanul WHERE ISNULL(otanul.deleted_at))";
        }

        if(!isset($request->arrdte_id) or empty($request->arrdte_id)){
            $aux_conddtenotnull = " true";
        }else{
            $aux_conddtenotnull = "ot.id NOT IN (SELECT otanul.ot_id FROM otanul WHERE ISNULL(otanul.deleted_at))";
        }
        if(!isset($request->arrdte_id) or empty($request->arrdte_id)){
            $aux_conddtedet = " true";
        }else{
            $aux_conddtedet = "dte.id IN ($request->strdte_id)";
        }

        if(!isset($request->ot_id) or empty($request->ot_id)){
            $aux_condot_id = " true";
        }else{
            $aux_condot_id = "ot.id = $request->ot_id";
            $aux_condFecha = " true";
        }

        if(!isset($request->aprobstatus) or empty($request->aprobstatus)){
            $aux_aprobstatus = " true";
        }else{
            switch ($request->aprobstatus) {
                case 0:
                    $aux_aprobstatus = " true";
                    break;
                case 1:
                    $aux_aprobstatus = " isnull(otanul.obs)";
                    break;    
                case 2:
                    $aux_aprobstatus = " not isnull(otanul.obs)";
                    break;
            }
        }
        if(!isset($request->aux_estado) or empty($request->aux_estado) or $request->aux_estado == "") {
            $aux_estado = " true";
        }else{
            $noanul = "";
            if(isset($request->noanul)){
                $noanul = " AND isnull(otanul.ot_id)";
            }
            switch ($request->aux_estado) {
                case 1:
                    $aux_estado = " (ot.aprobstatus = 0 or ot.aprobstatus = 3) $noanul";
                    break;
                case 2:
                    $aux_estado = " ot.aprobstatus = 1 $noanul";
                    break;
                case 3:
                    $aux_estado = " ot.aprobstatus = 2 $noanul";
                    break;
                case 4:
                    $aux_estado = " not isnull(otanul.ot_id)";
                    break;
                /* case 2:
                    $aux_estado = " isnull(otanul.obs)";
                    break; */
            }
        }
        

        //dd($aux_condfoliocontrol_id);
        $sucurArray = $user->sucursales->pluck('id')->toArray();
        $sucurcadena = implode(",", $sucurArray);
        $aux_condsucurArray = "ot.sucursal_id  in ($sucurcadena)";
        if(!isset($request->sucursal_id) or empty($request->sucursal_id) or ($request->sucursal_id == "")){
            $aux_sucursal_idCond = "true";
        }else{
            $aux_sucursal_idCond = "ot.sucursal_id = $request->sucursal_id";
        }
        $aux_centroeconomico_idCond = " true ";
        /* if(!isset($request->centroeconomico_id) or empty($request->centroeconomico_id) or ($request->centroeconomico_id == "")){
            $aux_centroeconomico_idCond = "true";
        }else{
            $aux_centroeconomico_idCond = "ot.centroeconomico_id = $request->centroeconomico_id";
        } */
        if(!isset($request->vendedor_id) or empty($request->vendedor_id) or ($request->vendedor_id == "")){
            $aux_vendedor_idCond = "true";
        }else{
            $aux_vendedor_idCond = "ot.vendedor_id in ($request->vendedor_id)";
        }

        $aux_verFacturas = can('ver-facturas-de-todos-los-usuarios',false);
        $aux_condFiltrarxUsuario = " true ";
        if(!$aux_verFacturas){
            $aux_condFiltrarxUsuario = " ot.usuario_id = $user->id ";
        }

        //Incluido el 13/05/2024, para el reporte ReportDTEFacController. 
        if(!isset($request->producto_id) or empty($request->producto_id) or ($request->producto_id == "")){
            $aux_producto_idCond = "true";
        }else{
            $aux_producto_idCond = "ot.id in (SELECT otdet.ot_id FROM otdet WHERE otdet.producto_id in ($request->producto_id) and ot.id=otdet.ot_id and isnull(otdet.deleted_at))";
        }

        if(!isset($request->areaproduccion_id) or empty($request->areaproduccion_id) or ($request->areaproduccion_id == "")){
            $aux_areaproduccion_idCond = "true";
        }else{
            $aux_areaproduccion_idCond = "ot.id in 
            (SELECT otdet.ot_id FROM otdet INNER JOIN producto
            ON producto.id = otdet.producto_id
            INNER JOIN categoriaprod
            ON categoriaprod.id = producto.categoriaprod_id
            WHERE categoriaprod.areaproduccion_id in ($request->areaproduccion_id) and ot.id=otdet.ot_id and isnull(otdet.deleted_at))";
        }

        
        if(!isset($request->modulo_id) or empty($request->modulo_id) or ($request->modulo_id == "")){
            $aux_modulo_id = "0";
        }else{
            $aux_modulo_id = $request->modulo_id;
        }

        $aux_condnd = "";
        $aux_condnd = "ot.id NOT IN (SELECT otnotaventa.ot_id FROM otnotaventa WHERE ISNULL(otnotaventa.deleted_at))";
        $aux_condnd = "ot.id IN (SELECT otnotaventa.ot_id FROM otnotaventa WHERE ISNULL(otnotaventa.deleted_at))";

        //AND ot.id NOT IN (SELECT otanul.ot_id FROM otanul WHERE ISNULL(otanul.deleted_at))

        $sql = "SELECT ot.id,ot.fechahora,cliente.razonsocial,cliente.rut,ot.kg,
        IFNULL(otoc.oc_id,notaventa.oc_id) as oc_id,IFNULL(otoc.oc_file,notaventa.oc_file) as oc_file,
        IFNULL(otoc.oc_id,null) as staus_oc,ot.aprobstatus,
        comuna.nombre as comuna_nombre,
        UNIX_TIMESTAMP(ot.updated_at) as updatednum_at,ot.updated_at,
        clientebloqueado.descripcion as clientebloqueado_descripcion,
        if(cliente.plazopago_id = 1,'Condición pago: Contado',clientebloqueado.descripcion) as clientebloqueado_desc,
        cliente.limitecredito,
        IFNULL(vista_datacobranza.tfac,0) AS datacobranza_tfac,
        IFNULL(vista_datacobranza.tdeuda,0) AS datacobranza_tdeuda,
        IFNULL(vista_datacobranza.tdeudafec,0) AS datacobranza_tdeudafec,
        IFNULL(vista_datacobranza.nrofacdeu,'') AS datacobranza_nrofacdeu,
        modulo.stamodapl as modulo_stamodapl,clientedesbloqueadomodulo.modulo_id,
        IFNULL(clientedesbloqueadopro.obs,'') AS clientedesbloqueadopro_obs,
        sum(otdet.kg) as aux_totalkg,
        '' as obsdev, '' as rutaeditar,
        otnotaventa.notaventa_id,notaventa.cotizacion_id,comuna.nombre as nombre_comuna,
        otanul.obs as otanul_obs,otanul.created_at as otanulcreated_at,
        GROUP_CONCAT(
            CONCAT_WS('|', otdet.producto_id, otdet.cant, otdet.preciounit, otdet.subtotal,otdet.kg, otdet.kgprod, if(ISNULL(acuerdotecnico.id),0,acuerdotecnico.id),unidadmedida.nombre)
            SEPARATOR ';'
        ) AS nvdetalle
        FROM ot INNER JOIN otdet
        ON ot.id = otdet.ot_id
        INNER JOIN cliente
        ON cliente.id = ot.cliente_id AND isnull(cliente.deleted_at)
        INNER JOIN comuna
        ON comuna.id = cliente.comunap_id AND isnull(comuna.deleted_at)
        LEFT JOIN clientebloqueado
        ON clientebloqueado.cliente_id = ot.cliente_id AND ISNULL(clientebloqueado.deleted_at)
        LEFT JOIN vista_datacobranza
        ON vista_datacobranza.cliente_id = ot.cliente_id
        LEFT JOIN clientedesbloqueado
        ON clientedesbloqueado.cliente_id = ot.cliente_id and isnull(clientedesbloqueado.notaventa_id) and isnull(clientedesbloqueado.deleted_at)
        LEFT JOIN clientedesbloqueadomodulo
        ON clientedesbloqueadomodulo.clientedesbloqueado_id = clientedesbloqueado.id and clientedesbloqueadomodulo.modulo_id = $aux_modulo_id
        LEFT JOIN modulo
        ON modulo.id = clientedesbloqueadomodulo.modulo_id
        LEFT JOIN clientedesbloqueadopro
        ON clientedesbloqueadopro.cliente_id = ot.cliente_id  and isnull(clientedesbloqueadopro.deleted_at)
        LEFT JOIN otnotaventa
        ON otnotaventa.ot_id = ot.id
        LEFT JOIN notaventa
        ON notaventa.id = otnotaventa.notaventa_id
        LEFT JOIN otoc
        ON otoc.ot_id = ot.id
        LEFT JOIN otanul
        ON otanul.ot_id = ot.id
        LEFT JOIN acuerdotecnico
        ON acuerdotecnico.producto_id = otdet.producto_id
        INNER JOIN unidadmedida
        ON unidadmedida.id = otdet.unidadmedida_id

        WHERE $aux_sucursal_idCond
        AND $aux_centroeconomico_idCond
        AND $aux_condot_id
        AND $aux_condFecha
        AND $aux_condrut
        AND $aux_condoc_id
        AND $aux_condnotaventa_id
        AND $aux_condsucurArray
        AND $aux_vendedor_idCond
        AND $aux_condFiltrarxUsuario
        AND $aux_producto_idCond
        AND $aux_areaproduccion_idCond
        AND $aux_condcomuna_id
        AND $aux_condvendedor_id
        AND $aux_estado
        AND isnull(ot.deleted_at)
        GROUP BY ot.id;";

        //dd($sql);
        
        //AND ISNULL(dte.statusgen)


        $datas = DB::select($sql);

        foreach ($datas as &$data) {
            //dd($data->nvdetalle);
    
            // Array para almacenar el resultado final
            $detalleArrayFinal = [];
    
            // Dividir el campo detallenv en registros individuales
            $detalleArray = explode(';', $data->nvdetalle);
            //dd($detalleArray);
    
            // Crear un array con todos los producto_id
            $productoIds = array_map(function ($detalle) {
                return explode('|', $detalle)[0]; // Extraemos solo producto_id
            }, $detalleArray);
    
            // Procesar cada registro de detallenv y agregar el nombre del producto
            //dd($detalleArray);
            foreach ($detalleArray as $index => $detalle) {
                //dd($detalle);
                $productoarray = Producto::atributosProducto($productoIds[$index]);
                list($producto_id, $cant, $precio, $subtotal,$kg,$kgprod, $id,$unidadmedida_nombre) = explode('|', $detalle);
                //$producto_nombre = isset($productos[$producto_id]) ? $productos[$producto_id] : 'Desconocido';
                $detalleFinal = implode('|', [$producto_id, $cant, $precio, $subtotal, $kg, $productoarray["nombre"],$kgprod, $id, $unidadmedida_nombre]);
                $detalleArrayFinal[] = $detalleFinal;
            }
            //dd(implode(';', $detalleArrayFinal));
    
            // Reconstruir el campo detallenv con los nuevos valores
            $data->nvdetalle = implode(';', $detalleArrayFinal);
        }

        /*
        $i = 0;
        foreach ($datas as $array) {
            $datas[$i]->rutacrear = route('crear_factura', ['id' => $array->id]);
            $i++;
        }*/
        //dd($datas);
        return $datas;
    }

    public static function etapasProdxPersona(){
        $user = Usuario::findOrFail(auth()->id());
        $sucurArray = $user->sucursales->pluck('id')->toArray();
        $sucurcadena = implode(",", $sucurArray);
        $aux_condsucurArray = "areaproduccionsuc.sucursal_id IN ($sucurcadena)";
        $sql = "SELECT personaetapaprod.areaproduccionsucetapaprod_id,
                areaproduccionsucetapaprod.etapaprod_id,etapaprod.nombre AS etapaprod_nombre,
                personaetapaprod.persona_id,areaproduccionsuc.sucursal_id,sucursal.nombre AS sucursal_nombre,
                areaproduccionsucetapaprod.orden
                FROM personaetapaprod INNER JOIN areaproduccionsucetapaprod
                ON personaetapaprod.areaproduccionsucetapaprod_id = areaproduccionsucetapaprod.id
                INNER JOIN etapaprod 
                ON areaproduccionsucetapaprod.etapaprod_id = etapaprod.id
                INNER JOIN persona
                ON persona.id = personaetapaprod.persona_id
                INNER JOIN areaproduccionsuc
                ON areaproduccionsuc.id = areaproduccionsucetapaprod.areaproduccionsuc_id
                INNER JOIN usuario
                ON usuario.id = persona.usuario_id
                INNER JOIN sucursal
                ON sucursal.id = areaproduccionsuc.sucursal_id
                WHERE persona.usuario_id = $user->id
                AND $aux_condsucurArray
                ORDER BY areaproduccionsuc.sucursal_id,areaproduccionsucetapaprod.orden;";
        $datas = DB::select($sql);
        return $datas;
}

}
