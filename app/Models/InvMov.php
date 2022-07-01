<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvMov extends Model
{
    use SoftDeletes;
    protected $table = "invmov";
    protected $fillable = [
        'fechahora',
        'annomes',
        'desc',
        'obs',
        'staanul',
        'invmovtipo_id',
        'invmovmodulo_id',
        'idmovmod',
        'sucursal_id',
        'usuario_id',
        'usuariodel_id'
    ];

    //RELACION DE UNO A VARIOS InvMovDet
    public function invmovdets()
    {
        return $this->hasMany(InvMovDet::class,'invmov_id');
    }

    //RELACION INVERSA InvMovModulo
    public function invmovmodulo()
    {
        return $this->belongsTo(InvMovModulo::class);
    }
    //Relacion inversa a Sucursal
    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }
    

    public static function stock($request){
        $aux_annomes = CategoriaGrupoValMes::annomes($request->mesanno);

        return InvMovDet::query()
            ->join("invmov","invmovdet.invmov_id","=","invmov.id")
            ->join("invbodegaproducto","invmovdet.invbodegaproducto_id","=","invbodegaproducto.id")
            ->join("producto","invbodegaproducto.producto_id","=","producto.id")
            ->join("categoriaprod","producto.categoriaprod_id","=","categoriaprod.id")
            ->join("invbodega","invbodegaproducto.invbodega_id","=","invbodega.id")
            ->join("claseprod","producto.claseprod_id","=","claseprod.id")
            ->join("invmovtipo","invmovdet.invmovtipo_id","=","invmovtipo.id")
            ->where("invmov.annomes","=",$aux_annomes)
            ->where(function($query) use ($request)  {
                if(!isset($request->sucursal_id) or empty($request->sucursal_id)){
                    true;
                }else{
                    $query->where("invmovdet.sucursal_id","=",$request->sucursal_id);
                }
            })
            ->where(function($query) use ($request)  {
                if(!isset($request->invbodega_id) or empty($request->invbodega_id)){
                    true;
                }else{
                    if(!is_array($request->invbodega_id)){
                        $aux_invbodegaid = explode(",", $request->invbodega_id);
                    }
                    $query->whereIn("invmovdet.invbodega_id",$aux_invbodegaid);
                    //$query->where("invmovdet.invbodega_id","=",$request->invbodega_id);
                }
            })
            ->where(function($query) use ($request)  {
                if(!isset($request->tipobodega) or empty($request->tipobodega)){
                    true;
                }else{
                    if(!is_array($request->tipobodega)){
                        $aux_tipobodega = explode(",", $request->tipobodega);
                    }
                    $query->whereIn("invbodega.tipo",$aux_tipobodega);
                }
            })
            ->where(function($query) use ($request)  {
                if(!isset($request->producto_id) or empty($request->producto_id)){
                    true;
                }else{
                    $aux_codprod = explode(",", $request->producto_id);
                    $query->whereIn("invmovdet.producto_id",$aux_codprod);
                }
            })
            ->where(function($query) use ($request)  {
                if(!isset($request->categoriaprod_id) or empty($request->categoriaprod_id)){
                    true;
                }else{
                    if(!is_array($request->categoriaprod_id)){
                        $aux_categoriaprodid = explode(",", $request->categoriaprod_id);
                    }
                    $query->whereIn("producto.categoriaprod_id",$aux_categoriaprodid);
                }
            })
            ->where(function($query) use ($request)  {
                if(!isset($request->areaproduccion_id) or empty($request->areaproduccion_id)){
                    true;
                }else{
                    if(!is_array($request->areaproduccion_id)){
                        $aux_areaproduccionid = explode(",", $request->areaproduccion_id);
                    }
                    $query->whereIn("categoriaprod.areaproduccion_id",$aux_areaproduccionid);
                    //$query->where("categoriaprod.areaproduccion_id","=",$request->areaproduccion_id);
                }
            })
            ->whereNull("invmov.staanul")
            ->havingRaw("SUM(cant) > 0")
            ->select([
                'invbodegaproducto.producto_id',
                'producto.nombre as producto_nombre',
                'producto.diametro',
                'producto.long',
                'producto.peso',
                'producto.tipounion',
                'claseprod.cla_nombre',
                'categoriaprod.nombre as categoria_nombre',
                'invbodegaproducto.invbodega_id',
                'invbodegaproducto_id',
                'invbodega.nombre as invbodega_nombre'
            ])
            ->selectRaw("SUM(if(invmovtipo.stacieinimes=1,cant,0)) as stockini")
            ->selectRaw("SUM(if(invmovtipo.stacieinimes=0 AND invmovdet.cant>0,cant,0)) AS mov_in")
            ->selectRaw("SUM(if(invmovtipo.stacieinimes=0 AND invmovdet.cant < -1,cant,0)) AS mov_out")
            ->selectRaw("SUM(cant) as stock")
            ->selectRaw("SUM(cantkg) as stockkg")
            ->groupBy('invbodegaproducto_id')
            ->orderBy('invbodegaproducto.producto_id')
            ->orderBy('invbodega.orden');
    }
    
}