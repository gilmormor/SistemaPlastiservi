<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class GrupoCatProm extends Model
{
    use SoftDeletes;
    protected $table = "grupocatprom";
    protected $fillable = [
        'nombre',
        'usuario_id',
        'usuariodel_id'
    ];

    //RELACION MUCHO A MUCHOS CON USUARIO A TRAVES DE cliente_sucursal
    public function categoriaprods()
    {
        return $this->belongsToMany(CategoriaProd::class, 'grupocatpromcategoriaprod','grupocatprom_id','categoriaprod_id')->withTimestamps();
    }

    public static function arraygrupocatprom(){
        $sql = "SELECT grupocatprom.id,grupocatprom.nombre,
        GROUP_CONCAT(DISTINCT categoriaprod.id ORDER BY categoriaprod.id) AS categoriaprod_ids,
        0.00 as totaldinero, 0.00 as totalkg, 0.00 promedio
        from grupocatprom inner join grupocatpromcategoriaprod
        on grupocatprom.id = grupocatpromcategoriaprod.grupocatprom_id
        INNER JOIN categoriaprod
        ON categoriaprod.id = grupocatpromcategoriaprod.categoriaprod_id
        where isnull(grupocatprom.deleted_at) 
        GROUP BY grupocatprom.id";
        return DB::select($sql);
    }

    
}
