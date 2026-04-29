<?php

namespace App\Models;

use App\Models\Seguridad\Usuario;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use SplFileInfo;

class NotaVenta extends Model
{
    use SoftDeletes;
    protected $table = "notaventa";
    protected $fillable = [
        'sucursal_id',
        'centroeconomico_id',
        'cotizacion_id',
        'fechahora',
        'direccioncot',
        'email',
        'telefono',
        'cliente_id',
        'clientedirec_id',
        'contacto',
        'contactoemail',
        'contactotelf',
        'observacion',
        'formapago_id',
        'vendedor_id',
        'plazoentrega',
        'lugarentrega',
        'plazopago_id',
        'tipoentrega_id',
        'region_id',
        'provincia_id',
        'comuna_id',
        'comunaentrega_id',
        'giro_id',
        'neto',
        'piva',
        'iva',
        'total',
        'moneda_id',
        'oc_id',
        'oc_file',
        'usuario_id',
        'aprobstatus',
        'aprobusu_id',
        'aprobfechahora',
        'visto',
        'usuariodel_id',
        'stadestino'
    ];

    public static function setFotonotaventa($foto,$notaventa_id,$request, $actual = false){
        //dd($foto);
        if ($foto) {
            if ($actual) {
                Storage::disk('public')->delete("imagenes/notaventa/$actual");
            }
            $file = $request->file('oc_file');
            $nombre = $file->getClientOriginalName();
            $info = new SplFileInfo($nombre);
            $ext = strtolower($info->getExtension()); //Obtener extencion de un archivo
            //$imageName = Str::random(10) . '.jpg';
            $imageName = $notaventa_id . '.' . $ext;
            //dd($imageName);
            //      $imagen = Image::make($foto)->encode('jpg', 75);
            //$imagen->fit(530, 470); //Fit() SUpuestamente mantiene la proporcion de la imagen
            /*$imagen->resize(530, 470, function ($constraint) {
                $constraint->upsize();
            });*/
            //Storage::disk('public')->put("imagenes/notaventa/$imageName", $imagen->stream());
            //Storage::disk('public')->put("imagenes/notaventa/$imageName", $file);
            $file->move(public_path() . "/storage/imagenes/notaventa/" , $imageName);
            //$request->file('')
            return $imageName;
        } else {
            if ($actual) {
                Storage::disk('public')->delete("imagenes/notaventa/$actual");
                return "null";
            }else{
                return false;
            }
        }
    }

    //RELACION DE UNO A MUCHOS NotaVentaDetalle
    public function notaventadetalles()
    {
        return $this->hasMany(NotaVentaDetalle::class,'notaventa_id');
    }
    //Relacion inversa a Cotizacion
    public function cotizacion()
    {
        return $this->belongsTo(Cotizacion::class);
    }

