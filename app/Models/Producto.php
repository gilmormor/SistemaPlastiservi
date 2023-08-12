<?php

namespace App\Models;

use App\Models\Seguridad\Usuario;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Producto extends Model
{
    use SoftDeletes;
    protected $table = "producto";
    protected $fillable = [
        'nombre',
        'descripcion',
        'estado',
        'codintprod',
        'codbarra',
        'diametro',
        'diamextmm',
        'diamextpg',
        'espesor',
        'long',
        'peso',
        'tipounion',
        'precioneto',
        'foto',
        'categoriaprod_id',
        'claseprod_id',
        'grupoprod_id',
        'color_id',
        'tipoprod',
        'stockmin',
        'stockmax',
        'acuerdotecnico_id',
        'usuariodel_id'
    ];

    //RELACION INVERSA PARA BUSCAR EL PADRE
    public function claseprod()
    {
        return $this->belongsTo(ClaseProd::class);
    }
    //RELACION INVERSA PARA BUSCAR EL PADRE
    public function categoriaprod()
    {
        return $this->belongsTo(CategoriaProd::class);
    }
    //RELACION UNO A MUCHOS CotizacionDetalle
    public function cotizaciondetalles()
    {
        return $this->hasMany(CotizacionDetalle::class);
    }
    //RELACION UNO A MUCHOS NotaventaDetalle
    public function notaventadetalles()
    {
        return $this->hasMany(NotaVentaDetalle::class);
    }

    //RELACION INVERSA PARA BUSCAR EL PADRE
    public function grupoprod()
    {
        return $this->belongsTo(GrupoProd::class);
    }
    //RELACION INVERSA PARA BUSCAR EL PADRE
    public function color()
    {
        return $this->belongsTo(Color::class);
    }

    //RELACION UNO A MUCHOS invbodegaproducto
    public function invbodegaproductos()
    {
        return $this->hasMany(InvBodegaProducto::class);
    }
    
    //RELACION UNO A MUCHOS InvmovDet
    public function invmovdets()
    {
        return $this->hasMany(InvMovDet::class);
    }
    //RELACION MUCHO A MUCHOS CON CLIENTE A TRAVES DE cliente_producto
    public function clientes()
    {
        return $this->belongsToMany(Cliente::class, 'cliente_producto')->withTimestamps();
    }

    public static function productosxUsuario($sucursal_id = false){
        $users = Usuario::findOrFail(auth()->id());
        if($sucursal_id){
            $sucurArray = [$sucursal_id];
        }else{
            $sucurArray = $users->sucursales->pluck('id')->toArray();
        }
        //Filtrando las categorias por sucursal, dependiendo de las sucursales asignadas al usuario logueado
        //******************* */
        $productos = CategoriaProd::join('categoriaprodsuc', 'categoriaprod.id', '=', 'categoriaprodsuc.categoriaprod_id')
        ->join('sucursal', 'categoriaprodsuc.sucursal_id', '=', 'sucursal.id')
        ->join('producto', 'categoriaprod.id', '=', 'producto.categoriaprod_id')
        ->join('claseprod', 'producto.claseprod_id', '=', 'claseprod.id')
        ->select([
                'producto.id',
                'producto.nombre',
                'claseprod.cla_nombre',
                'producto.codintprod',
                'producto.diamextmm',
                'producto.diamextpg',
                'producto.diametro',
                'producto.espesor',
                'producto.long',
                'producto.peso',
                'producto.tipounion',
                'producto.precioneto',
                'categoriaprod.precio',
                'categoriaprodsuc.sucursal_id',
                'categoriaprod.unidadmedida_id'
                ])
        ->whereIn('categoriaprodsuc.sucursal_id', $sucurArray)
        ->where('producto.deleted_at','=',null)
        ->groupBy('producto.id')
        ->orderBy('producto.id', 'asc')
        ->get();
        return $productos;
    }
    //RELACION UNO A UNO CON ACUERDOTECNICO
    public function acuerdotecnico()
    {
        return $this->hasOne(AcuerdoTecnico::class);
    }

    //RELACION MUCHO A MUCHOS vendedor A TRAVES DE producto_vendedor
    public function vendedores()
    {
        return $this->belongsToMany(Vendedor::class, 'producto_vendedor');
    }
    

    public static function productosxUsuarioSQL($sucursal_id = false){
        $users = Usuario::findOrFail(auth()->id());
        if($sucursal_id){
            $sucurArray = [$sucursal_id];
        }else{
            $sucurArray = $users->sucursales->pluck('id')->toArray();
        }
        $sucurcadena = implode(",", $sucurArray);

        $sql = "SELECT producto.id,producto.nombre,producto.codintprod,producto.diamextmm,producto.diamextpg,
                if(isnull(at_claseprod_id),CAST(claseprod.cla_nombre AS CHAR),at_claseprod.cla_nombre) as cla_nombre,
                if(isnull(at_ancho),CAST(producto.diametro AS CHAR),at_ancho) as diametro,
                if(isnull(at_espesor),producto.espesor,at_espesor) as espesor,
                if(isnull(at_largo),producto.long,at_largo) as long1,producto.long,
                if(isnull(at_espesor),producto.peso,at_espesor) as peso,
                producto.peso,producto.tipounion,producto.precioneto,categoriaprod.precio,
                categoriaprodsuc.sucursal_id,categoriaprod.unidadmedida_id,producto.tipoprod,acuerdotecnico.id as acuerdotecnico_id
                from producto inner join categoriaprod
                on producto.categoriaprod_id = categoriaprod.id and isnull(producto.deleted_at) and isnull(categoriaprod.deleted_at)
                INNER JOIN claseprod
                on producto.claseprod_id = claseprod.id and isnull(claseprod.deleted_at)
                INNER JOIN categoriaprodsuc
                on categoriaprod.id = categoriaprodsuc.categoriaprod_id
                INNER JOIN sucursal
                ON categoriaprodsuc.sucursal_id = sucursal.id
                LEFT JOIN acuerdotecnico
                ON producto.id = acuerdotecnico.producto_id
                LEFT JOIN claseprod as at_claseprod
                ON at_claseprod.id = acuerdotecnico.at_claseprod_id
                WHERE sucursal.id in ($sucurcadena)
                AND producto.estado = 1
                GROUP BY producto.id
                ORDER BY producto.id asc;";
        //dd($sql);
        $datas = DB::select($sql);
        return $datas;
    }

    public static function productosxCliente($request){
        $cliente_idCond = " true";
        if(isset($request->cliente_id) and ($request->cliente_id != "undefined")){
            $cliente_idCond = "if(categoriaprod.asoprodcli = 1, ((producto.id IN (SELECT producto_id FROM cliente_producto WHERE 
                                cliente_producto.cliente_id = $request->cliente_id)) OR producto.tipoprod = 1), TRUE )";
        }
        //dd($cliente_idCond);
        $users = Usuario::findOrFail(auth()->id());
        if(isset($request->sucursal_id) and ($request->sucursal_id != "undefined")){
            $sucurArray = [$request->sucursal_id];
        }else{
            $sucurArray = $users->sucursales->pluck('id')->toArray();
        }
        $sucurcadena = implode(",", $sucurArray);
        if(!isset($request->tipoprod) or ($request->tipoprod == "undefined") or is_null($request->tipoprod)){
            $tipoprodCond = "producto.tipoprod = 0";
        }else{
            $tipoprodCond = "producto.tipoprod = " . $request->tipoprod;
        }
        $sql = "SELECT producto.id,producto.nombre,producto.codintprod,producto.diamextmm,producto.diamextpg,
                if(isnull(at_claseprod_id),CAST(claseprod.cla_nombre AS CHAR),at_claseprod.cla_nombre) as cla_nombre,
                if(isnull(at_ancho),CAST(producto.diametro AS CHAR),at_ancho) as diametro,
                if(isnull(at_espesor),producto.espesor,at_espesor) as espesor,
                if(isnull(at_largo),producto.long,at_largo) as long1,producto.long,
                if(isnull(at_espesor),producto.peso,at_espesor) as peso,
                producto.tipounion,producto.precioneto,categoriaprod.precio,
                categoriaprodsuc.sucursal_id,categoriaprod.unidadmedida_id,producto.tipoprod,
                acuerdotecnico.id as acuerdotecnico_id,at_ancho,at_largo,at_espesor
                from producto inner join categoriaprod
                on producto.categoriaprod_id = categoriaprod.id and isnull(producto.deleted_at) and isnull(categoriaprod.deleted_at)
                INNER JOIN claseprod
                on producto.claseprod_id = claseprod.id and isnull(claseprod.deleted_at)
                INNER JOIN categoriaprodsuc
                on categoriaprod.id = categoriaprodsuc.categoriaprod_id
                INNER JOIN sucursal
                ON categoriaprodsuc.sucursal_id = sucursal.id
                LEFT JOIN acuerdotecnico
                ON producto.id = acuerdotecnico.producto_id
                LEFT JOIN claseprod as at_claseprod
                ON at_claseprod.id = acuerdotecnico.at_claseprod_id
                WHERE sucursal.id in ($sucurcadena)
                and $cliente_idCond
                and $tipoprodCond
                AND producto.estado = 1
                GROUP BY producto.id
                ORDER BY producto.id asc;";
        //dd($sql);
        $datas = DB::select($sql);
        return $datas;
    }

    public static function AsignarProductosAClientes($request){
        //dd($request);
        $cliente_idCond = "false";
        //dd($request->producto_id);
        if($request->cliente_id and isset($request->producto_id)){
            if(is_null($request->producto_id)){
                $request->producto_id = "";
            }
            $cliente_idCond = "categoriaprod.asoprodcli = 1 and producto.tipoprod = 0
                                AND producto.id NOT IN ($request->producto_id)";
        };
        $users = Usuario::findOrFail(auth()->id());
        if($request->sucursal_id){
            $sucurArray = [$request->sucursal_id];
        }else{
            $sucurArray = $users->sucursales->pluck('id')->toArray();
        }
        $sucurcadena = implode(",", $sucurArray);

        $sql = "SELECT producto.id,producto.nombre,claseprod.cla_nombre,producto.codintprod,producto.diamextmm,producto.diamextpg,
                producto.diametro,producto.espesor,producto.long,producto.peso,producto.tipounion,producto.precioneto,categoriaprod.precio,
                categoriaprodsuc.sucursal_id,categoriaprod.unidadmedida_id,producto.tipoprod,acuerdotecnico.id as acuerdotecnico_id
                from producto inner join categoriaprod
                on producto.categoriaprod_id = categoriaprod.id and isnull(producto.deleted_at) and isnull(categoriaprod.deleted_at)
                INNER JOIN claseprod
                on producto.claseprod_id = claseprod.id and isnull(claseprod.deleted_at)
                INNER JOIN categoriaprodsuc
                on categoriaprod.id = categoriaprodsuc.categoriaprod_id
                INNER JOIN sucursal
                ON categoriaprodsuc.sucursal_id = sucursal.id
                LEFT JOIN acuerdotecnico
                ON producto.id = acuerdotecnico.producto_id
                WHERE sucursal.id in ($sucurcadena)
                and $cliente_idCond
                GROUP BY producto.id
                ORDER BY producto.id asc;";
        //dd($sql);
        $datas = DB::select($sql);
        return $datas;
    }

    public static function pendientexProducto1($request,$aux_sql,$orden){
        //dd($request);
        if($orden==1){
            $aux_orden = "notaventadetalle.notaventa_id desc";
        }else{
            $aux_orden = "notaventa.cliente_id";
        }
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
            if(is_array($request->vendedor_id)){
                $aux_vendedorid = implode ( ',' , $request->vendedor_id);
            }else{
                $aux_vendedorid = $request->vendedor_id;
            }
            $vendedorcond = " notaventa.vendedor_id in ($aux_vendedorid) ";
    
            //$vendedorcond = "notaventa.vendedor_id='$request->vendedor_id'";
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
        if(!isset($request->rut) or empty($request->rut)){
            $aux_condrut = " true";
        }else{
            $aux_condrut = "cliente.rut='$request->rut'";
        }
        if(!isset($request->oc_id) or empty($request->oc_id)){
            $aux_condoc_id = " true";
        }else{
            $aux_condoc_id = "notaventa.oc_id='$request->oc_id'";
        }
        if(!isset($request->giro_id) or empty($request->giro_id)){
            $aux_condgiro_id = " true";
        }else{
            $aux_condgiro_id = "notaventa.giro_id='$request->giro_id'";
        }
        if(!isset($request->areaproduccion_id) or empty($request->areaproduccion_id)){
            $aux_condareaproduccion_id = " true";
        }else{
            $aux_condareaproduccion_id = "categoriaprod.areaproduccion_id='$request->areaproduccion_id'";
        }
        if(!isset($request->tipoentrega_id) or empty($request->tipoentrega_id)){
            $aux_condtipoentrega_id = " true";
        }else{
            $aux_condtipoentrega_id = "notaventa.tipoentrega_id='$request->tipoentrega_id'";
        }
        if(!isset($request->notaventa_id) or empty($request->notaventa_id)){
            $aux_condnotaventa_id = " true";
        }else{
            $aux_condnotaventa_id = "notaventa.id='$request->notaventa_id'";
        }
    
        if(!isset($request->aprobstatus) or empty($request->aprobstatus)){
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
        $user = Usuario::findOrFail(auth()->id());
        $sucurArray = implode ( ',' , $user->sucursales->pluck('id')->toArray());
        if(!isset($request->sucursal_id) or empty($request->sucursal_id) or $request->sucursal_id == "x"){
            //$aux_condsucursal_id = " true ";
            $aux_condsucursal_id = " notaventa.sucursal_id in (0)";
        }else{
            if(is_array($request->sucursal_id)){
                $aux_sucursal = implode ( ',' , $request->sucursal_id);
            }else{
                $aux_sucursal = $request->sucursal_id;
            }
            $aux_condsucursal_id = " (notaventa.sucursal_id in ($aux_sucursal) and notaventa.sucursal_id in ($sucurArray))";
        }
        if(empty($request->plazoentregad) or empty($request->plazoentregah)){
            $aux_condplazoentrega = " true";
        }else{
            $fecha = date_create_from_format('d/m/Y', $request->plazoentregad);
            $plazoentregad = date_format($fecha, 'Y-m-d')." 00:00:00";
            $fecha = date_create_from_format('d/m/Y', $request->plazoentregah);
            $plazoentregah = date_format($fecha, 'Y-m-d')." 23:59:59";
            $aux_condplazoentrega = "notaventa.plazoentrega>='$plazoentregad' and notaventa.plazoentrega<='$plazoentregah'";
        }
    
    
        $aux_condproducto_id = " true";
        if(!empty($request->producto_id)){
    
            $aux_codprod = explode(",", $request->producto_id);
            $aux_codprod = implode ( ',' , $aux_codprod);
            $aux_condproducto_id = "notaventadetalle.producto_id in ($aux_codprod)";
        }
        if(!isset($request->categoriaprod_id) or empty($request->categoriaprod_id)){
            $aux_condcategoriaprod_id = " true";
        }else{
    
            if(is_array($request->categoriaprod_id)){
                $aux_categoriaprodid = implode ( ',' , $request->categoriaprod_id);
            }else{
                $aux_categoriaprodid = $request->categoriaprod_id;
            }
            $aux_condcategoriaprod_id = " producto.categoriaprod_id in ($aux_categoriaprodid) ";
        }
        if(!isset($request->groupby) or empty($request->groupby)){
            $aux_groupby = " group by notaventadetalle.id ";
        }else{
            $aux_groupby = $request->groupby;
        }
        if(!isset($request->orderby) or empty($request->orderby)){
            $aux_orderby = "order by notaventadetalle.id desc ";
        }else{
            $aux_orderby = $request->orderby;
        }

        if($aux_sql==1){
            $sql = "SELECT notaventadetalle.notaventa_id as id,notaventa.fechahora,notaventa.cliente_id,notaventa.comuna_id,notaventa.comunaentrega_id,
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
            $sql = "SELECT notaventa.fechahora,notaventadetalle.producto_id,
            sum(notaventadetalle.cant) as cant,sum(if(isnull(vista_sumorddespxnvdetid.cantdesp),0,vista_sumorddespxnvdetid.cantdesp)) AS cantdesp,
            producto.nombre,cliente.razonsocial,notaventadetalle.id,
            notaventadetalle.notaventa_id,oc_file,
            if(isnull(acuerdotecnico.id),producto.diametro,at_ancho) as diametro,
            if(isnull(acuerdotecnico.id),producto.long,at_largo) as largo,
            if(isnull(acuerdotecnico.id),producto.peso,at_espesor) as peso,
            producto.long,
            notaventa.oc_id,
            claseprod.cla_nombre,producto.tipounion,
            notaventadetalle.totalkilos,
            subtotal,notaventa.comunaentrega_id,notaventa.plazoentrega,
            notaventadetalle.precioxkilo,
            acuerdotecnico.id as acuerdotecnico_id,at_ancho,at_largo,at_espesor
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
            LEFT JOIN acuerdotecnico
            ON producto.id = acuerdotecnico.producto_id and isnull(acuerdotecnico.deleted_at)
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
            $aux_groupby
            $aux_orderby;";
            
        }
        $datas = DB::select($sql);
        return $datas;
        
    }

    public static function productosxClienteTemp($request){ //Esta funcion es temporal mientras fusiono las 2 ramas de Santa Ester y San Bernardo
        //dd($request);
        $cliente_idCond = "true";
        if($request->cliente_id and $request->cliente_id != "undefined"){
            $cliente_idCond = " TRUE ";
        }
        $users = Usuario::findOrFail(auth()->id());
        if(($request->sucursal_id and $request->sucursal_id !=  "undefined") and ($request->sucursal_id != "x")){
            $sucurArray = [$request->sucursal_id];
        }else{
            $sucurArray = $users->sucursales->pluck('id')->toArray();
        }
        $sucurcadena = implode(",", $sucurArray);

        $sql = "SELECT producto.id,producto.nombre,claseprod.cla_nombre,producto.codintprod,producto.diamextmm,producto.diamextpg,
                producto.diametro,producto.espesor,producto.long,producto.peso,producto.tipounion,producto.precioneto,categoriaprod.precio,
                categoriaprodsuc.sucursal_id,categoriaprod.unidadmedida_id,producto.tipoprod,'' as acuerdotecnico_id
                from producto inner join categoriaprod
                on producto.categoriaprod_id = categoriaprod.id and isnull(producto.deleted_at) and isnull(categoriaprod.deleted_at)
                INNER JOIN claseprod
                on producto.claseprod_id = claseprod.id and isnull(claseprod.deleted_at)
                INNER JOIN categoriaprodsuc
                on categoriaprod.id = categoriaprodsuc.categoriaprod_id
                INNER JOIN sucursal
                ON categoriaprodsuc.sucursal_id = sucursal.id
                WHERE sucursal.id in ($sucurcadena)
                and $cliente_idCond
                GROUP BY producto.id
                ORDER BY producto.id asc;";
        //dd($sql);
        $datas = DB::select($sql);
        return $datas;
    }

    public static function pendientexProducto($request,$aux_sql,$orden){
        //dd($request);
        if($orden==1){
            $aux_orden = "notaventadetalle.notaventa_id desc";
        }else{
            $aux_orden = "notaventa.cliente_id";
        }
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
            if(is_array($request->vendedor_id)){
                $aux_vendedorid = implode ( ',' , $request->vendedor_id);
            }else{
                $aux_vendedorid = $request->vendedor_id;
            }
            $vendedorcond = " notaventa.vendedor_id in ($aux_vendedorid) ";
    
            //$vendedorcond = "notaventa.vendedor_id='$request->vendedor_id'";
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
        if(!isset($request->rut) or empty($request->rut)){
            $aux_condrut = " true";
        }else{
            $aux_condrut = "cliente.rut='$request->rut'";
        }
        if(!isset($request->oc_id) or empty($request->oc_id)){
            $aux_condoc_id = " true";
        }else{
            $aux_condoc_id = "notaventa.oc_id='$request->oc_id'";
        }
        if(!isset($request->giro_id) or empty($request->giro_id)){
            $aux_condgiro_id = " true";
        }else{
            $aux_condgiro_id = "notaventa.giro_id='$request->giro_id'";
        }
        if(!isset($request->areaproduccion_id) or empty($request->areaproduccion_id)){
            $aux_condareaproduccion_id = " true";
        }else{
            $aux_condareaproduccion_id = "categoriaprod.areaproduccion_id='$request->areaproduccion_id'";
        }
        if(!isset($request->tipoentrega_id) or empty($request->tipoentrega_id)){
            $aux_condtipoentrega_id = " true";
        }else{
            $aux_condtipoentrega_id = "notaventa.tipoentrega_id='$request->tipoentrega_id'";
        }
        if(!isset($request->notaventa_id) or empty($request->notaventa_id)){
            $aux_condnotaventa_id = " true";
        }else{
            $aux_condnotaventa_id = "notaventa.id='$request->notaventa_id'";
        }
    
        if(!isset($request->aprobstatus) or empty($request->aprobstatus)){
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
        $user = Usuario::findOrFail(auth()->id());
        $sucurArray = implode ( ',' , $user->sucursales->pluck('id')->toArray());
        if(!isset($request->sucursal_id) or empty($request->sucursal_id) or $request->sucursal_id == "x"){
            //$aux_condsucursal_id = " true ";
            $aux_condsucursal_id = " notaventa.sucursal_id in (0)";
        }else{
            if(is_array($request->sucursal_id)){
                $aux_sucursal = implode ( ',' , $request->sucursal_id);
            }else{
                $aux_sucursal = $request->sucursal_id;
            }
            $aux_condsucursal_id = " (notaventa.sucursal_id in ($aux_sucursal) and notaventa.sucursal_id in ($sucurArray))";
        }
        if(empty($request->plazoentregad) or empty($request->plazoentregah)){
            $aux_condplazoentrega = " true";
        }else{
            $fecha = date_create_from_format('d/m/Y', $request->plazoentregad);
            $plazoentregad = date_format($fecha, 'Y-m-d')." 00:00:00";
            $fecha = date_create_from_format('d/m/Y', $request->plazoentregah);
            $plazoentregah = date_format($fecha, 'Y-m-d')." 23:59:59";
            $aux_condplazoentrega = "notaventa.plazoentrega>='$plazoentregad' and notaventa.plazoentrega<='$plazoentregah'";
        }
    
    
        $aux_condproducto_id = " true";
        if(!empty($request->producto_id)){
    
            $aux_codprod = explode(",", $request->producto_id);
            $aux_codprod = implode ( ',' , $aux_codprod);
            $aux_condproducto_id = "notaventadetalle.producto_id in ($aux_codprod)";
        }
        if(!isset($request->categoriaprod_id) or empty($request->categoriaprod_id)){
            $aux_condcategoriaprod_id = " true";
        }else{
    
            if(is_array($request->categoriaprod_id)){
                $aux_categoriaprodid = implode ( ',' , $request->categoriaprod_id);
            }else{
                $aux_categoriaprodid = $request->categoriaprod_id;
            }
            $aux_condcategoriaprod_id = " producto.categoriaprod_id in ($aux_categoriaprodid) ";
        }
        if(!isset($request->groupby) or empty($request->groupby)){
            $aux_groupby = " group by notaventadetalle.id ";
        }else{
            $aux_groupby = $request->groupby;
        }
        if(!isset($request->orderby) or empty($request->orderby)){
            $aux_orderby = "order by notaventadetalle.id desc ";
        }else{
            $aux_orderby = $request->orderby;
        }

        if($aux_sql==1){
            $sql = "SELECT notaventadetalle.notaventa_id as id,notaventa.fechahora,notaventa.cliente_id,notaventa.comuna_id,notaventa.comunaentrega_id,
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
            $sql = "SELECT notaventa.fechahora,notaventadetalle.producto_id,
            sum(notaventadetalle.cant) as cant,sum(if(isnull(vista_sumorddespxnvdetid.cantdesp),0,vista_sumorddespxnvdetid.cantdesp)) AS cantdesp,
            producto.nombre,cliente.razonsocial,notaventadetalle.id,
            notaventadetalle.notaventa_id,oc_file,
            producto.diametro,notaventa.oc_id,
            claseprod.cla_nombre,producto.long,producto.peso,producto.tipounion,
            notaventadetalle.totalkilos,
            subtotal,notaventa.comunaentrega_id,notaventa.plazoentrega,
            notaventadetalle.precioxkilo
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
            $aux_groupby
            $aux_orderby;";
            
        }
        $datas = DB::select($sql);
        return $datas;
        
    }

    public static function productosxUsuarioRep($request){
        $users = Usuario::findOrFail(auth()->id());
        if($request->sucursal_id){
            $sucurArray = [$request->sucursal_id];
        }else{
            $sucurArray = $users->sucursales->pluck('id')->toArray();
        }
        $sucurArray = implode ( ',' , $sucurArray);
        if(!isset($request->sucursal_id) or empty($request->sucursal_id) or $request->sucursal_id == "x"){
            //$aux_condsucursal_id = " true ";
            $aux_condsucursal_id = " categoriaprodsuc.sucursal_id in (0)";
        }else{
            if(is_array($request->sucursal_id)){
                $aux_sucursal = implode ( ',' , $request->sucursal_id);
            }else{
                $aux_sucursal = $request->sucursal_id;
            }
            $aux_condsucursal_id = " (categoriaprodsuc.sucursal_id in ($aux_sucursal) and categoriaprodsuc.sucursal_id in ($sucurArray))";
        }

        if(!isset($request->producto_id) or empty($request->producto_id)){
            $aux_producto_idCodn = "true";
        }else{
            $aux_producto_idCodn = " producto.id in ($request->producto_id) ";
        }

        if(!isset($request->categoriaprod_id) or empty($request->categoriaprod_id)){
            $aux_categoriaprod_idCond = "true";
        }else{
            $aux_categoriaprodid = $request->categoriaprod_id;
            if(is_array($request->categoriaprod_id)){
                $aux_categoriaprodid = implode(",", $request->categoriaprod_id);
            }
            $aux_categoriaprod_idCond = " producto.categoriaprod_id in ($aux_categoriaprodid) ";
        }
        if(!isset($request->areaproduccion_id) or empty($request->areaproduccion_id)){
            $aux_areaproduccion_idCond = "true";
        }else{
            $aux_areaproduccionid = $request->areaproduccion_id;
            if(is_array($request->areaproduccion_id)){
                $aux_areaproduccionid = implode(",", $request->areaproduccion_id);
            }
            $aux_areaproduccion_idCond = " categoriaprod.areaproduccion_id in ($aux_areaproduccionid) ";
        }




        $sql = "SELECT producto.id as producto_id,producto.nombre as producto_nombre,claseprod.cla_nombre,producto.codintprod,
            producto.diamextmm,producto.diamextpg,
            producto.diametro,producto.espesor,producto.long,producto.peso,producto.tipounion,producto.precioneto,
            categoriaprod.nombre as categoria_nombre,categoriaprod.precio,categoriaprodsuc.sucursal_id,categoriaprod.unidadmedida_id,
            producto.precioneto
            FROM producto INNER JOIN claseprod
            ON producto.claseprod_id=claseprod.id AND isnull(producto.deleted_at) AND isnull(claseprod.deleted_at)
            INNER JOIN categoriaprod
            ON producto.categoriaprod_id=categoriaprod.id AND isnull(categoriaprod.deleted_at)
            INNER JOIN categoriaprodsuc
            ON categoriaprod.id = categoriaprodsuc.categoriaprod_id AND isnull(categoriaprodsuc.deleted_at)
            INNER JOIN sucursal
            ON categoriaprodsuc.sucursal_id = sucursal.id AND isnull(sucursal.deleted_at)
            WHERE $aux_areaproduccion_idCond
            and $aux_producto_idCodn
            and $aux_categoriaprod_idCond
            and $aux_condsucursal_id
            ORDER BY producto.id;";
            
        $datas = DB::select($sql);
        return $datas;
        
    }

}
