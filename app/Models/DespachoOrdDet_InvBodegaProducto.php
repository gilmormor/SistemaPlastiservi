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

    //RELACION DE UNO A MUCHOS invmovdet_bodorddesps
    public function invmovdet_bodorddesps()
    {
        return $this->hasMany(InvMovDet_BodOrdDesp::class,'despachoorddet_invbodegaproducto_id');
    }
    
}
