<?php

namespace App\Models;

use App\Events\GuardarFacturaDespacho;
use App\Models\Seguridad\Usuario;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class DespachoOrd extends Model
{
    use SoftDeletes;
    protected $table = "despachoord";
    protected $fillable = [
        'despachosol_id',
        'notaventa_id',
        'usuario_id',
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
        'guiadespacho',
        'guiadespachofec',
        'numfactura',
        'fechafactura',
        'numfacturafec',
        'despachoobs_id',
        'bloquearhacerguia',
        'usuariodel_id'
    ];

    //RELACION DE UNO A MUCHOS DespachoOrdDet
    public function despachoorddets()
    {
        return $this->hasMany(DespachoOrdDet::class,'despachoord_id');
    }

    //Relacion inversa a DespachoSol
    public function despachosol()
    {
        return $this->belongsTo(DespachoSol::class);
    }

    //Relacion inversa a NotaVenta
    public function notaventa()
    {
        return $this->belongsTo(NotaVenta::class);
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

    //Relacion inversa a TipoEntrega
    public function tipoentrega()
    {
        return $this->belongsTo(TipoEntrega::class);
    }

    //RELACION DE UNO A MUCHOS DespachoOrdAnul
    public function despachoordanul()
    {
        return $this->hasOne(DespachoOrdAnul::class,'despachoord_id');
    }

    //RELACION DE UNO A MUCHOS DespachoOrdRec
    public function despachoordrecs()
    {
        return $this->hasOne(DespachoOrdRec::class,'despachoord_id');
    }
    //RELACION DE MUCHOS A MUCHOS CON TABLA INVMOV
    public function invmovs()
    {
        return $this->belongsToMany(InvMov::class, 'despachoord_invmov','despachoord_id','invmov_id')->withTimestamps();
    }

    //RELACION DE UNO A MUCHOS dteguiadesp
    public function dteguiadesps()
    {
        return $this->hasMany(DteGuiaDesp::class,'despachoord_id');
    }
    

    public static function consultaOrdDespxAsigGuiaDesp($request){
        if(!isset($request->notaventa_id) or empty($request->notaventa_id)){
            $aux_notaventa_idCodn = "true";
        }else{
            $aux_notaventa_idCodn = " notaventa.id in ($request->notaventa_id) ";
        }
        $sql = "SELECT despachoord.id,despachoord.despachosol_id,despachoord.fechahora,despachoord.fechaestdesp,
        cliente.razonsocial,notaventa.oc_id,notaventa.oc_file,despachoord.notaventa_id,
        '' as notaventaxk,comuna.nombre as comuna_nombre,
        tipoentrega.nombre as tipoentrega_nombre,tipoentrega.icono,clientebloqueado.descripcion as clientebloqueado_descripcion,
        SUM(despachoorddet.cantdesp * (notaventadetalle.totalkilos / notaventadetalle.cant)) as aux_totalkg,
        sum(round((despachoorddet.cantdesp * notaventadetalle.preciounit) * ((notaventa.piva+100)/100))) as subtotal,
        despachoord.updated_at,despachoord.aprguiadesp
        FROM despachoord INNER JOIN notaventa
        ON despachoord.notaventa_id = notaventa.id AND ISNULL(despachoord.deleted_at) and isnull(notaventa.deleted_at)
        INNER JOIN cliente
        ON cliente.id = notaventa.cliente_id AND isnull(cliente.deleted_at)
        INNER JOIN comuna
        ON comuna.id = despachoord.comunaentrega_id AND isnull(comuna.deleted_at)
        INNER JOIN despachoorddet
        ON despachoorddet.despachoord_id = despachoord.id AND ISNULL(despachoorddet.deleted_at)
        INNER JOIN notaventadetalle
        ON notaventadetalle.id = despachoorddet.notaventadetalle_id AND ISNULL(notaventadetalle.deleted_at)
        INNER JOIN tipoentrega
        ON tipoentrega.id = despachoord.tipoentrega_id AND ISNULL(tipoentrega.deleted_at)
        LEFT JOIN clientebloqueado
        ON clientebloqueado.cliente_id = notaventa.cliente_id AND ISNULL(clientebloqueado.deleted_at)
        WHERE despachoord.aprguiadesp='1' and isnull(despachoord.guiadespacho)
        AND despachoord.id NOT IN (SELECT despachoordanul.despachoord_id FROM despachoordanul WHERE ISNULL(despachoordanul.deleted_at))
        AND despachoord.notaventa_id NOT IN (SELECT notaventacerrada.notaventa_id FROM notaventacerrada WHERE ISNULL(notaventacerrada.deleted_at))
        AND $aux_notaventa_idCodn
        GROUP BY despachoorddet.despachoord_id;";
        return DB::select($sql);
    }
    //Relacion uno a Muchos con guiadesp
    public function guiadesp()
    {
        return $this->hasMany(GuiaDesp::class,"despachoord_id");
    }
    
    //RELACION DE UNO A MUCHOS despachoordanulguiafact
    public function despachoordanulguiafacts()
    {
        return $this->hasMany(DespachoOrdAnulGuiaFact::class,'despachoord_id');
    }

    public static function guardarfactdesp($dtedte)
    {
        $dte = $dtedte->dte;
        $despachoord = DespachoOrd::findOrFail($dtedte->dteguiadesp->despachoord_id);
        $notaventacerrada = NotaVentaCerrada::where('notaventa_id',$despachoord->notaventa_id)->get();
        if(count($notaventacerrada) == 0){
            $despachoord->numfactura = $dte->nrodocto;
            $despachoord->fechafactura = $dte->fchemis;
            $despachoord->numfacturafec = $dte->fchemisgen;
            if ($despachoord->save()) {
                Event(new GuardarFacturaDespacho($despachoord));
                return response()->json([
                                        'mensaje' => 'ok',
                                        'despachoord' => $despachoord
                                        ]);
            } else {
                return response()->json(['mensaje' => 'ng']);
            }    
        }else{
            $mensaje = 'Nota Venta fue cerrada: Observ: ' . $notaventacerrada[0]->observacion . ' Fecha: ' . date("d/m/Y h:i:s A", strtotime($notaventacerrada[0]->created_at));
            return response()->json(['mensaje' => $mensaje]);
        }
    }


    public static function consultaOrdDespxAsigFact($request){
        if(!isset($request->notaventa_id) or empty($request->notaventa_id)){
            $aux_notaventa_idCodn = "true";
        }else{
            $aux_notaventa_idCodn = " notaventa.id in ($request->notaventa_id) ";
        }    
        $sql = "SELECT despachoord.id,despachoord.despachosol_id,despachoord.fechahora,despachoord.fechaestdesp,
        cliente.razonsocial,notaventa.oc_id,notaventa.oc_file,despachoord.notaventa_id,
        '' as notaventaxk,comuna.nombre as comuna_nombre,despachoord.guiadespacho,
        tipoentrega.nombre as tipoentrega_nombre,tipoentrega.icono,clientebloqueado.descripcion as clientebloqueado_descripcion,
        SUM(despachoorddet.cantdesp * (notaventadetalle.totalkilos / notaventadetalle.cant)) as aux_totalkg,
        sum(round((despachoorddet.cantdesp * notaventadetalle.preciounit) * ((notaventa.piva+100)/100))) as subtotal,
        despachoord.updated_at,despachoord.aprguiadesp,dte.aprobstatus
        FROM despachoord INNER JOIN notaventa
        ON despachoord.notaventa_id = notaventa.id AND ISNULL(despachoord.deleted_at) and isnull(notaventa.deleted_at)
        INNER JOIN cliente
        ON cliente.id = notaventa.cliente_id AND isnull(cliente.deleted_at)
        INNER JOIN comuna
        ON comuna.id = despachoord.comunaentrega_id AND isnull(comuna.deleted_at)
        INNER JOIN despachoorddet
        ON despachoorddet.despachoord_id = despachoord.id AND ISNULL(despachoorddet.deleted_at)
        INNER JOIN notaventadetalle
        ON notaventadetalle.id = despachoorddet.notaventadetalle_id AND ISNULL(notaventadetalle.deleted_at)
        INNER JOIN tipoentrega
        ON tipoentrega.id = despachoord.tipoentrega_id AND ISNULL(tipoentrega.deleted_at)
        LEFT JOIN clientebloqueado
        ON clientebloqueado.cliente_id = notaventa.cliente_id AND ISNULL(clientebloqueado.deleted_at)
        LEFT JOIN dte
        ON despachoord.guiadespacho = dte.nrodocto
        WHERE despachoord.aprguiadesp='1'
        AND NOT isnull(despachoord.guiadespacho)
        AND ((SELECT COUNT(dteguiadesp.id) /*ESTA VALIDACION ES PARA CUANDO LA GUIA ES ANULADA DESDE (dtefactura/listarguiadesp JAVASCRIPT anularguia()) PERO SIN DEVOLVER A ORDEN DE DESPACHO, LA ORDEN DE DESPACHO SIGUE ASIGNADA A LA GUIA ANULADA */
            FROM dteguiadesp INNER JOIN dteanul
            ON dteguiadesp.dte_id = dteanul.dte_id
            WHERE dteguiadesp.despachoord_id = despachoord.id) = 0)
        AND isnull(despachoord.numfactura)
        AND despachoord.id NOT IN (SELECT despachoordanul.despachoord_id FROM despachoordanul WHERE ISNULL(despachoordanul.deleted_at))
        AND despachoord.notaventa_id NOT IN (SELECT notaventacerrada.notaventa_id FROM notaventacerrada WHERE ISNULL(notaventacerrada.deleted_at))
        AND $aux_notaventa_idCodn
        AND dte.indtraslado in (1,2,9)
        GROUP BY despachoorddet.despachoord_id;";
        return DB::select($sql);
        //and indtraslado in (1,2,9) // indtraslado = 1: Operación constituye venta1 2: Ventas por efectuar 3: Consignaciones 4: Entrega gratuita 5: Traslados internos 6: Otros traslados no venta 7: Guía de devolución 8: Traslado para exportación. (no venta) 9: Venta para exportación
    }

    public static function consultaindex($request = null){

        $user = Usuario::findOrFail(auth()->id());
        $sucurArray = $user->sucursales->pluck('id')->toArray();
        $sucurcadena = implode(",", $sucurArray);
        $arraySucFisxUsu = implode(",", sucFisXUsu($user->persona));

        if(!isset($request->despachoord_id) or empty($request->despachoord_id)){
            $aux_conddespachoord_id = " true";
        }else{
            $aux_conddespachoord_id = "despachoord.id='$request->despachoord_id'";
        }
        //SQL ORIGONAL ANTES DE OPTIMIZAR CON JOIN EN VEZ DE NOT IN
        /* $sql = "SELECT despachoord.id,despachoord.despachosol_id,despachoord.fechahora,despachoord.fechaestdesp,
        notaventa.cliente_id,cliente.razonsocial,notaventa.oc_id,notaventa.oc_file,despachoord.notaventa_id,
        '' as notaventaxk,comuna.nombre as comuna_nombre, sucursal.nombre as sucursal_nombre,
        tipoentrega.nombre as tipoentrega_nombre,tipoentrega.icono,clientebloqueado.descripcion as clientebloqueado_descripcion,
        SUM(despachoorddet.cantdesp * (notaventadetalle.totalkilos / notaventadetalle.cant)) as aux_totalkg,
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
        despachoord.updated_at,
        if(cliente.plazopago_id = 1,'Condición pago: Contado',clientebloqueado.descripcion) as clientebloqueado_desc,
        cliente.limitecredito,
        IFNULL(vista_datacobranza.tfac,0) AS datacobranza_tfac,
        IFNULL(vista_datacobranza.tdeuda,0) AS datacobranza_tdeuda,
        IFNULL(vista_datacobranza.tdeudafec,0) AS datacobranza_tdeudafec,
        IFNULL(vista_datacobranza.nrofacdeu,'') AS datacobranza_nrofacdeu,
        modulo.stamodapl as modulo_stamodapl,clientedesbloqueadomodulo.modulo_id,
        IFNULL(clientedesbloqueadopro.obs,'') AS clientedesbloqueadopro_obs
        FROM despachoord INNER JOIN notaventa
        ON despachoord.notaventa_id = notaventa.id AND ISNULL(despachoord.deleted_at) and isnull(notaventa.deleted_at)
        INNER JOIN cliente
        ON cliente.id = notaventa.cliente_id AND isnull(cliente.deleted_at)
        INNER JOIN comuna
        ON comuna.id = despachoord.comunaentrega_id AND isnull(comuna.deleted_at)
        INNER JOIN despachoorddet
        ON despachoorddet.despachoord_id = despachoord.id AND ISNULL(despachoorddet.deleted_at)
        INNER JOIN notaventadetalle
        ON notaventadetalle.id = despachoorddet.notaventadetalle_id AND ISNULL(notaventadetalle.deleted_at)
        INNER JOIN tipoentrega
        ON tipoentrega.id = despachoord.tipoentrega_id AND ISNULL(tipoentrega.deleted_at)
        LEFT JOIN clientebloqueado
        ON clientebloqueado.cliente_id = notaventa.cliente_id AND ISNULL(clientebloqueado.deleted_at)
        INNER JOIN despachosol
        ON despachoord.despachosol_id = despachosol.id AND ISNULL(despachosol.deleted_at)
        INNER JOIN sucursal
        ON despachosol.sucursal_id = sucursal.id AND ISNULL(sucursal.deleted_at)
        INNER JOIN producto
        ON producto.id = notaventadetalle.producto_id AND ISNULL(producto.deleted_at)
        INNER JOIN categoriaprod
        ON categoriaprod.id=producto.categoriaprod_id AND ISNULL(categoriaprod.deleted_at)
        LEFT JOIN vista_datacobranza
        ON vista_datacobranza.cliente_id = notaventa.cliente_id
        LEFT JOIN clientedesbloqueado
        ON clientedesbloqueado.cliente_id = notaventa.cliente_id and clientedesbloqueado.notaventa_id = notaventa.id and not isnull(clientedesbloqueado.notaventa_id) and isnull(clientedesbloqueado.deleted_at)
        LEFT JOIN clientedesbloqueadomodulo
        ON clientedesbloqueadomodulo.clientedesbloqueado_id = clientedesbloqueado.id and clientedesbloqueadomodulo.modulo_id = 8
        LEFT JOIN modulo
        ON modulo.id = clientedesbloqueadomodulo.modulo_id
        LEFT JOIN clientedesbloqueadopro
        ON clientedesbloqueadopro.cliente_id = notaventa.cliente_id  and isnull(clientedesbloqueadopro.deleted_at)
        WHERE categoriaprod.id in (SELECT categoriaprodsuc.categoriaprod_id 
            FROM categoriaprodsuc 
            WHERE categoriaprodsuc.categoriaprod_id = categoriaprod.id
            AND categoriaprodsuc.sucursal_id IN ($arraySucFisxUsu))
        AND ISNULL(despachoord.aprguiadesp)
        AND despachoord.id NOT IN (SELECT despachoordanul.despachoord_id FROM despachoordanul WHERE ISNULL(despachoordanul.deleted_at))
        AND despachoord.notaventa_id NOT IN (SELECT notaventacerrada.notaventa_id FROM notaventacerrada WHERE ISNULL(notaventacerrada.deleted_at))
        AND despachosol.sucursal_id in ($sucurcadena)
        AND $aux_conddespachoord_id
        GROUP BY despachoorddet.despachoord_id;"; */

        $sql = "SELECT
                    despachoord.id,
                    despachoord.despachosol_id,
                    despachoord.fechahora,
                    despachoord.fechaestdesp,
                    notaventa.cliente_id,
                    cliente.razonsocial,
                    notaventa.oc_id,
                    notaventa.oc_file,
                    despachoord.notaventa_id,
                    '' AS notaventaxk,
                    comuna.nombre AS comuna_nombre,
                    sucursal.nombre AS sucursal_nombre,
                    tipoentrega.nombre AS tipoentrega_nombre,
                    tipoentrega.icono,
                    clientebloqueado.descripcion AS clientebloqueado_descripcion,

                    SUM(
                        despachoorddet.cantdesp *
                        (notaventadetalle.totalkilos / notaventadetalle.cant)
                    ) AS aux_totalkg,

                    despachoord.updated_at,

                    IF(
                        cliente.plazopago_id = 1,
                        'Condición pago: Contado',
                        clientebloqueado.descripcion
                    ) AS clientebloqueado_desc,

                    cliente.limitecredito,

                    IFNULL(vista_datacobranza.tfac, 0) AS datacobranza_tfac,
                    IFNULL(vista_datacobranza.tdeuda, 0) AS datacobranza_tdeuda,
                    IFNULL(vista_datacobranza.tdeudafec, 0) AS datacobranza_tdeudafec,
                    IFNULL(vista_datacobranza.nrofacdeu, '') AS datacobranza_nrofacdeu,

                    modulo.stamodapl AS modulo_stamodapl,
                    clientedesbloqueadomodulo.modulo_id,
                    IFNULL(clientedesbloqueadopro.obs, '') AS clientedesbloqueadopro_obs

                FROM despachoord
                INNER JOIN notaventa
                    ON despachoord.notaventa_id = notaventa.id
                    AND despachoord.deleted_at IS NULL
                    AND notaventa.deleted_at IS NULL

                INNER JOIN cliente
                    ON cliente.id = notaventa.cliente_id
                    AND cliente.deleted_at IS NULL

                INNER JOIN comuna
                    ON comuna.id = despachoord.comunaentrega_id
                    AND comuna.deleted_at IS NULL

                INNER JOIN despachoorddet
                    ON despachoorddet.despachoord_id = despachoord.id
                    AND despachoorddet.deleted_at IS NULL

                INNER JOIN notaventadetalle
                    ON notaventadetalle.id = despachoorddet.notaventadetalle_id
                    AND notaventadetalle.deleted_at IS NULL

                INNER JOIN producto
                    ON producto.id = notaventadetalle.producto_id
                    AND producto.deleted_at IS NULL

                INNER JOIN categoriaprod
                    ON categoriaprod.id = producto.categoriaprod_id
                    AND categoriaprod.deleted_at IS NULL

                INNER JOIN categoriaprodsuc
                    ON categoriaprodsuc.categoriaprod_id = categoriaprod.id
                    AND categoriaprodsuc.sucursal_id IN ($arraySucFisxUsu)

                INNER JOIN tipoentrega
                    ON tipoentrega.id = despachoord.tipoentrega_id
                    AND tipoentrega.deleted_at IS NULL

                INNER JOIN despachosol
                    ON despachoord.despachosol_id = despachosol.id
                    AND despachosol.deleted_at IS NULL

                INNER JOIN sucursal
                    ON despachosol.sucursal_id = sucursal.id
                    AND sucursal.deleted_at IS NULL

                LEFT JOIN clientebloqueado
                    ON clientebloqueado.cliente_id = notaventa.cliente_id
                    AND clientebloqueado.deleted_at IS NULL

                LEFT JOIN vista_datacobranza
                    ON vista_datacobranza.cliente_id = notaventa.cliente_id

                LEFT JOIN clientedesbloqueado
                    ON clientedesbloqueado.cliente_id = notaventa.cliente_id
                    AND clientedesbloqueado.notaventa_id = notaventa.id
                    AND clientedesbloqueado.deleted_at IS NULL

                LEFT JOIN clientedesbloqueadomodulo
                    ON clientedesbloqueadomodulo.clientedesbloqueado_id = clientedesbloqueado.id
                    AND clientedesbloqueadomodulo.modulo_id = 8

                LEFT JOIN modulo
                    ON modulo.id = clientedesbloqueadomodulo.modulo_id

                LEFT JOIN clientedesbloqueadopro
                    ON clientedesbloqueadopro.cliente_id = notaventa.cliente_id
                    AND clientedesbloqueadopro.deleted_at IS NULL
                /* 🔥 reemplazo NOT IN despachoordanul */
                LEFT JOIN despachoordanul
                    ON despachoordanul.despachoord_id = despachoord.id
                    AND despachoordanul.deleted_at IS NULL

                /* 🔥 reemplazo NOT IN notaventacerrada */
                LEFT JOIN notaventacerrada
                    ON notaventacerrada.notaventa_id = despachoord.notaventa_id
                    AND notaventacerrada.deleted_at IS NULL

                WHERE
                    despachoord.aprguiadesp IS NULL
                    AND despachoordanul.despachoord_id IS NULL
                    AND notaventacerrada.notaventa_id IS NULL
                    AND despachosol.sucursal_id in ($sucurcadena)
                    AND $aux_conddespachoord_id
                GROUP BY despachoord.id;";

        return DB::select($sql);
    
    }
}
