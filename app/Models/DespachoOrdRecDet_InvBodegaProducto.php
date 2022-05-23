<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DespachoOrdRecDet_InvBodegaProducto extends Model
{
    use SoftDeletes;
    protected $table = "despachoordrecdet_invbodegaproducto";
    protected $fillable = [
        'despachoordrecdet_id',
        'invbodegaproducto_id',
        'cant',
        'cantkg'
    ];

    //RELACION INVERSA DespachoOrdDet
    public function despachoordrecdet()
    {
        return $this->belongsTo(DespachoOrdRecDet::class);
    }
}
