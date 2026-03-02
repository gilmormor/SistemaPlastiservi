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
        'productoinsumo_id',
        'cant',
        'costounitario',
        'usuario_id',
        'usuariodel_id'
    ];

    //RELACION INVERSA productoComp
    public function productoinsumo()
    {
        return $this->belongsTo(ProductoInsumo::class, 'productoinsumo_id');
    }
}
