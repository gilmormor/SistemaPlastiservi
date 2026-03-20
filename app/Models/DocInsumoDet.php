<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocInsumoDet extends Model
{
    use SoftDeletes;
    protected $table = "docinsumodet";
    protected $fillable = [
        'origentipo',
        'origendet_id',
        'insumo_id',
        'cant',
        'canttotal',
        'unidadesproducto',
        'costounitario',
        'costototal',
        'usuario_id',
        'usuariodel_id'
    ];

    //RELACION INVERSA insumo
    public function insumo()
    {
        return $this->belongsTo(Insumo::class, 'insumo_id');
    }
}
