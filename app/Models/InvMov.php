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
        'sucursal_id',
        'usuario_id',
        'usuariodel_id'
    ];

    //RELACION DE UNO A VARIOS InvMovDet
    public function invmovdets()
    {
        return $this->hasMany(InvMovDet::class);
    }

    //RELACION INVERSA InvMovModulo
    public function invmovmodulo()
    {
        return $this->belongsTo(InvMovModulo::class);
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
                    $query->where("invmovdet.invbodega_id","=",$request->invbodega_id);
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
                    $query->where("categoriaprod.areaproduccion_id","=",$request->areaproduccion_id);
                }
            })
            ->whereNull("invmov.staanul")
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
            ->selectRaw("SUM(cant) as stock")
            ->groupBy('invbodegaproducto_id')
            ->orderBy('invbodegaproducto.producto_id')
            ->orderBy('invbodega.orden');
    }
    
}