<?php

namespace App\Models;

use App\Models\Seguridad\Usuario;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class DespachoSol extends Model
{
    use SoftDeletes;
    protected $table = "despachosol";
    protected $fillable = [
        'notaventa_id',
        'sucursal_id',
        'usuario_id',
        'sucursal_id',
        'fechahora',
        'comunaentrega_id',
        'tipoentrega_id',
        'plazoentrega',
        'lugarentrega',
        'contacto',
        'contactoemail',
        'contactotelf',
        'observacion',
        'fechaestdesp',
        'tipoguiadesp',
        'usuariodel_id'
    ];

    //RELACION DE UNO A MUCHOS DespachoSolDet
    public function despachosoldets()
    {
        return $this->hasMany(DespachoSolDet::class,'despachosol_id');
    }

    //Relacion inversa a NotaVenta
    public function notaventa()
    {
        return $this->belongsTo(NotaVenta::class);
    }

    //RELACION DE UNO A MUCHOS DespachoSolOrd
    public function despachoords()
    {
        return $this->hasMany(DespachoOrd::class,'despachosol_id');
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

    public function comunaentrega()
    {
        return $this->belongsTo(Comuna::class,'comunaentrega_id');
    }
    //RELACION DE UNO A uno DespachoSolAnul
    public function despachosolanul()
    {
        return $this->hasOne(DespachoSolAnul::class,'despachosol_id');
    }

    //Relacion inversa a TipoEntrega
    public function tipoentrega()
    {
        return $this->belongsTo(TipoEntrega::class);
    }
    
    //RELACION DE MUCHOS A MUCHOS CON TABLA INVMOV
    public function invmovs()
    {
        return $this->belongsToMany(InvMov::class, 'despachosol_invmov','despachosol_id','invmov_id')->withTimestamps();
    }
    //Relacion inversa a Sucursal
    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }
    
    //RELACION de uno a uno despachosoldte
    public function despachosoldte()
    {
        return $this->hasOne(DespachoSolDTE::class,"despachosol_id");
    }

    //RELACION de uno a uno despachosoldev
    public function despachosoldev()
    {
        return $this->hasOne(DespachoSolDev::class,"despachosol_id");
    }

    //RELACION de uno a uno DespachoSolEnvOrdDesp
    public function despachosolenvorddesp()
    {
        return $this->hasOne(DespachoSolEnvOrdDesp::class,"despachosol_id");
    }

    public static function consultaindex($request = null){
        $user = Usuario::findOrFail(auth()->id());
        $sucurArray = $user->sucursales->pluck('id')->toArray();
        $sucurcadena = implode(",", $sucurArray);

        if(!isset($request->despachosol_id) or empty($request->despachosol_id)){
            $aux_conddespachosol_id = " true";
        }else{
            $aux_conddespachosol_id = "despachosol.id='$request->despachosol_id'";
        }
        $aux_usuario_id = auth()->id();
        $sql = "SELECT despachosol.id,despachosol.fechahora,notaventa.cliente_id,cliente.razonsocial,notaventa.oc_id,
        notaventa.oc_file,despachosol.notaventa_id,
        '' as notaventaxk,comuna.nombre as comuna_nombre,
        tipoentrega.nombre as tipoentrega_nombre,tipoentrega.icono,
        SUM(despachosoldet.cantsoldesp * (notaventadetalle.totalkilos / notaventadetalle.cant)) as aux_totalkg,
        (SELECT obs
            FROM despachosoldev
            WHERE despachosol_id = despachosol.id
            ORDER by id DESC LIMIT 1) AS obsdev,
        $aux_usuario_id as creausuario_id,despachosol.usuario_id,despachosol.updated_at,
        (SELECT CONCAT(dte.nrodocto,';',oc_id,';',oc_folder,'/',oc_file,';',dte.id) as nrodocto
            FROM dteoc INNER JOIN dte
            ON dteoc.dte_id = dte.id AND ISNULL(dteoc.deleted_at) AND ISNULL(dte.deleted_at)
            INNER JOIN dteguiadesp
            ON dteoc.dte_id = dteguiadesp.dte_id AND ISNULL(dteguiadesp.deleted_at)
            WHERE dteoc.oc_id = notaventa.oc_id
            AND dteoc.oc_folder = 'notaventa'
            AND isnull(dteguiadesp.notaventa_id)
            AND dte.cliente_id= notaventa.cliente_id
            AND dteguiadesp.dte_id NOT IN (SELECT dteanul.dte_id 
                                            FROM dteanul 
                                            WHERE dteanul.dte_id = dteguiadesp.dte_id 
                                            and ISNULL(dteanul.deleted_at))
            GROUP BY dteoc.oc_id) as dte_nrodocto,
        clientebloqueado.descripcion as clientebloqueado_descripcion,
        if(cliente.plazopago_id = 1,'Condición pago: Contado',clientebloqueado.descripcion) as clientebloqueado_desc,
        cliente.limitecredito,
        IFNULL(vista_datacobranza.tfac,0) AS datacobranza_tfac,
        IFNULL(vista_datacobranza.tdeuda,0) AS datacobranza_tdeuda,
        IFNULL(vista_datacobranza.tdeudafec,0) AS datacobranza_tdeudafec,
        IFNULL(vista_datacobranza.nrofacdeu,'') AS datacobranza_nrofacdeu,
        modulo.stamodapl as modulo_stamodapl,clientedesbloqueadomodulo.modulo_id,
        clientedesbloqueadomodulo_orddesp.modulo_id as modulo_id_orddesp,
        IFNULL(clientedesbloqueadopro.obs,'') AS clientedesbloqueadopro_obs
        FROM despachosol INNER JOIN notaventa
        ON despachosol.notaventa_id = notaventa.id AND ISNULL(despachosol.deleted_at) and isnull(notaventa.deleted_at)
        INNER JOIN cliente
        ON cliente.id = notaventa.cliente_id AND isnull(cliente.deleted_at)
        INNER JOIN comuna
        ON comuna.id = despachosol.comunaentrega_id AND isnull(comuna.deleted_at)
        INNER JOIN despachosoldet
        ON despachosoldet.despachosol_id = despachosol.id AND ISNULL(despachosoldet.deleted_at)
        INNER JOIN notaventadetalle
        ON notaventadetalle.id = despachosoldet.notaventadetalle_id AND ISNULL(notaventadetalle.deleted_at)
        INNER JOIN tipoentrega
        ON tipoentrega.id = despachosol.tipoentrega_id AND ISNULL(tipoentrega.deleted_at)
        LEFT JOIN clientebloqueado
        ON clientebloqueado.cliente_id = notaventa.cliente_id AND ISNULL(clientebloqueado.deleted_at)
        LEFT JOIN vista_datacobranza
        ON vista_datacobranza.cliente_id = notaventa.cliente_id
        LEFT JOIN clientedesbloqueado
        ON clientedesbloqueado.cliente_id = notaventa.cliente_id and clientedesbloqueado.notaventa_id = notaventa.id and not isnull(clientedesbloqueado.notaventa_id) and isnull(clientedesbloqueado.deleted_at)
        LEFT JOIN clientedesbloqueadomodulo
        ON clientedesbloqueadomodulo.clientedesbloqueado_id = clientedesbloqueado.id and clientedesbloqueadomodulo.modulo_id = 5
        LEFT JOIN modulo
        ON modulo.id = clientedesbloqueadomodulo.modulo_id
        LEFT JOIN clientedesbloqueadopro
        ON clientedesbloqueadopro.cliente_id = notaventa.cliente_id  and isnull(clientedesbloqueadopro.deleted_at)
    
        LEFT JOIN clientedesbloqueado as clientedesbloqueado_orddesp
        ON clientedesbloqueado_orddesp.cliente_id = notaventa.cliente_id and clientedesbloqueado_orddesp.notaventa_id = notaventa.id and not isnull(clientedesbloqueado_orddesp.notaventa_id) and isnull(clientedesbloqueado_orddesp.deleted_at)
        LEFT JOIN clientedesbloqueadomodulo as clientedesbloqueadomodulo_orddesp
        ON clientedesbloqueadomodulo_orddesp.clientedesbloqueado_id = clientedesbloqueado_orddesp.id and clientedesbloqueadomodulo_orddesp.modulo_id = 7
    
        WHERE ISNULL(despachosol.aprorddesp)
        AND despachosol.id NOT IN (SELECT despachosolanul.despachosol_id FROM despachosolanul WHERE ISNULL(despachosolanul.deleted_at))
        AND despachosol.notaventa_id NOT IN (SELECT notaventacerrada.notaventa_id FROM notaventacerrada WHERE ISNULL(notaventacerrada.deleted_at))
        AND notaventa.sucursal_id in ($sucurcadena)
        AND $aux_conddespachosol_id
        GROUP BY despachosoldet.despachosol_id;";
    
        return DB::select($sql);
    
    }

    public static function consultasoldespIndexPicking($request){
        //dd($request);
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
            //dd("Entro aqui");
            if(is_array($request->vendedor_id)){
                $aux_vendedorid = implode ( ',' , $request->vendedor_id);
            }else{
                $aux_vendedorid = $request->vendedor_id;
            }
            $vendedorcond = " notaventa.vendedor_id in ($aux_vendedorid) ";
    
            //$vendedorcond = "notaventa.vendedor_id='$request->vendedor_id'";
        }
        //dd($vendedorcond);
        $sucurArray = implode ( ',' , $user->sucursales->pluck('id')->toArray());
        if(!isset($request->sucursal_id) or empty($request->sucursal_id)){
            $aux_condsucursal_id = " notaventa.sucursal_id in ($sucurArray) ";
        }else{
            if(is_array($request->sucursal_id)){
                $aux_sucursal = implode ( ',' , $request->sucursal_id);
            }else{
                $aux_sucursal = $request->sucursal_id;
            }
            $aux_condsucursal_id = " (notaventa.sucursal_id in ($aux_sucursal) and notaventa.sucursal_id in ($sucurArray))";
        }
        if(empty($request->fechad) or empty($request->fechah)){
            $aux_condFecha = " true";
        }else{
            $fecha = date_create_from_format('d/m/Y', $request->fechad);
            $fechad = date_format($fecha, 'Y-m-d')." 00:00:00";
            $fecha = date_create_from_format('d/m/Y', $request->fechah);
            $fechah = date_format($fecha, 'Y-m-d')." 23:59:59";
            $aux_condFecha = "despachosol.fechahora>='$fechad' and despachosol.fechahora<='$fechah'";
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
            $aux_condtipoentrega_id = "despachosol.tipoentrega_id='$request->tipoentrega_id'";
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
            $aux_condcomuna_id = " despachosol.comunaentrega_id in ($aux_comuna) ";
        }
    
    
        $aux_condaprobord = "true";
        switch ($request->filtro) {
            case 1:
                //Filtra solo las aprobadas. Esto es para la consulta para crear ordenes de Despacho
                $aux_condaprobord = "despachosol.aprorddesp = 1";
                break;
            case 2:
                //Muestra todo sin importar si fue aprobadada o no. Esto es para el reporte
                $aux_condaprobord = "true";
                break;
        }
        if(empty($request->fechaestdesp)){
            $aux_condfechaestdesp = " true";
        }else{
            $fecha = date_create_from_format('d/m/Y', $request->fechaestdesp);
            $fechad = date_format($fecha, 'Y-m-d');
            $aux_condfechaestdesp = "despachosol.fechaestdesp='$fechad'";
        }
    
        if(empty($request->id)){
            $aux_condid = " true";
        }else{
            $aux_condid = "despachosol.id='$request->id'";
        }
    
        $aux_condproducto_id = " true";
        if(!empty($request->producto_id)){
            $aux_codprod = explode(",", $request->producto_id);
            $aux_codprod = implode ( ',' , $aux_codprod);
            $aux_condproducto_id = "notaventadetalle.producto_id in ($aux_codprod)";
        }
    
        if(empty($request->sta_picking)){
            $aux_condsta_picking = " true";
        }else{
            switch ($request->sta_picking) {
                case 0:
                    $aux_condsta_picking = " true";
                    break;
                case 1:
                    $aux_condsta_picking = "despachosoldet_invbodegaproducto.cant != 0";
                    break;
                case 2:
                    $aux_condsta_picking = "despachosoldet_invbodegaproducto.cant = 0";
                    break;    
            }
    
        }
    
        //$suma = DespachoSol::findOrFail(2)->despachosoldets->where('notaventadetalle_id',1);

        if(!isset($request->despachosol_id) or empty($request->despachosol_id)){
            $aux_conddespachosol_id = " true";
        }else{
            $aux_conddespachosol_id = "despachosol.id='$request->despachosol_id'";
        }

    
        $aux_notinNullSoldesp = "despachosol.id NOT IN (SELECT despachosolanul.despachosol_id FROM despachosolanul WHERE isnull(despachosolanul.deleted_at))";
        $aux_sqlsumdesp = "SELECT cantdesp
                            FROM vista_sumorddespdet
                            WHERE despachosoldet_id=despachosoldet.id";
        $aux_condactivas = "if((if(isnull(($aux_sqlsumdesp)),0,($aux_sqlsumdesp))
                        ) >= despachosoldet.cantsoldesp,FALSE,TRUE)
                        AND $aux_notinNullSoldesp";
        //$aux_condactivas = "true";
    
        $sql = "SELECT despachosol.id,despachosol.fechahora,notaventa.cliente_id,cliente.rut,cliente.razonsocial,notaventa.oc_id,
                notaventa.oc_file,
                comuna.nombre as comunanombre,sucursal.nombre as sucursal_nombre,
                despachosol.notaventa_id,despachosol.fechaestdesp,tipoentrega.nombre as tipentnombre,tipoentrega.icono,
                IFNULL(vista_despordxdespsoltotales.totalkilos,0) as totalkilosdesp,
                IFNULL(vista_despordxdespsoltotales.subtotal,0) as subtotaldesp,
                vista_despsoltotales.totalkilos,
                vista_despsoltotales.subtotalsoldesp,despachosol.updated_at,
                clientebloqueado.descripcion as clientebloqueado_descripcion,
                despachosolenvorddesp.despachosol_id as despachosolenvorddesp_despachosol_id,
                despachosolenvorddesp.despachosol_id as despachosolenvorddesp_updated_at,
                cliente.limitecredito,
                IFNULL(vista_datacobranza.tfac,0) AS datacobranza_tfac,
                IFNULL(vista_datacobranza.tdeuda,0) AS datacobranza_tdeuda,
                IFNULL(vista_datacobranza.tdeudafec,0) AS datacobranza_tdeudafec,
                IFNULL(vista_datacobranza.nrofacdeu,'') AS datacobranza_nrofacdeu,
                clientedesbloqueado.obs as clientedesbloqueado_obs,
                modulo.stamodapl as modulo_stamodapl,clientedesbloqueadomodulo.modulo_id,
                clientedesbloqueadomodulo_orddesp.modulo_id as modulo_id_orddesp,
                if(cliente.plazopago_id = 1,'Condición pago: Contado',clientebloqueado.descripcion) as clientebloqueado_desc,
                IFNULL(clientedesbloqueadopro.obs,'') AS clientedesbloqueadopro_obs
                FROM despachosol INNER JOIN despachosoldet
                ON despachosol.id=despachosoldet.despachosol_id
                AND $aux_condactivas
                INNER JOIN notaventa
                ON notaventa.id=despachosol.notaventa_id
                INNER JOIN notaventadetalle
                ON despachosoldet.notaventadetalle_id=notaventadetalle.id
                INNER JOIN producto
                ON notaventadetalle.producto_id=producto.id
                INNER JOIN categoriaprod
                ON categoriaprod.id=producto.categoriaprod_id
                INNER JOIN areaproduccion
                ON areaproduccion.id=categoriaprod.areaproduccion_id
                INNER JOIN cliente
                ON cliente.id=notaventa.cliente_id
                INNER JOIN comuna
                ON comuna.id=despachosol.comunaentrega_id
                INNER JOIN tipoentrega
                ON tipoentrega.id=despachosol.tipoentrega_id
                INNER JOIN vista_despsoltotales
                ON despachosol.id = vista_despsoltotales.id
                LEFT JOIN vista_despordxdespsoltotales
                ON despachosol.id = vista_despordxdespsoltotales.despachosol_id
                INNER JOIN sucursal
                ON notaventa.sucursal_id = sucursal.id AND ISNULL(sucursal.deleted_at)
                INNER JOIN despachosoldet_invbodegaproducto
                ON despachosoldet.id = despachosoldet_invbodegaproducto.despachosoldet_id AND ISNULL(despachosoldet_invbodegaproducto.deleted_at)
                LEFT JOIN clientebloqueado
                ON clientebloqueado.cliente_id = notaventa.cliente_id AND ISNULL(clientebloqueado.deleted_at)
                LEFT JOIN despachosolenvorddesp
                ON despachosolenvorddesp.despachosol_id = despachosol.id AND ISNULL(despachosolenvorddesp.deleted_at)
                LEFT JOIN vista_datacobranza
                ON vista_datacobranza.cliente_id = notaventa.cliente_id
                LEFT JOIN clientedesbloqueado
                ON clientedesbloqueado.cliente_id = notaventa.cliente_id and clientedesbloqueado.notaventa_id = notaventa.id and not isnull(clientedesbloqueado.notaventa_id) and isnull(clientedesbloqueado.deleted_at)
                LEFT JOIN clientedesbloqueadomodulo
                ON clientedesbloqueadomodulo.clientedesbloqueado_id = clientedesbloqueado.id and clientedesbloqueadomodulo.modulo_id = 6
                LEFT JOIN modulo
                ON modulo.id = clientedesbloqueadomodulo.modulo_id
                LEFT JOIN clientedesbloqueadopro
                ON clientedesbloqueadopro.cliente_id = notaventa.cliente_id  and isnull(clientedesbloqueadopro.deleted_at)
    
                LEFT JOIN clientedesbloqueado as clientedesbloqueado_orddesp
                ON clientedesbloqueado_orddesp.cliente_id = notaventa.cliente_id and clientedesbloqueado_orddesp.notaventa_id = notaventa.id and not isnull(clientedesbloqueado_orddesp.notaventa_id) and isnull(clientedesbloqueado_orddesp.deleted_at)
                LEFT JOIN clientedesbloqueadomodulo as clientedesbloqueadomodulo_orddesp
                ON clientedesbloqueadomodulo_orddesp.clientedesbloqueado_id = clientedesbloqueado_orddesp.id and clientedesbloqueadomodulo_orddesp.modulo_id = 7
    
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
                and $aux_condaprobord
                and $aux_condfechaestdesp
                and $aux_condid
                and $aux_condproducto_id
                and $aux_condsucursal_id
                and $aux_condsta_picking
                AND $aux_conddespachosol_id
                and notaventa.id not in (select notaventa_id from notaventacerrada where isnull(notaventacerrada.deleted_at))
                and isnull(despachosol.deleted_at) AND isnull(notaventa.deleted_at) AND isnull(notaventadetalle.deleted_at)
                and isnull(despachosoldet.deleted_at)
                and despachosol.id not in (SELECT despachosol_id FROM despachosolenvorddesp where despachosolenvorddesp.despachosol_id = despachosol.id AND despachosolenvorddesp.staenvdesp = 1 AND ISNULL(despachosolenvorddesp.deleted_at))
                AND despachosol.id NOT IN (SELECT despachoord.despachosol_id 
                                                from despachoord 
                                                WHERE despachoord.id NOT IN 
                                                    (SELECT despachoordanul.despachoord_id from despachoordanul 
                                                        WHERE despachoordanul.despachoord_id = despachoord.id 
                                                        AND ISNULL(despachoordanul.deleted_at))
                                                AND isnull(despachoord.aprguiadesp)
                                                AND ISNULL(despachoord.deleted_at))
                GROUP BY despachosol.id
                ORDER BY despachosol.id ASC;";
    /*
    (select sum(cantsoldesp) as cantsoldesp
                        from despachosol inner join despachosoldet
                        on despachosol.id=despachosoldet.despachosol_id
                        where despachosol.id not in (select despachosol_id from despachosolanul)
                        and despachosoldet.notaventadetalle_id=notaventadetalle.id
                        despachosol.deleted_at is null
                        group by notaventadetalle_id)
    */
        //dd($sql);
        /* if($request->despachosol_id == 27481){
            dd($sql);
        } */

        $datas = DB::select($sql);
        filtrarclientesbloqueados($request,$datas);
        //dd($datas);
        return $datas;
    }
    
    public static function consultasoldesp($request){
        //dd($request);
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
        $sucurArray = implode ( ',' , $user->sucursales->pluck('id')->toArray());
        if(!isset($request->sucursal_id) or empty($request->sucursal_id)){
            //$aux_condsucursal_id = " true ";
            $aux_condsucursal_id = " despachosol.sucursal_id in ($sucurArray)";
        }else{
            if(is_array($request->sucursal_id)){
                $aux_sucursal = implode ( ',' , $request->sucursal_id);
            }else{
                $aux_sucursal = $request->sucursal_id;
            }
            $aux_condsucursal_id = " (despachosol.sucursal_id in ($aux_sucursal) and despachosol.sucursal_id in ($sucurArray))";
        }
        $sucurArray = $user->sucursales->pluck('id')->toArray();
        $sucurcadena = implode(",", $sucurArray);
    
    
        if(empty($request->fechad) or empty($request->fechah)){
            $aux_condFecha = " true";
        }else{
            $fecha = date_create_from_format('d/m/Y', $request->fechad);
            $fechad = date_format($fecha, 'Y-m-d')." 00:00:00";
            $fecha = date_create_from_format('d/m/Y', $request->fechah);
            $fechah = date_format($fecha, 'Y-m-d')." 23:59:59";
            $aux_condFecha = "despachosol.fechahora>='$fechad' and despachosol.fechahora<='$fechah'";
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
            $aux_condtipoentrega_id = "despachosol.tipoentrega_id='$request->tipoentrega_id'";
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
        if(!isset($request->sucursal_id) or empty($request->sucursal_id)){
            $aux_condsucursal_id = " true";
        }else{
            $aux_condsucursal_id = "notaventa.sucursal_id='$request->sucursal_id'";
        }
    */
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
            $aux_condcomuna_id = " despachosol.comunaentrega_id in ($aux_comuna) ";
        }
    
    
        $aux_condaprobord = "true";
        switch ($request->filtro) {
            case 1:
                //Filtra solo las aprobadas. Esto es para la consulta para crear ordenes de Despacho
                $aux_condaprobord = "despachosol.aprorddesp = 1";
                break;
            case 2:
                //Muestra todo sin importar si fue aprobadada o no. Esto es para el reporte
                $aux_condaprobord = "true";
                break;
        }
        if(empty($request->fechaestdesp)){
            $aux_condfechaestdesp = " true";
        }else{
            $fecha = date_create_from_format('d/m/Y', $request->fechaestdesp);
            $fechad = date_format($fecha, 'Y-m-d');
            $aux_condfechaestdesp = "despachosol.fechaestdesp='$fechad'";
        }
    
        if(empty($request->id)){
            $aux_condid = " true";
        }else{
            $aux_condid = "despachosol.id='$request->id'";
        }
    
        $aux_condproducto_id = " true";
        if(!empty($request->producto_id)){
            $aux_codprod = explode(",", $request->producto_id);
            $aux_codprod = implode ( ',' , $aux_codprod);
            $aux_condproducto_id = "notaventadetalle.producto_id in ($aux_codprod)";
        }
    
    
        //$suma = DespachoSol::findOrFail(2)->despachosoldets->where('notaventadetalle_id',1);
    
        $aux_notinNullSoldesp = "despachosol.id NOT IN (SELECT despachosolanul.despachosol_id FROM despachosolanul WHERE isnull(despachosolanul.deleted_at))";
        $aux_sqlsumdesp = "SELECT cantdesp
                            FROM vista_sumorddespdet
                            WHERE despachosoldet_id=despachosoldet.id";
        $aux_condactivas = "if((if(isnull(($aux_sqlsumdesp)),0,($aux_sqlsumdesp))
                        ) >= despachosoldet.cantsoldesp,FALSE,TRUE)
                        AND $aux_notinNullSoldesp";
        //$aux_condactivas = "true";
    
        $aux_orden = "ORDER BY despachosol.id DESC";
        //dd($request->orden);
        if(isset($request->orden) and !empty($request->orden) and $request->orden === null){
            $aux_orden = "ORDER BY $request->orden";
        }
        $aux_codsolenvord = " true ";
        if(isset($request->solenvord) and !empty($request->solenvord) and $request->solenvord == "1"){
            $aux_codsolenvord = "despachosol.id in (SELECT despachosol_id FROM despachosolenvorddesp WHERE despachosolenvorddesp.despachosol_id = despachosol.id AND despachosolenvorddesp.staenvdesp = 1 AND ISNULL(despachosolenvorddesp.deleted_at))";
        }
        //dd($aux_orden);
        $sql = "SELECT despachosol.id,despachosol.fechahora,notaventa.cliente_id,cliente.rut,cliente.razonsocial,notaventa.oc_id,
                notaventa.oc_file,notaventa.sucursal_id,
                comuna.nombre as comunanombre,sucursal.nombre as sucursal_nombre,
                despachosol.notaventa_id,despachosol.fechaestdesp,tipoentrega.nombre as tipentnombre,tipoentrega.icono,
                IFNULL(vista_despordxdespsoltotales.totalkilos,0) as totalkilosdesp,
                IFNULL(vista_despordxdespsoltotales.subtotal,0) as subtotaldesp,
                vista_despsoltotales.totalkilos,
                vista_despsoltotales.subtotalsoldesp,despachosol.updated_at,
                (SELECT CONCAT(dte.nrodocto,';',oc_id,';',oc_folder,'/',oc_file,';',dte.id) as nrodocto
                    FROM dteoc INNER JOIN dte
                    ON dteoc.dte_id = dte.id AND ISNULL(dteoc.deleted_at) AND ISNULL(dte.deleted_at)
                    INNER JOIN dteguiadesp
                    ON dteoc.dte_id = dteguiadesp.dte_id AND ISNULL(dteguiadesp.deleted_at)
                    WHERE dteoc.oc_id = notaventa.oc_id
                    AND dteoc.oc_folder = 'notaventa'
                    AND isnull(dteguiadesp.notaventa_id)
                    AND dte.cliente_id= notaventa.cliente_id
                    AND dteguiadesp.dte_id NOT IN (SELECT dteanul.dte_id 
                                                    FROM dteanul 
                                                    WHERE dteanul.dte_id = dteguiadesp.dte_id 
                                                    and ISNULL(dteanul.deleted_at))
                    GROUP BY dteoc.oc_id) as dte_nrodocto,
                despachosol.aprorddesp,
                clientebloqueado.descripcion as clientebloqueado_descripcion,
                despachosolenvorddesp.despachosol_id as despachosolenvorddesp_despachosol_id,
                despachosolenvorddesp.despachosol_id as despachosolenvorddesp_updated_at,
                cliente.limitecredito,
                if(cliente.plazopago_id = 1,'Condición pago: Contado',clientebloqueado.descripcion) as clientebloqueado_desc,
                IFNULL(vista_datacobranza.tfac,0) AS datacobranza_tfac,
                IFNULL(vista_datacobranza.tdeuda,0) AS datacobranza_tdeuda,
                IFNULL(vista_datacobranza.tdeudafec,0) AS datacobranza_tdeudafec,
                IFNULL(vista_datacobranza.nrofacdeu,'') AS datacobranza_nrofacdeu,
                modulo.stamodapl as modulo_stamodapl,clientedesbloqueadomodulo.modulo_id,
                clientedesbloqueadomodulo_orddesp.modulo_id as modulo_id_orddesp,
                IFNULL(clientedesbloqueadopro.obs,'') AS clientedesbloqueadopro_obs
                FROM despachosol INNER JOIN despachosoldet
                ON despachosol.id=despachosoldet.despachosol_id
                AND $aux_condactivas
                INNER JOIN notaventa
                ON notaventa.id=despachosol.notaventa_id
                INNER JOIN notaventadetalle
                ON despachosoldet.notaventadetalle_id=notaventadetalle.id
                INNER JOIN producto
                ON notaventadetalle.producto_id=producto.id
                INNER JOIN categoriaprod
                ON categoriaprod.id=producto.categoriaprod_id
                INNER JOIN areaproduccion
                ON areaproduccion.id=categoriaprod.areaproduccion_id
                INNER JOIN cliente
                ON cliente.id=notaventa.cliente_id
                INNER JOIN comuna
                ON comuna.id=despachosol.comunaentrega_id
                INNER JOIN tipoentrega
                ON tipoentrega.id=despachosol.tipoentrega_id
                INNER JOIN vista_despsoltotales
                ON despachosol.id = vista_despsoltotales.id
                LEFT JOIN vista_despordxdespsoltotales
                ON despachosol.id = vista_despordxdespsoltotales.despachosol_id
                INNER JOIN sucursal
                ON despachosol.sucursal_id = sucursal.id AND ISNULL(sucursal.deleted_at)
                LEFT JOIN clientebloqueado
                ON clientebloqueado.cliente_id = notaventa.cliente_id AND ISNULL(clientebloqueado.deleted_at)
                LEFT JOIN despachosolenvorddesp
                ON despachosolenvorddesp.despachosol_id = despachosol.id AND ISNULL(despachosolenvorddesp.deleted_at)
                LEFT JOIN vista_datacobranza
                ON vista_datacobranza.cliente_id = notaventa.cliente_id
                LEFT JOIN clientedesbloqueado
                ON clientedesbloqueado.cliente_id = notaventa.cliente_id and clientedesbloqueado.notaventa_id = notaventa.id and not isnull(clientedesbloqueado.notaventa_id) and isnull(clientedesbloqueado.deleted_at)
                LEFT JOIN clientedesbloqueadomodulo
                ON clientedesbloqueadomodulo.clientedesbloqueado_id = clientedesbloqueado.id and clientedesbloqueadomodulo.modulo_id = 7
                LEFT JOIN modulo
                ON modulo.id = clientedesbloqueadomodulo.modulo_id
                LEFT JOIN clientedesbloqueadopro
                ON clientedesbloqueadopro.cliente_id = notaventa.cliente_id  and isnull(clientedesbloqueadopro.deleted_at)
    
                LEFT JOIN clientedesbloqueado as clientedesbloqueado_orddesp
                ON clientedesbloqueado_orddesp.cliente_id = notaventa.cliente_id and clientedesbloqueado_orddesp.notaventa_id = notaventa.id and not isnull(clientedesbloqueado_orddesp.notaventa_id) and isnull(clientedesbloqueado_orddesp.deleted_at)
                LEFT JOIN clientedesbloqueadomodulo as clientedesbloqueadomodulo_orddesp
                ON clientedesbloqueadomodulo_orddesp.clientedesbloqueado_id = clientedesbloqueado_orddesp.id and clientedesbloqueadomodulo_orddesp.modulo_id = 7
    
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
                and $aux_condaprobord
                and $aux_condfechaestdesp
                and $aux_condid
                and $aux_condproducto_id
                and $aux_condsucursal_id
                and notaventa.id not in (select notaventa_id from notaventacerrada where isnull(notaventacerrada.deleted_at))
                and isnull(despachosol.deleted_at) AND isnull(notaventa.deleted_at) AND isnull(notaventadetalle.deleted_at)
                and isnull(despachosoldet.deleted_at)
                AND despachosol.sucursal_id in ($sucurcadena)
                AND $aux_codsolenvord
                AND despachosol.id NOT IN (SELECT despachoord.despachosol_id 
                                                from despachoord 
                                                WHERE despachoord.id NOT IN 
                                                    (SELECT despachoordanul.despachoord_id from despachoordanul 
                                                        WHERE despachoordanul.despachoord_id = despachoord.id 
                                                        AND ISNULL(despachoordanul.deleted_at))
                                                AND isnull(despachoord.aprguiadesp)
                                                AND ISNULL(despachoord.deleted_at))
                GROUP BY despachosol.id
                $aux_orden;";
    /*
    (select sum(cantsoldesp) as cantsoldesp
                        from despachosol inner join despachosoldet
                        on despachosol.id=despachosoldet.despachosol_id
                        where despachosol.id not in (select despachosol_id from despachosolanul)
                        and despachosoldet.notaventadetalle_id=notaventadetalle.id
                        despachosol.deleted_at is null
                        group by notaventadetalle_id)
    */
        //dd("$sql");
        $datas = DB::select($sql);
        filtrarclientesbloqueados($request,$datas);
        //dd($datas);
        return $datas;
    }

}
