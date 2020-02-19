<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AreaProduccion extends Model
{
    use SoftDeletes;
    protected $table = "areaproduccion";
    protected $fillable = [
        'nombre',
        'descripcion'
    ];
    //RELACION UNO A MUCHOS CATEGORIAPROD
    public function categoriaprods()
    {
        return $this->hasMany(CategoriaProd::class);
    }
}
