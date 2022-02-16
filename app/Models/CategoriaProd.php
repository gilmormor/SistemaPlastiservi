<?php

namespace App\Models;

use App\Models\Seguridad\Usuario;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class CategoriaProd extends Model
{
    use SoftDeletes;
    protected $table = "categoriaprod";
    protected $fillable = [
        'nombre',
        'descripcion',
        'precio',
        'areaproduccion_id',
        'sta_precioxkilo',
        'unidadmedida_id',
        'unidadmedidafact_id',
        'usuariodel_id',
        'mostdatosad',
        'mostunimed'
    ];

    public function productos()
    {
        return $this->hasMany(Producto::class,'categoriaprod_id');
    }
    public function sucursales()
    {
        return $this->belongsToMany(Sucursal::class, 'categoriaprodsuc','categoriaprod_id');
    }
    public function claseprods()
    {
        return $this->hasMany(ClaseProd::class,'categoriaprod_id');
    }
    public function grupoprods()
    {
        return $this->hasMany(GrupoProd::class,'categoriaprod_id');
    }
    //Relacion inversa a AreaProduccion
    public function areaproduccion()
    {
        return $this->belongsTo(AreaProduccion::class);
    }

    //Relacion inversa a UnidadMedida
    public function unidadmedida()
    {
        return $this->belongsTo(UnidadMedida::class);
    }

    //Relacion inversa a UnidadMedida
    public function unidadmedidafact()
    {
        return $this->belongsTo(UnidadMedida::class,'unidadmedidafact_id');
    }

    public function invbodegas()
    {
        return $this->belongsToMany(InvBodega::class, 'categoriaprod_invbodega','categoriaprod_id','invbodega_id');
    }


    public static function categoriasxUsuario($sucursal_id = false){
        $categoriaprods = CategoriaProd::join('categoriaprodsuc', function ($join)  use ($sucursal_id) {
            $user = Usuario::findOrFail(auth()->id());
            if($sucursal_id){
                $sucurArray = $user->sucursales->where('id','=',$sucursal_id)->pluck('id')->toArray();
            }else{
                $sucurArray = $user->sucursales->pluck('id')->toArray();
            }
            $join->on('categoriaprod.id', '=', 'categoriaprodsuc.categoriaprod_id')
            ->whereIn('categoriaprodsuc.sucursal_id', $sucurArray);
                    })
            ->select([
                'categoriaprod.id',
                'categoriaprod.nombre',
                'categoriaprod.descripcion',
                'categoriaprod.precio',
                'categoriaprod.areaproduccion_id',
                'categoriaprod.sta_precioxkilo',
                'categoriaprod.unidadmedida_id',
                'categoriaprod.unidadmedidafact_id'
            ])
            ->groupBy('categoriaprod.id')
            ->get();
        return $categoriaprods;
    }

    public static function catxUsuCostoAnnoMes($request){
        if(empty($request['categoriaprod_id'])){
            $cond_categoria = "";
        }else{
            $aux_categoriaprod_id = $request['categoriaprod_id'];
            $cond_categoria = " and grupoprod.categoriaprod_id!=$aux_categoriaprod_id";
        }
        $annomes = $request['annomes'];
        $user = Usuario::findOrFail(auth()->id());
        $sucurArray = $user->sucursales->pluck('id')->toArray();
        $sucurCadena = implode(",",$sucurArray);
        $sql = "
            SELECT categoriaprod.id,categoriaprod.nombre
            FROM categoriaprod INNER JOIN grupoprod
            ON categoriaprod.id=grupoprod.categoriaprod_id
            INNER JOIN categoriaprodsuc
            ON categoriaprod.id=categoriaprodsuc.categoriaprod_id AND categoriaprodsuc.sucursal_id in ($sucurCadena)
            WHERE ISNULL(categoriaprod.deleted_at) AND ISNULL(grupoprod.deleted_at)
            AND grupoprod.id NOT IN (SELECT grupoprod_id 
                                        FROM categoriagrupovalmes inner join grupoprod
                                        on categoriagrupovalmes.grupoprod_id=grupoprod.id $cond_categoria
                                        WHERE annomes=$annomes 
                                        and isnull(categoriagrupovalmes.deleted_at) )
            GROUP BY categoriaprod.id,categoriaprod.nombre 
        ";
        $datas = DB::select($sql);
        return $datas;
    }

    
}
