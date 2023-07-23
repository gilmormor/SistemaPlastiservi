<?php

namespace App\Models;

use App\Models\Seguridad\Usuario;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class GuiaDesp extends Model
{
    use SoftDeletes;
    protected $table = "guiadesp";
    protected $fillable = [
        'nrodocto',
        'fchemis',
        'fchemisgen',
        'fechahora',
        'despachoord_id',
        'notaventa_id',
        'sucursal_id',
        'cliente_id',
        'comuna_id',
        'vendedor_id',    
        'obs',
        'ot',
        'oc_id',
        'oc_file',
        'tipodespacho',
        'indtraslado',
        'mntneto',
        'tasaiva',
        'iva',
        'mnttotal',
        'kgtotal',
        'tipoentrega_id',
        'lugarentrega',
        'comunaentrega_id',
        'centroeconomico_id',
        'aprobstatus',
        'aprobusu_id',
        'aprobfechahora',
        'usuario_id',
        'usuariodel_id'
    ];


    //RELACION DE UNO A MUCHOS GuiaDespDet
    public function guiadespdets()
    {
        return $this->hasMany(GuiaDespDet::class,'guiadesp_id');
    }

    //Relacion inversa a DespachoOrd
    public function despachoord()
    {
        return $this->belongsTo(DespachoOrd::class);
    }

    //Relacion inversa a NotaVenta
    public function notaventa()
    {
        return $this->belongsTo(NotaVenta::class);
    }

    //RELACION INVERSA Cliente
    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
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
/*    
    //RELACION DE UNO A MUCHOS DespachoOrdAnul
    public function despachoordanul()
    {
        return $this->hasOne(DespachoOrdAnul::class,'despachoord_id');
    }
*/
/*
    //RELACION DE UNO A MUCHOS DespachoOrdRec
    public function despachoordrecs()
    {
        return $this->hasOne(DespachoOrdRec::class,'despachoord_id');
    }
*/

    //RELACION DE UNO A MUCHOS GuiaDespAnul
    public function guiadespanul()
    {
        return $this->hasOne(GuiaDespAnul::class,'guiadesp_id');
    }

    //RELACION MUCHO A MUCHOS CON Factura A TRAVES DE factura_guiadesp
    public function facturas()
    {
        return $this->belongsToMany(FacturaGuiaDesp::class, 'factura_guiadesp')->withTimestamps();
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
    
        if(!isset($request->fechad) or empty($request->fechad) or empty($request->fechah)){
            $aux_condFecha = " true";
        }else{
            $fecha = date_create_from_format('d/m/Y', $request->fechad);
            $fechad = date_format($fecha, 'Y-m-d')." 00:00:00";
            $fecha = date_create_from_format('d/m/Y', $request->fechah);
            $fechah = date_format($fecha, 'Y-m-d')." 23:59:59";
            $aux_condFecha = "guiadesp.fechahora>='$fechad' and guiadesp.fechahora<='$fechah'";
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
            $aux_condtipoentrega_id = "notaventa.tipoentrega_id='$request->tipoentrega_id'";
        }
        if(!isset($request->notaventa_id) or empty($request->notaventa_id)){
            $aux_condnotaventa_id = " true";
        }else{
            $aux_condnotaventa_id = "notaventa.id='$request->notaventa_id'";
        }

        if(!isset($request->guiadesp_id) or empty($request->guiadesp_id)){
            $aux_condguiadesp_id = " true";
        }else{
            $aux_condguiadesp_id = "guiadesp.nrodocto='$request->guiadesp_id'";
        }

        if(!isset($request->guiadesp_id) or empty($request->guiadesp_id)){
            $aux_condguiadesp_id = " true";
        }else{
            $aux_condguiadesp_id = "guiadesp.nrodocto='$request->guiadesp_id'";
        }
        $aux_condproducto_id = " true";
        if(!empty($request->producto_id)){
            $aux_codprod = explode(",", $request->producto_id);
            $aux_codprod = implode ( ',' , $aux_codprod);
            $aux_condproducto_id = "guiadespdet.producto_id in ($aux_codprod)";
        }

        $aux_aprobstatus = " true";
        if(!empty($request->aprobstatus)){
            switch ($request->aprobstatus) {
                case 0:
                    $aux_aprobstatus = " true";
                    break;
                case 1:
                    $aux_aprobstatus = " isnull(guiadespanul.obs)";
                    break;    
                case 2:
                    $aux_aprobstatus = " not isnull(guiadespanul.obs)";
                    break;
            }
        }
    
        if(!isset($request->comuna_id) or empty($request->comuna_id)){
            $aux_condcomuna_id = " true";
        }else{
            $aux_condcomuna_id = "notaventa.comunaentrega_id='$request->comuna_id'";
        }
    
        $sql = "SELECT guiadesp.id,guiadesp.nrodocto,guiadesp.fechahora,cliente.rut,cliente.razonsocial,
        notaventa.oc_id as nvoc_id,notaventa.oc_file as nvoc_file,
        guiadesp.oc_id,guiadesp.oc_file,comuna.nombre as comunanombre,
        tipoentrega.nombre as tipoentrega_nombre,tipoentrega.icono,
        guiadesp.notaventa_id,despachoord.fechaestdesp,guiadesp.despachoord_id,despachoord.despachosol_id,
        guiadespanul.obs as guiadespanul_obs,guiadespanul.created_at as guiadespanulcreated_at
        FROM guiadesp INNER JOIN guiadespdet
        ON guiadesp.id=guiadespdet.guiadesp_id and isnull(guiadesp.deleted_at) and isnull(guiadespdet.deleted_at)
        LEFT JOIN notaventa
        ON notaventa.id=guiadesp.notaventa_id and isnull(notaventa.deleted_at)
        INNER JOIN notaventadetalle
        ON guiadespdet.notaventadetalle_id=notaventadetalle.id and isnull(notaventadetalle.deleted_at)
        INNER JOIN despachoord
        ON despachoord.id=guiadesp.despachoord_id and isnull(despachoord.deleted_at)
        INNER JOIN despachoorddet
        ON despachoord.id=despachoorddet.despachoord_id and isnull(despachoorddet.deleted_at)
        INNER JOIN producto
        ON guiadespdet.producto_id=producto.id and isnull(producto.deleted_at)
        INNER JOIN categoriaprod
        ON categoriaprod.id=producto.categoriaprod_id and isnull(categoriaprod.deleted_at)
        INNER JOIN areaproduccion
        ON areaproduccion.id=categoriaprod.areaproduccion_id and isnull(areaproduccion.deleted_at)
        INNER JOIN cliente
        ON cliente.id=notaventa.cliente_id and isnull(cliente.deleted_at)
        INNER JOIN comuna
        ON comuna.id=guiadesp.comunaentrega_id and isnull(comuna.deleted_at)
        INNER JOIN tipoentrega
        ON tipoentrega.id = guiadesp.tipoentrega_id AND ISNULL(tipoentrega.deleted_at)
        LEFT JOIN guiadespanul
        ON guiadespanul.guiadesp_id=guiadesp.id and isnull(guiadespanul.deleted_at)        
        WHERE not isnull(guiadesp.nrodocto)
        and $aux_condproducto_id
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
        GROUP BY guiadesp.id
        ORDER BY guiadesp.id desc;";

        $datas = DB::select($sql);
        return $datas;
    }

}
