<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DespachoOrdDet_InvBodegaProducto extends Model
{
    use SoftDeletes;
    protected $table = "despachoorddet_invbodegaproducto";
    protected $fillable = [
        'despachoorddet_id',
        'invbodegaproducto_id',
        'cant',
        'cantkg'
    ];

    //RELACION INVERSA Producto
    public function deapachoorddet()
    {
        return $this->belongsTo(DespachoOrdDet::class);
    }
    //RELACION INVERSA InvBodegaProducto
    public function invbodegaproducto()
    {
        return $this->belongsTo(InvBodegaProducto::class);
    }
}
