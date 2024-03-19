<?php

namespace App\Models;

use App\Http\Controllers\SoapController;
use App\Models\Seguridad\Usuario;
use DOMDocument;
use DOMXPath;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;
use SplFileInfo;
use setasign\Fpdi\Fpdi;
use Illuminate\Support\SimpleXMLElement;
use SimpleXMLElement as GlobalSimpleXMLElement;

class Dte extends Model
{
    use SoftDeletes;
    protected $table = "dte";
    protected $fillable = [
        'foliocontrol_id',
        'nrodocto',
        'fchemis',
        'fchemisgen',
        'fechahora',
        'sucursal_id',
        'cliente_id',
        'comuna_id',
        'ciudad_id',
        'vendedor_id',    
        'obs',
        'tipodespacho',
        'indtraslado',
        'mntneto',
        'tasaiva',
        'iva',
        'mnttotal',
        'kgtotal',
        'centroeconomico_id',
        'statusgen',
        'aprobstatus',
        'aprobusu_id',
        'aprobfechahora',
        'usuario_id',
        'usuariodel_id'
    ];

    //RELACION DE UNO A MUCHOS dtedet
    public function dtedets()
    {
        return $this->hasMany(DteDet::class,'dte_id');
    }

    //RELACION INVERSA Cliente
    public function foliocontrol()
    {
        return $this->belongsTo(Foliocontrol::class);
    }
    
    //RELACION INVERSA Cliente
    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    //RELACION INVERSA Comuna
    public function comuna()
    {
        return $this->belongsTo(Comuna::class);
    }
    
    //RELACION INVERSA Ciudad
    public function ciudad()
    {
        return $this->belongsTo(Ciudad::class);
    }    
    
    //RELACION DE UNO A MUCHOS DteOC
    public function dteocs()
    {
        return $this->hasMany(DteOC::class,'dte_id');
    }

