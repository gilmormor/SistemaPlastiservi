<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CategoriaGrupoCosto extends Model
{
    use SoftDeletes;
    protected $table = "categoriagrupocosto";
    protected $fillable = [
        'grupoprod_id',
        'unidadmedida_id',
        'costo'
    ];

    //RELACION INVERSA PARA BUSCAR EL PADRE DE UNA CLASE
    public function grupoprod()
    {
        return $this->belongsTo(GrupoProd::class);
    }
    

}
