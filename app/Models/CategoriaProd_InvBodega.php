<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CategoriaProd_InvBodega extends Model
{
    use SoftDeletes;
    protected $table = "categoriaprod_invbodega";
    protected $fillable = [
        'categoriaprod_id',
        'invbodega_id',
        'usuariodel_id'
    ];

    //RELACION INVERSA PARA BUSCAR EL PADRE
    public function categoriaprod()
    {
        return $this->belongsTo(CategoriaProd::class,'categoriaprod_id');
    }
    //RELACION INVERSA PARA BUSCAR EL PADRE
    public function invbodega()
    {
        return $this->belongsTo(InvBodega::class,'invbodega_id');
    }
}
