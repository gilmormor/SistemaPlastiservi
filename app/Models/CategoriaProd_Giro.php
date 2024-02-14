<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CategoriaProd_Giro extends Model
{
    use SoftDeletes;
    protected $table = "categoriaprod_giro";
    protected $fillable = [
        'categoriaprod_id',
        'giro_id',
        'preciokg',
        'usuario_id',
        'usuariodel_id'
    ];

    //RELACION INVERSA PARA BUSCAR EL PADRE
    public function categoriaprod()
    {
        return $this->belongsTo(CategoriaProd::class,'categoriaprod_id');
    }
    //RELACION INVERSA PARA BUSCAR EL PADRE
    public function Giro()
    {
        return $this->belongsTo(Giro::class,'giro_id');
    }
}
