<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Op extends Model
{
    use SoftDeletes;
    protected $table = "op";
    protected $fillable = [
        'otdet_id',
        'cantprod',
        'kgprod',
        'prioridad',
        'obs',
        'usuario_id',
        'usuariodel_id'
    ];

    //RELACION DE UNO A MUCHOS NotaVentaDetalle
    public function opdets()
    {
        return $this->hasMany(OpDet::class,'op_id');
    }
}
