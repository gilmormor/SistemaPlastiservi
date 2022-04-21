<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DespachoOrdDet_InvBodegaProducto extends Model
{
    protected $table = "despachoorddet_invbodegaproducto";
    protected $fillable = [
        'despachoorddet_id',
        'invbodegaproducto_id',
        'cant',
        'cantkg'
    ];

    //RELACION INVERSA DespachoOrdDet
    public function despachoorddet()
    {
        return $this->belongsTo(DespachoOrdDet::class);
    }
    //RELACION INVERSA InvBodegaProducto
    public function invbodegaproducto()
    {
        return $this->belongsTo(InvBodegaProducto::class);
    }
}
