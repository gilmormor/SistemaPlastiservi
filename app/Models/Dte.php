<?php

namespace App\Models;

use App\Models\Seguridad\Usuario;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

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

    //RELACION DE UNO A MUCHOS GuiaDespDet
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
        dte.updated_at,'' as rutacrear,
        dteanul.obs as anul_obs,dteanul.created_at as anul_created_at
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
        LEFT JOIN dteanul
        ON dteanul.dte_id = dte.id AND ISNULL(dteanul.deleted_at)
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
        dteanul.obs as anul_obs,dteanul.created_at as anul_created_at,
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
        LEFT JOIN dteanul
        ON dteanul.dte_id = dte.id AND ISNULL(dteanul.deleted_at)
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
        AND $aux_conddtenotnull
        AND $aux_conddtedet
        AND isnull(despachoord.numfactura)
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
}
