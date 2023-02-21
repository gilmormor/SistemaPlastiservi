<?php

namespace App\Models;

use App\Http\Controllers\SoapController;
use App\Models\Seguridad\Usuario;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use setasign\Fpdi\Fpdi;

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

    //RELACION DE UNO A MUCHOS DteOC
    public function dteocs()
    {
        return $this->hasMany(DteOC::class,'dte_id');
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
    
    public static function reportguiadesppage($request){
        //dd($request->tipobodega);
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

        $sucurArray = $user->sucursales->pluck('id')->toArray();
        $sucurcadena = implode(",", $sucurArray);
        $aux_condsucurArray = "dte.sucursal_id  in ($sucurcadena)";

        if(!isset($request->fechad) or empty($request->fechad) or empty($request->fechah)){
            $aux_condFecha = " true";
        }else{
            $fecha = date_create_from_format('d/m/Y', $request->fechad);
            $fechad = date_format($fecha, 'Y-m-d')." 00:00:00";
            $fecha = date_create_from_format('d/m/Y', $request->fechah);
            $fechah = date_format($fecha, 'Y-m-d')." 23:59:59";
            $aux_condFecha = "dte.fechahora>='$fechad' and dte.fechahora<='$fechah'";
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
            }
        }
    
        if(!isset($request->comuna_id) or empty($request->comuna_id)){
            $aux_condcomuna_id = " true";
        }else{
            $aux_condcomuna_id = "notaventa.comunaentrega_id='$request->comuna_id'";
        }
    
        $sql = "SELECT dte.id,dte.nrodocto,dte.fechahora,cliente.rut,cliente.razonsocial,
        notaventa.oc_id as nvoc_id,notaventa.oc_file as nvoc_file,dte.centroeconomico_id,
        dteoc.oc_id,dteoc.oc_file,comuna.nombre as comunanombre,
        tipoentrega.nombre as tipoentrega_nombre,tipoentrega.icono,
        dteguiadesp.notaventa_id,despachoord.fechaestdesp,dteguiadesp.despachoord_id,despachoord.despachosol_id,
        dteanul.obs as dteanul_obs,dteanul.created_at as dteanulcreated_at
        FROM dte INNER JOIN dtedet
        ON dte.id=dtedet.dte_id and isnull(dte.deleted_at) and isnull(dtedet.deleted_at)
        INNER JOIN dteguiadesp
        ON dte.id = dteguiadesp.dte_id and isnull(dteguiadesp.deleted_at)
        LEFT JOIN notaventa
        ON notaventa.id=dteguiadesp.notaventa_id and isnull(notaventa.deleted_at)
        INNER JOIN dtedet_despachoorddet
        ON dtedet_despachoorddet.dtedet_id = dtedet.id and isnull(dtedet_despachoorddet.deleted_at)
        INNER JOIN notaventadetalle
        ON notaventadetalle.id=dtedet_despachoorddet.notaventadetalle_id and isnull(notaventadetalle.deleted_at)
        INNER JOIN despachoord
        ON despachoord.id=dteguiadesp.despachoord_id and isnull(despachoord.deleted_at)
        INNER JOIN despachoorddet
        ON despachoord.id=despachoorddet.despachoord_id and isnull(despachoorddet.deleted_at)
        INNER JOIN producto
        ON dtedet.producto_id=producto.id and isnull(producto.deleted_at)
        INNER JOIN categoriaprod
        ON categoriaprod.id=producto.categoriaprod_id and isnull(categoriaprod.deleted_at)
        INNER JOIN areaproduccion
        ON areaproduccion.id=categoriaprod.areaproduccion_id and isnull(areaproduccion.deleted_at)
        INNER JOIN cliente
        ON cliente.id=notaventa.cliente_id and isnull(cliente.deleted_at)
        INNER JOIN comuna
        ON comuna.id=dteguiadesp.comunaentrega_id and isnull(comuna.deleted_at)
        INNER JOIN tipoentrega
        ON tipoentrega.id = dteguiadesp.tipoentrega_id AND ISNULL(tipoentrega.deleted_at)
        LEFT JOIN dteanul
        ON dteanul.dte_id=dte.id and isnull(dteanul.deleted_at)
        LEFT JOIN dteoc
        ON dteoc.dte_id=dte.id and isnull(dteoc.deleted_at)
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
        GROUP BY dte.id
        ORDER BY dte.id desc;";

        $datas = DB::select($sql);
        return $datas;
    }

    public static function consultalistarguiadesppage($request){
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
        }

        $user = Usuario::findOrFail(auth()->id());
        $sucurArray = $user->sucursales->pluck('id')->toArray();
        $sucurcadena = implode(",", $sucurArray);
    
        $sql = "SELECT dte.id,dte.nrodocto,dte.fchemis,dteguiadesp.despachoord_id,notaventa.cotizacion_id,
        despachoord.despachosol_id,dte.fechahora,despachoord.fechaestdesp,
        cliente.razonsocial,notaventa.oc_id,notaventa.oc_file,dteguiadesp.notaventa_id,
        '' as notaventaxk,comuna.nombre as comuna_nombre,
        tipoentrega.nombre as tipoentrega_nombre,'  ' as te,tipoentrega.icono,clientebloqueado.descripcion as clientebloqueado_descripcion,
        dte.kgtotal as aux_totalkg,
        dte.mnttotal as subtotal,
        dte.updated_at,'' as rutacrear
        FROM dte INNER JOIN dteguiadesp
        ON dte.id = dteguiadesp.dte_id AND ISNULL(dte.deleted_at) and isnull(dteguiadesp.deleted_at)
        INNER JOIN despachoord
        ON dteguiadesp.despachoord_id = despachoord.id AND ISNULL(dte.deleted_at) AND ISNULL(despachoord.deleted_at)
        INNER JOIN notaventa
        ON notaventa.id = dteguiadesp.notaventa_id and isnull(notaventa.deleted_at)
        INNER JOIN cliente
        ON cliente.id = notaventa.cliente_id AND isnull(cliente.deleted_at)
        INNER JOIN comuna
        ON comuna.id = despachoord.comunaentrega_id AND isnull(comuna.deleted_at)
        INNER JOIN tipoentrega
        ON tipoentrega.id = despachoord.tipoentrega_id AND ISNULL(tipoentrega.deleted_at)
        LEFT JOIN clientebloqueado
        ON clientebloqueado.cliente_id = notaventa.cliente_id AND ISNULL(clientebloqueado.deleted_at)
        WHERE $vendedorcond
        and $aux_condFecha
        and $aux_condrut
        and $aux_condoc_id
        and $aux_condgiro_id
        and $aux_condtipoentrega_id
        and $aux_condnotaventa_id
        and $aux_condcomuna_id
        AND dte.sucursal_id in ($sucurcadena)
        AND NOT ISNULL(dte.nrodocto)
        AND NOT ISNULL(dte.fchemis)
        and $aux_conddteguiausada
        and dte.foliocontrol_id = 2
        AND dte.id not in (SELECT dte_id from dteanul where ISNULL(dteanul.deleted_at))
        order BY dte.nrodocto;";

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


        $user = Usuario::findOrFail(auth()->id());
        $sucurArray = $user->sucursales->pluck('id')->toArray();
        $sucurcadena = implode(",", $sucurArray);
    
        $sql = "SELECT dte.id,dte.nrodocto,dte.fchemis,dteguiadesp.despachoord_id,notaventa.cotizacion_id,
        despachoord.despachosol_id,dte.fechahora,despachoord.fechaestdesp,dte.centroeconomico_id,
        cliente.razonsocial,notaventa.oc_id,notaventa.oc_file,dteguiadesp.notaventa_id,
        '' as notaventaxk,comuna.nombre as comuna_nombre,
        tipoentrega.nombre as tipoentrega_nombre,'  ' as te,tipoentrega.icono,clientebloqueado.descripcion as clientebloqueado_descripcion,
        dte.kgtotal as aux_totalkg,
        dte.mnttotal as subtotal,
        dte.updated_at,'' as rutacrear,
        dtedet.id as dtedet_id,dtedet.dtedet_id as dtedetr_id,dtedet_despachoorddet.despachoorddet_id,dtedet_despachoorddet.notaventadetalle_id,
        dtedet.producto_id,dtedet.nrolindet,dtedet.vlrcodigo,dtedet.nmbitem,dtedet.dscitem,dtedet.qtyitem,dtedet.unmditem,
        dtedet.unidadmedida_id,dtedet.prcitem,dtedet.montoitem,dtedet.obsdet,dtedet.itemkg,
        notaventadetalle.precioxkilo,notaventadetalle.precioxkiloreal,
        dte.vendedor_id
        FROM dte INNER JOIN dteguiadesp
        ON dte.id = dteguiadesp.dte_id AND ISNULL(dte.deleted_at) and isnull(dteguiadesp.deleted_at)
        INNER JOIN despachoord
        ON dteguiadesp.despachoord_id = despachoord.id AND ISNULL(despachoord.deleted_at)
        INNER JOIN notaventa
        ON notaventa.id = dteguiadesp.notaventa_id and isnull(notaventa.deleted_at)
        INNER JOIN cliente
        ON cliente.id = notaventa.cliente_id AND isnull(cliente.deleted_at)
        INNER JOIN comuna
        ON comuna.id = despachoord.comunaentrega_id AND isnull(comuna.deleted_at)
        INNER JOIN tipoentrega
        ON tipoentrega.id = despachoord.tipoentrega_id AND ISNULL(tipoentrega.deleted_at)
        INNER JOIN dtedet
        ON dtedet.dte_id = dte.id AND ISNULL(dtedet.deleted_at)
        INNER JOIN dtedet_despachoorddet
        ON dtedet_despachoorddet.dtedet_id = dtedet.id AND ISNULL(dtedet_despachoorddet.deleted_at)
        INNER JOIN notaventadetalle
        ON notaventadetalle.id = dtedet_despachoorddet.notaventadetalle_id AND ISNULL(notaventadetalle.deleted_at)
        LEFT JOIN clientebloqueado
        ON clientebloqueado.cliente_id = notaventa.cliente_id AND ISNULL(clientebloqueado.deleted_at)
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
        AND isnull(despachoord.numfactura)
        AND dte.id not in (SELECT dte_id from dteanul where ISNULL(dteanul.deleted_at))
        order BY dte.nrodocto;";
    
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


        $user = Usuario::findOrFail(auth()->id());
        $sucurArray = $user->sucursales->pluck('id')->toArray();
        $sucurcadena = implode(",", $sucurArray);
    
        $sql = "SELECT dte.id,dte.fechahora,cliente.rut,cliente.razonsocial,comuna.nombre as nombre_comuna,
        clientebloqueado.descripcion as clientebloqueado_descripcion,
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
        GROUP BY dtedte1.dte_id) AS nrodocto_guiadesp,
        dteanul.obs as dteanul_obs,dteanul.created_at as dteanulcreated_at,
        dte.nrodocto,dte.updated_at
        FROM dte INNER JOIN dtedte
        ON dte.id = dtedte.dte_id AND ISNULL(dte.deleted_at) and isnull(dtedte.deleted_at)
        INNER JOIN dteguiadesp
        ON dtedte.dter_id = dteguiadesp.dte_id and isnull(dteguiadesp.deleted_at)
        INNER JOIN despachoord
        ON despachoord.id = dteguiadesp.despachoord_id and isnull(despachoord.deleted_at)
        INNER JOIN notaventa
        ON notaventa.id = despachoord.notaventa_id and isnull(notaventa.deleted_at)
        INNER JOIN cliente
        ON dte.cliente_id  = cliente.id AND ISNULL(cliente.deleted_at)
        INNER JOIN comuna
        ON comuna.id = cliente.comunap_id AND ISNULL(comuna.deleted_at)
        LEFT JOIN clientebloqueado
        ON dte.cliente_id = clientebloqueado.cliente_id AND ISNULL(clientebloqueado.deleted_at)
        LEFT JOIN dteanul
        ON dteanul.dte_id = dte.id AND ISNULL(dteanul.deleted_at)
        WHERE dte.foliocontrol_id=1 
        AND dte.sucursal_id IN ($sucurcadena)
        AND $aux_conddte_id
        AND $aux_condFecha
        AND $aux_condnrodocto
        AND $aux_condrut
        AND $aux_condoc_id
        AND $aux_condnotaventa_id
        GROUP BY dte.id
        ORDER BY dte.id desc;";

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
            if(count($dtesql) > 0){
                $dte = Dte::findOrFail($dtesql[0]->dte_id);
                foreach ($dte->dtedters as $dtedter) {
                    if(!isset($dtedter->dte->dteanul)){
                        if($dtedter->dte->dtencnd->codref == 1){
                            unset($respuesta['dte'][0]);
                            //EL DOCUMENTO QUE SE BUSCA, PREVIAMENTE SE IDENTIFICA SI FUE ANULADO POR OTRO DOCUMENTO
                            $respuesta['mensaje'] = $dte->foliocontrol->desc . ":" . $request->nrodocto . " anulado por " . $dtedter->dte->foliocontrol->desc . ":" . $dtedter->dte->nrodocto;
                            return $respuesta;
                        }
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
                if(str_contains("NC,ND", $request->TipoDTE)){
                    if($dte->foliocontrol_id == 1){
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
                        }
                    }
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
                            //$dteQueAnula->dtedter->dte->nrodocto= NUMERO DE DOCUMENTO GENERADO POR SII DEL DOCUMENTO QUE ANULA
                            if($dteQueAnula->dtedter and !empty($dteQueAnula->dtedter->dte->nrodocto)){
                                //AQUI INCLUSO BUSCO SI ESE DTE QUE ANULA TAMBIEN ES ANULADO POR OTRO DTE
                                //EJEMPLO: SI LA NC:30 FUE ANULADA POR UNA ND:155, PERO ESTA ND:155 TAMBIEN FUE ANULADA POR NC:31, ESTO QUIERE QUE LA NC :30 VUELVE A ESTAR ACTIVA
                                do {
                                    $dteQueAnula = $dteQueAnula->dtedter->dte;
                                    $contanul++;
                                } while ($dteQueAnula->dtedter);
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
                    'mensaje'=>'Se agotaron los folios. Se deben de pedir nuevos folios',
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
            return response()->json([
                'id' => 0,
                'mensaje'=>'Numero de folio no encontrado.',
                'tipo_alert' => 'error'
            ]);
        }
        if($foliocontrol->ultfoliouti >= $foliocontrol->ultfoliohab ){
            return response()->json([
                'id' => 0,
                'mensaje'=>'Se agotaron los folios. Se deben de pedir nuevos folios',
                'tipo_alert' => 'error'
            ]);
        }
        $foliocontrol = Foliocontrol::findOrFail($dte->foliocontrol_id);
        if($foliocontrol->bloqueo == 1){
                return response()->json([
                    'id' => 0,
                    'mensaje'=>'Folio bloqueado, vuelva a intentar. Folio: ' . $foliocontrol->ultfoliouti,
                    'tipo_alert' => 'error'
                ]);
        }else{
            //Si $foliocontrol->bloqueo = 0;
            //Bloqueo el registro para que no pueda ser modificado por otro usuario
            //Al procesar el registro desbloqueo 
            $foliocontrol->bloqueo = 1;
            $foliocontrol->save();
        }
        $empresa = Empresa::findOrFail(1);
        $soap = new SoapController();
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
                    return response()->json([
                        'id' => 0,
                        'mensaje'=>'Error: #' . $Solicitar_Folio->Estatus . " " . $Solicitar_Folio->MsgEstatus,
                        'tipo_alert' => 'error'                
                    ]);    
                }
            }else{
                return response()->json([
                    'id' => 0,
                    'mensaje'=>'Error: ' . $Solicitar_Folio,
                    'tipo_alert' => 'error'                
                ]);    
            }
        }while($bandNoExisteFolio);
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
                $nombreArchPDF =  $foliocontrol->nombrepdf . str_pad($aux_folio, 8, "0", STR_PAD_LEFT);
                Storage::disk('public')->put('/facturacion/dte/procesados/' . $nombreArchPDF . '.xml', $Carga_TXTDTE->XML);
                Storage::disk('public')->put('/facturacion/dte/procesados/' . $nombreArchPDF . '.pdf', $Carga_TXTDTE->PDF);
                Storage::disk('public')->put('/facturacion/dte/procesados/' . $nombreArchPDF . '_cedible.pdf', $Carga_TXTDTE->PDFCedible);

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
                return response()->json([
                    'id' => 1
                ]);    

            }else{
                return response()->json([
                    'id' => 0,
                    'mensaje'=>'Error: #' . $Carga_TXTDTE->Estatus . " " . $Carga_TXTDTE->MsgEstatus,
                    'tipo_alert' => 'error'                
                ]);    
            }
        }else{
            return response()->json([
                'id' => 0,
                'mensaje'=>'Error: ' . $Solicitar_Folio,
                'tipo_alert' => 'error'                
            ]);
        }
    }

    public static function anulardte($request)
    {
        //PROCESO DE ANULAR DTE SIN HABER ASIGNADO O GENERADO UN NUMERO DE DTE (DOCUMENTO TRIBUTARIO LELECTRONICO)
        //dd($request);
        $dte = Dte::findOrFail($request->dte_id);
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
            if($dte->foliocontrol_id == 1){
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


        $user = Usuario::findOrFail(auth()->id());
        $sucurArray = $user->sucursales->pluck('id')->toArray();
        $sucurcadena = implode(",", $sucurArray);
    
        $sql = "SELECT dte.id,dte.fechahora,dte.nrodocto,cliente.rut,cliente.razonsocial,comuna.nombre as nombre_comuna,
        clientebloqueado.descripcion as clientebloqueado_descripcion,
        dteorigen.id as dteorigen_id,dteorigen.nrodocto as dteorigen_nrodocto,
        dte.updated_at, dteorigen.updated_at as dteorigen_updated_at,
        dteanul.obs as dteanul_obs,dteanul.created_at as dteanulcreated_at
        FROM dte INNER JOIN dtedte
        ON dte.id = dtedte.dte_id AND ISNULL(dte.deleted_at) and isnull(dtedte.deleted_at)
        LEFT JOIN dte as dteorigen
        ON dtedte.dter_id = dteorigen.id and isnull(dteorigen.deleted_at)
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
        AND $aux_conddte_id
        AND $aux_condFecha
        AND $aux_condnrodocto
        AND $aux_condrut
        AND $aux_condoc_id
        AND $aux_condnotaventa_id
        GROUP BY dte.id
        ORDER BY dte.id desc;";
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
    $contacto = strtoupper(sanear_string(substr(trim($dte->cliente->contactonombre . " Telf:" . $dte->cliente->contactotelef),0,80)));
    $CorreoRecep = strtoupper(substr(trim($dte->cliente->contactoemail),0,80));
    $RznSocRecep = strtoupper(sanear_string(substr(trim($dte->cliente->razonsocial),0,100)));
    $GiroRecep = strtoupper(sanear_string(substr(trim($dte->cliente->giro),0,42)));
    $DirRecep = strtoupper(sanear_string(substr(trim($dte->cliente->direccion),0,70)));
    $CmnaRecep = strtoupper(sanear_string(substr(trim($dte->cliente->comuna->nombre),0,20)));
    $CiudadRecep = strtoupper(sanear_string(substr(trim($dte->cliente->provincia->nombre),0,20)));
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
        $contacto = strtoupper(sanear_string(substr(trim($dte->dteguiadesp->notaventa->contacto . " Telf:" . $dte->dteguiadesp->notaventa->contactotelf),0,80)));
        $CorreoRecep = strtoupper(substr(trim($dte->cliente->contactoemail),0,80));
        $RznSocRecep = strtoupper(sanear_string(substr(trim($dte->cliente->razonsocial),0,100)));
        $GiroRecep = strtoupper(sanear_string(substr(trim($dte->cliente->giro),0,42)));
        $DirRecep = strtoupper(sanear_string(substr(trim($dte->cliente->direccion),0,70)));
        $CmnaRecep = strtoupper(sanear_string(substr(trim($dte->cliente->comuna->nombre),0,20)));
        $CiudadRecep = strtoupper(sanear_string(substr(trim($dte->cliente->provincia->nombre),0,20)));
    
        $FolioRef = $dte->dteguiadesp->notaventa->oc_id;
    
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
        
            $TpoDocRef = (empty($dte->dteguiadesp->despachoord_id) ? "" : "OD:" . $dte->dteguiadesp->despachoord_id . " ") . (empty($dte->dteguiadesp->ot) ? "" : "OT:" . $dte->dteguiadesp->ot . " ")  . (empty($dte->obs) ? "" : $dte->obs . " ") . (empty($dte->lugarentrega) ? "" : $dte->dteguiadesp->lugarentrega . " ")  . (empty($dte->dteguiadesp->comunaentrega_id) ? "" : $dte->dteguiadesp->comunaentrega->nombre . " ");
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
        
            }
            $contenido .= "</Documento>" .
            "</DTE>";
    
            //"</DTE>]]>";
    
            //dd($contenido);
        
        }
    
        return $contenido;
    }
    if($dte->foliocontrol->tipodocto == 33){
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
        $contacto = strtoupper(sanear_string(substr(trim($dte->cliente->contactonombre . " Telf:" . $dte->cliente->contactotelef),0,80)));
        $CorreoRecep = strtoupper(substr(trim($dte->cliente->contactoemail),0,80));
        $RznSocRecep = strtoupper(sanear_string(substr(trim($dte->cliente->razonsocial),0,100)));
        $GiroRecep = strtoupper(sanear_string(substr(trim($dte->cliente->giro),0,42)));
        $DirRecep = strtoupper(sanear_string(substr(trim($dte->cliente->direccion),0,70)));
        $CmnaRecep = strtoupper(sanear_string(substr(trim($dte->cliente->comuna->nombre),0,20)));
        $CiudadRecep = strtoupper(sanear_string(substr(trim($dte->cliente->provincia->nombre),0,20)));
        $formapago_desc = $dte->dtefac->formapago->descripcion;
        $FchVenc = $dte->dtefac->fchvenc;
        if($dte->dtefac->formapago_id == 2 or $dte->dtefac->formapago_id == 3){
            $formapago_desc .= " " . $dte->cliente->plazopago->descripcion;
        }
        $FolioRef = substr(trim($dte->oc_id),0,20);
        $contenido = "";
    
        if($tipoArch == "XML"){
            $FchEmis = $dte->fchemis;
            $contenido = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\" standalone=\"yes\"?>" .
            "<DTE version=\"1.0\">" .
            "<Documento ID=\"R" .$empresa->rut . "T52F" . $Folio . "\">" .
            "<Encabezado>" .
            "<IdDoc>" .
            "<TipoDTE>33</TipoDTE>" .
            "<Folio>$Folio</Folio>" .
            "<FchEmis>$FchEmis</FchEmis>" .
            "<TipoDespacho>$dte->tipodespacho</TipoDespacho>" .
            "<TpoImpresion>N</TpoImpresion>" .
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
                $oc_id = $dtedte->dteguiadesp->notaventa->oc_id;
                $array_ocs [$oc_id] = [
                    "oc_id" => $oc_id,
                    "fecha" => date("Y-m-d", strtotime($dtedte->dteguiadesp->notaventa->fechahora))
                    
                ];
                $array_GDs[] = [
                    "nrodocto" => $dtedte->dter->nrodocto,
                    "fchemis" => date("Y-m-d", strtotime($dtedte->dter->fchemis))
                ];
            }
            //$array_ocs = explode(",", $aux_dte[0]->oc_id);
            $i = 2;
            $aux_RazonRef = strtoupper(sanear_string(($dte->dtefac->hep ? ("Hep: " . $dte->dtefac->hep) : "") . ($dte->obs ? (" " . $dte->obs) : "")));
            $aux_RazonRefImp = false;
            foreach ($array_ocs as $array_oc) {
                if(!is_null($oc_id) and !empty($oc_id)){
                    $i++;
                    $contenido .= "<Referencia>" .
                    "<NroLinRef>$i</NroLinRef>" .
                    "<TpoDocRef>801</TpoDocRef>" .
                    "<FolioRef>" . $array_oc["oc_id"] . "</FolioRef>" .
                    "<FchRef>" . $array_oc["fecha"] . "</FchRef>";
                    if($aux_RazonRefImp == false){
                        $aux_RazonRefImp = true;
                        $contenido .= "<RazonRef>$aux_RazonRef</RazonRef>";
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
        $contacto = strtoupper(sanear_string(substr(trim($dte->cliente->contactonombre . " Telf:" . $dte->cliente->contactotelef),0,80)));
        $CorreoRecep = strtoupper(substr(trim($dte->cliente->contactoemail),0,80));
        $RznSocRecep = strtoupper(sanear_string(substr(trim($dte->cliente->razonsocial),0,100)));
        $GiroRecep = strtoupper(sanear_string(substr(trim($dte->cliente->giro),0,42)));
        $DirRecep = strtoupper(sanear_string(substr(trim($dte->cliente->direccion),0,70)));
        $CmnaRecep = strtoupper(sanear_string(substr(trim($dte->cliente->comuna->nombre),0,20)));
        $CiudadRecep = strtoupper(sanear_string(substr(trim($dte->cliente->provincia->nombre),0,20)));
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
        if($tipoArch == "XML"){
            $FchEmis = $dte->fchemis;
    
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
            //dd($dte->dtedte->dter);

            if($TipoDocto == 61 or $TipoDocto == 56){
                $TipoDocto = $dte->dtedte->dter->foliocontrol->tipodocto;
                $aux_nrodocto = $dte->dtedte->dter->nrodocto;
                $aux_FchEmis = $dte->dtedte->dter->fchemis;
                $aux_codref = $dte->dtencnd->codref;
                $TpoDocRef = strtoupper(sanear_string(substr(empty($dte->obs) ? " " : $dte->obs,0,90)));
    
                $contenido .= "<Referencia>" .
                "<NroLinRef>1</NroLinRef>" .
                "<TpoDocRef>$TipoDocto</TpoDocRef>" .
                "<FolioRef>$aux_nrodocto</FolioRef>" .
                "<FchRef>$aux_FchEmis</FchRef>" .
                "<CodRef>$aux_codref</CodRef>" .
                "<RazonRef>$TpoDocRef</RazonRef>" .
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
    (SELECT dte1.nrodocto
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
    formapago.descripcion as formapago_desc,plazopago.dias as plazopago_dias
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
    ON  cliente.plazopago_id = plazopago.id and isnull(plazopago.deleted_at)
    WHERE dte.foliocontrol_id in ($request->condFoliocontrol) 
    AND dte.nrodocto = $request->nrodocto
    AND dte.id NOT IN (SELECT dteanul.dte_id FROM dteanul WHERE ISNULL(dteanul.deleted_at))
    AND dte.statusgen = 1
    ORDER BY dte.id desc;";
    $dte = DB::select($sql);
    return $dte;    
}