<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

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
    

    public static function stock($request,$agrupar = "invbodegaproducto_id"){
        $aux_annomes = CategoriaGrupoValMes::annomes($request->mesanno);

        return InvMovDet::query()
            ->join("invmov","invmovdet.invmov_id","=","invmov.id")
            ->join("invbodegaproducto","invmovdet.invbodegaproducto_id","=","invbodegaproducto.id")
            ->join("producto","invbodegaproducto.producto_id","=","producto.id")
            ->join("categoriaprod","producto.categoriaprod_id","=","categoriaprod.id")
            ->join("invbodega","invbodegaproducto.invbodega_id","=","invbodega.id")
            ->join("claseprod","producto.claseprod_id","=","claseprod.id")
            ->join("invmovtipo","invmovdet.invmovtipo_id","=","invmovtipo.id")
            ->leftJoin('acuerdotecnico', 'producto.id', '=', 'acuerdotecnico.producto_id')
            ->where("invmov.annomes","=",$aux_annomes)
            ->whereNull("invmov.deleted_at")
            ->whereNull("invmovdet.deleted_at")
            ->whereNull("invbodegaproducto.deleted_at")
            ->whereNull("producto.deleted_at")
            ->whereNull("categoriaprod.deleted_at")
            ->whereNull("invbodega.deleted_at")
            ->whereNull("claseprod.deleted_at")
            ->whereNull("invmovtipo.deleted_at")
            ->whereNull("acuerdotecnico.deleted_at")
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
                DB::raw("if(isnull(acuerdotecnico.id), producto.diametro, at_ancho) as diametro"),
                DB::raw("if(isnull(acuerdotecnico.id), producto.long, at_largo) as largo"),
                DB::raw("if(isnull(acuerdotecnico.id), producto.peso, at_espesor) as peso"),
                'producto.long',
                'producto.tipounion',
                'claseprod.cla_nombre',
                'categoriaprod.nombre as categoria_nombre',
                'invbodegaproducto.invbodega_id',
                'invbodegaproducto_id',
                'invbodega.nombre as invbodega_nombre',
                'acuerdotecnico.id as acuerdotecnico_id'
            ])
            ->selectRaw("SUM(if(invmovtipo.stacieinimes=1,cant,0)) as stockini")
            ->selectRaw("SUM(if(invmovtipo.stacieinimes=0 AND invmovdet.cant>0,cant,0)) AS mov_in")
            ->selectRaw("SUM(if(invmovtipo.stacieinimes=0 AND invmovdet.cant < -1,cant,0)) AS mov_out")
            ->selectRaw("SUM(cant) as stock")
            ->selectRaw("SUM(if(invbodega.tipo=2,cant,0)) as stockBodProdTerm")
            ->selectRaw("SUM(if(invbodega.tipo=1,cant,0)) as stockPiking")
            ->selectRaw("SUM(cantkg) as stockkg")
            ->groupBy($agrupar)
            ->orderBy('invbodegaproducto.producto_id')
            ->orderBy('invbodega.orden');
    }

    public static function stocksql($request,$agrupar = "invbodegaproducto_id"){
        //dd($request->tipobodega);
        $aux_annomes = CategoriaGrupoValMes::annomes($request->mesanno);

        if(!isset($request->sucursal_id) or empty($request->sucursal_id) or ($request->sucursal_id == "x")){
            $aux_sucursal_idCond = "false";
        }else{
            $aux_sucursal_idCond = "invmovdet.sucursal_id = $request->sucursal_id";
        }
        if(!isset($request->tipobodega) or empty($request->tipobodega)){
            $aux_tipobodegaCond = "true";
        }else{
            $aux_tipobodega = $request->tipobodega;
            if(is_array($request->tipobodega)){
                $aux_tipobodega = implode(",", $request->tipobodega);
            }
            $aux_tipobodegaCond = " invbodega.tipo in ($aux_tipobodega) ";
        }
        if(!isset($request->invbodega_id) or empty($request->invbodega_id)){
            $aux_invbodega_idCond = "true";
        }else{
            $aux_invbodegaid = $request->invbodega_id;
            if(is_array($request->invbodega_id)){
                $aux_invbodegaid = implode(",", $request->invbodega_id);
            }
            $aux_invbodega_idCond = " invmovdet.invbodega_id in ($aux_invbodegaid) ";
        }
        if(!isset($request->producto_id) or empty($request->producto_id)){
            $aux_producto_idCodn = "true";
        }else{
            $aux_producto_idCodn = " invmovdet.producto_id in ($request->producto_id) ";
        }

        if(!isset($request->categoriaprod_id) or empty($request->categoriaprod_id)){
            $aux_categoriaprod_idCond = "true";
        }else{
            $aux_categoriaprodid = $request->categoriaprod_id;
            if(is_array($request->categoriaprod_id)){
                $aux_categoriaprodid = implode(",", $request->categoriaprod_id);
            }
            $aux_categoriaprod_idCond = " producto.categoriaprod_id in ($aux_categoriaprodid) ";
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

        $sql = "SELECT invbodegaproducto.producto_id, producto.nombre as producto_nombre, 
        if(isnull(acuerdotecnico.id),producto.diametro,at_ancho) as diametro,
        if(isnull(acuerdotecnico.id),producto.long,at_largo) as largo,
        if(isnull(acuerdotecnico.id),producto.peso,at_espesor) as peso,
        producto.long,
        producto.tipounion, claseprod.cla_nombre,
        categoriaprod.nombre as categoria_nombre, invbodegaproducto.invbodega_id, invbodegaproducto_id, 
        invbodega.nombre as invbodega_nombre, SUM(if(invmovtipo.stacieinimes=1,cant,0)) as stockini, 
        SUM(if(invmovtipo.stacieinimes=0 AND invmovdet.cant>0,cant,0)) AS mov_in, 
        SUM(if(invmovtipo.stacieinimes=0 AND invmovdet.cant < -1,cant,0)) AS mov_out, SUM(cant) as stock, 
        SUM(if(invbodega.tipo=2,cant,0)) as stockBodProdTerm, SUM(if(invbodega.tipo=1,cant,0)) as stockPiking, 
        SUM(cantkg) as stockkg,
        acuerdotecnico.id as acuerdotecnico_id,at_ancho,at_largo,at_espesor
        from invmovdet inner join invmov 
        on invmovdet.invmov_id = invmov.id and isnull(invmovdet.deleted_at) and isnull(invmov.deleted_at)
        inner join invbodegaproducto 
        on invmovdet.invbodegaproducto_id = invbodegaproducto.id and isnull(invbodegaproducto.deleted_at)
        inner join producto 
        on invbodegaproducto.producto_id = producto.id and isnull(producto.deleted_at)
        inner join categoriaprod 
        on producto.categoriaprod_id = categoriaprod.id and isnull(categoriaprod.deleted_at)
        inner join invbodega 
        on invbodegaproducto.invbodega_id = invbodega.id and isnull(invbodega.deleted_at)
        inner join claseprod 
        on producto.claseprod_id = claseprod.id and isnull(claseprod.deleted_at)
        inner join invmovtipo 
        on invmovdet.invmovtipo_id = invmovtipo.id and isnull(invmovtipo.deleted_at)
        LEFT JOIN acuerdotecnico
        ON producto.id = acuerdotecnico.producto_id
        where invmov.annomes = $aux_annomes 
        and $aux_sucursal_idCond 
        and $aux_tipobodegaCond
        and $aux_invbodega_idCond
        and $aux_producto_idCodn
        and $aux_categoriaprod_idCond
        and $aux_areaproduccion_idCond
        group by $agrupar 
        having SUM(cant) != 0 
        order by invbodegaproducto.producto_id asc, invbodega.orden ASC;";


        $datas = DB::select($sql);
        return $datas;

    }


}