<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BancoTipoCta extends Model
{
    use SoftDeletes;
    protected $table = "bancotipocta";
    protected $fillable = [
        'desc',
        'usuario_id',
        'usuariodel_id'
    ];
    //RELACION UNO A MUCHOS BANCO
    public function bancos()
    {
        return $this->hasMany(Banco::class);
    }

}
