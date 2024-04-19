<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Banco extends Model
{
    use SoftDeletes;
    protected $table = "banco";
    protected $fillable = [
        'bancotipocta_id',
        'nombre',
        'desc',
        'numcta',
        'usuario_id',
        'usuariodel_id'
    ];

    //RELACION INVERSA PARA BUSCAR EL PADRE DE UNA CLASE
    public function bancotipocta()
    {
        return $this->belongsTo(BancoTipoCta::class);
    }
    
}