    //RELACION DE UNO A UNO DteOC
    public function dteoc()
    {
        return $this->hasOne(DteOC::class,'dte_id');
    }
    
    
    //Relacion inversa a Usuario
    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }

    //Relacion inversa a User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    //RELACION DE UNO A MUCHOS GuiaDespAnul
    public function dteanul()
    {
        return $this->hasOne(DteAnul::class,'dte_id');
    }

    //RELACION de uno a uno dteguiadesp
    public function dteguiadesp()
    {
        return $this->hasOne(DteGuiaDesp::class);
    }

    //RELACION de uno a uno dtefac
    public function dtefac()
    {
        return $this->hasOne(DteFac::class);
    }

    //RELACION de uno a uno dteguiausada
    public function dteguiausada()
    {
        return $this->hasOne(DteGuiaUsada::class);
    }

    //RELACION DE UNO A MUCHOS DteDte
    public function dtedtes()
    {
        return $this->hasMany(DteDte::class,'dte_id');
    }

    //DE UNO A UNO PARA EL CASO DE LAS NOTAS DE CREDITO Y DEBITO
    //RELACION de uno a uno DteDte
    public function dtedte()
    {
        return $this->hasOne(DteDte::class,'dte_id');
    }
    
    //RELACION DE UNO A MUCHOS dtedtefactasosiadas FACTURAS O DTE ASOCIADAS A LA FACTURA INICIAL
    //ESTO ES PARA PODER DEFINIR TODAS LAS NOTAS DE CREDITO Y DEBITO ASICIADAS A UNA FACTURA
    public function dtedtefacasosiadas()
    {
        return $this->hasMany(DteDte::class,'dtefac_id');
    }

    //RELACION DE UNO A MUCHOS dtedte al campo dter_id esto es para saber a los documentos que esta relacionado el DTE origen
    public function dtedters()
    {
        return $this->hasMany(DteDte::class,'dter_id');
    }

    //RELACION UNO A UNO CON DTEDTE CON EL CAMPO dter_id, esto se presta para cuando dte anula a otro es decir que tiene un solo dte asociado
    //RELACION de uno a uno DteDter
    public function dtedter()
    {
        return $this->hasOne(DteDte::class,'dter_id');
    }
    
    //RELACION de uno a uno dtencnd
    public function dtencnd()
    {
        return $this->hasOne(DteNcNd::class);
    }

    //RELACION DE UNO A MUCHOS dteguiausada
    public function dteguiausadas()
    {
        return $this->hasMany(DteGuiaUsada::class,'dte_id');
    }

    //RELACION de uno a uno dtedev
    public function dtedev()
    {
        return $this->hasOne(DteDev::class);
    }

    //RELACION de uno a uno dteguiadespnv
    public function dteguiadespnv()
    {
        return $this->hasOne(DteGuiaDespNV::class);
    }

    //RELACION DE UNO A MUCHOS despachosoldte
    public function despachosoldtes()
    {
        return $this->hasMany(DespachoSolDTE::class,'dte_id');
    }

    //RELACION INVERSA Sucursal
    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }    
    //RELACION INVERSA Vendedor
    public function vendedor()
    {
        return $this->belongsTo(Vendedor::class);
    }
    
    public static function reportguiadesppage($request){
        $user = Usuario::findOrFail(auth()->id());
        if(empty($request->vendedor_id)){
            //$user = Usuario::findOrFail(auth()->id());
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

        $sucurArray = $user->sucursales->pluck('id')->toArray();
        $sucurcadena = implode(",", $sucurArray);
        $aux_condsucurArray = "dte.sucursal_id  in ($sucurcadena)";
        if(!isset($request->sucursal_id) or empty($request->sucursal_id) or ($request->sucursal_id == "")){
            $aux_sucursal_idCond = "true";
        }else{
            $aux_sucursal_idCond = "dte.sucursal_id = $request->sucursal_id";
        }
        if(!isset($request->fechad) or empty($request->fechad) or empty($request->fechah)){
            $aux_condFecha = " true";
        }else{
            /*
            $fecha = date_create_from_format('d/m/Y', $request->fechad);
            $fechad = date_format($fecha, 'Y-m-d')." 00:00:00";
            $fecha = date_create_from_format('d/m/Y', $request->fechah);
            $fechah = date_format($fecha, 'Y-m-d')." 23:59:59";
            $aux_condFecha = "dte.fechahora>='$fechad' and dte.fechahora<='$fechah'";
            */
            $fecha = date_create_from_format('d/m/Y', $request->fechad);
            $fechad = date_format($fecha, 'Y-m-d');
            $fecha = date_create_from_format('d/m/Y', $request->fechah);
            $fechah = date_format($fecha, 'Y-m-d');
            $aux_condFecha = "dte.fchemis>='$fechad' and dte.fchemis<='$fechah'";


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
            $aux_areaproduccion_idCond = "true";
        }else{
            $aux_areaproduccionid = $request->areaproduccion_id;
            if(is_array($request->areaproduccion_id)){
                $aux_areaproduccionid = implode(",", $request->areaproduccion_id);
            }
            $aux_areaproduccion_idCond = " categoriaprod.areaproduccion_id in ($aux_areaproduccionid) ";
        }

        if(!isset($request->tipoentrega_id) or empty($request->tipoentrega_id)){
            $aux_condtipoentrega_id = " true";
        }else{
            $aux_condtipoentrega_id = "dteguiadesp.tipoentrega_id='$request->tipoentrega_id'";
        }
        if(!isset($request->notaventa_id) or empty($request->notaventa_id)){
            $aux_condnotaventa_id = " true";
        }else{
            $aux_condnotaventa_id = "notaventa.id='$request->notaventa_id'";
        }

        if(!isset($request->guiadesp_id) or empty($request->guiadesp_id)){
            $aux_condguiadesp_id = " true";
        }else{
            $aux_condguiadesp_id = "dte.nrodocto='$request->guiadesp_id'";
        }

        $aux_condproducto_id = " true";
        if(!empty($request->producto_id)){
            $aux_codprod = explode(",", $request->producto_id);
            $aux_codprod = implode ( ',' , $aux_codprod);
            $aux_condproducto_id = "dtedet.producto_id in ($aux_codprod)";
        }

        $aux_aprobstatus = " true";
        if(!empty($request->aprobstatus)){
            switch ($request->aprobstatus) {
                case 0:
                    $aux_aprobstatus = " true";
                    break;
                case 1:
                    $aux_aprobstatus = " isnull(dteanul.obs)";
                    break;    
                case 2:
                    $aux_aprobstatus = " not isnull(dteanul.obs)";
                    break;
                case 3:
                    $aux_aprobstatus = " dte.id in (SELECT dter_id from dtedte where isnull(dtedte.deleted_at))";
                    break;
                case 4:
                    $aux_aprobstatus = " dte.indtraslado = 1 AND dte.id not in (SELECT dter_id from dtedte where isnull(dtedte.deleted_at)) and isnull(dteanul.obs)";
                    break;
                case 5:
                    $aux_aprobstatus = " dte.indtraslado = 6";
                    break;
            }
        }
        if(!isset($request->comuna_id) or empty($request->comuna_id)){
            $aux_condcomuna_id = "true";
        }else{
            $aux_condcomuna_id = $request->comuna_id;
            if(is_array($request->comuna_id)){
                $aux_condcomuna_id = implode(",", $request->comuna_id);
            }
            $aux_condcomuna_id = " notaventa.comunaentrega_id in ($aux_condcomuna_id) ";
        }

        $aux_conddteguiausada = " true";
        //dd(!empty($request->dteguiausada));
        if(isset($request->dteguiausada) and !empty($request->dteguiausada)){
            $aux_conddteguiausada = "dte.id in (SELECT dteguiausada.dte_id FROM dteguiausada WHERE ISNULL(dteguiausada.deleted_at))";
        }

        $aux_conddteguiausadaActasParaLiberar = " true";
        //dd(!empty($request->dteguiausada));
        if(isset($request->dteguiausadaActasParaLiberar) and !empty($request->dteguiausadaActasParaLiberar)){
            $aux_conddteguiausadaActasParaLiberar = "ISNULL((SELECT GROUP_CONCAT(DISTINCT dte1.nrodocto) AS nrodocto
            FROM dtedte as dtedte1 INNER JOIN dte as dte1
            ON dte1.id = dtedte1.dte_id AND isnull(dte1.deleted_at) AND isnull(dtedte1.deleted_at)
            WHERE dtedte1.dter_id = dte.id
            AND (dte1.foliocontrol_id = 1) 
            and dte1.id in (SELECT vista_dtefacsaldoncnd.id FROM vista_dtefacsaldoncnd where vista_dtefacsaldoncnd.id and vista_dtefacsaldoncnd.mntnetoncnd_saldo>0)
            AND dtedte1.dte_id NOT IN (SELECT dteanul.dte_id FROM dteanul WHERE isnull(dteanul.deleted_at))))";
        }

        if(!isset($request->nrodocto) or empty($request->nrodocto)){
            $aux_condnrodocto = " true";
        }else{
            $aux_condnrodocto = "dte.nrodocto ='$request->nrodocto'";
            $aux_condFecha = " true";
        }
        if(!isset($request->nrofactura) or empty($request->nrofactura)){
            $aux_condnrofactura = " true";
        }else{
            $aux_condnrofactura = " $request->nrofactura in (SELECT GROUP_CONCAT(DISTINCT dte1.nrodocto) AS nrodocto
            FROM dtedte as dtedte1 INNER JOIN dte as dte1
            ON dte1.id = dtedte1.dte_id AND isnull(dte1.deleted_at) AND isnull(dtedte1.deleted_at)
            WHERE dtedte1.dter_id = dte.id
            AND (dte1.foliocontrol_id = 1) 
            AND dtedte1.dte_id NOT IN (SELECT dteanul.dte_id FROM dteanul WHERE isnull(dteanul.deleted_at)))";
            $aux_condFecha = " true";
        }
    
        $aux_verguias = can('ver-guias-de-despacho-de-todos-los-usuarios',false);
        $aux_condFiltrarxUsuario = " true ";
        if(!$aux_verguias){
            $aux_condFiltrarxUsuario = " dte.usuario_id = $user->id ";
        }

        $sql = "SELECT dte.id,dte.nrodocto,dte.fchemis,dte.fchemisgen,dte.fechahora,cliente.rut,cliente.razonsocial,
        dte.mnttotal,dte.kgtotal,
        if(isnull(notaventa.oc_id),dteoc.oc_id,notaventa.oc_id) as nvoc_id,
        if(isnull(notaventa.oc_file),CONCAT(dteoc.oc_folder,'/',dteoc.oc_file),notaventa.oc_file) as nvoc_file,
        dte.centroeconomico_id,dte.indtraslado,
        dteoc.oc_id,dteoc.oc_file,comuna.nombre as comunanombre,
        tipoentrega.nombre as tipoentrega_nombre,tipoentrega.icono,dtedte.dter_id,
        dteguiadesp.notaventa_id,dteguiadespnv.notaventa_id as dteguiadespnv_notaventa_id,
        despachoord.fechaestdesp,dteguiadesp.despachoord_id,despachoord.despachosol_id,
        (SELECT GROUP_CONCAT(DISTINCT dte1.nrodocto) AS nrodocto
        FROM dtedte as dtedte1 INNER JOIN dte as dte1
        ON dte1.id = dtedte1.dte_id AND isnull(dte1.deleted_at) AND isnull(dtedte1.deleted_at)
        WHERE dtedte1.dter_id = dte.id
        AND (dte1.foliocontrol_id = 1) 
        AND dtedte1.dte_id NOT IN (SELECT dteanul.dte_id FROM dteanul WHERE isnull(dteanul.deleted_at))) as fact_nrodocto,
        (SELECT GROUP_CONCAT(DISTINCT foliocontrol.nombrepdf) AS nombrepdf
        FROM dtedte as dtedte1 INNER JOIN dte as dte1
        ON dte1.id = dtedte1.dte_id AND isnull(dte1.deleted_at) AND isnull(dtedte1.deleted_at)
        INNER JOIN foliocontrol
        ON foliocontrol.id = dte1.foliocontrol_id
        WHERE dtedte1.dter_id = dte.id
        AND (dte1.foliocontrol_id = 1) 
        AND dtedte1.dte_id NOT IN (SELECT dteanul.dte_id FROM dteanul WHERE isnull(dteanul.deleted_at))) as fact_nombrepdf,
        dteanul.obs as dteanul_obs,dteanul.created_at as dteanulcreated_at,
        dte_rel_guia.nrodocto as guiaorigenprecio_nrodocto,dte.updated_at as dteupdated_at,
        dteguiausada.id as dteguiausada_id,dteguiausada.updated_at as dteguiausadaupdated_at,
        dte.usuario_id,usuario.usuario
        FROM dte INNER JOIN dtedet
        ON dte.id=dtedet.dte_id and isnull(dte.deleted_at) and isnull(dtedet.deleted_at)
        INNER JOIN dteguiadesp
        ON dte.id = dteguiadesp.dte_id and isnull(dteguiadesp.deleted_at)
        LEFT JOIN dteguiadespnv
        ON dteguiadespnv.dte_id = dte.id
        LEFT JOIN notaventa
        ON (notaventa.id=dteguiadesp.notaventa_id or notaventa.id=dteguiadespnv.notaventa_id) and isnull(notaventa.deleted_at)
        LEFT JOIN dtedet_despachoorddet
        ON dtedet_despachoorddet.dtedet_id = dtedet.id and isnull(dtedet_despachoorddet.deleted_at)
        LEFT JOIN notaventadetalle
        ON notaventadetalle.id=dtedet_despachoorddet.notaventadetalle_id and isnull(notaventadetalle.deleted_at)
        LEFT JOIN despachoord
        ON despachoord.id=dteguiadesp.despachoord_id and isnull(despachoord.deleted_at)
        LEFT JOIN despachoorddet
        ON despachoord.id=despachoorddet.despachoord_id and isnull(despachoorddet.deleted_at)
        INNER JOIN producto
        ON dtedet.producto_id=producto.id and isnull(producto.deleted_at)
        INNER JOIN categoriaprod
        ON categoriaprod.id=producto.categoriaprod_id and isnull(categoriaprod.deleted_at)
        INNER JOIN areaproduccion
        ON areaproduccion.id=categoriaprod.areaproduccion_id and isnull(areaproduccion.deleted_at)
        INNER JOIN cliente
        ON cliente.id=dte.cliente_id and isnull(cliente.deleted_at)
        INNER JOIN comuna
        ON comuna.id=dteguiadesp.comunaentrega_id and isnull(comuna.deleted_at)
        INNER JOIN tipoentrega
        ON tipoentrega.id = dteguiadesp.tipoentrega_id AND ISNULL(tipoentrega.deleted_at)
        LEFT JOIN dteanul
        ON dteanul.dte_id=dte.id and isnull(dteanul.deleted_at)
        LEFT JOIN dteoc
        ON dteoc.dte_id=dte.id and isnull(dteoc.deleted_at)
        LEFT JOIN dtedte
        ON dtedte.dter_id = dte.id AND isnull(dtedte.deleted_at)
        LEFT JOIN dtedte as dtedte_rel_guia
        ON dtedte_rel_guia.dte_id = dte.id AND isnull(dtedte_rel_guia.deleted_at)
        LEFT JOIN dte as dte_rel_guia
        ON dtedte_rel_guia.dter_id = dte_rel_guia.id AND (dte_rel_guia.foliocontrol_id = 2)  AND isnull(dte_rel_guia.deleted_at)
        LEFT JOIN dteguiausada
        ON dteguiausada.dte_id = dte.id AND isnull(dteguiausada.deleted_at)
        LEFT JOIN usuario
        ON dte.usuario_id=usuario.id
        WHERE $aux_condproducto_id
        and $aux_condguiadesp_id
        and $vendedorcond
        and $aux_condFecha
        and $aux_condrut
        and $aux_condoc_id
        and $aux_condgiro_id
        and $aux_areaproduccion_idCond
        and $aux_condtipoentrega_id
        and $aux_condnotaventa_id
        and $aux_condcomuna_id
        and $aux_aprobstatus
        and $aux_condsucurArray
        and $aux_sucursal_idCond
        and $aux_conddteguiausada
        and $aux_conddteguiausadaActasParaLiberar
        and $aux_condnrodocto
        and $aux_condnrofactura
        and $aux_condFiltrarxUsuario
        GROUP BY dte.id
        ORDER BY dte.id asc;";
        //dd($sql);
        $datas = DB::select($sql);
        return $datas;
    }

    public static function consultalistarguiadesppage($request){
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
            $vendedorcond = "notaventa.vendedor_id='$request->vendedor_id'";
        }
    
        if(empty($request->fechad) or empty($request->fechah)){
            $aux_condFecha = " true";
        }else{
            $fecha = date_create_from_format('d/m/Y', $request->fechad);
            $fechad = date_format($fecha, 'Y-m-d');
            $fecha = date_create_from_format('d/m/Y', $request->fechah);
            $fechah = date_format($fecha, 'Y-m-d');
            $aux_condFecha = "dte.fchemis>='$fechad' and dte.fchemis<='$fechah'";
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
            $aux_condcomuna_id = "dte.comuna_id='$request->comuna_id'";
        }
        /*
        if(empty($request->dtenotnull)){
            $aux_conddtenotnull = " true";
        }else{
            $aux_conddtenotnull = "dte.id NOT IN (SELECT dteanul.dte_id FROM dteanul WHERE ISNULL(dteanul.deleted_at))";
        }
        */
        if(empty($request->dteguiausada)){
            $aux_conddteguiausada = " true";
        }else{
            $aux_conddteguiausada = "dte.id NOT IN (SELECT dteguiausada.dte_id FROM dteguiausada WHERE ISNULL(dteguiausada.deleted_at))";
            /*
            $aux_conddteguiausada = "dte.id NOT IN (SELECT dtedte.dter_id
                                        FROM dtedte
                                        WHERE dtedte.dter_id = dte.id 
                                        AND ISNULL(dtedte.deleted_at)
                                        AND dtedte.dte_id NOT IN (SELECT dteanul.dte_id FROM dteanul WHERE ISNULL(dteanul.deleted_at)))";
            */
            
        }
        if(!isset($request->indtraslado) or empty($request->indtraslado)){
            $aux_condindtraslado = " true";
        }else{
            $aux_condindtraslado = "dte.indtraslado = $request->indtraslado";
        }
        $user = Usuario::findOrFail(auth()->id());
        $sucurArray = $user->sucursales->pluck('id')->toArray();
        $sucurcadena = implode(",", $sucurArray);
        $aux_condsucurArray = "dte.sucursal_id  in ($sucurcadena)";
        if(!isset($request->sucursal_id) or empty($request->sucursal_id) or ($request->sucursal_id == "") or ($request->sucursal_id == "undefined")){
            $aux_sucursal_idCond = "true";
        }else{
            $aux_sucursal_idCond = "dte.sucursal_id = $request->sucursal_id";
        }
        $aux_condindtraslado = "true";
        if(isset($request->aux_condindtraslado) and $request->aux_condindtraslado != "" and $request->aux_condindtraslado != null){
            $aux_condindtraslado = $request->aux_condindtraslado;
        }
        $sql = "SELECT dte.id,dte.nrodocto,dte.fchemis,dteguiadesp.despachoord_id,notaventa.cotizacion_id,
        despachoord.despachosol_id,dte.fechahora,despachoord.fechaestdesp,dte.indtraslado,
        cliente.razonsocial,cliente.rut,cliente.razonsocial,despachosol.notaventa_id as notaventa_id,
        if(isnull(notaventa.oc_id),dteoc.oc_id,notaventa.oc_id) as oc_id,
        if(isnull(notaventa.oc_file),CONCAT(dteoc.oc_folder,'/',dteoc.oc_file),notaventa.oc_file) as oc_file,
        if(isnull(dteguiadesp.notaventa_id),despachosol.notaventa_id,dteguiadesp.notaventa_id) as notaventa_id,
        '' as notaventaxk,comuna.nombre as comuna_nombre,
        tipoentrega.nombre as tipoentrega_nombre,'  ' as te,tipoentrega.icono,clientebloqueado.descripcion as clientebloqueado_descripcion,
        dte.kgtotal as aux_totalkg,
        dte.mnttotal as subtotal,
        dte.updated_at,despachoord.updated_at as despordupdated_at,'' as rutacrear,
        dteoc.oc_id as dteoc_oc_id,dteoc.oc_file as dteoc_oc_file,tipodespacho,
        dteguiadespnv.notaventa_id as dteguiadespnv_notaventa_id
        FROM dte INNER JOIN dteguiadesp
        ON dte.id = dteguiadesp.dte_id AND ISNULL(dte.deleted_at) and isnull(dteguiadesp.deleted_at)
        LEFT JOIN dteguiadespnv
        ON dteguiadespnv.dte_id = dte.id
        LEFT JOIN despachoord
        ON dteguiadesp.despachoord_id = despachoord.id AND ISNULL(despachoord.deleted_at)
        LEFT JOIN despachosol
        ON despachoord.despachosol_id = despachosol.id AND ISNULL(despachosol.deleted_at)
        LEFT JOIN notaventa
        ON (notaventa.id = dteguiadesp.notaventa_id or notaventa.id=dteguiadespnv.notaventa_id) and isnull(notaventa.deleted_at)
        INNER JOIN cliente
        ON cliente.id = dte.cliente_id AND isnull(cliente.deleted_at)
        LEFT JOIN comuna
        ON comuna.id = dte.comuna_id AND isnull(comuna.deleted_at)
        LEFT JOIN tipoentrega
        ON tipoentrega.id = despachoord.tipoentrega_id AND ISNULL(tipoentrega.deleted_at)
        LEFT JOIN clientebloqueado
        ON clientebloqueado.cliente_id = notaventa.cliente_id AND ISNULL(clientebloqueado.deleted_at)
        LEFT JOIN dteoc
        ON dteoc.dte_id=dte.id and isnull(dteoc.deleted_at)
        WHERE $vendedorcond
        AND $aux_condFecha
        AND $aux_condrut
        AND $aux_condoc_id
        AND $aux_condgiro_id
        AND $aux_condtipoentrega_id
        AND $aux_condnotaventa_id
        AND $aux_condcomuna_id
        AND dte.sucursal_id in ($sucurcadena)
        AND NOT ISNULL(dte.nrodocto)
        AND NOT ISNULL(dte.fchemis)
        AND $aux_conddteguiausada
        AND dte.foliocontrol_id = 2
        AND dte.statusgen = 1
        AND $aux_condindtraslado
        AND dte.id not in (SELECT dte_id from dteanul where ISNULL(dteanul.deleted_at))
        AND $aux_condsucurArray
        AND $aux_sucursal_idCond
        order BY dte.nrodocto;";
        //dd($sql);
        //AND $aux_conddtenotnull


        $arrays = DB::select($sql);
        $i = 0;
        foreach ($arrays as $array) {
            $arrays[$i]->rutacrear = route('crear_factura', ['id' => $array->id]);
            $i++;
        }
        return $arrays;
    }

    public static function consultadtedet($request){
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
            $vendedorcond = "notaventa.vendedor_id='$request->vendedor_id'";
        }
    
        if(empty($request->fechad) or empty($request->fechah)){
            $aux_condFecha = " true";
        }else{
            $fecha = date_create_from_format('d/m/Y', $request->fechad);
            $fechad = date_format($fecha, 'Y-m-d');
            $fecha = date_create_from_format('d/m/Y', $request->fechah);
            $fechah = date_format($fecha, 'Y-m-d');
            $aux_condFecha = "dte.fchemis>='$fechad' and dte.fchemis<='$fechah'";
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
    
        if(empty($request->dtenotnull)){
            $aux_conddtenotnull = " true";
        }else{
            $aux_conddtenotnull = "dte.id NOT IN (SELECT dteanul.dte_id FROM dteanul WHERE ISNULL(dteanul.deleted_at))";
        }

        if(empty($request->arrdte_id)){
            $aux_conddtenotnull = " true";
        }else{
            $aux_conddtenotnull = "dte.id NOT IN (SELECT dteanul.dte_id FROM dteanul WHERE ISNULL(dteanul.deleted_at))";
        }
        if(empty($request->strdte_id)){
            $aux_conddtedet = " true";
        }else{
            $aux_conddtedet = "dte.id IN ($request->strdte_id)";
        }

        $filtroNoMostrarGuiasUsadas = "dte.id NOT IN (SELECT dteguiausada.dte_id FROM dteguiausada WHERE ISNULL(dteguiausada.deleted_at))";
        $filtroNoMostrarDTeAnuladas = "dte.id not in (SELECT dte_id from dteanul where ISNULL(dteanul.deleted_at))";
        $aux_aprobstatus = " true";
        $unionOtrasTablas = "";
        $otrosCampos = "";
        $aux_sucursal_idCond =" true";
        if(isset($request->filtroguiasusadas)){
            $filtroNoMostrarGuiasUsadas = "true";
            $filtroNoMostrarDTeAnuladas = "true";
            $otrosCampos = ",dtedte.dter_id,
                            (SELECT GROUP_CONCAT(DISTINCT dte1.nrodocto) AS nrodocto
                            FROM dtedte as dtedte1 INNER JOIN dte as dte1
                            ON dte1.id = dtedte1.dte_id AND isnull(dte1.deleted_at) AND isnull(dtedte1.deleted_at)
                            WHERE dtedte1.dter_id = dte.id
                            AND (dte1.foliocontrol_id = 1) 
                            AND dtedte1.dte_id NOT IN (SELECT dteanul.dte_id FROM dteanul WHERE isnull(dteanul.deleted_at))) as fact_nrodocto,
                            dteanul.obs as dteanul_obs,dteanul.created_at as dteanulcreated_at
             ";
            $unionOtrasTablas = " LEFT JOIN dtedte
                                ON dtedte.dter_id = dte.id AND isnull(dtedte.deleted_at)
                                LEFT JOIN dteanul
                                ON dteanul.dte_id=dte.id and isnull(dteanul.deleted_at)
                            ";

            $aux_aprobstatus = " true";
            if(!empty($request->aprobstatus)){
                switch ($request->aprobstatus) {
                    case 0:
                        $aux_aprobstatus = " true";
                        break;
                    case 1:
                        $aux_aprobstatus = " isnull(dteanul.obs)";
                        break;    
                    case 2:
                        $aux_aprobstatus = " not isnull(dteanul.obs)";
                        break;
                    case 3:
                        $aux_aprobstatus = " dte.id in (SELECT dter_id from dtedte where isnull(dtedte.deleted_at))";
                        break;
                    case 4:
                        $aux_aprobstatus = " dte.indtraslado = 1 AND dte.id not in (SELECT dter_id from dtedte where isnull(dtedte.deleted_at)) and isnull(dteanul.obs)";
                        break;
                    case 5:
                        $aux_aprobstatus = " dte.indtraslado = 6";
                        break;
                }
            }
            $sucurArray = $user->sucursales->pluck('id')->toArray();
            $sucurcadena = implode(",", $sucurArray);
            $aux_condsucurArray = "dte.sucursal_id  in ($sucurcadena)";
            if(!isset($request->sucursal_id) or empty($request->sucursal_id) or ($request->sucursal_id == "")){
                $aux_sucursal_idCond = "true";
            }else{
                $aux_sucursal_idCond = "dte.sucursal_id = $request->sucursal_id";
            }
        }

        $user = Usuario::findOrFail(auth()->id());
        $sucurArray = $user->sucursales->pluck('id')->toArray();
        $sucurcadena = implode(",", $sucurArray);

        $aux_condproducto_id = " true";
        if(!empty($request->producto_id)){
            $aux_codprod = explode(",", $request->producto_id);
            $aux_codprod = implode ( ',' , $aux_codprod);
            $aux_condproducto_id = "dtedet.producto_id in ($aux_codprod)";
        }

        $aux_verguias = can('ver-dte-detalle-todos-los-usuarios',false);
        $aux_condFiltrarxUsuario = " true ";
        if(!$aux_verguias){
            $aux_condFiltrarxUsuario = " dte.usuario_id = $user->id ";
        }
    
        
        $sql = "SELECT dte.id,dte.nrodocto,dte.fchemis,dteguiadesp.despachoord_id,notaventa.cotizacion_id,
        despachoord.despachosol_id,dte.fechahora,despachoord.fechaestdesp,dte.centroeconomico_id,
        cliente.rut,cliente.razonsocial,
        if(isnull(notaventa.oc_id),dteoc.oc_id,notaventa.oc_id) as oc_id,
        if(isnull(notaventa.oc_file),CONCAT(dteoc.oc_folder,'/',dteoc.oc_file),notaventa.oc_file) as oc_file,
        dteguiadesp.notaventa_id,
        '' as notaventaxk,comuna.nombre as comuna_nombre,
        tipoentrega.nombre as tipoentrega_nombre,'  ' as te,tipoentrega.icono,clientebloqueado.descripcion as clientebloqueado_descripcion,
        dte.kgtotal as aux_totalkg,
        dte.mnttotal as subtotal,
        dte.updated_at,'' as rutacrear,
        dtedet.id as dtedet_id,dtedet.dtedet_id as dtedetr_id,dtedet_despachoorddet.despachoorddet_id,dtedet_despachoorddet.notaventadetalle_id,
        dtedet.producto_id,dtedet.nrolindet,dtedet.vlrcodigo,dtedet.nmbitem,dtedet.dscitem,dtedet.qtyitem,dtedet.unmditem,
        dtedet.unidadmedida_id,dtedet.prcitem,dtedet.montoitem,dtedet.obsdet,dtedet.itemkg,
        notaventadetalle.precioxkilo,notaventadetalle.precioxkiloreal,
        dte.mnttotal,dte.kgtotal,
        dte.vendedor_id,persona.rut as vendedor_rut,concat(persona.nombre, ' ' ,persona.apellido) AS vendedor_nombre,
        dte.indtraslado,dte.usuario_id,usuario.usuario
        $otrosCampos
        FROM dte INNER JOIN dteguiadesp
        ON dte.id = dteguiadesp.dte_id AND ISNULL(dte.deleted_at) and isnull(dteguiadesp.deleted_at)
        LEFT JOIN despachoord
        ON dteguiadesp.despachoord_id = despachoord.id AND ISNULL(despachoord.deleted_at)
        LEFT JOIN notaventa
        ON notaventa.id = dteguiadesp.notaventa_id and isnull(notaventa.deleted_at)
        INNER JOIN cliente
        ON cliente.id = dte.cliente_id AND isnull(cliente.deleted_at)
        INNER JOIN comuna
        ON comuna.id = dte.comuna_id AND isnull(comuna.deleted_at)
        LEFT JOIN tipoentrega
        ON tipoentrega.id = despachoord.tipoentrega_id AND ISNULL(tipoentrega.deleted_at)
        INNER JOIN dtedet
        ON dtedet.dte_id = dte.id AND ISNULL(dtedet.deleted_at)
        LEFT JOIN dtedet_despachoorddet
        ON dtedet_despachoorddet.dtedet_id = dtedet.id AND ISNULL(dtedet_despachoorddet.deleted_at)
        LEFT JOIN notaventadetalle
        ON notaventadetalle.id = dtedet_despachoorddet.notaventadetalle_id AND ISNULL(notaventadetalle.deleted_at)
        LEFT JOIN clientebloqueado
        ON clientebloqueado.cliente_id = notaventa.cliente_id AND ISNULL(clientebloqueado.deleted_at)
        LEFT JOIN dteoc
        ON dteoc.dte_id=dte.id and isnull(dteoc.deleted_at)
        LEFT JOIN vendedor
        ON dte.vendedor_id=vendedor.id and isnull(vendedor.deleted_at)
        LEFT JOIN persona
        ON vendedor.persona_id=persona.id and isnull(persona.deleted_at)
        LEFT JOIN usuario
        ON dte.usuario_id=usuario.id
        $unionOtrasTablas
        WHERE $vendedorcond
        AND $aux_condFecha
        AND $aux_condrut
        AND $aux_condoc_id
        AND $aux_condgiro_id
        AND $aux_condtipoentrega_id
        AND $aux_condnotaventa_id
        AND $aux_condcomuna_id
        AND dte.sucursal_id in ($sucurcadena)
        AND NOT ISNULL(dte.nrodocto)
        AND NOT ISNULL(dte.fchemis)
        AND $aux_conddtenotnull
        AND $aux_conddtedet
        AND $filtroNoMostrarGuiasUsadas
        AND $filtroNoMostrarDTeAnuladas
        AND $aux_aprobstatus
        AND $aux_sucursal_idCond
        AND $aux_condproducto_id
        AND $aux_condFiltrarxUsuario
        order BY dte.nrodocto,dtedet.id;";
        //dd($sql);
        $arrays = DB::select($sql);
        /*
        $i = 0;
        foreach ($arrays as $array) {
            $arrays[$i]->rutacrear = route('crear_factura', ['id' => $array->id]);
            $i++;
        }*/
        //dd($arrays);
        return $arrays;
    }

    public static function reportdtefac($request){
        $vendedorcond = " true ";
        $user = Usuario::findOrFail(auth()->id());
        if(isset($request->vendedor_id)){
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
                $vendedorcond = "notaventa.vendedor_id='$request->vendedor_id'";
            }    
        }
    
        if(!isset($request->fechad) or !isset($request->fechah) or empty($request->fechad) or empty($request->fechah)){
            $aux_condFecha = " true";
        }else{
            $fecha = date_create_from_format('d/m/Y', $request->fechad);
            $fechad = date_format($fecha, 'Y-m-d');
            $fecha = date_create_from_format('d/m/Y', $request->fechah);
            $fechah = date_format($fecha, 'Y-m-d');
            $aux_condFecha = "dte.fchemis>='$fechad' and dte.fchemis<='$fechah'";
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
            $aux_condFecha = " true";
        }
        if(!isset($request->giro_id) or empty($request->giro_id)){
            $aux_condgiro_id = " true";
        }else{
            $aux_condgiro_id = "notaventa.giro_id='$request->giro_id'";
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
            $aux_condFecha = " true";
        }
    
        if(!isset($request->comuna_id) or empty($request->comuna_id)){
            $aux_condcomuna_id = " true";
        }else{
            $aux_condcomuna_id = "notaventa.comunaentrega_id='$request->comuna_id'";
        }
    
        if(!isset($request->dtenotnull) or empty($request->dtenotnull)){
            $aux_conddtenotnull = " true";
        }else{
            $aux_conddtenotnull = "dte.id NOT IN (SELECT dteanul.dte_id FROM dteanul WHERE ISNULL(dteanul.deleted_at))";
        }

        if(!isset($request->arrdte_id) or empty($request->arrdte_id)){
            $aux_conddtenotnull = " true";
        }else{
            $aux_conddtenotnull = "dte.id NOT IN (SELECT dteanul.dte_id FROM dteanul WHERE ISNULL(dteanul.deleted_at))";
        }
        if(!isset($request->arrdte_id) or empty($request->arrdte_id)){
            $aux_conddtedet = " true";
        }else{
            $aux_conddtedet = "dte.id IN ($request->strdte_id)";
        }

        if(!isset($request->nrodocto) or empty($request->nrodocto)){
            $aux_condnrodocto = " true";
        }else{
            $aux_condnrodocto = "dte.nrodocto = $request->nrodocto";
            $aux_condFecha = " true";
        }
        if(!isset($request->dte_id) or empty($request->dte_id)){
            $aux_conddte_id = " true";
        }else{
            $aux_conddte_id = "dte.id = $request->dte_id";
            $aux_condFecha = " true";
        }

        if(!isset($request->statusgen) or empty($request->statusgen)){
            $aux_condstatusgen = " true";
        }else{
            $aux_condstatusgen = "dte.statusgen = $request->statusgen";
        }
        //dd($request->foliocontrol_id);
        if(!isset($request->foliocontrol_id) or empty($request->foliocontrol_id)){
            $aux_condfoliocontrol_id = " (dte.foliocontrol_id=1 or dte.foliocontrol_id=7) ";
        }else{
            $aux_condfoliocontrol_id = " dte.foliocontrol_id in $request->foliocontrol_id";
        }
        //dd($aux_condfoliocontrol_id);
        if(!isset($request->aprobstatus) or empty($request->aprobstatus)){
            $aux_aprobstatus = " true";
        }else{
            switch ($request->aprobstatus) {
                case 0:
                    $aux_aprobstatus = " true";
                    break;
                case 1:
                    $aux_aprobstatus = " isnull(dteanul.obs)";
                    break;    
                case 2:
                    $aux_aprobstatus = " not isnull(dteanul.obs)";
                    break;
            }
        }
        //dd($aux_condfoliocontrol_id);
        $sucurArray = $user->sucursales->pluck('id')->toArray();
        $sucurcadena = implode(",", $sucurArray);
        $aux_condsucurArray = "dte.sucursal_id  in ($sucurcadena)";
        if(!isset($request->sucursal_id) or empty($request->sucursal_id) or ($request->sucursal_id == "")){
            $aux_sucursal_idCond = "true";
        }else{
            $aux_sucursal_idCond = "dte.sucursal_id = $request->sucursal_id";
        }
        if(!isset($request->vendedor_id) or empty($request->vendedor_id) or ($request->vendedor_id == "")){
            $aux_vendedor_idCond = "true";
        }else{
            $aux_vendedor_idCond = "dte.vendedor_id in ($request->vendedor_id)";
        }

        $aux_verFacturas = can('ver-facturas-de-todos-los-usuarios',false);
        $aux_condFiltrarxUsuario = " true ";
        if(!$aux_verFacturas){
            $aux_condFiltrarxUsuario = " dte.usuario_id = $user->id ";
        }

        $sql = "SELECT dte.id,dte.fchemis,dte.fechahora,cliente.rut,cliente.razonsocial,comuna.nombre as nombre_comuna,
        clientebloqueado.descripcion as clientebloqueado_descripcion,mnttotal,dte.kgtotal,
        GROUP_CONCAT(DISTINCT dtedte.dter_id) AS dter_id,
        GROUP_CONCAT(DISTINCT notaventa.cotizacion_id) AS cotizacion_id,
        GROUP_CONCAT(DISTINCT if(isnull(notaventa.oc_id),dteoc.oc_id,notaventa.oc_id)) AS oc_id,
        GROUP_CONCAT(DISTINCT if(isnull(notaventa.oc_file),CONCAT(dteoc.oc_folder,'/',dteoc.oc_file),notaventa.oc_file)) AS oc_file,
        GROUP_CONCAT(DISTINCT dteguiadesp.notaventa_id) AS notaventa_id,
        dteguiadespnv.notaventa_id as dteguiadespnv_notaventa_id,
        GROUP_CONCAT(DISTINCT despachoord.despachosol_id) AS despachosol_id,
        GROUP_CONCAT(DISTINCT dteguiadesp.despachoord_id) AS despachoord_id,
        (SELECT GROUP_CONCAT(DISTINCT dte1.nrodocto) 
        FROM dte AS dte1 INNER JOIN dtedte AS dtedte1
        ON dte1.id = dtedte1.dter_id AND ISNULL(dte1.deleted_at) and isnull(dtedte1.deleted_at)
        WHERE dtedte1.dte_id = dte.id
        GROUP BY dtedte1.dte_id) AS nrodocto_guiadesp,
        (SELECT GROUP_CONCAT(DISTINCT foliocontrolGD.nombrepdf) 
        FROM dte AS dte1 INNER JOIN dtedte AS dtedte1
        ON dte1.id = dtedte1.dter_id AND ISNULL(dte1.deleted_at) and isnull(dtedte1.deleted_at)
        INNER JOIN foliocontrol AS foliocontrolGD
        ON foliocontrolGD.id = dte1.foliocontrol_id AND ISNULL(foliocontrolGD.deleted_at)
        WHERE dtedte1.dte_id = dte.id
        GROUP BY dtedte1.dte_id) AS nombrepdf_guiadesp,
        dteanul.obs as dteanul_obs,dteanul.created_at as dteanulcreated_at,
        foliocontrol.tipodocto,foliocontrol.nombrepdf,
        dte.nrodocto,dte.updated_at,dtefac.staverfacdesp,dtefac.updated_at as dtefac_updated_at
        FROM dte LEFT JOIN dtedte
        ON dte.id = dtedte.dte_id AND ISNULL(dte.deleted_at) and isnull(dtedte.deleted_at)
        LEFT JOIN dteguiadesp
        ON dtedte.dter_id = dteguiadesp.dte_id and isnull(dteguiadesp.deleted_at)
        LEFT JOIN dteguiadespnv
        ON dteguiadespnv.dte_id =dtedte.dter_id
        LEFT JOIN despachoord
        ON despachoord.id = dteguiadesp.despachoord_id and isnull(despachoord.deleted_at)
        LEFT JOIN notaventa
        ON (notaventa.id = despachoord.notaventa_id or notaventa.id=dteguiadespnv.notaventa_id) and isnull(notaventa.deleted_at)
        INNER JOIN cliente
        ON dte.cliente_id  = cliente.id AND ISNULL(cliente.deleted_at)
        INNER JOIN comuna
        ON comuna.id = cliente.comunap_id AND ISNULL(comuna.deleted_at)
        LEFT JOIN clientebloqueado
        ON dte.cliente_id = clientebloqueado.cliente_id AND ISNULL(clientebloqueado.deleted_at)
        LEFT JOIN dteanul
        ON dteanul.dte_id = dte.id AND ISNULL(dteanul.deleted_at)
        INNER JOIN foliocontrol
        ON  foliocontrol.id = dte.foliocontrol_id AND ISNULL(foliocontrol.deleted_at)
        INNER JOIN dtefac
        ON dtefac.dte_id = dte.id
        LEFT JOIN dteoc
        ON dteoc.dte_id=dte.id and isnull(dteoc.deleted_at)
        WHERE $aux_condfoliocontrol_id
        AND dte.sucursal_id IN ($sucurcadena)
        AND $aux_sucursal_idCond
        AND $aux_conddte_id
        AND $aux_condFecha
        AND $aux_condnrodocto
        AND $aux_condrut
        AND $aux_condoc_id
        AND $aux_condnotaventa_id
        AND $aux_condstatusgen
        AND $aux_aprobstatus
        AND $aux_condsucurArray
        AND $aux_vendedor_idCond
        AND $aux_condFiltrarxUsuario
        AND NOT ISNULL(dte.nrodocto)
        GROUP BY dte.id
        ORDER BY dte.nrodocto asc;";

        //dd($sql);
        
        //AND ISNULL(dte.statusgen)


        $arrays = DB::select($sql);
        /*
        $i = 0;
        foreach ($arrays as $array) {
            $arrays[$i]->rutacrear = route('crear_factura', ['id' => $array->id]);
            $i++;
        }*/
        //dd($arrays);
        return $arrays;
    }

    //CONSULTAR POR NUM FACTURA PARA TRAER EL DTE MAS EL DTEDET 
    //SE ENVIA EL SALDO DEL DETDET RESTANDO LAS NC Y SUMANDO LAS ND
    public static function consdte_dtedet($request){
        //dd($request);
        if($request->ajax()){
            $respuesta = array();
            $respuesta['mensaje'] = "";
            $respuesta['totaloriginal'] = 0;
            $respuesta['totalmodificado'] = 0;
            $dtesql = consultasql_dte($request);
            $respuesta['dte'] = $dtesql;
            //dd($respuesta['dte']);
            if(count($dtesql)){
                $dte = Dte::findOrFail($dtesql[0]->dte_id);
                if($dte->statusgen == null){
                    $respuesta['mensaje'] = "Documento no ha sido aceptado por SII";
                    return $respuesta;
                }
                foreach ($dte->dtedters as $dtedter) {
                    if(!isset($dtedter->dte->dteanul)){
                        /*
                        if($dtedter->dte->dtencnd->codref == 1){
                            unset($respuesta['dte'][0]);
                            //EL DOCUMENTO QUE SE BUSCA, PREVIAMENTE SE IDENTIFICA SI FUE ANULADO POR OTRO DOCUMENTO
                            $respuesta['mensaje'] = $dte->foliocontrol->desc . ":" . $request->nrodocto . " anulado por " . $dtedter->dte->foliocontrol->desc . ":" . $dtedter->dte->nrodocto;
                            return $respuesta;
                        }
                        */
                        if(($dtedter->dte->foliocontrol_id == 5)){
                            if($dtedter->dte->dtencnd){
                                //dd($dtedter->dte);
                                $respuesta['dte'][0]->dte_nombreDocRefAnul = $dtedter->dte->foliocontrol->desc;
                                if($dtedter->dte->nrodocto == null){
                                    $respuesta['dte'][0]->dte_dterefanul = "En espera de ser Generada.";
                                }else{
                                    $respuesta['dte'][0]->dte_dterefanul = $dtedter->dte->nrodocto;
                                }
                                $respuesta['dte'][0]->dte_idrefanul = $dtedter->dte->id;
                                $respuesta['dte'][0]->codref = $dtedter->dte->dtencnd->codref;
                            }
                        }    
                    }
                }
                $dtefactdets1 = $dte->dtedets;
                $aux_saldomntneto = $dte->mntneto;
                $aux_saldomntnetoband = 0;
                $aux_nrodoctoQueAnula = [];
                if(str_contains("NC,ND", $request->TipoDTE)){
                    if($dte->foliocontrol_id == 1 or $dte->foliocontrol_id == 7){
                        //dd($dte->dtedtefacasosiadas);
                        foreach ($dte->dtedtefacasosiadas as $dtedtefacasosiada) {
                            //BUSCO TODOS LOS DTE RELACIONADOS A LA FACTURA EXCLUYENDO LA FACTURA ORIGINAL
                            //ME UBICO EN EL DTE RELACIONADO
                            $dteAsoc = Dte::findOrFail($dtedtefacasosiada->dte_id);
                            $operador = 1;
                            //VALIDO QUE LOS DOCUMENTOS ASOCIADOS SEAN NC O ND (NOTA CREDITO O DEBITO) BUSCANDO SI TIENE REGISTROS RELACIONADOS EN $dteAsoc->dtencnd
                            if(isset($dteAsoc->dtencnd) and is_null($dteAsoc->dteanul)){
                                if($dteAsoc->foliocontrol_id == 5){
                                    $operador = -1;
                                    //dd($dteAsoc->dtedets);
                                }
                                //dd($dteAsoc->dtedets);
                                foreach ($dteAsoc->dtedets as $dtedet) {
                                    $dtedetorigen = Dte::busDTEDETOrig($dtedet,$request->tdfoliocontrol_id);
                                    //dd($dtedetorigen);
                                    //$request->tdfoliocontrol_id;
                                }
                                $aux_saldomntneto += ($dteAsoc->mntneto * $operador);
                                $aux_saldomntnetoband++;
                            }
                            if($aux_saldomntneto == 0){
                                $aux_nrodoctoQueAnula = [
                                    "id" => $dteAsoc->id,
                                    "nrodocto" => $dteAsoc->nrodocto,
                                    "foliocontrol_desc" => $dteAsoc->foliocontrol->desc,
                                ];
                            }
                        }
                    }
                }
                $respuesta['anulado'] = false;
                if($aux_saldomntneto > 0){
                    $aux_nrodoctoQueAnula = [];
                }else{
                    $respuesta['mensaje'] = $dte->foliocontrol->desc . ":" . $request->nrodocto . " anulado por " . $aux_nrodoctoQueAnula["foliocontrol_desc"] . ":" . $aux_nrodoctoQueAnula["nrodocto"];
                    $respuesta['anulado'] = true;
                }
                $i = 0;
                foreach ($dte->dtedets as $dtedet){
                    $dte->dtedets[$i]->dtedetAsociados = [];
                    $dtedetAsociados = [];
                    foreach ($dtedet->dtedets as $dtedetss) {
                        $contanul = 0;
                        if(!is_null($dtedetss->dte->nrodocto)){ //SI NO SE HA ASIGNADO DTE NO ESTA EN SII, ES DECIR NO SE HA ASIGNADO A UN DTE
                            $dteQueAnula = $dtedetss->dte;
                            //SI EL DTE TIENE UN DTE QUE LO ANULA LO BUSCO
                            //SI TIENE stedter ESE DTE QUE ANULA EL CAMPO nrodocto DEBE SER DIFERENTE A EMPTY O VACIO 
                            //$dteQueAnula->dtedter->dte->statusgen= NUMERO DE DOCUMENTO ACEPTADO POR SII DEL DOCUMENTO QUE ANULA
                            if($dteQueAnula->dtedter){ 
                                if(!empty($dteQueAnula->dtedter->dte->statusgen)){
                                    //AQUI INCLUSO BUSCO SI ESE DTE QUE ANULA TAMBIEN ES ANULADO POR OTRO DTE
                                    //EJEMPLO: SI LA NC:30 FUE ANULADA POR UNA ND:155, PERO ESTA ND:155 TAMBIEN FUE ANULADA POR NC:31, ESTO QUIERE QUE LA NC :30 VUELVE A ESTAR ACTIVA
                                    do {
                                        $dteQueAnula = $dteQueAnula->dtedter->dte;
                                        $contanul++;
                                    } while ($dteQueAnula->dtedter);
                                }
                            }
                            //SI EL VALOR DE $contanul ES 0 EL DOCUMENTO NUNCA FUE ANULADO, AHORA SI ES PAR ESO QUIERE DECIR QUE EL DOCUMENTO QUE EL NUNCA FUE ANULADO POR OTRO.
                            //CUALQUIER A DE ESTAAS 2 OPCIONES QUIERE DECIR QUE EL DOCUMENTO ESTA ACTIVO O VIVO AUN.
                            if(($contanul % 2) == 0){
                                $dtedetAsociados[] = [
                                    "id" => $dtedetss->id,
                                    "dte_id" => $dtedetss->dte_id,
                                    "nrodocto" => $dtedetss->dte->nrodocto,
                                    "dtedet_id" => $dtedetss->dtedet_id,
                                    "foliocontrol_id" => $dtedetss->dte->foliocontrol_id,
                                ]; //"dte_id=" . $dtedetss->dte->id . ",foliocontrol_id=" . $dtedetss->dte->foliocontrol_id . ",dtedet_id=" . $dtedetss->id;
                            }
                        }
                    }
                    $dte->dtedets[$i]->dtedetAsociados = $dtedetAsociados; //implode(",", $dtedetAsociados);
                    $i++;
                }
                //dd($dte->dtedets);
                $respuesta['totaloriginal'] = $dte->mntneto;
                $respuesta['dtefacdet'] = $dte->dtedets;
                $respuesta['totalmodificado'] = $aux_saldomntneto;
                $respuesta['nrodoctoQueAnula'] = $aux_nrodoctoQueAnula;
                


                //hasta aqui la prueba

                /*
                foreach($dtefactdets1 as $dtefactdet1)
                $dtefactdets = array(); //$dte->dtedets;
                foreach ($dte->dtedets as $dtedet) {
                    $respuesta['totaloriginal'] += $dtedet->montoitem;
                }
                if($request->TipoDTE == "NC"){
                    foreach ($dte->dtedtefacasosiadas as $dtedtefacasosiada) {
                        //BUSCO TODOS LOS DTE RELACIONADOS A LA FACTURA EXCLUYENDO LA FACTURA ORIGINAL
                        if($dtedtefacasosiada->dte_id != $dte->id){ //EXCLUYO LA MISMA FACTURA
                            //ME UBICO EN EL DTE RELACIONADO
                            $dteNCND = Dte::findOrFail($dtedtefacasosiada->dte_id);
                            if(is_null($dteNCND->dteanul)){
                                if($dteNCND->foliocontrol_id == 5){
                                    $operador = -1;
                                    //RECORRO EL DETALLE DE LA NC
                                    foreach ($dteNCND->dtedets as $dteNCNDdet) {
                                        for ($i=0; $i < count($dtefactdets1); $i++) { 
                                            if($dteNCNDdet->dtedet_id == $dtefactdets1[$i]->id){
                                                $dtefactdets1[$i]->qtyitem += ($dteNCNDdet->qtyitem * $operador);
                                                $dtefactdets1[$i]->montoitem += ($dteNCNDdet->montoitem * $operador);
                                            }
                                        }
                                    }
                                    //CON ESTO SE TRAE TODOS LOS DTE ASOCIADOS A LA NC
                                    //ES DECIR TODAS LAS ND GENERADAS CONTRA LA NC
                                    //ESTO ES PARA BUSCAR LAS ND QUE ANULARON O RESTARON A LA NC EN CURSO
                                    foreach ($dteNCND->dtedters as $dtedter){
                                        //LUEGO RECORRO EL DETALLE DE LA ND PARA SUMARLE AL DETALLE FACTURA
                                        foreach ($dtedter->dte->dtedets as $dtedet) {
                                            for ($i=0; $i < count($dtefactdets1); $i++) { 
                                                if($dtedet->dtedet_id == $dtefactdets1[$i]->id){
                                                    dd($dtedet);
                                                    $dtefactdets1[$i]->qtyitem += ($dtedet->qtyitem);
                                                    $dtefactdets1[$i]->montoitem += ($dtedet->montoitem);
                                                }
                                            }                                                
                                        }
                                    }
                                }
                            }
                        }
                    }    
                }
                if ($respuesta['dte'][0]->codref == 1){
                    $respuesta['mensaje'] = "Documento anulado, " . $respuesta['dte'][0]->dte_nombreDocRefAnul . ": " . $respuesta['dte'][0]->dte_dterefanul;
                }else{
                    foreach ($dtefactdets1 as $dtefactdet) {
                        if($dtefactdet->qtyitem <= 0){
                            $dtefactdet->qtyitem = 1;
                            $dtefactdet->prcitem = $dtefactdet->montoitem;
                        }    
                        if($dtefactdet->montoitem > 0){ //SI EL REGISTRO QUEDO EN CERO 0 LO ELIMINO DE LO QUE ENVIO
                            $dtefactdets[] = $dtefactdet;
                        }
                    }
                    if(count($dtefactdets) <= 0){
                        $respuesta['mensaje'] = "Documento sin saldo para Hacer Nota de Crédito.";
                    }
                }
                //$respuesta['dtefacdet'] = $dtefactdets;
                $dte = Dte::findOrFail($dtesql[0]->dte_id);
                //dd($dte->dtedets);
                $respuesta['dtefacdet'] = $dte->dtedets;
                //$respuesta['dtefacdet'] = $dtefactdets->toArray();
                foreach ($dtefactdets as $dtefactdet) {
                    $respuesta['totalmodificado'] += $dtefactdet->montoitem;
                }
                */
            }else{
                $respuesta['mensaje'] = "Número de documento no existe";
            }
            //dd($respuesta);
            return $respuesta;
        }        
    }

    public static function generardte($request){
        if ($request->ajax()) {
            //dd($request);
            $dte = Dte::findOrFail($request->dte_id);
            if(is_null($dte->cliente->giro) or empty($dte->cliente->giro) or $dte->cliente->giro ==""){
                return response()->json([
                    'id' => 0,
                    'mensaje'=>'Giro de Cliente no puede estar vacio.',
                    'tipo_alert' => 'error'
                ]);
            }
            if($dte->updated_at != $request->updated_at){
                return response()->json([
                    'id' => 0,
                    'mensaje'=>'Registro fué creado o modificado por otro usuario.',
                    'tipo_alert' => 'error'
                ]);
            }
            if(!is_null($dte->statusgen)){
                return response()->json([
                    'id' => 0,
                    'mensaje'=>'DTE ya fue Generada! Nro: ' . $dte->nrodocto,
                    'tipo_alert' => 'error'
                ]);
            }
            //$foliocontrol = Foliocontrol::where("doc","=","FAVE")->get();
            $foliocontrol = Foliocontrol::findOrFail($request->foliocontrol_id);
            if(is_null($foliocontrol)){
                return response()->json([
                    'id' => 0,
                    'mensaje'=>'Numero de folio no encontrado.',
                    'tipo_alert' => 'error'
                ]);
            }
            if($foliocontrol->ultfoliouti >= $foliocontrol->ultfoliohab ){
                return response()->json([
                    'id' => 0,
                    'mensaje'=>'Se agotaron los folios. Se deben pedir nuevos folios',
                    'tipo_alert' => 'error'
                ]);
            }
            /*
            $ArchivoTXT = dtefactura($dte->id,"12345","XML",$request);
            dd($ArchivoTXT);
            */
            //dd("entro");
            $dte->fchemis = date('Y-m-d');
            $dte->save();
            $foliocontrol = Foliocontrol::findOrFail($dte->foliocontrol_id);
            if($foliocontrol->bloqueo == 1){
                $aux_guidesp = Dte::whereNotNull("nrodocto")
                            ->whereNull("statusgen")
                            ->whereNull("deleted_at")
                            ->get();
                //dd($aux_guidesp);
                if(count($aux_guidesp) == 0){
                    return response()->json([
                        'id' => 0,
                        'mensaje'=>'Folio bloqueado, vuelva a intentar. Folio: ' . $foliocontrol->ultfoliouti,
                        'tipo_alert' => 'error'
                    ]);
                }else{
                    if(is_null($dte->nrodocto)){
                        return response()->json([
                            'id' => 0,
                            'mensaje' => 'Existe un DTE pendiente por Generar: ' . $aux_guidesp[0]->nrodocto,
                            'tipo_alert' => 'error'
                        ]);        
                    }
                }
            }else{
                //Si $foliocontrol->bloqueo = 0;
                //Bloqueo el registro para que no pueda ser modificado por otro usuario
                //Al procesar el registro desbloqueo 
                $foliocontrol->bloqueo = 1;
                $foliocontrol->save();
            }
            $empresa = Empresa::findOrFail(1);
            $soap = new SoapController();
            $aux_folio = $dte->nrodocto;
            //dd($dte->nrodocto);
            if(!is_null($dte->nrodocto)){
                $Estado_DTE = $soap->Estado_DTE($empresa->rut,$foliocontrol->tipodocto,$aux_folio);
                if($Estado_DTE->Estatus == 3){
                    $bandNoExisteFolio = false;
                    $aux_folio = null;
                    $dte->nrodocto = null;
                    $foliocontrol->bloqueo = 0;
                    $foliocontrol->save();
                }
            }
            //dd($dte->nrodocto);
            if(is_null($dte->nrodocto)){
                $bandNoExisteFolio = true;
                do {
                    $Solicitar_Folio = $soap->Solicitar_Folio($empresa->rut,$foliocontrol->tipodocto);
                    if(isset($Solicitar_Folio->Estatus)){
                        if($Solicitar_Folio->Estatus == 0){
                            $Estado_DTE = $soap->Estado_DTE($empresa->rut,$foliocontrol->tipodocto,$Solicitar_Folio->Folio);
                            if($Estado_DTE->Estatus == 3){
                                $bandNoExisteFolio = false;
                                $aux_folio = $Solicitar_Folio->Folio;
                            }
                        }else{
                            $foliocontrol->bloqueo = 0;
                            $foliocontrol->save();
                            //dd($Solicitar_Folio);
                            return response()->json([
                                'id' => 0,
                                'mensaje'=>'Error: #' . $Solicitar_Folio->Estatus . " " . $Solicitar_Folio->MsgEstatus,
                                'tipo_alert' => 'error'                
                            ]);    
                        }
                    }else{
                        $foliocontrol->bloqueo = 0;
                        $foliocontrol->save();    
                        return response()->json([
                            'id' => 0,
                            'mensaje'=>'Error: ' . $Solicitar_Folio,
                            'tipo_alert' => 'error'                
                        ]);    
                    }
                }while($bandNoExisteFolio);
            }
            $tipoArch = "XML";
            $ArchivoTXT = dtefactura($dte->id,$aux_folio,$tipoArch,$request);
            $Carga_TXTDTE = $soap->Carga_TXTDTE($ArchivoTXT,$tipoArch);
            //$Carga_TXTDTE = $soap->Carga_TXTDTE($ArchivoTXT,"XML");
            //dd($Carga_TXTDTE->Estatus);
            if(isset($Carga_TXTDTE->Estatus)){
                //ACTUALIZO EL CAMPO nrodocto
                //SI OCURRIO ALGUN ERROR SE QUE TENGO EL FOLIO, 
                //SE QUE NO LO PUEDO VOLVER A PEDIR PORQUE POR ALGUNA RAZON SE GENERO UN ERROR EN EL ULTIMO FOLIO SOLICITADO
                /*$aux_giadesp = Dte::where('id', $dte->id)
                        ->update(['nrodocto' => $aux_folio]);*/
                if($Carga_TXTDTE->Estatus == 0){
                    $dte->fchemisgen = date("Y-m-d H:i:s");
                    //$date = str_replace('/', '-', $request->fchemis);
                    //$dte->fchemis = date('Y-m-d', strtotime($date));
                    //$dte->fchemis = date("Y-m-d H:i:s");
                    /*
                    $dte->pdf = $Carga_TXTDTE->PDF;
                    $dte->pdfcedible = $Carga_TXTDTE->PDFCedible;
                    $dte->xml = $Carga_TXTDTE->XML;
                    */
                    $dte->nrodocto = $aux_folio;
                    $dte->statusgen = 1;
                    $dte->aprobstatus = 1;
                    $dte->aprobusu_id = auth()->id();
                    $dte->aprobfechahora = date("Y-m-d H:i:s");
        
                    $dte->save();
                    //$fchemisDMY = date("d-m-Y_His",strtotime($dte->fchemis));
                    $nombreArchPDF =  $foliocontrol->nombrepdf . str_pad($aux_folio, 8, "0", STR_PAD_LEFT);
                    Storage::disk('public')->put('/facturacion/dte/procesados/' . $nombreArchPDF . '.xml', $Carga_TXTDTE->XML);
                    Storage::disk('public')->put('/facturacion/dte/procesados/' . $nombreArchPDF . '.pdf', $Carga_TXTDTE->PDF);
                    Storage::disk('public')->put('/facturacion/dte/procesados/' . $nombreArchPDF . '_cedible.pdf', $Carga_TXTDTE->PDFCedible);
                    /*
                    $pdf = new Fpdi();
                    $files = array("storage/facturacion/dte/procesados/" . $nombreArchPDF . ".pdf","storage/facturacion/dte/procesados/" . $nombreArchPDF . "_cedible.pdf");
                    foreach ($files as $file) {
                        $pageCount = $pdf->setSourceFile($file);
                        for ($pagNo=1; $pagNo <= $pageCount; $pagNo++) { 
                            $template = $pdf->importPage($pagNo);
                            $size = $pdf->getTemplateSize($template);
                            $pdf->AddPage($size['orientation'], $size);
                            $pdf->useTemplate($template);
                        }
                    }
                    $pdf->Output("F","storage/facturacion/dte/procesados/" . $nombreArchPDF . "_U.pdf");
                    */

                    //dd($Carga_TXTDTE);
                }else{
                    /*
                    $foliocontrol->bloqueo = 0;
                    $foliocontrol->save();
                    */
                    return response()->json([
                        'id' => 0,
                        'mensaje'=>'Error: #' . $Carga_TXTDTE->Estatus . " " . $Carga_TXTDTE->MsgEstatus,
                        'tipo_alert' => 'error'                
                    ]);    
                }
            }else{
                /*
                $foliocontrol->bloqueo = 0;
                $foliocontrol->save();
                */
                return response()->json([
                    'id' => 0,
                    'mensaje'=>'Error: ' . $Solicitar_Folio,
                    'tipo_alert' => 'error'                
                ]);
            }

            $dte = Dte::findOrFail($request->dte_id);
            return updatenumfact($dte,$foliocontrol,$request);
        } else {
            abort(404);
        }    
    }

    public static function generardteprueba($dte){
        /*
        $tipoArch = "XML";
        $ArchivoTXT = dtefacturaprueba($dte,"2514",$tipoArch);
        dd($ArchivoTXT);
        */
        $foliocontrol = Foliocontrol::findOrFail($dte->foliocontrol_id);
        if(is_null($foliocontrol)){
            return [
                'id' => 0,
                'mensaje'=>'Numero de folio no encontrado.',
                'tipo_alert' => 'error'
            ];
        }
        if($foliocontrol->ultfoliouti >= $foliocontrol->ultfoliohab ){
            return [
                'id' => 0,
                'mensaje'=>'Se agotaron los folios. Se deben pedir nuevos folios',
                'tipo_alert' => 'error'
            ];
        }
        $foliocontrol = Foliocontrol::findOrFail($dte->foliocontrol_id);
        if($foliocontrol->bloqueo == 1){
                return [
                    'id' => 0,
                    'mensaje'=>'Folio bloqueado, vuelva a intentar. Folio: ' . $foliocontrol->ultfoliouti,
                    'tipo_alert' => 'error'
                ];
        }else{
            //Si $foliocontrol->bloqueo = 0;
            //Bloqueo el registro para que no pueda ser modificado por otro usuario
            //Al procesar el registro desbloqueo 
            $foliocontrol->bloqueo = 1;
            $foliocontrol->save();
        }
        $empresa = Empresa::findOrFail(1);
        $soap = new SoapController();
        //SI DOCUMENTO YA FUE ASIGNADO, QUIERE DECIR QUE SE PUEDE VOLVER A GENERAR
        //PERO SI NO HA SIDO ELIMINADO EN LA PLATAFORMA DE BES NO DEJA VOLVER A SUBIRLO
        if($dte->nrodocto != ""){
            $Estado_DTE = $soap->Estado_DTE($empresa->rut,$foliocontrol->tipodocto,$dte->nrodocto);
            if($Estado_DTE->EstadoDTE == 0 and $Estado_DTE->Estatus == 3){ // EN ESTE CASO EL DOCUMENTO NO EXISTE EN LA PLATAFORMA BES, ESO QUIERE DECIR QUE FUE ELIMINADO PREVIAMENTE
                $aux_folio = $dte->nrodocto;
            }else{ //PERO SI NO HA SIDO ELIMINADO EN PLATAFORMA BES NO DEJA VOLVER A GENERAR EL DTE
                return [
                    'id' => 0,
                    'titulo' => "DTE $dte->nrodocto Generado en SII.",
                    'mensaje' => "Estatus: " . $Estado_DTE->DescEstado,
                    'tipo_alert' => 'error'
                ];
            }
        }else{
            //SI EL $dte->nrodocto VIENE VACIO O = A NULL, ESO QUIERE DECIR QUE SE VA A GENERAR LA PRIMERA VEZ EL DTE
            $bandNoExisteFolio = true;
            do {
                $Solicitar_Folio = $soap->Solicitar_Folio($empresa->rut,$foliocontrol->tipodocto);
                if(isset($Solicitar_Folio->Estatus)){
                    if($Solicitar_Folio->Estatus == 0){
                        $Estado_DTE = $soap->Estado_DTE($empresa->rut,$foliocontrol->tipodocto,$Solicitar_Folio->Folio);
                        if($Estado_DTE->Estatus == 3){
                            $bandNoExisteFolio = false;
                            $aux_folio = $Solicitar_Folio->Folio;
                        }
                    }else{
                        //dd($Solicitar_Folio);
                        return [
                            'id' => 0,
                            'mensaje'=>'Error: #' . $Solicitar_Folio->Estatus . " " . $Solicitar_Folio->MsgEstatus,
                            'tipo_alert' => 'error'                
                        ];
                    }
                }else{
                    return [
                        'id' => 0,
                        'mensaje'=>'Error: TipoDTE ' . $foliocontrol->tipodocto . ", " . $Solicitar_Folio,
                        'tipo_alert' => 'error'                
                    ];
                }
            }while($bandNoExisteFolio);    
        }

        $tipoArch = "XML";
        $ArchivoTXT = dtefacturaprueba($dte,$aux_folio,$tipoArch);
        $Carga_TXTDTE = $soap->Carga_TXTDTE($ArchivoTXT,$tipoArch);
        if(isset($Carga_TXTDTE->Estatus)){
            //ACTUALIZO EL CAMPO nrodocto
            //SI OCURRIO ALGUN ERROR SE QUE TENGO EL FOLIO, 
            //SE QUE NO LO PUEDO VOLVER A PEDIR PORQUE POR ALGUNA RAZON SE GENERO UN ERROR EN EL ULTIMO FOLIO SOLICITADO
            if($Carga_TXTDTE->Estatus == 0){
                $dte->fchemisgen = date("Y-m-d H:i:s");
                $dte->nrodocto = $aux_folio;
                /* 
                dte::guardarPdfXmlSii($aux_folio,$foliocontrol,$Carga_TXTDTE);
                $nombreArchPDF =  $foliocontrol->nombrepdf . str_pad($aux_folio, 8, "0", STR_PAD_LEFT);
                Storage::disk('public')->put('/facturacion/dte/procesados/' . $nombreArchPDF . '.xml', $Carga_TXTDTE->XML);
                Storage::disk('public')->put('/facturacion/dte/procesados/' . $nombreArchPDF . '.pdf', $Carga_TXTDTE->PDF);
                Storage::disk('public')->put('/facturacion/dte/procesados/' . $nombreArchPDF . '_cedible.pdf', $Carga_TXTDTE->PDFCedible);
                */
/*
                $pdf = new Fpdi();
                $files = array("storage/facturacion/dte/procesados/" . $nombreArchPDF . ".pdf","storage/facturacion/dte/procesados/" . $nombreArchPDF . "_cedible.pdf");
                foreach ($files as $file) {
                    $pageCount = $pdf->setSourceFile($file);
                    for ($pagNo=1; $pagNo <= $pageCount; $pagNo++) { 
                        $template = $pdf->importPage($pagNo);
                        $size = $pdf->getTemplateSize($template);
                        $pdf->AddPage($size['orientation'], $size);
                        $pdf->useTemplate($template);
                    }
                }
                $pdf->Output("F","storage/facturacion/dte/procesados/" . $nombreArchPDF . "_U.pdf");
                */
                return [
                    'id' => 1,
                    'Carga_TXTDTE' => $Carga_TXTDTE
                ];
                

            }else{
                return [
                    'id' => 0,
                    'mensaje'=>'Error: #' . $Carga_TXTDTE->Estatus . " " . $Carga_TXTDTE->MsgEstatus,
                    'tipo_alert' => 'error'                
                ];
            }
        }else{
            /* return [
                'id' => 0,
                'mensaje'=> nl2br("Error: Texto contiene letras acentuadas o caracteres especiales. POR FAVOR REVISAR DATOS DEL CLIENTE \n\n") . $Carga_TXTDTE,
                'tipo_alert' => "error"
            ]; */
            return [
                'id' => 0,
                'mensaje'=> nl2br("Error: Documento no fue guardado en SII. Error devueldo por BES. Variable Carga_TXTDTE->Estatus no existe. \n\n") . $Carga_TXTDTE,
                'tipo_alert' => "error"
            ];

        }
    }

    public static function guardarPdfXmlSii($aux_folio,$foliocontrol,$Carga_TXTDTE){
        $nombreArchPDF =  $foliocontrol->nombrepdf . str_pad($aux_folio, 8, "0", STR_PAD_LEFT);
        Storage::disk('public')->put('/facturacion/dte/procesados/' . $nombreArchPDF . '.xml', $Carga_TXTDTE->XML);
        Storage::disk('public')->put('/facturacion/dte/procesados/' . $nombreArchPDF . '.pdf', $Carga_TXTDTE->PDF);
        Storage::disk('public')->put('/facturacion/dte/procesados/' . $nombreArchPDF . '_cedible.pdf', $Carga_TXTDTE->PDFCedible);
    }

    public static function anulardte($request)
    {
        //PROCESO DE ANULAR DTE SIN HABER ASIGNADO O GENERADO UN NUMERO DE DTE (DOCUMENTO TRIBUTARIO LELECTRONICO)
        //dd($request);
        $dte = Dte::findOrFail($request->dte_id);
        $request->request->add(['obs' => $dte->foliocontrol->desc]);
        $request->request->add(['motanul_id' => 5]);
        $request->request->add(['moddevgiadesp_id' => $dte->foliocontrol->doc]);

        $aux_dtehijos = false;
        foreach ($dte->dtedters as $dtedter) {
            if($dtedter->dte->dteanul == null){
                return response()->json([
                    "id" => 0,
                    "title" => "Registro no puede se anulado. ",
                    "mensaje" => "Previamente debe anular documento asociado:\n" . $dtedter->dte->foliocontrol->desc . " Nro: " . $dtedter->dte->nrodocto . "",
                    "tipo_alert" => 'error'
                ]);
    
            }
        }
        if($request->updated_at != $dte->updated_at){
            return response()->json([
                'id' => 0,
                'mensaje'=>'Registro no puede editado, fué modificado por otro usuario.',
                'tipo_alert' => 'error'
            ]);
        }
        $dte->updated_at = date("Y-m-d H:i:s"); //ACTUALIZO LA FECHA PARA EVITAR QUE OTRO USUARIO HAGA ALGO SOBRE EL REGISTRO MODIFICADO

        //INSERTO UN REGISTRO EN DTE ANULADAS
        $dteanul = new DteAnul();
        $dteanul->dte_id = $request->dte_id;
        if(isset($request->obs)){
            $dteanul->obs = $request->obs;
        }
        if(isset($request->motanul_id)){
            $dteanul->motanul_id = $request->motanul_id;
        }
        if(isset($request->moddevgiadesp_id)){
            $dteanul->moddevgiadesp_id = $request->moddevgiadesp_id;
        }
        $dteanul->usuario_id = auth()->id();

        foreach ($dte->dtedtes as $dtedte) {
            //SI ES FACTURA ELIMINO LOS REGISTROS EN DTEGUIAUSADA
            if($dteanul->moddevgiadesp_id == "FC"){
                //DteDte::destroy($dtedte->id); //ELIMINO LAS GUIAS ASOCIADAS A LA FACTURA
                DteGuiaUsada::destroy($dtedte->dter->dteguiausada->id); //ELIMINO LAS GUIAS USADAS POR LA FACTURA
            }
            //ACTUALIZAO EL CAMPO UPDATED_AT DE LOS REGISTROS ASOCIADOS
            $dter_id = Dte::findOrFail($dtedte->dter_id);
            $dter_id->updated_at = date("Y-m-d H:i:s");
            $dter_id->save();
            if(($dtedte->dtefac_id != $dtedte->dter_id) and ($dtedte->dtefac_id != $dtedte->dte_id)){
                $dtefac_id = Dte::findOrFail($dtedte->dtefac_id);
                $dtefac_id->updated_at = date("Y-m-d H:i:s");
                $dtefac_id->save();
            }
        }
        if($dte->save() and $dteanul->save()){
            return response()->json([
                'id' => 1,
                'mensaje'=>'Registro procesado con exito.',
                'tipo_alert' => 'success'
            ]);
        }else{
            return response()->json([
                'id' => 0,
                'mensaje'=>'Ocurrio un error al intentan Guardar el registro.',
                'tipo_alert' => 'error'
            ]);
        }
    }

    public static function busDTEOrig($dte_id){
        //BUSCAR FACTURA ORIGEN
        $aux_dteId = $dte_id;
        $arrayDTE = null;
        do{
            $dte = Dte::findOrFail($aux_dteId);
            $aux_dteId = $dte->dtedte ? $dte->dtedte->dter_id : "";
            if($dte->foliocontrol_id == 1 or $dte->foliocontrol_id == 7){
                $arrayDTE = [
                    "dtefac_id" => $dte->id,
                    "dte_id"    => $dte->id,
                    "dter_id"   => $aux_dteId,
                    "nrodocto_factura" => $dte->nrodocto
                ];
                break;
            }
        }while (isset($dte->dtedte) and $dte->foliocontrol_id != 1);
        return $arrayDTE;

    }

    public static function busDTEDETOrig($dtedet1,$foliocontrol_id){
        //BUSCAR FACTURA ORIGEN
        //dd($dtedet);
        $aux_dtedet_id = $dtedet1->dtedet_id;
        $arrayDTEDet = null;
        $dtedet = DteDet::findOrFail($aux_dtedet_id);
        $dte = $dtedet->dte;
        do{
            if($dte->foliocontrol_id == $foliocontrol_id){
                $arrayDTEDet = $dtedet;
                break;
            }else{
                //BUSCO EL ORIGEN DEL DTE 
                $dte = $dte->dtedte->dter;
            }
        }while ($dte->foliocontrol_id != $foliocontrol_id);
        return $arrayDTEDet;

    }

    public static function reportdtencnd($request){
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
            $vendedorcond = "notaventa.vendedor_id='$request->vendedor_id'";
        }
    
        if(empty($request->fechad) or empty($request->fechah)){
            $aux_condFecha = " true";
        }else{
            $fecha = date_create_from_format('d/m/Y', $request->fechad);
            $fechad = date_format($fecha, 'Y-m-d');
            $fecha = date_create_from_format('d/m/Y', $request->fechah);
            $fechah = date_format($fecha, 'Y-m-d');
            $aux_condFecha = "dte.fchemis>='$fechad' and dte.fchemis<='$fechah'";
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
    
        if(empty($request->dtenotnull)){
            $aux_conddtenotnull = " true";
        }else{
            $aux_conddtenotnull = "dte.id NOT IN (SELECT dteanul.dte_id FROM dteanul WHERE ISNULL(dteanul.deleted_at))";
        }

        if(empty($request->arrdte_id)){
            $aux_conddtenotnull = " true";
        }else{
            $aux_conddtenotnull = "dte.id NOT IN (SELECT dteanul.dte_id FROM dteanul WHERE ISNULL(dteanul.deleted_at))";
        }
        if(empty($request->strdte_id)){
            $aux_conddtedet = " true";
        }else{
            $aux_conddtedet = "dte.id IN ($request->strdte_id)";
        }

        if(empty($request->nrodocto)){
            $aux_condnrodocto = " true";
        }else{
            $aux_condnrodocto = "dte.nrodocto = $request->nrodocto";
        }

        if(empty($request->dte_id)){
            $aux_conddte_id = " true";
        }else{
            $aux_conddte_id = "dte.id = $request->dte_id";
        }
        if(!isset($request->aprobstatus) or empty($request->aprobstatus)){
            $aux_aprobstatus = " true";
        }else{
            switch ($request->aprobstatus) {
                case 0:
                    $aux_aprobstatus = " true";
                    break;
                case 1:
                    $aux_aprobstatus = " isnull(dteanul.obs)";
                    break;    
                case 2:
                    $aux_aprobstatus = " not isnull(dteanul.obs)";
                    break;
            }
        }


        $user = Usuario::findOrFail(auth()->id());
        $sucurArray = $user->sucursales->pluck('id')->toArray();
        $sucurcadena = implode(",", $sucurArray);
        $aux_condsucurArray = "dte.sucursal_id  in ($sucurcadena)";
        if(!isset($request->sucursal_id) or empty($request->sucursal_id) or ($request->sucursal_id == "")){
            $aux_sucursal_idCond = "true";
        }else{
            $aux_sucursal_idCond = "dte.sucursal_id = $request->sucursal_id";
        }
        $sql = "SELECT dte.id,dte.fechahora,dte.nrodocto,cliente.rut,cliente.razonsocial,comuna.nombre as nombre_comuna,
        clientebloqueado.descripcion as clientebloqueado_descripcion,
        dteorigen.id as dteorigen_id,dteorigen.nrodocto as dteorigen_nrodocto,foliocontrol.doc,foliocontrol.nombrepdf,
        dte.updated_at, dteorigen.updated_at as dteorigen_updated_at,dte.mnttotal,
        dteanul.obs as dteanul_obs,dteanul.created_at as dteanulcreated_at
        FROM dte INNER JOIN dtedte
        ON dte.id = dtedte.dte_id AND ISNULL(dte.deleted_at) and isnull(dtedte.deleted_at)
        LEFT JOIN dte as dteorigen
        ON dtedte.dter_id = dteorigen.id and isnull(dteorigen.deleted_at)
        LEFT JOIN foliocontrol
        ON foliocontrol.id = dteorigen.foliocontrol_id
        INNER JOIN cliente
        ON dte.cliente_id  = cliente.id AND ISNULL(cliente.deleted_at)
        INNER JOIN comuna
        ON comuna.id = cliente.comunap_id AND ISNULL(comuna.deleted_at)
        LEFT JOIN clientebloqueado
        ON dte.cliente_id = clientebloqueado.cliente_id AND ISNULL(clientebloqueado.deleted_at)
        LEFT JOIN dteanul
        ON dteanul.dte_id = dte.id AND ISNULL(dteanul.deleted_at)
        WHERE dte.foliocontrol_id = $request->foliocontrol_id
        AND dte.sucursal_id IN ($sucurcadena)
        AND $aux_sucursal_idCond
        AND $aux_conddte_id
        AND $aux_condFecha
        AND $aux_condnrodocto
        AND $aux_condrut
        AND $aux_condoc_id
        AND $aux_condnotaventa_id
        AND $aux_aprobstatus
        AND $aux_condsucurArray
        GROUP BY dte.id
        ORDER BY dte.id asc;";
        //dd($sql);

        $arrays = DB::select($sql);
        return $arrays;
    }

    public static function updateStatusGen($dte,$request){
        $dte->statusgen = 1;
        $dte->aprobstatus = 1;
        $dte->aprobusu_id = auth()->id();
        $dte->aprobfechahora = date("Y-m-d H:i:s");
        if ($dte->save()) {
            return response()->json([
                                    'mensaje' => 'ok',
                                    'status' => '0',
                                    'id' => $request->id,
                                    'nfila' => $request->nfila,
                                    'nrodocto' => $dte->nrodocto
                                    ]);
        } else {
            return response()->json(['mensaje' => 'ng']);
        }
    }

    public static function setFoto($foto,$id,$request,$ininombre,$ruta, $actual = false){
        //dd($foto);
        $id = $ininombre . str_pad($id, 8, "0", STR_PAD_LEFT);
        if ($foto) {
            if ($actual) {
                $actual = $id;
                Storage::disk('public')->delete("imagenes/$ruta/$actual");
            }
            $file = $request->file('oc_file');
            $nombre = $file->getClientOriginalName();
            $info = new SplFileInfo($nombre);
            $ext = strtolower($info->getExtension()); //Obtener extencion de un archivo
            //$imageName = Str::random(10) . '.jpg';
            $imageName = $id . '.' . $ext;
            //dd($imageName);
            //      $imagen = Image::make($foto)->encode('jpg', 75);
            //$imagen->fit(530, 470); //Fit() SUpuestamente mantiene la proporcion de la imagen
            /*$imagen->resize(530, 470, function ($constraint) {
                $constraint->upsize();
            });*/
            //Storage::disk('public')->put("imagenes/notaventa/$imageName", $imagen->stream());
            //Storage::disk('public')->put("imagenes/notaventa/$imageName", $file);
            $file->move(public_path() . "/storage/imagenes/$ruta/" , $imageName);
            //$request->file('')
            return $imageName;
        } else {
            if ($actual) {
                $actual = $id;
                Storage::disk('public')->delete("imagenes/$ruta/$actual");
                return "null";
            }else{
                return false;
            }
        }
    }

    public static function procesarDTE($request)
    {
        $dte = Dte::findOrFail($request->dte_id);   
        if($dte->updated_at != $request->updated_at){
            return response()->json([
                'id' => 0,
                'mensaje'=>'Registro modificado por otro usuario.',
                'tipo_alert' => 'warning'
            ]);
        }
        $empresa = Empresa::findOrFail(1);
        return Dte::updateStatusGen($dte,$request);
        //ESTA VALIDACION LA DESACTIVE A PETICION DE ERIKA BUSTOS
        //LE PARECE MUCHO TIEMPO DE ESPERA POR LA APROBACION DE UN DTE POR PARTE DE BES Y SII
        /*
        if($dte->foliocontrol_id == 2){
            return Dte::updateStatusGen($dte,$request);
        }else{
            $soap = new SoapController();
            $Estado_DTE = $soap->Estado_DTE($empresa->rut,$dte->foliocontrol->tipodocto,$dte->nrodocto);
            //$Estado_DTE = $soap->Estado_DTE($empresa->rut,$dte->foliocontrol->tipodocto,"200");
            //dd($Estado_DTE);
            if($Estado_DTE->Estatus == 3){
                return response()->json([
                    'id' => 0,
                    'title' => "DTE aun no ha sido acetado por SII",
                    'mensaje' => "Estado: " . $Estado_DTE->MsgEstatus . "\n DTE Nro: " . $dte->nrodocto,
                    'tipo_alert' => 'warning'
                ]);
            }
            if($Estado_DTE->EstadoDTE == 16){
                return Dte::updateStatusGen($dte,$request);
            }else{
                return response()->json([
                    'id' => 0,
                    'title' => "DTE aun no ha sido acetado por SII",
                    'mensaje' => "Estado: " . $Estado_DTE->DescEstado . "\n DTE Nro: " . $dte->nrodocto,
                    'tipo_alert' => 'warning'
                ]);
            }    
        }
        */
    }

    public static function reportestadocli($request){
        //dd($request);
        /*
        $vendedorcond = " true ";
        if(isset($request->vendedor_id)){
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
        }
        */
    
        if(!isset($request->fechad) or !isset($request->fechah) or empty($request->fechad) or empty($request->fechah)){
            $aux_condFecha = " true";
        }else{
            $fecha = date_create_from_format('d/m/Y', $request->fechad);
            $fechad = date_format($fecha, 'Y-m-d');
            $fecha = date_create_from_format('d/m/Y', $request->fechah);
            $fechah = date_format($fecha, 'Y-m-d');
            $aux_condFecha = "dte.fchemis>='$fechad' and dte.fchemis<='$fechah'";
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
    
        if(!isset($request->nrodocto) or empty($request->nrodocto)){
            $aux_condnrodocto = " true";
        }else{
            $aux_condnrodocto = "dte.nrodocto = $request->nrodocto";
        }
        if(!isset($request->dte_id) or empty($request->dte_id)){
            $aux_conddte_id = " true";
        }else{
            $aux_conddte_id = "dte.id = $request->dte_id";
        }

        if(!isset($request->statusgen) or empty($request->statusgen)){
            $aux_condstatusgen = " true";
        }else{
            $aux_condstatusgen = "dte.statusgen = $request->statusgen";
        }
        //dd($request->foliocontrol_id);
        if(!isset($request->foliocontrol_id) or empty($request->foliocontrol_id)){
            $aux_condfoliocontrol_id = " dte.foliocontrol_id=1 ";
        }else{
            $aux_condfoliocontrol_id = " dte.foliocontrol_id in $request->foliocontrol_id";
        }
        //dd($aux_condfoliocontrol_id);
        $user = Usuario::findOrFail(auth()->id());
        $sucurArray = $user->sucursales->pluck('id')->toArray();
        $sucurcadena = implode(",", $sucurArray);
        $aux_condsucurArray = "dte.sucursal_id  in ($sucurcadena)";
        if(!isset($request->sucursal_id) or empty($request->sucursal_id) or ($request->sucursal_id == "")){
            $aux_sucursal_idCond = "true";
        }else{
            $aux_sucursal_idCond = "dte.sucursal_id = $request->sucursal_id";
        }

        if(!isset($request->groupby) or empty($request->groupby)){
            $aux_groupby = "";
        }else{
            $aux_groupby = $request->groupby;
        }
        if(!isset($request->orderby) or empty($request->orderby)){
            $aux_orderby = "";
        }else{
            $aux_orderby = $request->orderby;
        }
        if(!isset($request->vendedor_id) or empty($request->vendedor_id)){
            $aux_condvendedor_id = " true";
        }else{
            $aux_condvendedor_id = "dte.vendedor_id in ($request->vendedor_id)";
        }
        $sql = "SELECT dte.id,dte.fechahora,dte.fchemis,dte.cliente_id,cliente.rut,cliente.razonsocial,comuna.nombre as nombre_comuna,
        clientebloqueado.descripcion as clientebloqueado_descripcion,dte.vendedor_id,
        GROUP_CONCAT(DISTINCT dtedte.dter_id) AS dter_id,
        GROUP_CONCAT(DISTINCT notaventa.cotizacion_id) AS cotizacion_id,
        GROUP_CONCAT(DISTINCT notaventa.oc_id) AS oc_id,
        GROUP_CONCAT(DISTINCT notaventa.oc_file) AS oc_file,
        GROUP_CONCAT(DISTINCT dteguiadesp.notaventa_id) AS notaventa_id,
        GROUP_CONCAT(DISTINCT despachoord.despachosol_id) AS despachosol_id,
        GROUP_CONCAT(DISTINCT dteguiadesp.despachoord_id) AS despachoord_id,
        (SELECT GROUP_CONCAT(DISTINCT dte1.nrodocto) 
        FROM dte AS dte1 INNER JOIN dtedte AS dtedte1
        ON dte1.id = dtedte1.dter_id AND ISNULL(dte1.deleted_at) and isnull(dtedte1.deleted_at)
        WHERE dtedte1.dte_id = dte.id
        GROUP BY dtedte1.dte_id) AS nrodocto_origen,
        (SELECT GROUP_CONCAT(DISTINCT dte1.nrodocto) 
        FROM dte AS dte1 INNER JOIN dtedte AS dtedte1
        ON dte1.id = dtedte1.dtefac_id AND ISNULL(dte1.deleted_at) and isnull(dtedte1.deleted_at)
        WHERE dtedte1.dte_id = dte.id
        GROUP BY dtedte1.dte_id) AS nrodoctofac_origen,
        (SELECT GROUP_CONCAT(DISTINCT foliocontrol.nombrepdf) 
        FROM dte AS dte1 INNER JOIN dtedte AS dtedte1
        ON dte1.id = dtedte1.dter_id AND ISNULL(dte1.deleted_at) and isnull(dtedte1.deleted_at)
        INNER JOIN foliocontrol
        ON foliocontrol.id = dte1.foliocontrol_id
        WHERE dtedte1.dte_id = dte.id
        GROUP BY dtedte1.dte_id) AS pdftipodte_origen,
        (SELECT GROUP_CONCAT(DISTINCT foliocontrol.desc) 
        FROM dte AS dte1 INNER JOIN dtedte AS dtedte1
        ON dte1.id = dtedte1.dter_id AND ISNULL(dte1.deleted_at) and isnull(dtedte1.deleted_at)
        INNER JOIN foliocontrol
        ON foliocontrol.id = dte1.foliocontrol_id
        WHERE dtedte1.dte_id = dte.id
        GROUP BY dtedte1.dte_id) AS foliocontroldesc_origen,
        dteanul.obs as dteanul_obs,dteanul.created_at as dteanulcreated_at,
        foliocontrol.doc as foliocontrol_doc,foliocontrol.desc as foliocontrol_desc,foliocontrol.tipodocto,foliocontrol.nombrepdf,
        dte.nrodocto,dte.updated_at,dtefac.staverfacdesp,dtefac.updated_at as dtefac_updated_at,
        (dte.mntneto * foliocontrol.signo) as mntneto,(dte.iva * foliocontrol.signo) as iva,(dte.mnttotal * foliocontrol.signo) as mnttotal_a,(dte.mnttotal * foliocontrol.signo) as mnttotal,
        sucursal.nombre as sucursal_nombre,if(isnull(dtefac.fchvenc),dte.fchemis,dtefac.fchvenc) as fchvenc,
        concat(persona.nombre,' ',persona.apellido) as vendedor_nombre,formapago.descripcion as formapago_descripcion
        FROM dte LEFT JOIN dtedte
        ON dte.id = dtedte.dte_id AND ISNULL(dte.deleted_at) and isnull(dtedte.deleted_at)
        LEFT JOIN dteguiadesp
        ON dtedte.dter_id = dteguiadesp.dte_id and isnull(dteguiadesp.deleted_at)
        LEFT JOIN despachoord
        ON despachoord.id = dteguiadesp.despachoord_id and isnull(despachoord.deleted_at)
        LEFT JOIN notaventa
        ON notaventa.id = despachoord.notaventa_id and isnull(notaventa.deleted_at)
        INNER JOIN cliente
        ON dte.cliente_id  = cliente.id AND ISNULL(cliente.deleted_at)
        INNER JOIN comuna
        ON comuna.id = cliente.comunap_id AND ISNULL(comuna.deleted_at)
        LEFT JOIN clientebloqueado
        ON dte.cliente_id = clientebloqueado.cliente_id AND ISNULL(clientebloqueado.deleted_at)
        LEFT JOIN dteanul
        ON dteanul.dte_id = dte.id AND ISNULL(dteanul.deleted_at)
        INNER JOIN foliocontrol
        ON  foliocontrol.id = dte.foliocontrol_id AND ISNULL(foliocontrol.deleted_at)
        LEFT JOIN dtefac
        ON dtefac.dte_id = dte.id
        INNER JOIN sucursal
        ON dte.sucursal_id = sucursal.id
        LEFT JOIN vendedor
        ON vendedor.id = dte.vendedor_id AND ISNULL(vendedor.deleted_at)
        LEFT JOIN persona
        ON persona.id = vendedor.persona_id AND ISNULL(persona.deleted_at)
        LEFT JOIN formapago
        ON formapago.id = dtefac.formapago_id AND ISNULL(formapago.deleted_at)
        WHERE $aux_condfoliocontrol_id
        AND dte.sucursal_id IN ($sucurcadena)
        AND $aux_sucursal_idCond
        AND $aux_conddte_id
        AND $aux_condFecha
        AND $aux_condnrodocto
        AND $aux_condrut
        AND $aux_condoc_id
        AND $aux_condnotaventa_id
        AND $aux_condstatusgen
        AND $aux_condvendedor_id
        AND $aux_condsucurArray
        AND NOT ISNULL(dte.nrodocto)
        AND dte.id NOT IN (SELECT dteanul.dte_id FROM dteanul WHERE ISNULL(dteanul.deleted_at))
        $aux_groupby
        $aux_orderby;";

        //dd($sql);
        $arrays = DB::select($sql);
        return $arrays;
    }

    public static function totalreportestadocli($request){
    
        if(!isset($request->fechad) or !isset($request->fechah) or empty($request->fechad) or empty($request->fechah)){
            $aux_condFecha = " true";
        }else{
            $fecha = date_create_from_format('d/m/Y', $request->fechad);
            $fechad = date_format($fecha, 'Y-m-d');
            $fecha = date_create_from_format('d/m/Y', $request->fechah);
            $fechah = date_format($fecha, 'Y-m-d');
            $aux_condFecha = "dte.fchemis>='$fechad' and dte.fchemis<='$fechah'";
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
    

        if(!isset($request->nrodocto) or empty($request->nrodocto)){
            $aux_condnrodocto = " true";
        }else{
            $aux_condnrodocto = "dte.nrodocto = $request->nrodocto";
        }
        if(!isset($request->dte_id) or empty($request->dte_id)){
            $aux_conddte_id = " true";
        }else{
            $aux_conddte_id = "dte.id = $request->dte_id";
        }

        if(!isset($request->statusgen) or empty($request->statusgen)){
            $aux_condstatusgen = " true";
        }else{
            $aux_condstatusgen = "dte.statusgen = $request->statusgen";
        }
        //dd($request->foliocontrol_id);
        if(!isset($request->foliocontrol_id) or empty($request->foliocontrol_id)){
            $aux_condfoliocontrol_id = " dte.foliocontrol_id=1 ";
        }else{
            $aux_condfoliocontrol_id = " dte.foliocontrol_id in $request->foliocontrol_id";
        }
        //dd($aux_condfoliocontrol_id);
        $user = Usuario::findOrFail(auth()->id());
        $sucurArray = $user->sucursales->pluck('id')->toArray();
        $sucurcadena = implode(",", $sucurArray);
        if(!isset($request->sucursal_id) or empty($request->sucursal_id) or ($request->sucursal_id == "")){
            $aux_sucursal_idCond = "true";
        }else{
            $aux_sucursal_idCond = "dte.sucursal_id = $request->sucursal_id";
        }
    
        $sql = "SELECT sum(dte.mnttotal * foliocontrol.signo) as mnttotal
        FROM dte INNER JOIN cliente
        ON dte.cliente_id  = cliente.id AND ISNULL(cliente.deleted_at)
        INNER JOIN foliocontrol
        ON  foliocontrol.id = dte.foliocontrol_id AND ISNULL(foliocontrol.deleted_at)
        WHERE $aux_condfoliocontrol_id
        AND dte.sucursal_id IN ($sucurcadena)
        AND $aux_sucursal_idCond
        AND $aux_conddte_id
        AND $aux_condFecha
        AND $aux_condnrodocto
        AND $aux_condrut
        AND $aux_condoc_id
        AND $aux_condstatusgen
        AND dte.id NOT IN (SELECT dteanul.dte_id FROM dteanul WHERE ISNULL(dteanul.deleted_at))
        AND NOT ISNULL(dte.nrodocto);";

        //dd($sql);
        $arrays = DB::select($sql);
        return $arrays;
    }

    public static function reportcomisionxvend($request){
        /*
        $vendedorcond = " true ";
        if(isset($request->vendedor_id)){
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
        }
        */
        $fechaannomes = date('Ym');
        if(!isset($request->fechad) or !isset($request->fechah) or empty($request->fechad) or empty($request->fechah)){
            $aux_condFecha = " true";
        }else{
            $fecha = date_create_from_format('d/m/Y', $request->fechad);
            $fechad = date_format($fecha, 'Y-m-d');
            $fecha = date_create_from_format('d/m/Y', $request->fechah);
            $fechah = date_format($fecha, 'Y-m-d');
            $fechaannomes = date_format($fecha, 'Ym');
            $aux_condFecha = "dte.fchemis>='$fechad' and dte.fchemis<='$fechah'";
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
    
        if(!isset($request->nrodocto) or empty($request->nrodocto)){
            $aux_condnrodocto = " true";
        }else{
            $aux_condnrodocto = "dte.nrodocto = $request->nrodocto";
        }
        if(!isset($request->dte_id) or empty($request->dte_id)){
            $aux_conddte_id = " true";
        }else{
            $aux_conddte_id = "dte.id = $request->dte_id";
        }

        if(!isset($request->statusgen) or empty($request->statusgen)){
            $aux_condstatusgen = " true";
        }else{
            $aux_condstatusgen = "dte.statusgen = $request->statusgen";
        }
        //dd($request->foliocontrol_id);
        if(!isset($request->foliocontrol_id) or empty($request->foliocontrol_id)){
            $aux_condfoliocontrol_id = " dte.foliocontrol_id=1 ";
        }else{
            $aux_condfoliocontrol_id = " dte.foliocontrol_id in $request->foliocontrol_id";
        }
        //dd($aux_condfoliocontrol_id);
        $user = Usuario::findOrFail(auth()->id());
        $sucurArray = $user->sucursales->pluck('id')->toArray();
        $sucurcadena = implode(",", $sucurArray);
        $aux_condsucurArray = "dte.sucursal_id  in ($sucurcadena)";
        if(!isset($request->sucursal_id) or empty($request->sucursal_id) or ($request->sucursal_id == "")){
            $aux_sucursal_idCond = "true";
        }else{
            $aux_sucursal_idCond = "dte.sucursal_id = $request->sucursal_id";
        }
        if(!isset($request->groupby) or empty($request->groupby)){
            $aux_groupby = "";
        }else{
            $aux_groupby = $request->groupby;
        }
        if(!isset($request->orderby) or empty($request->orderby)){
            $aux_orderby = "";
        }else{
            $aux_orderby = $request->orderby;
        }
        if(!isset($request->vendedor_id) or empty($request->vendedor_id)){
            $aux_condvendedor_id = " true";
        }else{
            $aux_condvendedor_id = "dte.vendedor_id in ($request->vendedor_id)";
        }
        $sucurJefAreaFis = Persona::sucursalFisica(auth()->id());
        $sucurJefAreaFis = implode(', ', $sucurJefAreaFis);
        $aux_condsucurJefAreaFis = "dte.sucursal_id in ($sucurJefAreaFis)";
        if(!isset($request->areaproduccion_id) or empty($request->areaproduccion_id)){
            $aux_condareaproduccion_id = " true ";
        }else{
            $aux_condareaproduccion_id = "categoriaprod.areaproduccion_id in ($request->areaproduccion_id)";
        }

        $sql = "SELECT dte.id,dte.fchemis,dte.fechahora,cliente.rut,cliente.razonsocial,comuna.nombre as nombre_comuna,
        clientebloqueado.descripcion as clientebloqueado_descripcion,dte.vendedor_id,
        persona.rut as vendedor_rut, CONCAT(persona.nombre,' ',persona.apellido) as vendedor_nombre,
        GROUP_CONCAT(DISTINCT dtedte.dter_id) AS dter_id,
        GROUP_CONCAT(DISTINCT notaventa.cotizacion_id) AS cotizacion_id,
        GROUP_CONCAT(DISTINCT notaventa.oc_id) AS oc_id,
        GROUP_CONCAT(DISTINCT notaventa.oc_file) AS oc_file,
        GROUP_CONCAT(DISTINCT dteguiadesp.notaventa_id) AS notaventa_id,
        GROUP_CONCAT(DISTINCT despachoord.despachosol_id) AS despachosol_id,
        GROUP_CONCAT(DISTINCT dteguiadesp.despachoord_id) AS despachoord_id,
        (SELECT GROUP_CONCAT(DISTINCT dte1.nrodocto) 
        FROM dte AS dte1 INNER JOIN dtedte AS dtedte1
        ON dte1.id = dtedte1.dter_id AND ISNULL(dte1.deleted_at) and isnull(dtedte1.deleted_at)
        WHERE dtedte1.dte_id = dte.id
        GROUP BY dtedte1.dte_id) AS nrodocto_origen,
        (SELECT GROUP_CONCAT(DISTINCT dte1.nrodocto) 
        FROM dte AS dte1 INNER JOIN dtedte AS dtedte1
        ON dte1.id = dtedte1.dtefac_id AND ISNULL(dte1.deleted_at) and isnull(dtedte1.deleted_at)
        WHERE dtedte1.dte_id = dte.id
        GROUP BY dtedte1.dte_id) AS nrodoctofac_origen,
        (SELECT GROUP_CONCAT(DISTINCT foliocontrol.nombrepdf) 
        FROM dte AS dte1 INNER JOIN dtedte AS dtedte1
        ON dte1.id = dtedte1.dter_id AND ISNULL(dte1.deleted_at) and isnull(dtedte1.deleted_at)
        INNER JOIN foliocontrol
        ON foliocontrol.id = dte1.foliocontrol_id
        WHERE dtedte1.dte_id = dte.id
        GROUP BY dtedte1.dte_id) AS pdftipodte_origen,
        (SELECT GROUP_CONCAT(DISTINCT foliocontrol.desc) 
        FROM dte AS dte1 INNER JOIN dtedte AS dtedte1
        ON dte1.id = dtedte1.dter_id AND ISNULL(dte1.deleted_at) and isnull(dtedte1.deleted_at)
        INNER JOIN foliocontrol
        ON foliocontrol.id = dte1.foliocontrol_id
        WHERE dtedte1.dte_id = dte.id
        GROUP BY dtedte1.dte_id) AS foliocontroldesc_origen,
        dteanul.obs as dteanul_obs,dteanul.created_at as dteanulcreated_at,
        foliocontrol.doc as foliocontrol_doc,foliocontrol.desc as foliocontrol_desc,foliocontrol.tipodocto,foliocontrol.nombrepdf,
        dte.nrodocto,dte.updated_at,dtefac.staverfacdesp,dtefac.updated_at as dtefac_updated_at,
        dtedet.producto_id,producto.diametro,claseprod.cla_nombre,producto.long,nmbitem,producto.tipounion,
        (dte.mnttotal * foliocontrol.signo) as mnttotal,
        sum(DISTINCT dtedet.montoitem * foliocontrol.signo) as montoitem,
        categoriaprodgrupo.comisionventas,
        categoriaprodgrupo.comisionventas as porc_comision,
        CEIL(sum(DISTINCT dtedet.montoitem * foliocontrol.signo) * (categoriaprodgrupo.comisionventas/100)) as comision,
        acuerdotecnico.at_ancho,acuerdotecnico.at_largo,acuerdotecnico.at_largo,at_espesor,
        materiaprima.nombre as materiaprima_nombre,materiaprima.desc as materiaprima_desc,
        unidadmedida.nombre as unidadmedida_nombre,at_formatofilm,
        sum(DISTINCT dtedet.qtyitem * foliocontrol.signo) as qtyitem,sum(DISTINCT dtedet.itemkg * foliocontrol.signo) as itemkg,sucursal.nombre as sucursal_nombre,
        sum(DISTINCT dtedet.prcitem * foliocontrol.signo) as prcitem,
        grupoprod.gru_nombre,categoriagrupovalmes.costo,
        dteorigen.id as dteorigen_id,dteorigen.fchemis as dteorigen_fchemis,dteorigen.nrodocto as dteorigen_nrodocto,
        dteorigen.updated_at as dteorigen_updated_at,
        foliocontrolorigen.doc as foliocontrolorigen_doc,foliocontrolorigen.nombrepdf as foliocontrolorigen_nombrepdf
        FROM dte LEFT JOIN dtedte
        ON dte.id = dtedte.dte_id AND ISNULL(dte.deleted_at) and isnull(dtedte.deleted_at)
        INNER JOIN dtedet
        ON dtedet.dte_id = dte.id
        LEFT JOIN dteguiadesp
        ON dtedte.dter_id = dteguiadesp.dte_id and isnull(dteguiadesp.deleted_at)
        LEFT JOIN despachoord
        ON despachoord.id = dteguiadesp.despachoord_id and isnull(despachoord.deleted_at)
        LEFT JOIN notaventa
        ON notaventa.id = despachoord.notaventa_id and isnull(notaventa.deleted_at)
        INNER JOIN cliente
        ON dte.cliente_id  = cliente.id AND ISNULL(cliente.deleted_at)
        INNER JOIN comuna
        ON comuna.id = cliente.comunap_id AND ISNULL(comuna.deleted_at)
        LEFT JOIN clientebloqueado
        ON dte.cliente_id = clientebloqueado.cliente_id AND ISNULL(clientebloqueado.deleted_at)
        LEFT JOIN dteanul
        ON dteanul.dte_id = dte.id AND ISNULL(dteanul.deleted_at)
        INNER JOIN foliocontrol
        ON  foliocontrol.id = dte.foliocontrol_id AND ISNULL(foliocontrol.deleted_at)
        LEFT JOIN dtefac
        ON dtefac.dte_id = dte.id
        LEFT JOIN producto
        ON dtedet.producto_id = producto.id
        LEFT JOIN categoriaprod
        ON categoriaprod.id = producto.categoriaprod_id
        LEFT JOIN categoriaprodgrupo
        ON categoriaprodgrupo.id = categoriaprod.categoriaprodgrupo_id
        LEFT JOIN claseprod
        ON producto.claseprod_id = claseprod.id
        LEFT JOIN vendedor
        ON dte.vendedor_id = vendedor.id
        LEFT JOIN persona
        ON vendedor.persona_id = persona.id
        LEFT JOIN acuerdotecnico
        ON producto.id = acuerdotecnico.producto_id and isnull(acuerdotecnico.deleted_at)
        LEFT JOIN materiaprima
        ON materiaprima.id = acuerdotecnico.at_materiaprima_id and isnull(materiaprima.deleted_at)
        INNER JOIN unidadmedida
        on unidadmedida.id = dtedet.unidadmedida_id
        INNER JOIN sucursal
        ON sucursal.id = dte.sucursal_id
        LEFT JOIN grupoprod
        ON producto.grupoprod_id = grupoprod.id
        LEFT JOIN categoriagrupovalmes
        ON categoriagrupovalmes.grupoprod_id = grupoprod.id and categoriagrupovalmes.annomes = '$fechaannomes' and isnull(categoriagrupovalmes.deleted_at)
        LEFT JOIN dte as dteorigen
        ON dtedte.dter_id = dteorigen.id and isnull(dteorigen.deleted_at)
        LEFT JOIN foliocontrol as foliocontrolorigen
        ON dteorigen.foliocontrol_id = foliocontrolorigen.id
        WHERE $aux_condfoliocontrol_id
        AND dte.sucursal_id IN ($sucurcadena)
        AND $aux_sucursal_idCond
        AND $aux_conddte_id
        AND $aux_condFecha
        AND $aux_condnrodocto
        AND $aux_condrut
        AND $aux_condoc_id
        AND $aux_condnotaventa_id
        AND $aux_condstatusgen
        AND $aux_condvendedor_id
        AND $aux_condsucurArray
        AND $aux_condsucurJefAreaFis
        AND $aux_condareaproduccion_id
        AND NOT ISNULL(dte.nrodocto)
        AND dte.id NOT IN (SELECT dteanul.dte_id FROM dteanul WHERE ISNULL(dteanul.deleted_at))
        $aux_groupby
        $aux_orderby;";

        //dd($sql);
        $arrays = DB::select($sql);
        return $arrays;
    }

    public static function buscarnrodocto($request){
        $dte = Dte::where('nrodocto' ,'=',$request->nrodocto)
                    ->where('foliocontrol_id' ,'=',$request->foliocontrol_id)->get();
        $sql = "SELECT *
            FROM dte
            WHERE nrodocto = $request->nrodocto
            AND foliocontrol_id = $request->foliocontrol_id
            and ISNULL(dte.deleted_at);";
        $datas = DB::select($sql);
        //dd($datas);
        if(count($datas) > 0){
            return response()->json([
                'respuesta' => 1,
                'Mensaje' => 'Encontrado',
                'data' => $datas[0]
            ]);
        }else{
            return response()->json([
                'respuesta' => 0,
                'Mensaje' => 'No encontrado',
                'data' => $datas
            ]);
        }
    }

    public static function subirSisCobranza($dte){
        $tipodocto = $dte->foliocontrol->tipodocto;
        if($tipodocto == 33 or $tipodocto == 34 or $tipodocto == 61 or $tipodocto == 56){
            $empresa = Empresa::findOrFail(1);
            if($empresa->actsiscob == 1){
                //dd("entro en obser DteFac");
                $soap = new SoapController();
                $xmlcliente = Dte::xmlClienteSisCobranza($dte);
                //dd($xmlcliente);
                $comando03creaclientes = $soap->Comando03CreaClientes($xmlcliente);
                //dd($comando03creaclientes);
                $xmlcobranza = Dte::xmlDocSisCobranza($dte);
                $cargadocumentoscobranza = $soap->Comando01CargaDocumentos($xmlcobranza);
                //dd($cargadocumentoscobranza);
            }    
        }
    }

    public static function xmlClienteSisCobranza($dte){
        $rutrecep = $dte->cliente->rut;
        $rutrecep = number_format( substr ( $rutrecep, 0 , -1 ) , 0, "", "") . '-' . substr ( $rutrecep, strlen($rutrecep) -1 , 1 );

        $CorreoRecep = strtoupper(substr(trim($dte->cliente->contactoemail),0,80));
        $RznSocRecep = strtoupper(sanear_string(substr(trim($dte->cliente->razonsocial),0,100)));
        $GiroRecep = strtoupper(sanear_string(substr(trim($dte->cliente->giro),0,40)));
        $DirRecep = strtoupper(sanear_string(substr(trim($dte->cliente->direccion),0,70)));
        $CmnaRecep = strtoupper(sanear_string(substr(trim($dte->cliente->comuna->nombre),0,20)));
        $CiudadRecep = strtoupper(sanear_string(substr(trim($dte->cliente->ciudad->nombre),0,20)));
        $telefonoRecep = strtoupper(sanear_string(substr(trim($dte->cliente->comuna->telefono),0,20)));
        $telefonoRecep = strtoupper(sanear_string(substr(trim($dte->cliente->comuna->telefono),0,20)));
        $plazopagoRecep = strtoupper(sanear_string(substr(trim($dte->cliente->plazopago->descripcion),0,20)));    

        $contenido = "<?xml version=\"1.0\" encoding=\"iso-8859-1\"?>
        <Raiz>
            <Cliente RUT=\"$rutrecep\" NOMFANTASIA=\"$RznSocRecep\" RAZONSOCIAL=\"$RznSocRecep\" GIRO=\"$GiroRecep\"
            DIRECCION=\"$DirRecep\"
            COMUNA=\"$CmnaRecep\" CIUDAD=\"$CiudadRecep\" FONO=\"$telefonoRecep\" VENDEDOR=\"$dte->vendedor_id\" GLOSAPAGO=\"$plazopagoRecep\"
            CREDITOAPROBADO=\"0\" DIRECCIONDESPACHO=\"$DirRecep\" >
            </Cliente>
        </Raiz>";
        return $contenido;
    }

    public static function xmlDocSisCobranza($dte){
        $rutrecep = $dte->cliente->rut;
        $rutrecep = number_format( substr ( $rutrecep, 0 , -1 ) , 0, "", "") . '-' . substr ( $rutrecep, strlen($rutrecep) -1 , 1 );

        $RznSocRecep = strtoupper(sanear_string(substr(trim($dte->cliente->razonsocial),0,100)));
        $GiroRecep = strtoupper(sanear_string(substr(trim($dte->cliente->giro),0,40)));
        $DirRecep = strtoupper(sanear_string(substr(trim($dte->cliente->direccion),0,70)));
        $CiuRecep = strtoupper(sanear_string(substr(trim($dte->cliente->ciudad->nombre),0,70)));
        $CmnaRecep = strtoupper(sanear_string(substr(trim($dte->cliente->comuna->nombre),0,20)));
        $telefono = strtoupper(sanear_string(substr(trim($dte->cliente->comuna->telefono),0,20)));
        
        $TipoDTE = $dte->foliocontrol->tipodocto;

        $aux_neto = $dte->mntneto;
        $aux_tasaiva = $dte->tasaiva;
        $aux_iva = $dte->iva;
        $aux_mnttotal = $dte->mnttotal;
        $FchEmis = date('d-m-Y', strtotime($dte->fchemis));
        $numfv = "";
        if($TipoDTE == 33 or $TipoDTE == 34){
            $vencimie = date('d-m-Y', strtotime($dte->dtefac->fchvenc));
            $codtipodoc = "FAV";
            $formapago_id = $dte->dtefac->formapago_id;
        }
        if($TipoDTE == 61){
            $vencimie = $FchEmis;
            $codtipodoc = "NCRV";
            $formapago_id = $dte->dtedte->dtefac->dtefac->formapago_id;
            $numfv = $dte->dtedte->dtefac->nrodocto;
        }
        if($TipoDTE == 56){
            $vencimie = $FchEmis;
            $codtipodoc = "NDV";
            $formapago_id = $dte->dtedte->dtefac->dtefac->formapago_id;
            $numfv = $dte->dtedte->dtefac->nrodocto;
        }

        if($formapago_id == 1 or $formapago_id == 3){
            $codformpago = "Contado";
        }
        if($formapago_id == 2){
            $codformpago = "Cheque a Fecha";
        }
        if($formapago_id == 5){
            $codformpago = "Crédito";
        }

        $contenido = "<?xml version=\"1.0\" encoding=\"iso-8859-1\"?>
        <Raiz>
            <EncabezadoDOC
                NUMFACT=\"$dte->nrodocto\" NUMREG=\"0\" FECHA=\"$FchEmis\" VENCIMIE=\"$vencimie\" RUTFACT=\"$rutrecep\"
                RAZSOC2=\"$RznSocRecep\" GIRO=\"$GiroRecep\"
                DIR=\"$DirRecep\" COMUNA=\"$CmnaRecep\" CIUDAD=\"$CiuRecep\" FONO=\"$telefono\"
                CODCOMIS=\"\" COMISION=\"0\" CODCODVEND=\"$dte->vendedor_id\" GLOSACON=\"\" NUMGUIAF=\"$numfv\" 
                CODFORMPAGO=\"$codformpago\"
                GARANTIA=\"0\" STOCK=\"1\" NULA=\"0\" CANCELA=\"0\" CODTIPODOC=\"$codtipodoc\"
                VENTA=\"1\" NDIASF=\"0\" NUMCORR=\"0\"
                CODTIPOVTA=\"\" NUMFV=\"0\" NOTAOBS=\"\"
                FECHCONT=\"$FchEmis\" AFECTO=\"0\" EXENTO=\"0\"
                DCTOTIPO=\"1\" DCTOPJE=\"0.00\" CODSUCUR=\"1\"
                TOTNETO=\"$aux_neto\" IVA=\"$aux_tasaiva\" TOTIVA=\"$aux_iva\"
                IMP1=\"0\" IMP2=\"0\" TOTAL=\"$aux_mnttotal\"
                MONEDA=\"$\" TASACBIO=\"1\"
                NUMCOT=\"0\" ABONO=\"0\" CODCTA=\"110401\" CODCC=\"1002\"
                SINANALIS=\"0\" ESELECTR=\"1\" E_ENVIADO=\"0\" FPAGELE=\"\"
                MPAGELE=\"\" RAZONREF=\"\" REFELE=\"\"
                OBRA=\"\" NUMORDENC=\"0\" NROPEDIDO=\"\" FCHPEDIDO=\"\"
                REFELE2=\"\" RAZONREF2=\"\"
                CONCIVAFP=\"\" CAJERO=\"0\" NROHESCODE=\"\"
                FECHAHES=\"\" PATENTE=\"\" NOMBRECHOFER=\"\" ENTREGAR=\"\" >
            </EncabezadoDOC>
            <Detalles>
                <DetalleDOC
                CODIGOPROD=\"1000\" DESCRIP=\"Producto\"
                    CANTIDAD=\"1\" PRECUNIT=\"$aux_neto\" DESCTO=\"0\" FECHENTR=\"\"
                    CODCTACTBLEDET=\"310101\" CODCENCOSTODET=\"1002\" ITEM=\"1\" FACTORIMP=\"0\"
                    NSERIE=\"\" CODBODEGA=\"B1\" TAX=\"0\" LOT_IDE=\"\" VENCE=\"\" TIP_TAX=\"0\">
                </DetalleDOC>
            </Detalles>
        </Raiz>";
        return $contenido;
    }

    public static function deudaClienteSisCobranza($rut){
        //dd($request);
        //$rut = "";
        if(!isset($rut) or empty($rut)){
            $aux_condrut = " true";
        }else{
            $aux_condrut = "cliente.rut='$rut'";
        }
        $sql = "SELECT id,rut,razonsocial,limitecredito
                    FROM cliente
                    WHERE $aux_condrut
                    ORDER BY razonsocial;";
        $clientes = DB::select($sql);
        //$clientes = cliente::get();
        //dd($clientes);
        $soap = new SoapController();
        $aux_cont = 0;
        $ArrayFact = "";
        foreach ($clientes as $cliente) {
            $ListaPendientes = $soap->Comando02ListaPendientes(formatearRUT($cliente->rut));

            $dom = new DOMDocument();
            $dom->loadXML($ListaPendientes);
            $xpath = new DOMXPath($dom);
    
            // Registra el espacio de nombres necesario (puedes ajustarlo según tu XML)
            $xpath->registerNamespace('diffgr', 'urn:schemas-microsoft-com:xml-diffgram-v1');
    
            // Consulta XPath para seleccionar todos los elementos <Table>
            $tables = $xpath->query('//diffgr:diffgram/NewDataSet/Table');
    
            $matriz = [];
            $TFac = 0;
            $TDeuda = 0;
            $TDeudaFec = 0;
            $datosFacDeuda = [];
            $ArrayNroFac = [];
            $cont = 0;
            foreach ($tables as $table) {
                $cont++;
                // Accede a los elementos hijos dentro de cada <Table>
                $nroFAV = $xpath->evaluate('string(NroFAV)', $table);
                $cliente1 = $xpath->evaluate('string(Cliente)', $table);
                $fechaFAV = $xpath->evaluate('string(FechaFAV)', $table);
                $Deuda = $xpath->evaluate('string(Monto)', $table);
                $pkFAVPendiente = $xpath->evaluate('string(PKFAVPENDIENTE)', $table);
                $vendedor = $xpath->evaluate('string(Vendedor)', $table);
                $numeroOC = $xpath->evaluate('string(NumeroOC)', $table);
    
    
                // Realiza las operaciones que desees con los valores obtenidos
                $NroFAV = substr($nroFAV,4,7);
                $dtefac = Dte::where("nrodocto",$NroFAV)
                                ->where("foliocontrol_id",1)
                                ->get();
                $mnttotal = 0;
                if(count($dtefac) > 0){
                    $dte = Dte::findOrFail($dtefac[0]->id);
                    $mnttotal = $dte->mnttotal;
                    $TFac += $dte->mnttotal;
                    $fecfact = $dte->fchemis;
                    if(isset($dte->dtefac->fchvenc)){
                        $fecvenc = $dte->dtefac->fchvenc;
                    }else{
                        $fecvenc = $dte->fchemis;
                    }
                }else{
                    $auxcliente = Cliente::findOrFail($cliente->id);
                    $fecfact = substr($fechaFAV,0,10);
                    $fecvenc = date('Y-m-d');
                    //$fecvenc = date('Y-m-d', strtotime($fecfact ."+ " . $auxcliente->plazopago->dias ? $auxcliente->plazopago->dias : "0" . " days"));
                }
                $TDeuda += $Deuda;
                $empresa = Empresa::findOrFail(1);
                $fecvencProrr = date('Y-m-d', strtotime($fecvenc ."+ " . $empresa->diasprorrogacob . " days"));
                if($cont > 1){
                    //dd($fecvencProrr);
                }
                //dd($fechacobro);
                if($fecvencProrr <= date('Y-m-d')){
                    $TDeudaFec += $Deuda;
                    $datosFacDeuda[] = [
                        'NroFAV' => $NroFAV,
                        'fecfact' => $fecfact,
                        'fecvenc' => $fecvenc,
                        'mnttot' => $mnttotal,
                        'Deuda' => $Deuda
                    ];
                    $ArrayNroFac[] = "(" . $NroFAV . "  " . date('d/m/Y', strtotime($fecvenc)) . ")";
                }
                $datosTotasFacDeuda[] = [
                    'NroFAV' => $NroFAV,
                    'fecfact' => $fecfact,
                    'fecvenc' => $fecvenc,
                    'mnttot' => $mnttotal,
                    'Deuda' => $Deuda
                ];
                $ArrayNroFac[] = "(" . $NroFAV . "  " . date('d/m/Y', strtotime($fecvenc)) . ")";

                $matriz [] = [
                    'NroFAV' => $NroFAV,
                    'Cliente' => $cliente1,
                    'FechaFAV' => $fechaFAV,
                    'fecfact' => $fecfact,
                    'fecvenc' => $fecvenc,
                    'mnttot' => $mnttotal,
                    'Deuda' => $Deuda,
                    //'PKFAVPendiente' => $pkFAVPendiente,
                    'Vendedor' => $vendedor,
                    'NumeroOC' => $numeroOC,
                ];
                /*
                echo 'NroFAV: ' . $nroFAV . PHP_EOL;
                echo 'Cliente: ' . $cliente . PHP_EOL;
                echo 'FechaFAV: ' . $fechaFAV . PHP_EOL;
                echo 'Monto: ' . $Deuda . PHP_EOL;
                echo 'PKFAVPendiente: ' . $pkFAVPendiente . PHP_EOL;
                echo 'Vendedor: ' . $vendedor . PHP_EOL;
                echo 'NumeroOC: ' . $numeroOC . PHP_EOL;
                */
                // Puedes realizar otras operaciones con los valores obtenidos
            }
            $ArrayFact = [
                "rut" => $cliente->rut,
                "razonsocial" => $cliente->razonsocial,
                "limitecredito" => $cliente->limitecredito,
                "TFac" => $TFac,
                "TDeuda" => $TDeuda,
                "TDeudaFec" => $TDeudaFec,
                "NroFacDeu" => implode(",", $ArrayNroFac),
                "datosFacDeuda" => $datosFacDeuda,
                "datosTotasFacDeuda" => $datosTotasFacDeuda
            ];   
            $aux_cont++;
            /* if($aux_cont > 100){
                break;
            } */
        }
        //dd($ArrayFact);
        return $ArrayFact;
    }
}

function dtefactura($id,$Folio,$tipoArch,$request){
    $aux_dte = consultaindex($id);
    $Folio = str_pad($Folio, 10, "0", STR_PAD_LEFT);
    $dte = Dte::findOrFail($id);
    $rutrecep = $dte->cliente->rut;
    $rutrecep = number_format( substr ( $rutrecep, 0 , -1 ) , 0, "", "") . '-' . substr ( $rutrecep, strlen($rutrecep) -1 , 1 );

    $empresa = Empresa::findOrFail(1);
    $RznSoc = strtoupper(sanear_string(substr(trim($empresa->razonsocial),0,100)));
    $GiroEmis = strtoupper(sanear_string(substr(trim($empresa->giro),0,80)));
    $Acteco = substr(trim($empresa->acteco),0,6);
    $DirOrigen = strtoupper(sanear_string(substr(trim($empresa->sucursal->direccion),0,60)));
    $CmnaOrigen = strtoupper(sanear_string(substr(trim($empresa->sucursal->comuna->nombre),0,20)));
    $CiudadOrigen = strtoupper(sanear_string(substr(trim($empresa->sucursal->comuna->provincia->nombre),0,20)));
    $contacto = strtoupper(sanear_string(substr(trim($dte->cliente->contactonombre . " Telf:" . $dte->cliente->contactotelef),0,40)));
    $CorreoRecep = strtoupper(substr(trim($dte->cliente->contactoemail),0,80));
    $RznSocRecep = strtoupper(sanear_string(substr(trim($dte->cliente->razonsocial),0,100)));
    $GiroRecep = strtoupper(sanear_string(substr(trim($dte->cliente->giro),0,40)));
    $DirRecep = strtoupper(sanear_string(substr(trim($dte->cliente->direccion),0,70)));
    $CmnaRecep = strtoupper(sanear_string(substr(trim($dte->cliente->comuna->nombre),0,20)));
    $CiudadRecep = strtoupper(sanear_string(substr(trim($dte->cliente->ciudad->nombre),0,20)));
    if($dte->foliocontrol_id == 1){
        $FchVenc = $dte->dtefac->fchvenc;
        $formapago_desc = $dte->dtefac->formapago->descripcion;
        if($dte->dtefac->formapago_id == 2 or $dte->dtefac->formapago_id == 3){
            $formapago_desc .= " " . $dte->cliente->plazopago->descripcion;
        }    
    }
    $FolioRef = substr(trim($dte->oc_id),0,20);
    $TipoDocto = $dte->foliocontrol->tipodocto;
    $contenido = "";
/*
    if($tipoArch == "TXT"){
        $fchemisDMY = date("d-m-Y_His",strtotime($dte->fchemis));
        $fchemis = date("d-m-Y",strtotime($dte->fchemis)); // date("Y-m-d");
        $contenido = "ENC|33||$Folio|$fchemis||$dte->tipodespacho|$dte->indtraslado|||||||||||$fchemis|" . 
        "$empresa->rut|$RznSoc|$GiroEmis|||$DirOrigen|$CmnaOrigen|$CiudadOrigen|||$rutrecep||" . 
        "$RznSocRecep|$GiroRecep|$contacto|$CorreoRecep|$DirRecep|$CmnaRecep|$CiudadRecep||||||||||" .
        "$dte->mntneto|||$dte->tasaiva|$dte->iva||||||$dte->mnttotal||$dte->mnttotal|\r\n" . 
        "ACT|$Acteco|\r\n";
        foreach ($dte->dtedets as $dtedet) {
            $contenido .= "DET|$dtedet->nrolindet||$dtedet->nmbitem|$dtedet->dscitem||||$dtedet->qtyitem|||$dtedet->unmditem|$dtedet->prcitem|||||||||$dtedet->montoitem|\r\n" . 
                        "ITEM|INTERNO|$dtedet->vlrcodigo|\r\n";
        }
        $TpoDocRef = (empty($dte->dteguiadesp->despachoord_id) ? "" : "OD:" . $dte->dteguiadesp->despachoord_id . " ") . (empty($dte->dteguiadesp->ot) ? "" : "OT:" . $dte->ot . " ")  . (empty($dte->obs) ? "" : $dte->obs . " ") . (empty($dte->lugarentrega) ? "" : $dte->lugarentrega . " ")  . (empty($dte->comunaentrega_id) ? "" : $dte->comunaentrega->nombre . " ");
        $TpoDocRef = substr(trim($TpoDocRef),0,90);
        $contenido .= "REF|1|801||$dte->oc_id||$fchemis||$TpoDocRef|";
    
    }
    */

    if($tipoArch == "XML"){
        $FchEmis = $dte->fchemis;
        //$FchEmis = date("d-m-Y",strtotime($dte->fchemis));
        /*$contenido = "<![CDATA[<?xml version=\"1.0\" encoding=\"ISO-8859-1\" standalone=\"yes\"?>" .*/
        //"<DTE version=\"1.0\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xmlns:xsd=\"http://www.w3.org/2001/XMLSchema\" xmlns=\"http://www.sii.cl/SiiDte\">" .
        /*$contenido = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\" standalone=\"yes\"?>" .
        "<DTE version=\"1.0\">" .
        */
        $contenido = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\" standalone=\"yes\"?>" .
        "<DTE version=\"1.0\">" .
        "<Documento ID=\"R" .$empresa->rut . "T52F" . $Folio . "\">" .
        "<Encabezado>" .
        "<IdDoc>" .
        "<TipoDTE>$TipoDocto</TipoDTE>" .
        "<Folio>$Folio</Folio>" .
        "<FchEmis>$FchEmis</FchEmis>" .
        "<TipoDespacho>$dte->tipodespacho</TipoDespacho>" .
        "<TpoImpresion>N</TpoImpresion>";
        if($dte->foliocontrol_id == 1){
            $contenido .= "<TermPagoGlosa>$formapago_desc</TermPagoGlosa>" .
            "<FchVenc>$FchVenc</FchVenc>";
    
        }
        $contenido .= "</IdDoc>" .
        "<Emisor>" .
        "<RUTEmisor>$empresa->rut</RUTEmisor>" .
        "<RznSoc>$RznSoc</RznSoc>" .
        "<GiroEmis>$GiroEmis</GiroEmis>" .
        "<Acteco>$Acteco</Acteco>" .
        "<DirOrigen>$DirOrigen</DirOrigen>" .
        "<CmnaOrigen>$CmnaOrigen</CmnaOrigen>" .
        "<CiudadOrigen>$CiudadOrigen</CiudadOrigen>" .
        "</Emisor>" .
        "<Receptor>" .
        "<RUTRecep>$rutrecep</RUTRecep>" .
        "<RznSocRecep>$RznSocRecep</RznSocRecep>" .
        "<GiroRecep>$GiroRecep</GiroRecep>" .
        "<Contacto>$contacto</Contacto>" .
        "<CorreoRecep>$CorreoRecep</CorreoRecep>" .
        "<DirRecep>$DirRecep</DirRecep>" .
        "<CmnaRecep>$CmnaRecep</CmnaRecep>" .
        "<CiudadRecep>$CiudadRecep</CiudadRecep>" .
        "</Receptor>" .
        "<Totales>" .
        "<MntNeto>$dte->mntneto</MntNeto>" .
        "<TasaIVA>$dte->tasaiva</TasaIVA>" .
        "<IVA>$dte->iva</IVA>" .
        "<MntTotal>$dte->mnttotal</MntTotal>" .
        "</Totales>" .
        "</Encabezado>";
    
        $aux_totalqtyitem = 0;
    
        foreach ($dte->dtedets as $dtedet) {
            $VlrCodigo = substr(trim($dtedet->vlrcodigo),0,35);
            $NmbItem = strtoupper(sanear_string(substr(trim($dtedet->nmbitem),0,80)));
            $DscItem = strtoupper(sanear_string(substr(trim($dtedet->dscitem),0,1000)));
            $UnmdItem = substr(trim($dtedet->unmditem),0,4);
            $contenido .= "<Detalle>" .
            "<NroLinDet>$dtedet->nrolindet</NroLinDet>" .
            "<CdgItem>" .
            "<TpoCodigo>INTERNO</TpoCodigo>" .
            "<VlrCodigo>" . $VlrCodigo . "</VlrCodigo>" .
            "</CdgItem>" .
            "<NmbItem>" . $VlrCodigo . "</NmbItem>" .
            "<DscItem>" . $NmbItem . "</DscItem>" .
            "<QtyItem>" . $dtedet->qtyitem . "</QtyItem>" .
            "<UnmdItem>" . $UnmdItem . "</UnmdItem>";
            if($dtedet->prcitem > 0){
                $contenido .= "<PrcItem>$dtedet->prcitem</PrcItem>";
            }
            $contenido .= "<MontoItem>$dtedet->montoitem</MontoItem>" .
            "</Detalle>";
            $aux_totalqtyitem += $dtedet->qtyitem;
        }
    
        $TpoDocRef = (empty($dte->dteguiadesp->despachoord_id) ? "" : "OD:" . $dte->dteguiadesp->despachoord_id . " ") . (empty($dte->ot) ? "" : "OT:" . $dte->ot . " ")  . (empty($dte->obs) ? "" : $dte->obs . " ") . (empty($dte->lugarentrega) ? "" : $dte->lugarentrega . " ")  . (empty($dte->comunaentrega_id) ? "" : $dte->comunaentrega->nombre . " ");
        $TpoDocRef = strtoupper(sanear_string(substr(trim($TpoDocRef),0,90)));

        //dd($aux_dte[0]->oc_id);

        if($TipoDocto == 33){
            $contenido .= "<Referencia>" .
            "<NroLinRef>1</NroLinRef>" .
            "<TpoDocRef>TPR</TpoDocRef>" .
            "<FolioRef>00001000</FolioRef>" .
            "<FchRef>$FchEmis</FchRef>" .
            "<RazonRef>TOTAL UNIDADES:$aux_totalqtyitem</RazonRef>" .
            "</Referencia>" .
            "<Referencia>" .
            "<NroLinRef>2</NroLinRef>" .
            "<TpoDocRef>SRD</TpoDocRef>" .
            "<FolioRef>00001000</FolioRef>" .
            "<FchRef>$FchEmis</FchRef>" .
            "<RazonRef>DESPACHO: $DirRecep</RazonRef>" .
            "</Referencia>";
    

            $array_ocs = explode(",", $aux_dte[0]->oc_id);
            $i = 2;
            $aux_RazonRef = substr($dte->dtefac->hep ? ("Hep: " . $dte->dtefac->hep) : "",0,90);;
            $aux_RazonRefImp = false;
            foreach ($array_ocs as $oc_id) {
                $i++;
                $contenido .= "<Referencia>" .
                "<NroLinRef>$i</NroLinRef>" .
                "<TpoDocRef>801</TpoDocRef>" .
                "<FolioRef>$oc_id</FolioRef>" .
                "<FchRef>$FchEmis</FchRef>";
                if($aux_RazonRefImp == false){
                    $aux_RazonRefImp = true;
                    $contenido .= "<RazonRef>$aux_RazonRef</RazonRef>";
                }
                $contenido .= "</Referencia>";
            }
    
            $array_dter_id = explode(",", $aux_dte[0]->dter_id);
            foreach ($array_dter_id as $dter_id) {
                $i++;
                $dtedg = Dte::findOrFail($dter_id);
                $contenido .= "<Referencia>" .
                "<NroLinRef>$i</NroLinRef>" .
                "<TpoDocRef>52</TpoDocRef>" .
                "<FolioRef>$dtedg->nrodocto</FolioRef>" .
                "<FchRef>$dtedg->fchemis</FchRef>";
                if($aux_RazonRefImp == false){
                    $aux_RazonRefImp = true;
                    $contenido .= "<RazonRef>$aux_RazonRef</RazonRef>";
                }
                $contenido .= "</Referencia>";
            }    
        }

        if($TipoDocto == 61 or $TipoDocto == 56){
            $TipoDocto = $dte->dtedte->dter->foliocontrol->tipodocto;
            $aux_nrodocto = $dte->dtedte->dter->nrodocto;
            $aux_FchEmis = $dte->dtedte->dter->fchemis;
            $TpoDocRef = strtoupper(sanear_string(substr(empty($dte->obs) ? "" : $dte->obs,0,90)));
            $aux_codref = $dte->dtencnd->codref;
            /*
            switch ($aux_codref) {
                case 1:
                    $TpoDocRef = "Anula Documento de Referencia. " . $TpoDocRef;
                    break;
                case 2:
                    $TpoDocRef = "Corrige Texto Documento Referencia. " . $TpoDocRef;
                    break;
                case 3:
                    $TpoDocRef = "Corrige montos. " . $TpoDocRef;
                    break;
            }
            */
            $contenido .= "<Referencia>" .
            "<NroLinRef>1</NroLinRef>" .
            "<TpoDocRef>$TipoDocto</TpoDocRef>" .
            "<FolioRef>$aux_nrodocto</FolioRef>" .
            "<FchRef>$aux_FchEmis</FchRef>" .
            "<CodRef>$aux_codref</CodRef>" .
            "<RazonRef>$TpoDocRef</RazonRef>" .
            "</Referencia>";

            /*EN COMENTARIO PORQUE SEGUN LO QUE ME DIJO GUILLERMO NO ES NECESARIO EN LAS NOTAS DE CREDITO Y DEBITO
            $contenido .= "<Referencia>" .
            "<NroLinRef>2</NroLinRef>" .
            "<TpoDocRef>TPR</TpoDocRef>" .
            "<FolioRef>00001000</FolioRef>" .
            "<FchRef>$FchEmis</FchRef>" .
            "<RazonRef>TOTAL UNIDADES:$aux_totalqtyitem</RazonRef>" .
            "</Referencia>" .
            "<Referencia>" .
            "<NroLinRef>3</NroLinRef>" .
            "<TpoDocRef>SRD</TpoDocRef>" .
            "<FolioRef>00001000</FolioRef>" .
            "<FchRef>$FchEmis</FchRef>" .
            "<RazonRef>DESPACHO: $DirRecep</RazonRef>" .
            "</Referencia>";
            */
    
        }
        $contenido .= "</Documento>" .
        "</DTE>";
    }
    /*
    echo $contenido;
    dd('e');
    */
    return $contenido;
}

function dtefacturaprueba($dte,$Folio,$tipoArch){
    if($dte->foliocontrol->tipodocto == 52){
        $Folio = str_pad($Folio, 10, "0", STR_PAD_LEFT);
        $rutrecep = $dte->cliente->rut;
        $rutrecep = number_format( substr ( $rutrecep, 0 , -1 ) , 0, "", "") . '-' . substr ( $rutrecep, strlen($rutrecep) -1 , 1 );
    
        $empresa = Empresa::findOrFail(1);
        $RznSoc = strtoupper(sanear_string(substr(trim($empresa->razonsocial),0,100)));
        $GiroEmis = strtoupper(sanear_string(substr(trim($empresa->giro),0,80)));
        $Acteco = substr(trim($empresa->acteco),0,6);
        $DirOrigen = strtoupper(sanear_string(substr(trim($empresa->sucursal->direccion),0,60)));
        $CmnaOrigen = strtoupper(sanear_string(substr(trim($empresa->sucursal->comuna->nombre),0,20)));
        $CiudadOrigen = strtoupper(sanear_string(substr(trim($empresa->sucursal->comuna->provincia->nombre),0,20)));
        if(isset($dte->dteguiadesp->notaventa)){
            $contacto = strtoupper(sanear_string(substr(trim($dte->dteguiadesp->notaventa->contacto . " Telf:" . $dte->dteguiadesp->notaventa->contactotelf),0,40)));
        }else{
            $contacto = strtoupper(sanear_string(substr(trim($dte->cliente->contactonombre . " Telf:" . $dte->cliente->contactotelef),0,40)));
        }
        $CorreoRecep = strtoupper(substr(trim($dte->cliente->contactoemail),0,80));
        $RznSocRecep = strtoupper(sanear_string(substr(trim($dte->cliente->razonsocial),0,100)));
        $GiroRecep = strtoupper(sanear_string(substr(trim($dte->cliente->giro),0,40)));
/*         $string = Str::ascii(substr(trim($dte->cliente->giro),0,40));
        $string = preg_replace('/[^A-Za-z0-9 ]/', '', $string);
        dd("Original: " . substr(trim($dte->cliente->giro),0,40) . "\nCambio: " . $GiroRecep . "\nNuevo valor: " . $string); */
        $DirRecep = strtoupper(sanear_string(substr(trim($dte->cliente->direccion),0,70)));
        $CmnaRecep = strtoupper(sanear_string(substr(trim($dte->cliente->comuna->nombre),0,20)));
        $CiudadRecep = strtoupper(sanear_string(substr(trim($dte->cliente->ciudad->nombre),0,20)));
    
        $FolioRef = "";
        if(isset($dte->dteguiadesp->notaventa->oc_id)){
            $FolioRef = $dte->dteguiadesp->notaventa->oc_id;
        }else{
            if(isset($dte->dteocs[0])){
                $FolioRef = $dte->dteocs[0]->oc_id;
            }
        }
    
        $contenido = "";
    
        if($tipoArch == "XML"){
            $FchEmis = $dte->fchemis;
            //$FchEmis = date("d-m-Y",strtotime($dte->fchemis));
            /*$contenido = "<![CDATA[<?xml version=\"1.0\" encoding=\"ISO-8859-1\" standalone=\"yes\"?>" .*/
            //"<DTE version=\"1.0\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xmlns:xsd=\"http://www.w3.org/2001/XMLSchema\" xmlns=\"http://www.sii.cl/SiiDte\">" .
            /*$contenido = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\" standalone=\"yes\"?>" .
            "<DTE version=\"1.0\">" .
            */
            $contenido = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\" standalone=\"yes\"?>" .
            "<DTE version=\"1.0\">" .
            "<Documento ID=\"R" .$empresa->rut . "T52F" . $Folio . "\">" .
            "<Encabezado>" .
            "<IdDoc>" .
            "<TipoDTE>52</TipoDTE>" .
            "<Folio>$Folio</Folio>" .
            "<FchEmis>$FchEmis</FchEmis>" .
            "<TipoDespacho>$dte->tipodespacho</TipoDespacho>" .
            "<IndTraslado>$dte->indtraslado</IndTraslado>" .
            "<TpoImpresion>N</TpoImpresion>" .
            "</IdDoc>" .
            "<Emisor>" .
            "<RUTEmisor>$empresa->rut</RUTEmisor>" .
            "<RznSoc>$RznSoc</RznSoc>" .
            "<GiroEmis>$GiroEmis</GiroEmis>" .
            "<Acteco>$Acteco</Acteco>" .
            "<FolioAut>sdfsdf</FolioAut>" .
            "<FchAut>$FchEmis</FchAut>" .
            "<DirOrigen>$DirOrigen</DirOrigen>" .
            "<CmnaOrigen>$CmnaOrigen</CmnaOrigen>" .
            "<CiudadOrigen>$CiudadOrigen</CiudadOrigen>" .
            "</Emisor>" .
            "<Receptor>" .
            "<RUTRecep>$rutrecep</RUTRecep>" .
            "<RznSocRecep>$RznSocRecep</RznSocRecep>" .
            "<GiroRecep>$GiroRecep</GiroRecep>" .
            "<Contacto>$contacto</Contacto>" .
            "<CorreoRecep>$CorreoRecep</CorreoRecep>" .
            "<DirRecep>$DirRecep</DirRecep>" .
            "<CmnaRecep>$CmnaRecep</CmnaRecep>" .
            "<CiudadRecep>$CiudadRecep</CiudadRecep>" .
            "</Receptor>" .
            "<Totales>" .
            "<MntNeto>$dte->mntneto</MntNeto>" .
            "<TasaIVA>$dte->tasaiva</TasaIVA>" .
            "<IVA>$dte->iva</IVA>" .
            "<MntTotal>$dte->mnttotal</MntTotal>" .
            "</Totales>" .
            "</Encabezado>";
        
            $aux_totalqtyitem = 0;
        
            foreach ($dte->dtedets as $dtedet) {
                $VlrCodigo = substr(trim($dtedet->vlrcodigo),0,35);
                $NmbItem = strtoupper(sanear_string(substr(trim($dtedet->nmbitem),0,80)));
                $DscItem = strtoupper(sanear_string(trim($dtedet->dscitem)));
                $UnmdItem = substr(trim($dtedet->unmditem),0,4);
                $contenido .= "<Detalle>" .
                "<NroLinDet>$dtedet->nrolindet</NroLinDet>" .
                "<CdgItem>" .
                "<TpoCodigo>INTERNO</TpoCodigo>" .
                "<VlrCodigo>" . $VlrCodigo . "</VlrCodigo>" .
                "</CdgItem>" .
                "<NmbItem>" . $VlrCodigo . "</NmbItem>" .
                "<DscItem>" . $NmbItem . "</DscItem>" .
                "<QtyItem>" . $dtedet->qtyitem . "</QtyItem>" .
                "<UnmdItem>" . $UnmdItem . "</UnmdItem>" .
                "<PrcItem>$dtedet->prcitem</PrcItem>" .
                "<MontoItem>$dtedet->montoitem</MontoItem>" .
                "</Detalle>";
                $aux_totalqtyitem += $dtedet->qtyitem;
            }
        
            //$TpoDocRef = (empty($dte->dteguiadesp->despachoord_id) ? "" : "OD:" . $dte->dteguiadesp->despachoord_id . " ") . (empty($dte->dteguiadesp->ot) ? "" : "OT:" . $dte->dteguiadesp->ot . " ")  . (empty($dte->obs) ? "" : $dte->obs . " ") . (empty($dte->lugarentrega) ? "" : $dte->dteguiadesp->lugarentrega . " ")  . (empty($dte->dteguiadesp->comunaentrega_id) ? "" : $dte->dteguiadesp->comunaentrega->nombre . " ");
            $TpoDocRef = (empty($dte->obs) ? "" : $dte->obs . " ");
            $TpoDocRef = strtoupper(sanear_string(substr(trim($TpoDocRef),0,90)));
        
            $contenido .= "<Referencia>" .
            "<NroLinRef>1</NroLinRef>" .
            "<TpoDocRef>TPR</TpoDocRef>" .
            "<FolioRef>00001000</FolioRef>" .
            "<FchRef>$FchEmis</FchRef>" .
            "<RazonRef>TOTAL UNIDADES:$aux_totalqtyitem</RazonRef>" .
            "</Referencia>" .
            "<Referencia>" .
            "<NroLinRef>2</NroLinRef>" .
            "<TpoDocRef>SRD</TpoDocRef>" .
            "<FolioRef>00001000</FolioRef>" .
            "<FchRef>$FchEmis</FchRef>" .
            "<RazonRef>DESPACHO: $DirRecep</RazonRef>" .
            "</Referencia>";
            if($FolioRef){
                $contenido .= "<Referencia>" .
                "<NroLinRef>3</NroLinRef>" .
                "<TpoDocRef>801</TpoDocRef>" .
                "<FolioRef>$FolioRef</FolioRef>" .
                "<FchRef>$FchEmis</FchRef>" .
                "<RazonRef>$TpoDocRef</RazonRef>" .
                "</Referencia>";
        
            }else{
                if(!empty($dte->obs)){
                    $FolioRef = date('dmY');
                    $contenido .= "<Referencia>" .
                    "<NroLinRef>3</NroLinRef>" .
                    "<TpoDocRef>801</TpoDocRef>" .
                    "<FolioRef>$FolioRef</FolioRef>" .
                    "<FchRef>$FchEmis</FchRef>" .
                    "<RazonRef>$TpoDocRef</RazonRef>" .
                    "</Referencia>";    
                }
            }
            $contenido .= "</Documento>" .
            "</DTE>";
        }
        //dd($contenido);
        return $contenido;
    }
    if($dte->foliocontrol->tipodocto == 33 or $dte->foliocontrol->tipodocto == 34){
        //$aux_dte = consultaindex($id);
        $Folio = str_pad($Folio, 10, "0", STR_PAD_LEFT);
        //$dte = Dte::findOrFail($id);
        $rutrecep = $dte->cliente->rut;
        $rutrecep = number_format( substr ( $rutrecep, 0 , -1 ) , 0, "", "") . '-' . substr ( $rutrecep, strlen($rutrecep) -1 , 1 );
    
        $empresa = Empresa::findOrFail(1);
        $RznSoc = strtoupper(sanear_string(substr(trim($empresa->razonsocial),0,100)));
        $GiroEmis = strtoupper(sanear_string(substr(trim($empresa->giro),0,80)));
        $Acteco = substr(trim($empresa->acteco),0,6);
        $DirOrigen = strtoupper(sanear_string(substr(trim($empresa->sucursal->direccion),0,60)));
        $CmnaOrigen = strtoupper(sanear_string(substr(trim($empresa->sucursal->comuna->nombre),0,20)));
        $CiudadOrigen = strtoupper(sanear_string(substr(trim($empresa->sucursal->comuna->provincia->nombre),0,20)));
        $contacto = strtoupper(sanear_string(substr(trim($dte->cliente->contactonombre . " Telf:" . $dte->cliente->contactotelef),0,40)));
        $CorreoRecep = strtoupper(substr(trim($dte->cliente->contactoemail),0,80));
        $RznSocRecep = strtoupper(sanear_string(substr(trim($dte->cliente->razonsocial),0,100)));
        $GiroRecep = strtoupper(sanear_string(substr(trim($dte->cliente->giro),0,40)));
        $DirRecep = strtoupper(sanear_string(substr(trim($dte->cliente->direccion),0,70)));
        $CmnaRecep = strtoupper(sanear_string(substr(trim($dte->cliente->comuna->nombre),0,20)));
        $CiudadRecep = strtoupper(sanear_string(substr(trim($dte->cliente->ciudad->nombre),0,20)));
        $formapago_desc = $dte->dtefac->formapago->descripcion;
        $FchVenc = $dte->dtefac->fchvenc;
        if($dte->dtefac->formapago_id == 2 or $dte->dtefac->formapago_id == 3){
            $formapago_desc .= " " . $dte->cliente->plazopago->descripcion;
        }
        $FolioRef = substr(trim($dte->oc_id),0,20);
        $contenido = "";
        $TipoDTE = $dte->foliocontrol->tipodocto;
    
        if($tipoArch == "XML"){
            $FchEmis = $dte->fchemis;
            $contenido = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\" standalone=\"yes\"?>" .
            "<DTE version=\"1.0\">" .
            "<Documento ID=\"R" . $empresa->rut . "T" . $TipoDTE . "F" . $Folio . "\">" .
            "<Encabezado>" .
            "<IdDoc>" .
            "<TipoDTE>$TipoDTE</TipoDTE>" .
            "<Folio>$Folio</Folio>" .
            "<FchEmis>$FchEmis</FchEmis>" .
            "<TipoDespacho>$dte->tipodespacho</TipoDespacho>" .
            "<TpoImpresion>N</TpoImpresion>" .
            "<FchPago>$FchVenc</FchPago>" .            
            "<TermPagoGlosa>$formapago_desc</TermPagoGlosa>" .
            "<FchVenc>$FchVenc</FchVenc>" .
            "</IdDoc>" .
            "<Emisor>" .
            "<RUTEmisor>$empresa->rut</RUTEmisor>" .
            "<RznSoc>$RznSoc</RznSoc>" .
            "<GiroEmis>$GiroEmis</GiroEmis>" .
            "<Acteco>$Acteco</Acteco>" .
            "<DirOrigen>$DirOrigen</DirOrigen>" .
            "<CmnaOrigen>$CmnaOrigen</CmnaOrigen>" .
            "<CiudadOrigen>$CiudadOrigen</CiudadOrigen>" .
            "</Emisor>" .
            "<Receptor>" .
            "<RUTRecep>$rutrecep</RUTRecep>" .
            "<RznSocRecep>$RznSocRecep</RznSocRecep>" .
            "<GiroRecep>$GiroRecep</GiroRecep>" .
            "<Contacto>$contacto</Contacto>" .
            "<CorreoRecep>$CorreoRecep</CorreoRecep>" .
            "<DirRecep>$DirRecep</DirRecep>" .
            "<CmnaRecep>$CmnaRecep</CmnaRecep>" .
            "<CiudadRecep>$CiudadRecep</CiudadRecep>" .
            "</Receptor>" .
            "<Totales>";
            if($TipoDTE == 33){
                $contenido .= "<MntNeto>$dte->mntneto</MntNeto>" .
                "<TasaIVA>$dte->tasaiva</TasaIVA>" .
                "<IVA>$dte->iva</IVA>" .
                "<MntTotal>$dte->mnttotal</MntTotal>";
            }else{
                $contenido .= "<MntExe>$dte->mnttotal</MntExe>" .
                "<MntTotal>$dte->mnttotal</MntTotal>";
            }
            $contenido .= "</Totales>" .
            "</Encabezado>";
        
            $aux_totalqtyitem = 0;
        
            foreach ($dte->dtedets as $dtedet) {
                $VlrCodigo = substr(trim($dtedet->vlrcodigo),0,35);
                $NmbItem = strtoupper(sanear_string(substr(trim($dtedet->nmbitem),0,80)));
                $DscItem = strtoupper(sanear_string(trim($dtedet->dscitem)));
                $UnmdItem = substr(trim($dtedet->unmditem),0,4);
                $contenido .= "<Detalle>" .
                "<NroLinDet>$dtedet->nrolindet</NroLinDet>" .
                "<CdgItem>" .
                "<TpoCodigo>INTERNO</TpoCodigo>" .
                "<VlrCodigo>" . $VlrCodigo . "</VlrCodigo>" .
                "</CdgItem>" .
                "<NmbItem>" . $VlrCodigo . "</NmbItem>" .
                "<DscItem>" . $NmbItem . "</DscItem>" .
                "<QtyItem>" . $dtedet->qtyitem . "</QtyItem>" .
                "<UnmdItem>" . $UnmdItem . "</UnmdItem>" .
                "<PrcItem>$dtedet->prcitem</PrcItem>" .
                "<MontoItem>$dtedet->montoitem</MontoItem>" .
                "</Detalle>";
                $aux_totalqtyitem += $dtedet->qtyitem;
            }
        
            $contenido .= "<Referencia>" .
            "<NroLinRef>1</NroLinRef>" .
            "<TpoDocRef>TPR</TpoDocRef>" .
            "<FolioRef>00001000</FolioRef>" .
            "<FchRef>$FchEmis</FchRef>" .
            "<RazonRef>TOTAL UNIDADES:$aux_totalqtyitem</RazonRef>" .
            "</Referencia>" .
            "<Referencia>" .
            "<NroLinRef>2</NroLinRef>" .
            "<TpoDocRef>SRD</TpoDocRef>" .
            "<FolioRef>00001000</FolioRef>" .
            "<FchRef>$FchEmis</FchRef>" .
            "<RazonRef>DESPACHO: $DirRecep</RazonRef>" .
            "</Referencia>";
            $array_ocs = []; //ARRAY ORDENES DE COMPPRA
            $array_GDs = []; //ARRAY GUIAS DE DESPACHO
            foreach ($dte->dtedtes as $dtedte) {
                /* EN COMENTARIO PORQUE ESTOY GUARDANDO EL NUMERO DE ORDEN DE COMPRA EN DTEOC DE TODAS LAS FACTURAS
                //TAMBIEN RECORDAR QUE SOLO SE PUEDE FACTURAR UNA UNICA ORDEN DE COMPRA POR FACTURA
                //NO SE PUEDEN FACTURAR MAS DE 1 ORDEN DE COMPRA POR FACTURA
                if($dtedte->dteguiadesp->notaventa){
                    $oc_id = $dtedte->dteguiadesp->notaventa->oc_id;
                    $array_ocs [$oc_id] = [
                        "oc_id" => $oc_id,
                        "fecha" => date("Y-m-d", strtotime($dtedte->dteguiadesp->notaventa->fechahora))
                        
                    ];
                }
                //FIN EN COMENTARIO PORQUE ESTOY GUARDANDO EL NUMERO DE ORDEN DE COMPRA EN DTEOC DE TODAS LAS FACTURAS
                */
                $array_GDs[] = [
                    "nrodocto" => $dtedte->dter->nrodocto,
                    "fchemis" => date("Y-m-d", strtotime($dtedte->dter->fchemis))
                ];
            }
            //$array_ocs = explode(",", $aux_dte[0]->oc_id);
            $i = 2;
            $aux_RazonRef = strtoupper(sanear_string($dte->obs ? (" " . $dte->obs) : ""));
            $aux_RazonRefImp = false;
            foreach ($dte->dteocs as $dteoc) {
                $array_ocs [$dteoc->oc_id] = [
                    "oc_id" => $dteoc->oc_id,
                    "fecha" => date("Y-m-d")
                ];                
            }
            foreach ($array_ocs as $array_oc) {
                if(!is_null($array_oc["oc_id"]) and !empty($array_oc["oc_id"])){
                    $i++;
                    $contenido .= "<Referencia>" .
                    "<NroLinRef>$i</NroLinRef>" .
                    "<TpoDocRef>801</TpoDocRef>" .
                    "<FolioRef>" . $array_oc["oc_id"] . "</FolioRef>" .
                    "<FchRef>" . $array_oc["fecha"] . "</FchRef>";
                    if($aux_RazonRefImp == false){
                        $aux_RazonRefImp = true;
                        if($aux_RazonRef != "" and !is_null($aux_RazonRef)){
                            $contenido .= "<RazonRef>$aux_RazonRef</RazonRef>";
                        }
                    }
                    $contenido .= "</Referencia>";    
                }
            }

            //$array_dter_id = explode(",", $aux_dte[0]->dter_id);
            foreach ($array_GDs as $array_GD) {
                $i++;
                $contenido .= "<Referencia>" .
                "<NroLinRef>$i</NroLinRef>" .
                "<TpoDocRef>52</TpoDocRef>" .
                "<FolioRef>" . $array_GD["nrodocto"] . "</FolioRef>" .
                "<FchRef>" . $array_GD["fchemis"] . "</FchRef>";
                if($aux_RazonRefImp == false){
                    $aux_RazonRefImp = true;
                    $contenido .= "<RazonRef>$aux_RazonRef</RazonRef>";
                }
                $contenido .= "</Referencia>";
            }
            if($dte->dtefac->hep){
                $i++;
                $contenido .= "<Referencia>" .
                "<NroLinRef>$i</NroLinRef>" .
                "<TpoDocRef>HES</TpoDocRef>" .
                "<FolioRef>" . $dte->dtefac->hep . "</FolioRef>" .
                "<FchRef>$FchEmis</FchRef>" .
                "</Referencia>";
            }
            if($dte->dtefac->notped){
                $i++;
                $contenido .= "<Referencia>" .
                "<NroLinRef>$i</NroLinRef>" .
                "<TpoDocRef>802</TpoDocRef>" .
                "<FolioRef>" . $dte->dtefac->notped . "</FolioRef>" .
                "<FchRef>$FchEmis</FchRef>" .
                "</Referencia>";
            }
            $contenido .= "</Documento>" .
            "</DTE>";
        }
        return $contenido;
    }
    if(($dte->foliocontrol->tipodocto == 56) or ($dte->foliocontrol->tipodocto == 61)){
        $Folio = str_pad($Folio, 10, "0", STR_PAD_LEFT);
        $rutrecep = $dte->cliente->rut;
        $rutrecep = number_format( substr ( $rutrecep, 0 , -1 ) , 0, "", "") . '-' . substr ( $rutrecep, strlen($rutrecep) -1 , 1 );

        $empresa = Empresa::findOrFail(1);
        $RznSoc = strtoupper(sanear_string(substr(trim($empresa->razonsocial),0,100)));
        $GiroEmis = strtoupper(sanear_string(substr(trim($empresa->giro),0,80)));
        $Acteco = substr(trim($empresa->acteco),0,6);
        $DirOrigen = strtoupper(sanear_string(substr(trim($empresa->sucursal->direccion),0,60)));
        $CmnaOrigen = strtoupper(sanear_string(substr(trim($empresa->sucursal->comuna->nombre),0,20)));
        $CiudadOrigen = strtoupper(sanear_string(substr(trim($empresa->sucursal->comuna->provincia->nombre),0,20)));
        $contacto = strtoupper(sanear_string(substr(trim($dte->cliente->contactonombre . " Telf:" . $dte->cliente->contactotelef),0,40)));
        $CorreoRecep = strtoupper(substr(trim($dte->cliente->contactoemail),0,80));
        $RznSocRecep = strtoupper(sanear_string(substr(trim($dte->cliente->razonsocial),0,100)));
        $GiroRecep = strtoupper(sanear_string(substr(trim($dte->cliente->giro),0,40)));
        $DirRecep = strtoupper(sanear_string(substr(trim($dte->cliente->direccion),0,70)));
        $CmnaRecep = strtoupper(sanear_string(substr(trim($dte->cliente->comuna->nombre),0,20)));
        $CiudadRecep = strtoupper(sanear_string(substr(trim($dte->cliente->ciudad->nombre),0,20)));
        if($dte->foliocontrol_id == 1 or $dte->foliocontrol_id == 7){
            $FchVenc = $dte->dtefac->fchvenc;
            $formapago_desc = $dte->dtefac->formapago->descripcion;
            if($dte->dtefac->formapago_id == 2 or $dte->dtefac->formapago_id == 3){
                $formapago_desc .= " " . $dte->cliente->plazopago->descripcion;
            }    
        }
        $FolioRef = substr(trim($dte->oc_id),0,20);
        $TipoDocto = $dte->foliocontrol->tipodocto;
        $contenido = "";
        if($tipoArch == "XML"){
            $FchEmis = $dte->fchemis;
    
            $contenido = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\" standalone=\"yes\"?>" .
            "<DTE version=\"1.0\">" .
            "<Documento ID=\"R" .$empresa->rut  . "T" . $TipoDocto . "F" .  $Folio . "\">" .
            "<Encabezado>" .
            "<IdDoc>" .
            "<TipoDTE>$TipoDocto</TipoDTE>" .
            "<Folio>$Folio</Folio>" .
            "<FchEmis>$FchEmis</FchEmis>" .
            "<TipoDespacho>$dte->tipodespacho</TipoDespacho>" .
            "<TpoImpresion>N</TpoImpresion>";
            if($dte->foliocontrol_id == 1 or $dte->foliocontrol_id == 7){
                $contenido .= "<TermPagoGlosa>$formapago_desc</TermPagoGlosa>" .
                "<FchVenc>$FchVenc</FchVenc>";
        
            }
            $contenido .= "</IdDoc>" .
            "<Emisor>" .
            "<RUTEmisor>$empresa->rut</RUTEmisor>" .
            "<RznSoc>$RznSoc</RznSoc>" .
            "<GiroEmis>$GiroEmis</GiroEmis>" .
            "<Acteco>$Acteco</Acteco>" .
            "<DirOrigen>$DirOrigen</DirOrigen>" .
            "<CmnaOrigen>$CmnaOrigen</CmnaOrigen>" .
            "<CiudadOrigen>$CiudadOrigen</CiudadOrigen>" .
            "</Emisor>" .
            "<Receptor>" .
            "<RUTRecep>$rutrecep</RUTRecep>" .
            "<RznSocRecep>$RznSocRecep</RznSocRecep>" .
            "<GiroRecep>$GiroRecep</GiroRecep>" .
            "<Contacto>$contacto</Contacto>" .
            "<CorreoRecep>$CorreoRecep</CorreoRecep>" .
            "<DirRecep>$DirRecep</DirRecep>" .
            "<CmnaRecep>$CmnaRecep</CmnaRecep>" .
            "<CiudadRecep>$CiudadRecep</CiudadRecep>" .
            "</Receptor>" .
            "<Totales>";
            if($dte->dtedte->dter->foliocontrol->tipodocto == 34){
                $contenido .= "<MntExe>$dte->mnttotal</MntExe>" .
                "<MntTotal>$dte->mnttotal</MntTotal>";
            }else{
                $contenido .= "<MntNeto>$dte->mntneto</MntNeto>" .
                "<TasaIVA>$dte->tasaiva</TasaIVA>" .
                "<IVA>$dte->iva</IVA>" .
                "<MntTotal>$dte->mnttotal</MntTotal>";    
            }
            $contenido .= "</Totales>" .
            "</Encabezado>";
        
            $aux_totalqtyitem = 0;
        
            foreach ($dte->dtedets as $dtedet) {
                $VlrCodigo = substr(trim($dtedet->vlrcodigo),0,35);
                $NmbItem = strtoupper(sanear_string(substr(trim($dtedet->nmbitem),0,80)));
                $DscItem = strtoupper(sanear_string(substr(trim($dtedet->dscitem),0,1000)));
                $UnmdItem = substr(trim($dtedet->unmditem),0,4);
                $contenido .= "<Detalle>" .
                "<NroLinDet>$dtedet->nrolindet</NroLinDet>" .
                "<CdgItem>" .
                "<TpoCodigo>INTERNO</TpoCodigo>" .
                "<VlrCodigo>" . $VlrCodigo . "</VlrCodigo>" .
                "</CdgItem>" .
                "<NmbItem>" . $VlrCodigo . "</NmbItem>" .
                "<DscItem>" . $NmbItem . "</DscItem>" .
                "<QtyItem>" . $dtedet->qtyitem . "</QtyItem>" .
                "<UnmdItem>" . $UnmdItem . "</UnmdItem>";
                if($dtedet->prcitem > 0){
                    $contenido .= "<PrcItem>$dtedet->prcitem</PrcItem>";
                }
                $contenido .= "<MontoItem>$dtedet->montoitem</MontoItem>" .
                "</Detalle>";
                $aux_totalqtyitem += $dtedet->qtyitem;
            }
            //dd($dte->dtedte->dter);

            if($TipoDocto == 61 or $TipoDocto == 56){
                $TpoDocRef = $dte->dtedte->dter->foliocontrol->tipodocto;
                $aux_nrodocto = $dte->dtedte->dter->nrodocto;
                $aux_FchEmis = $dte->dtedte->dter->fchemis;
                $aux_codref = $dte->dtencnd->codref;
                $RazonRef = strtoupper(sanear_string(substr(empty($dte->obs) ? " " : $dte->obs,0,90)));
    
                $contenido .= "<Referencia>" .
                "<NroLinRef>1</NroLinRef>" .
                "<TpoDocRef>$TpoDocRef</TpoDocRef>" .
                "<FolioRef>$aux_nrodocto</FolioRef>" .
                "<FchRef>$aux_FchEmis</FchRef>" .
                "<CodRef>$aux_codref</CodRef>" .
                "<RazonRef>$RazonRef</RazonRef>" .
                "</Referencia>";
            }
            $contenido .= "</Documento>" .
            "</DTE>";
        }
        return $contenido;
    }


}

function updatenumfact($dte,$foliocontrol,$request){
    $foliocontrol->ultfoliouti = $dte->nrodocto;
    $foliocontrol->bloqueo = 0;    
    if ($dte->save() and $foliocontrol->save()) {
        return response()->json([
                                'mensaje' => 'ok',
                                'status' => '0',
                                'id' => $request->id,
                                'nfila' => $request->nfila,
                                'nrodocto' => $dte->nrodocto
                                ]);
    } else {
        return response()->json(['mensaje' => 'ng']);
    }
}

function updatenumfactprueba($dte,$foliocontrol,$request){
    $foliocontrol->ultfoliouti = $dte->nrodocto;
    $foliocontrol->bloqueo = 0;    
    if ($dte->save() and $foliocontrol->save()) {
        return response()->json([
                                'mensaje' => 'ok',
                                'status' => '0',
                                'id' => $request->id,
                                'nfila' => $request->nfila,
                                'nrodocto' => $dte->nrodocto
                                ]);
    } else {
        return response()->json(['mensaje' => 'ng']);
    }
}

function consultaindex($dte_id){
    $user = Usuario::findOrFail(auth()->id());
    $sucurArray = $user->sucursales->pluck('id')->toArray();
    $sucurcadena = implode(",", $sucurArray);

    if(empty($dte_id)){
        $aux_conddte_id = " true";
    }else{
        $aux_conddte_id = "dte.id = $dte_id";
    }

    $sql = "SELECT dte.id,dte.fechahora,cliente.rut,cliente.razonsocial,comuna.nombre as nombre_comuna,
    clientebloqueado.descripcion as clientebloqueado_descripcion,
    GROUP_CONCAT(DISTINCT dtedtenc.dter_id) AS dter_id,
    GROUP_CONCAT(DISTINCT notaventa.cotizacion_id) AS cotizacion_id,
    GROUP_CONCAT(DISTINCT notaventa.oc_id) AS oc_id,
    GROUP_CONCAT(DISTINCT notaventa.oc_file) AS oc_file,
    GROUP_CONCAT(DISTINCT dteguiadesp.notaventa_id) AS notaventa_id,
    GROUP_CONCAT(DISTINCT despachoord.despachosol_id) AS despachosol_id,
    GROUP_CONCAT(DISTINCT dteguiadesp.despachoord_id) AS despachoord_id,
    (SELECT GROUP_CONCAT(DISTINCT dte1.nrodocto) 
    FROM dte AS dte1
    where dte1.id = dtedtefac.dter_id
    GROUP BY dte1.id) AS nrodocto_guiadesp,
    (SELECT GROUP_CONCAT(DISTINCT dte1.nrodocto) AS nrodocto
    FROM dte AS dte1
    where dte1.id = dtedtenc.dtefac_id
    GROUP BY dte1.id) AS nrodocto_factura,
    dte.updated_at
    FROM dte INNER JOIN dtedte as dtedtenc
    ON dte.id = dtedtenc.dte_id AND ISNULL(dte.deleted_at) and isnull(dtedtenc.deleted_at)
    INNER JOIN dtefac
    ON dtedtenc.dtefac_id = dtefac.dte_id
    INNER JOIN dtedte as dtedtefac
    on dtedtefac.dte_id = dtedtenc.dtefac_id
    INNER JOIN dteguiadesp
    ON dtedtefac.dter_id = dteguiadesp.dte_id
    INNER JOIN despachoord
    ON despachoord.id = dteguiadesp.despachoord_id
    INNER JOIN notaventa
    ON notaventa.id = despachoord.notaventa_id
    INNER JOIN cliente
    ON dte.cliente_id  = cliente.id AND ISNULL(cliente.deleted_at)
    INNER JOIN comuna
    ON comuna.id = cliente.comunap_id
    LEFT JOIN clientebloqueado
    ON dte.cliente_id = clientebloqueado.cliente_id AND ISNULL(clientebloqueado.deleted_at)
    WHERE dte.foliocontrol_id=5 
    AND dte.id NOT IN (SELECT dteanul.dte_id FROM dteanul WHERE ISNULL(dteanul.deleted_at))
    AND ISNULL(dte.statusgen)
    AND dte.sucursal_id IN ($sucurcadena)
    AND $aux_conddte_id
    GROUP BY dte.id
    ORDER BY dte.id desc;";

    return DB::select($sql);
}

function consultasql_dte($request){
    $aux_statusCond = "true";
    if (isset($request->statusgen)){
        $aux_statusCond = "dte.statusgen = $request->statusgen";
    }
    //dte_idrefanul: CODIGO DTE QUE ANULO EL DTE
    //codref: CODIGO REFERENCIA QUE INDICA EL MOTIVO DE LA ANULACION DEL DTE
    $sql = "SELECT dte.id as dte_id,dte.fchemis,dte.nrodocto,dte.foliocontrol_id,dte.fechahora,dte.centroeconomico_id,
    dte.vendedor_id,dte.obs,dte.indtraslado,dte.updated_at,dtefac.hep,dtefac.formapago_id,dtefac.fchvenc,
    0 as dte_idrefanul,0 as codref,
    cliente.id as cliente_id,cliente.rut,cliente.razonsocial,
    cliente.telefono,cliente.email,cliente.direccion,cliente.contactonombre,
    cliente.formapago_id,cliente.plazopago_id,cliente.giro_id,cliente.giro,cliente.regionp_id,
    cliente.provinciap_id,cliente.comunap_id,
    clientebloqueado.descripcion,comuna.nombre as comuna_nombre,provincia.nombre as provincia_nombre,
    formapago.descripcion as formapago_desc,plazopago.dias as plazopago_dias,
    foliocontrol.tipodocto,foliocontrol.nombrepdf,dte.statusgen
    FROM dte INNER JOIN cliente
    ON dte.cliente_id  = cliente.id AND ISNULL(dte.deleted_at) AND ISNULL(cliente.deleted_at)
    LEFT JOIN dtefac
    on dte.id = dtefac.dte_id
    LEFT JOIN clientebloqueado
    ON dte.cliente_id = clientebloqueado.cliente_id AND ISNULL(clientebloqueado.deleted_at)
    left join comuna
    ON cliente.comunap_id=comuna.id and isnull(comuna.deleted_at)
    left join provincia
    ON cliente.provinciap_id=provincia.id and isnull(provincia.deleted_at)
    INNER JOIN formapago
    ON  cliente.formapago_id = formapago.id and isnull(formapago.deleted_at)
    INNER JOIN plazopago
    ON cliente.plazopago_id = plazopago.id and isnull(plazopago.deleted_at)
    INNER JOIN foliocontrol
    ON foliocontrol.id = dte.foliocontrol_id
    WHERE dte.foliocontrol_id in ($request->condFoliocontrol) 
    AND dte.nrodocto = $request->nrodocto
    AND dte.id NOT IN (SELECT dteanul.dte_id FROM dteanul WHERE ISNULL(dteanul.deleted_at))
    AND $aux_statusCond
    ORDER BY dte.id desc;";
    $dte = DB::select($sql);
    return $dte;    
}

