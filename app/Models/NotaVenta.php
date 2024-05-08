<?php

namespace App\Models;

use App\Models\Seguridad\Usuario;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;
use SplFileInfo;

class NotaVenta extends Model
{
    use SoftDeletes;
    protected $table = "notaventa";
    protected $fillable = [
        'sucursal_id',
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
        if(!isset($request->oc_id) or empty($request->oc_id)){
            $aux_condoc_id = " true";
        }else{
            $aux_condoc_id = "notaventa.oc_id='$request->oc_id'";
            $aux_condFecha = " true";
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
        if(!isset($request->notaventa_id) or empty($request->notaventa_id)){
            $aux_condnotaventa_id = " true";
        }else{
            $aux_condnotaventa_id = "notaventa.id='$request->notaventa_id'";
            $aux_condFecha = " true";
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
    
/*
        if(empty($request->comuna_id)){
            $aux_condcomuna_id = " true";
        }else{
            $aux_condcomuna_id = "notaventa.comunaentrega_id='$request->comuna_id'";
        }
*/
        //if(empty($aux_comuna )){
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

        
        if($aux_consulta == 1){
            $sql = "SELECT notaventadetalle.notaventa_id as id,notaventa.fechahora,notaventa.cliente_id,notaventa.comuna_id,notaventa.comunaentrega_id,
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
            notaventa.inidespacho,notaventa.guiasdespacho,notaventa.findespacho
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
            and isnull(notaventa.deleted_at) and isnull(notaventadetalle.deleted_at)
            $cond_group
            $cond_order;";
            //dd($sql);
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
            and isnull(notaventa.deleted_at) and isnull(notaventadetalle.deleted_at)
            GROUP BY areaproduccion_id,areaproduccion.nombre;";
        }
        //dd($sql);
        $datas = DB::select($sql);
        return $datas;
    }

    public static function consultatotcantod($id){
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
                /* $sql = "SELECT despachoord.notaventa_id,notaventadetalle.id as notaventadetalle_id,
                        notaventadetalle.producto_id,notaventadetalle.cant AS cantnv,
                        sum(despachoorddet.cantdesp) AS canddespreal
                        FROM despachoord JOIN despachoorddet 
                        ON despachoord.id = despachoorddet.despachoord_id
                        INNER JOIN notaventadetalle
                        ON notaventadetalle.id = despachoorddet.notaventadetalle_id
                        WHERE NOT(despachoord.id IN (SELECT despachoordanul.despachoord_id FROM despachoordanul))
                        and despachoord.guiadespacho is not null
                        and despachoorddet.notaventadetalle_id = $nvdet->notaventadetalle_id
                        and isnull(despachoord.deleted_at) and isnull(despachoorddet.deleted_at)
                        group by notaventadetalle.id;"; */

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
}
