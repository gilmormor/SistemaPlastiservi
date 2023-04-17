<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CategoriaProdGrupo extends Model
{
    use SoftDeletes;
    protected $table = "categoriaprodgrupo";
    protected $fillable = [
        'nombre',
        'desc'
    ];

    //RELACION UNO A MUCHOS CategoriaProd
    public function categoriaprods()
    {
        return $this->hasMany(CategoriaProd::class);
    }
}