    //RELACION INVERSA Cliente
    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }
    //RELACION INVERSA ClienteDirecc
    public function clientedirec()
    {
        return $this->belongsTo(ClienteDirec::class);
    }
    //Relacion inversa a FormaPago
    public function formapago()
    {
        return $this->belongsTo(FormaPago::class);
    }
    //Relacion inversa a Vendedor
    public function vendedor()
    {
        return $this->belongsTo(Vendedor::class);
    }
    //Relacion inversa a PlazoPago
    public function plazopago()
    {
        return $this->belongsTo(PlazoPago::class);
    }
    //Relacion inversa a Comuna
    public function comuna()
    {
        return $this->belongsTo(Comuna::class);
    }
    public function comunaentrega()
    {
        return $this->belongsTo(Comuna::class,'comunaentrega_id');
    }
    //Relacion inversa a Sucursal
    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }
    //Relacion inversa a TipoEntrega
    public function tipoentrega()
    {
        return $this->belongsTo(TipoEntrega::class);
    }
    //RELACION DE UNO A MUCHOS NotaVentaCerrada
    public function notaventacerradas()
    {
        return $this->hasMany(NotaVentaCerrada::class,'notaventa_id');
    }
    //RELACION UNO A UNO NotaVentaCerrada
    public function notaventacerrada()
    {
        return $this->hasOne(NotaVentaCerrada::class,'notaventa_id');
    }
    
    //Relacion inversa a Giro
    public function giro()
    {
        return $this->belongsTo(Giro::class);
    }
    //Relacion inversa a Despachoobs
    public function despachoobs()
    {
        return $this->belongsTo(DespachoObs::class);
    }
    //RELACION DE UNO A MUCHOS dteguiadespnv
    public function dteguiadespnvs()
    {
        return $this->hasMany(DteGuiaDespNV::class,'notaventa_id');
    }
    //Relacion inversa a Moneda
    public function moneda()
    {
        return $this->belongsTo(Moneda::class);
    }
    //RELACION DE UNO A MUCHOS DespachoOrd
    public function despachoords()
    {
        return $this->hasMany(DespachoOrd::class,'notaventa_id');
    }

    //RELACION DE UNO A MUCHOS DespachoSol
    public function despachosols()
    {
        return $this->hasMany(DespachoSol::class,'notaventa_id');
    }
    
    //RELACION UNO A UNO clientedesbloqueado
    public function clientedesbloqueado()
    {
        return $this->hasOne(ClienteDesBloqueado::class,'notaventa_id');
    }

    //Relacion inversa a CentroEconomico
    public function centroeconomico()
    {
        return $this->belongsTo(CentroEconomico::class);
    }
    
    //Relacion inversa a Usuario
    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }

    //Relacion inversa a Usuario quien aprobo NV
    public function usuarioaprob()
    {
        return $this->belongsTo(Usuario::class, 'aprobusu_id');
    }    

    public static function consulta($request,$aux_consulta){
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
            $aux_rut = str_replace(".","",$request->rut);
            $aux_rut = str_replace("-","",$aux_rut);
            $aux_condrut = "cliente.rut='$aux_rut'";
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
    
        $aux_aprobstatus = "";
        //dd($request->aprobstatus);
        if(is_array($request->aprobstatus)){
            if(!empty($request->aprobstatus)){
                if(in_array('1',$request->aprobstatus)){
                    $aux_aprobstatus = " notaventa.aprobstatus='0'";
                }
                if(in_array('2',$request->aprobstatus)){
                    
                    $aux_aprobstatus .= " or notaventa.aprobstatus='2'";
                }
                if(in_array('3',$request->aprobstatus)){
                    $aux_aprobstatus .= " or (notaventa.aprobstatus='1' or notaventa.aprobstatus='3')";
                }
                if(in_array('4',$request->aprobstatus)){
                    $aux_aprobstatus .= " or notaventa.aprobstatus='4'";
                }
                if(in_array('7',$request->aprobstatus)){
                    $aux_aprobstatus .= " or notaventa.id in (SELECT notaventa_id
                                                            FROM notaventacerrada
                                                            WHERE ISNULL(notaventacerrada.deleted_at))";
                }
                if(in_array('8',$request->aprobstatus)){
                    $aux_aprobstatus .= " or !isnull(notaventa.anulada)";
                }
            }
        }else{
            //dd($request->aprobstatus);
            switch ($request->aprobstatus) {
                case 1:
                    $aux_aprobstatus = " notaventa.aprobstatus='0'";
                    break;
                case 2:
                    $aux_aprobstatus = " notaventa.aprobstatus='$request->aprobstatus'";
                    break;    
                case 3:
                    $aux_aprobstatus = " (notaventa.aprobstatus='1' or notaventa.aprobstatus='3')";
                    break;
                case 4:
                    $aux_aprobstatus = " notaventa.aprobstatus='$request->aprobstatus'";
                    break;
                case 7:
                    $aux_aprobstatus = " notaventa.id in (SELECT notaventa_id
                                                            FROM notaventacerrada
                                                            WHERE ISNULL(notaventacerrada.deleted_at))";
                    break;
                case 8:
                    $aux_aprobstatus = " !isnull(notaventa.anulada)";
                    break;
                }
        }
        if (empty($aux_aprobstatus)){
            $aux_aprobstatus = " true ";
        }else{
            if (substr($aux_aprobstatus, 0, 4) == " or "){
                $aux_aprobstatus = substr($aux_aprobstatus, 4, 500);
            }
            $aux_aprobstatus = "(" . $aux_aprobstatus . ")";    
        }
        //dd($aux_aprobstatus);
        
        $aux_condproducto_id = " true";
        if(!empty($request->producto_idM)){
            $aux_condproducto_id = str_replace(".","",$request->producto_idM);
            $aux_condproducto_id = str_replace("-","",$aux_condproducto_id);
            $aux_condproducto_id = "notaventadetalle.producto_id='$aux_condproducto_id'";
        }
        if(isset($request->producto_id) or !empty($request->producto_id)){   
            $aux_codprod = explode(",", $request->producto_id);
            $aux_codprod = implode ( ',' , $aux_codprod);
            $aux_condproducto_id = "notaventadetalle.producto_id in ($aux_codprod)";
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
        $aux_condsucursal_id = " notaventa.sucursal_id in ($sucurArray) ";

        if(!isset($request->sucursal_id) or empty($request->sucursal_id) or ($request->sucursal_id == "")){
            $aux_sucursal_idCond = "true";
        }else{
            $aux_sucursal_idCond = "notaventa.sucursal_id = $request->sucursal_id";
        }
        if(!isset($request->group) or empty($request->group) or ($request->group == "")){
            $cond_group = "GROUP BY notaventadetalle.notaventa_id,notaventa.fechahora,notaventa.cliente_id,notaventa.comuna_id,notaventa.comunaentrega_id,
            notaventa.oc_id,notaventa.anulada,cliente.rut,cliente.razonsocial,aprobstatus,visto,oc_file,
            notaventa.inidespacho,notaventa.guiasdespacho,notaventa.findespacho";
        }else{
            $cond_group = $request->group;
        }
        if(!isset($request->order) or empty($request->order) or ($request->order == "")){
            $cond_order = "";
        }else{
            $cond_order = $request->order;
        }

        if(!isset($request->notaventa_id) or empty($request->notaventa_id)){
            $aux_condnotaventa_id = " true";
        }else{
            $aux_condnotaventa_id = "notaventa.id='$request->notaventa_id'";
            $aux_condFecha = " true";
            $aux_aprobstatus = " true";
        }
        if(!isset($request->oc_id) or empty($request->oc_id)){
            $aux_condoc_id = " true";
        }else{
            $aux_condoc_id = "notaventa.oc_id='$request->oc_id'";
            $aux_condFecha = " true";
            $aux_aprobstatus = " true";
        }

        if(!isset($request->categoriaprod_id) or empty($request->categoriaprod_id)){
            $aux_condcategoriaprod_id = " true";
        }else{
            //$aux_condcategoriaprod_id = "categoriaprod.id='$request->categoriaprod_id'";
            if(is_array($request->categoriaprod_id)){
                $aux_categoriaprodid = implode ( ',' , $request->categoriaprod_id);
            }else{
                $aux_categoriaprodid = $request->categoriaprod_id;
            }
            $aux_condcategoriaprod_id = " producto.categoriaprod_id in ($aux_categoriaprodid) ";
        }
        if(!isset($request->claseprod_id) or empty($request->claseprod_id)){
            $aux_condclaseprod_id = " true";
        }else{
            //$aux_condclaseprod_id = "claseprod.id='$request->claseprod_id'";
            if(is_array($request->claseprod_id)){
                $aux_claseprodid = implode ( ',' , $request->claseprod_id);
            }else{
                $aux_claseprodid = $request->claseprod_id;
            }
            $aux_condclaseprod_id = " EXISTS (
				    SELECT 1
				    FROM notaventadetalle nd2
				    INNER JOIN producto p2 ON p2.id = nd2.producto_id
				    WHERE nd2.notaventa_id = notaventa.id
				    AND p2.claseprod_id IN ($aux_claseprodid)
				)";
        }
        //dd($aux_condclaseprod_id);
        if($aux_consulta == 1){
            $sql = "SELECT notaventadetalle.notaventa_id as id,notaventa.fechahora,
            notaventadetalle.id as notaventadetalle_id,notaventadetalle.producto_id,producto.glosa,
            notaventa.cliente_id,notaventa.comuna_id,
            notaventa.comunaentrega_id,
            notaventa.oc_id,notaventa.anulada,cliente.rut,cliente.razonsocial,aprobstatus,visto,oc_file,
            sum(notaventadetalle.cant) AS cant,sum(notaventadetalle.precioxkilo) AS precioxkilo,
            sum(notaventadetalle.totalkilos) AS totalkilos,sum(notaventadetalle.subtotal) AS subtotal,
            sum(if(areaproduccion.id=1,notaventadetalle.totalkilos,0)) AS pvckg,
            sum(if(areaproduccion.id=2,notaventadetalle.totalkilos,0)) AS cankg,
            sum(if(areaproduccion.id=1,notaventadetalle.subtotal,0)) AS pvcpesos,
            sum(if(areaproduccion.id=2,notaventadetalle.subtotal,0)) AS canpesos,
            sum(notaventadetalle.subtotal) AS totalps,
            ROUND(sum(notaventadetalle.subtotal * ((notaventa.piva + 100) /100) ),0) AS total,
            comuna.nombre as comunanombre,
            notaventa.inidespacho,notaventa.guiasdespacho,notaventa.findespacho,
            cliente.limitecredito,
            IFNULL(vista_datacobranza.tfac,0) AS datacobranza_tfac,
            IFNULL(vista_datacobranza.tdeuda,0) AS datacobranza_tdeuda,
            IFNULL(vista_datacobranza.tdeudafec,0) AS datacobranza_tdeudafec,
            IFNULL(vista_datacobranza.nrofacdeu,'') AS datacobranza_nrofacdeu
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
            INNER JOIN comuna
            ON comuna.id=notaventa.comunaentrega_id
            LEFT JOIN vista_datacobranza
            ON vista_datacobranza.cliente_id = notaventa.cliente_id
            WHERE $vendedorcond
            and $aux_condFecha
            and $aux_condrut
            and $aux_condoc_id
            and $aux_condgiro_id
            and $aux_condareaproduccion_id
            and $aux_condtipoentrega_id
            and $aux_condnotaventa_id
            and $aux_aprobstatus
            and $aux_condproducto_id
            and $aux_condcomuna_id
            and $aux_condsucursal_id
            and $aux_sucursal_idCond
            and $aux_condcategoriaprod_id
            and isnull(notaventa.deleted_at) and isnull(notaventadetalle.deleted_at)
            and $aux_condclaseprod_id
            $cond_group
            $cond_order;";
        }
                //and notaventa.id not in (select notaventa_id from notaventacerrada where isnull(notaventacerrada.deleted_at))
        if($aux_consulta == 2){
            $sql = "SELECT areaproduccion_id,areaproduccion.nombre,
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
            WHERE $vendedorcond
            and $aux_condFecha
            and $aux_condrut
            and $aux_condoc_id
            and $aux_condgiro_id
            and $aux_condareaproduccion_id
            and $aux_condtipoentrega_id
            and $aux_condnotaventa_id
            and $aux_aprobstatus
            and $aux_condproducto_id
            and $aux_condcomuna_id
            and $aux_sucursal_idCond
            and $aux_condcategoriaprod_id
            and $aux_condclaseprod_id
            and isnull(notaventa.deleted_at) and isnull(notaventadetalle.deleted_at)
            GROUP BY areaproduccion_id,areaproduccion.nombre;";
        }
        //dd($sql);
        $datas = DB::select($sql);
        return $datas;
    }

    public static function consultatotcantodOld($id){
        //TOMANDO EN CUENTA QUE EN PLANTA SANTA ESTER PERMITE DESPACHAR POR ENCIMA DEL LA CANT EN NV
        //VALIDAR, SI LA CANTIDAD DESPACHADA ES MAYOR AL ITEM DE LA NV, SE DEBE TOMAR PARA CONTROL LA CANTIDAD DE LA NV
        //ESTO PARA NO SUMAR LO TOTAL DESPACHADO, YA QUE PUEDE SOBREPASAR EL TOTAL EN CANTIDAD DE LA NV
        //DANDO ASI UN VALOR ERRONEO, COMO SI LA NV YA ESTUVIESE TOTALMENTE DESPACHADA Y NO ES ASI.
        $sql = "SELECT despachoord.notaventa_id,notaventadetalle.id as notaventadetalle_id,notaventadetalle.producto_id,
                notaventadetalle.cant AS cantnv
                FROM despachoord JOIN despachoorddet 
                ON despachoord.id = despachoorddet.despachoord_id
                INNER JOIN notaventadetalle
                ON notaventadetalle.id = despachoorddet.notaventadetalle_id
                WHERE NOT(despachoord.id IN (SELECT despachoordanul.despachoord_id FROM despachoordanul))
                and despachoord.guiadespacho is not null
                and despachoord.notaventa_id = $id
                and isnull(despachoord.deleted_at) and isnull(despachoorddet.deleted_at)
                group by notaventadetalle.id;";
        //dd("$sql");
        $nvdets = DB::select($sql);
        $aux_totalcantnv = 0;
        $aux_cantdesptotalmax = 0;
        if($nvdets){
            foreach ($nvdets as $nvdet) {
                $aux_totalcantnv += $nvdet->cantnv;

                $sql = "SELECT notaventadetalle.notaventa_id,notaventadetalle.id as notaventadetalle_id,
                        notaventadetalle.producto_id,notaventadetalle.cant AS cantnv,
                        if(isnull(vista_sumorddespxnvdetid.cantdesp),0,vista_sumorddespxnvdetid.cantdesp) AS canddespreal
                        FROM notaventadetalle LEFT JOIN vista_sumorddespxnvdetid
                        ON notaventadetalle.id=vista_sumorddespxnvdetid.notaventadetalle_id
                        WHERE notaventadetalle.id = $nvdet->notaventadetalle_id
                        ORDER by notaventadetalle.id;";
                //dd("$sql");
                $datas = DB::select($sql);
                //dd($datas);
                if($datas){
                    if($datas[0]->canddespreal > $nvdet->cantnv){
                        $aux_cantdesptotalmax += $nvdet->cantnv;
                    }else{
                        $aux_cantdesptotalmax += $datas[0]->canddespreal;
                    }
                }
            }
        }
        return $aux_cantdesptotalmax;
        //DE AQUI PARA ABAJO FUE SUSTITUIDO POR LO DE ARRIBA 05/04/2024
        //cantdesptopenv = CANTIDAD TOPE DE DESPACHO SEGUN NOTA DE VENTA
        //EN SANTA ESTER SE PUEDE DESPACHAR MAS DE LO QUE DICE LA NOTA DE VENTA
        $sql = "SELECT despachoord.notaventa_id,notaventadetalle.id as notaventadetalle_id,notaventadetalle.producto_id,
                sum(notaventadetalle.cant) AS cantnv,sum(cantdesp) AS canddespreal,
                if(sum(cantdesp)>notaventadetalle.cant,sum(notaventadetalle.cant),sum(cantdesp)) AS cantdesptopenv
                FROM despachoord JOIN despachoorddet 
                ON despachoord.id = despachoorddet.despachoord_id
                INNER JOIN notaventadetalle
                ON notaventadetalle.id = despachoorddet.notaventadetalle_id
                WHERE NOT(despachoord.id IN (SELECT despachoordanul.despachoord_id FROM despachoordanul))
                and despachoord.guiadespacho is not null
                and despachoord.notaventa_id = $id
                and isnull(despachoord.deleted_at) and isnull(despachoorddet.deleted_at)
                group by despachoorddet.id;";
        //dd("$sql");
        $datas = DB::select($sql);
        //dd($datas);
        $aux_cant = 0;
        if($datas){
            foreach ($datas as $data) {
                $aux_cant += $data->cantdesptopenv;
            }
            //$aux_cant = $datas[0]->cantdesp;
            $sql = "SELECT sum(despachoordrecdet.cantrec) AS cantrec
            FROM despachoordrecdet INNER JOIN despachoordrec
            ON despachoordrecdet.despachoordrec_id=despachoordrec.id AND ISNULL(despachoordrec.anulada) AND ISNULL(despachoordrec.deleted_at) AND ISNULL(despachoordrecdet.deleted_at)
            INNER JOIN despachoord
            ON despachoord.id = despachoordrec.despachoord_id AND ISNULL(despachoord.deleted_at)
            WHERE despachoord.notaventa_id=$id
            AND despachoordrec.aprobstatus=2
            and NOT(despachoord.id IN (SELECT despachoordanul.despachoord_id FROM despachoordanul WHERE ISNULL(despachoordanul.deleted_at)));";
            $datas = DB::select($sql);
            if($datas){
                $aux_cant -= $datas[0]->cantrec;
            }    
        }
        return $aux_cant;
    }

    /**
     * VERSIÓN ULTRA OPTIMIZADA - Todo en una sola consulta SQL
     */
    public static function consultatotcantod($notaventa_id)
    {
        $sql = "
            SELECT 
                nvdet.id AS notaventadetalle_id,
                nvdet.cant AS cantnv,
                COALESCE((
                    SELECT SUM(oddet.cantdesp)
                    FROM despachoorddet oddet
                    INNER JOIN despachoord od ON oddet.despachoord_id = od.id
                    WHERE oddet.notaventadetalle_id = nvdet.id
                    AND od.guiadespacho IS NOT NULL
                    AND od.deleted_at IS NULL
                    AND oddet.deleted_at IS NULL
                    AND NOT EXISTS (
                        SELECT 1 FROM despachoordanul 
                        WHERE despachoordanul.despachoord_id = od.id 
                            AND despachoordanul.deleted_at IS NULL
                    )
                ), 0) AS total_despachado,
                COALESCE((
                    SELECT SUM(rdet.cantrec)
                    FROM despachoorddet oddet
                    INNER JOIN despachoord od ON oddet.despachoord_id = od.id
                    INNER JOIN despachoordrec rec ON od.id = rec.despachoord_id
                    INNER JOIN despachoordrecdet rdet ON rec.id = rdet.despachoordrec_id
                    WHERE oddet.notaventadetalle_id = nvdet.id
                    AND rec.aprobstatus = 2
                    AND rec.anulada IS NULL
                    AND rec.deleted_at IS NULL
                    AND rdet.deleted_at IS NULL
                    AND od.guiadespacho IS NOT NULL
                    AND od.deleted_at IS NULL
                    AND oddet.deleted_at IS NULL
                    AND NOT EXISTS (
                        SELECT 1 FROM despachoordanul 
                        WHERE despachoordanul.despachoord_id = od.id 
                            AND despachoordanul.deleted_at IS NULL
                    )
                ), 0) AS total_rechazado
            FROM notaventadetalle nvdet
            WHERE nvdet.notaventa_id = ?
            AND EXISTS (
                SELECT 1
                FROM despachoorddet oddet
                INNER JOIN despachoord od ON oddet.despachoord_id = od.id
                WHERE oddet.notaventadetalle_id = nvdet.id
                    AND od.guiadespacho IS NOT NULL
                    AND od.deleted_at IS NULL
                    AND oddet.deleted_at IS NULL
                    AND NOT EXISTS (
                        SELECT 1 FROM despachoordanul 
                        WHERE despachoordanul.despachoord_id = od.id 
                        AND despachoordanul.deleted_at IS NULL
                    )
            )
            GROUP BY nvdet.id, nvdet.cant
        ";
        
        $resultados = DB::select($sql, [$notaventa_id]);
        
        $total = 0;
        foreach ($resultados as $row) {
            $neto = $row->total_despachado - $row->total_rechazado;
            if ($neto > $row->cantnv) {
                $total += $row->cantnv;
            } else {
                $total += $neto;
            }
        }
        
        return $total;
    }

    public static function consultagrupcatprom($request){
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
            $aux_codvend = $request->vendedor_id;
            if(is_array($request->vendedor_id)){
                $aux_codvend = implode ( ',' , $request->vendedor_id);
            }
            $vendedorcond = "notaventa.vendedor_id in ($aux_codvend)";
        }
    
        if(empty($request->fechad) or empty($request->fechah)){
            $aux_condFecha = " true";
        }else{
            $fecha = date_create_from_format('d/m/Y', $request->fechad);
            $fechad = date_format($fecha, 'Y-m-d') . " 00:00:00";
            $fecha = date_create_from_format('d/m/Y', $request->fechah);
            $fechah = date_format($fecha, 'Y-m-d') . " 23:59:59";
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
    
        if(empty($request->comuna_id)){
            $aux_condcomuna_id = " true";
        }else{
            $aux_condcomuna_id = "notaventa.comunaentrega_id='$request->comuna_id'";
        }

        $sucurArray = $user->sucursales->pluck('id')->toArray();
        $sucurcadena = implode(",", $sucurArray);
        
        if(!isset($request->areaproduccion_id) AND empty($request->areaproduccion_id)){
            $aux_condareaproduccion_id = " true";
        }else{
            $aux_condareaproduccion_id = "categoriaprod.areaproduccion_id IN ($request->areaproduccion_id)";
        }

        if(!isset($request->sucursal_id) or empty($request->sucursal_id) or ($request->sucursal_id == "")){
            $aux_sucursal_idCond = "true";
        }else{
            $aux_sucursal_idCond = "notaventa.sucursal_id = $request->sucursal_id";
        }

        $aux_condproducto_id = " true";
        if(!empty($request->producto_id)){
            $aux_codprod = explode(",", $request->producto_id);
            $aux_codprod = implode ( ',' , $aux_codprod);
            $aux_condproducto_id = "notaventadetalle.producto_id in ($aux_codprod)";
        }
        
        $sql = "SELECT grupocatprom.nombre,notaventa.vendedor_id,
        persona.rut as vendedor_rut,CONCAT(persona.nombre,' ',persona.apellido) AS vendedor_nombre,
        SUM(notaventadetalle.totalkilos) AS totalkilos,SUM(subtotal) AS subtotal
        FROM notaventa INNER JOIN notaventadetalle
        ON notaventadetalle.notaventa_id = notaventa.id AND ISNULL(notaventa.deleted_at) 
        AND ISNULL(notaventadetalle.deleted_at) AND ISNULL(notaventa.anulada)
        INNER JOIN cliente
        ON cliente.id = notaventa.cliente_id AND isnull(cliente.deleted_at)
        INNER JOIN comuna
        ON comuna.id = notaventa.comuna_id AND isnull(comuna.deleted_at)
        LEFT JOIN vendedor
        ON notaventa.vendedor_id=vendedor.id and isnull(vendedor.deleted_at)
        LEFT JOIN persona
        ON vendedor.persona_id=persona.id and isnull(persona.deleted_at)
        LEFT JOIN usuario
        ON notaventa.usuario_id=usuario.id
        INNER JOIN producto
        ON producto.id = notaventadetalle.producto_id
        INNER JOIN categoriaprod
        ON categoriaprod.id = producto.categoriaprod_id
        INNER JOIN grupocatpromcategoriaprod
        ON grupocatpromcategoriaprod.categoriaprod_id = categoriaprod.id
        INNER JOIN grupocatprom
        ON grupocatprom.id = grupocatpromcategoriaprod.grupocatprom_id and isnull(grupocatprom.deleted_at)
        WHERE $vendedorcond
        AND $aux_condFecha
        AND $aux_condrut
        AND $aux_condoc_id
        AND $aux_condgiro_id
        AND $aux_condtipoentrega_id
        AND $aux_condnotaventa_id
        AND $aux_condcomuna_id
        AND notaventa.sucursal_id in ($sucurcadena)
        AND $aux_sucursal_idCond
        AND $aux_condproducto_id
        AND $aux_condareaproduccion_id
        AND notaventadetalle.totalkilos > 0
        GROUP BY grupocatprom.id,notaventa.vendedor_id
        ORDER BY notaventa.vendedor_id,grupocatprom.nombre;";
        //dd($sql);
        $arrays = DB::select($sql);
        /*
        $i = 0;
        foreach ($arrays as $array) {
            $arrays[$i]->rutacrear = route('crear_factura', ['id' => $array->id]);
            $i++;
        }*/
        foreach ($arrays as &$array) {
            //dd($array->totalkilos);
            $array->promedio = round(($array->subtotal / $array->totalkilos),2);
        }
        //dd($arrays);
        return $arrays;
    }
    public static function totaldineropendNV($cliente_id,$request){
        $aux_cliente = Cliente::findOrFail($cliente_id);
        //dd($aux_cliente->sucursales);
        $request->merge(['fechad' => null]);
        $request->request->set('fechad', null);
        $request->merge(['fechah' => "10/06/2024"]);
        $request->request->set('fechah', "10/06/2024");
        $request->merge(['aprobstatus' => "3"]);
        $request->request->set('aprobstatus', "3");
        $aux_Tdeudapxp = 0;
        foreach ($aux_cliente->sucursales as $sucursal) {
            $request->merge(['sucursal_id' => $sucursal->id]);
            $request->request->set('sucursal_id', $sucursal->id);
            //ASIGNO BLANCO A producto_id PORQUE SE VIENE CON UN VALOR
            $request->merge(['producto_id' => ""]);
            $request->request->set('producto_id', "");
            $pendxprod = Producto::pendxprod($request);
            for ($i = 0; $i < count($pendxprod->original["data"]); $i++) {
                $aux_Tdeudapxp += $pendxprod->original["data"][$i]["subtotalplata"];
            }
        }
        return $aux_Tdeudapxp;
    }

    public static function pendDespPendFact($cliente_id,$request = null){
        //NotaVentaPendDesp::truncate();
        if(!isset($request->consultarnvpendfact) or $request->consultarnvpendfact == 0){
            return [
                "cant" => 0, 
                "TotalNVPendDesp" => 0, 
                "IDsNVPendDesp" => "",
                "TotalDteguiasPend" => 0,
                "IDsDteguiasPend" => "",
            ];
        }
        $cliente = Cliente::findOrFail($cliente_id);
        //dd($cliente->sucursales);
        $aux_cant = 0;
        $aux_total = 0;
        $TotalDteguiasPend = 0;
        $IDsDteguiasPends = [];
        $aux_idnvs = [];
        $request1 = new Request();
        $request1->merge([
            "fechad" => null,
            "fechah" => null,
            "rut" => $cliente->rut,
            "vendedor_id" => null,
            "oc_id" => null,
            "tipoentrega_id" => null,
            "notaventa_id" => null,
            "aprobstatus" => "3",
            "comuna_id" => null,
            "despachoord_id" => null,
            "filtro" => "1",
            "dtenotnull" => "1",
            "dteguiausada" => "1",
            "sucursal_id" => null,
            "centroeconomico_id" => null,
            'aux_condindtraslado' => "indtraslado != 6"
        ]);
        //dd($request1);
        $dteguias = Dte::consultalistarguiadesppage($request1);
        foreach ($dteguias as $dteguia) {
            $dte = Dte::findOrFail($dteguia->id);
            foreach ($dte->dtedets as $dtedet) {
                //dd($dtedet->dtedet_despachoorddet);
                if(isset($dtedet->dtedet_despachoorddet)){
                    $notaventa_id = $dtedet->dtedet_despachoorddet->notaventadetalle->notaventa_id;
                    if (!in_array($notaventa_id, $aux_idnvs, true)) {
                        $idnvs[] = $notaventa_id;
                    }
                    //$aux_idnvs[] = $dtedet->dtedet_despachoorddet->notaventadetalle->notaventa_id;
                    $aux_cant += $dtedet->qtyitem;
                    $aux_total += $dtedet->montoitem * (($dte->tasaiva / 100) + 1);
                    $TotalDteguiasPend += $dtedet->montoitem * (($dte->tasaiva / 100) + 1);
                    $IDsDteguiasPends[] = $dtedet->dte->nrodocto;
                }
            }
            //dd($dte->id);
        }
        //dd("entro");
        //SOLO EJECUTA SI EL REQUEST TIENE EL CAMPO staconsNvPendDesp Y SU VALOR ES 1
        //ESTO PARA NO HACER LA CONSULTA DE LO QUE ESTA PENDIENTE DE DESPACHO EN EL MODULO DE ENVIAR A GESTION LAS NOTA DE VENTA
        //dd($request);
        if(isset($request->staconsNVPendDesp) and $request->staconsNVPendDesp == 1){
            $request2 = new Request();
            foreach($cliente->sucursales as $sucursal){
                /* $request2->merge(['sucursal_id' => $sucursal->id]);
                $request2->merge(['sta_devarray' => 1]); */
                $request2->merge([
                    "fechad" => null,
                    "fechah" => date("Y-m-d"),
                    "plazoentregad" => null,
                    "plazoentregah" => date("Y-m-d"),
                    "rut" => $cliente->rut,
                    "vendedor_id" => null,
                    "oc_id" => null,
                    "giro_id" => null,
                    "areaproduccion_id" => null,
                    "tipoentrega_id" => null,
                    "notaventa_id" => null,
                    "aprobstatus" => "3",
                    "aprobstatusdesc" => "Aprobadas",
                    "comuna_id" => null,
                    "dte_id" => "undefined",
                    "producto_id" => null,
                    "categoriaprod_id" => null,
                    "sucursal_id" => $sucursal->id,
                    "filtro" => "0",
                    "filtroacutec" => "0",
                    'sta_devarray' => 1,
                    'staConsStock' => isset($request->staConsStock) ? $request->staConsStock : 1,
                ]);
                //dd($request2);
                /* foreach ($clientes as $cliente) {
                    $request2->merge(['rut' => $cliente->rut]);
                } */
                
                $datas = Producto::pendxprod($request2);
                //dd($datas); 
                foreach ($datas as $data) {
                    $notaventa_id = $data->notaventa_id;
                    //echo $notaventa_id;
                    //$notaventa = Notaventa::findOrFail($request->notaventa_id);
                    if (!in_array($notaventa_id, $aux_idnvs, true)) {
                        $aux_idnvs[] = $data->notaventa_id;
                    }
                    $aux_cant += $data->cantsaldo;
                    $aux_total += $data->subtotalplata * (($data->piva / 100) + 1);
                }
            }
        }
        //dd($aux_total);
        //VALIDAR SI EL REQUEST TIENE EL CAMPO notaventa_id Y SI NO ES VACIO
        //SI ES ASI, SE DEBE VALIDAR SI LA NOTA DE VENTA TIENE APROBSTATUS 1 O 3, Y SI ES ASI, SE SUMA EL TOTAL A LA VARIABLE $aux_total
        if(isset($request->notaventa_id) and !empty($request->notaventa_id)){
            $notaventa = Notaventa::findOrFail($request->notaventa_id);
            if($notaventa->aprobstatus == null or $notaventa->aprobstatus == 0){
                $aux_total += $notaventa->total;
                $aux_idnvs[] = $notaventa->id;
            }
        }
        return [
            "cant" => $aux_cant, 
            "TotalNVPendDesp" => round($aux_total), 
            "IDsNVPendDesp" => implode(',', $aux_idnvs),
            "TotalDteguiasPend" => round($TotalDteguiasPend),
            "IDsDteguiasPend" => implode(',', $IDsDteguiasPends),
        ];
    }

    public static function pickingactivo($notaventa_id)
    {
        $notaventa = Notaventa::findOrFail($notaventa_id);
        foreach ($notaventa->notaventadetalles as $notaventadetalle) {
            if($notaventadetalle->producto->categoriaprod->stadespsinstock == 0){
                foreach ($notaventadetalle->despachosoldets as $despachosoldet) {
                    $pickingcant = DespachoSol::pickingxitem($despachosoldet->id);
                    if($pickingcant > 0){
                        return [
                            "pickingactivo" => true,
                            "despachosol_id" => $despachosoldet->despachosol_id,
                            "producto_id" => $notaventadetalle->producto_id,
                            "pickingcant" => $pickingcant
                        ];
                    }
                }
            }
        }
        return [
            "pickingactivo" => false
        ];
    }
}