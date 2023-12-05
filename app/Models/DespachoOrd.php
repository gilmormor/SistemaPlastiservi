<?php

namespace App\Models;

use App\Events\GuardarFacturaDespacho;
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
        WHERE despachoord.aprguiadesp='1' and NOT isnull(despachoord.guiadespacho) and isnull(despachoord.numfactura)
        AND despachoord.id NOT IN (SELECT despachoordanul.despachoord_id FROM despachoordanul WHERE ISNULL(despachoordanul.deleted_at))
        AND despachoord.notaventa_id NOT IN (SELECT notaventacerrada.notaventa_id FROM notaventacerrada WHERE ISNULL(notaventacerrada.deleted_at))
        AND $aux_notaventa_idCodn
        GROUP BY despachoorddet.despachoord_id;";
        return DB::select($sql);
    }
}
