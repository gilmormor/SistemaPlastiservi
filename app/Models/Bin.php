<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bin extends Model
{
    use SoftDeletes;
    protected $table = "bin";
    protected $fillable = [
        'sucursal_id',
        'nombre',
        'desc',
        'capkg',
        'usuario_id',
        'usuariodel_id'
    ];

    //Relacion inversa a Sucursal
    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }
    //RELACION DE UNO A MUCHOS ProduccionMezclado
    public function produccionmezclados()
    {
        return $this->hasMany(ProduccionMezclado::class,'bin_id');
    }
}
