<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DespachoSolDet_InvBodegaProducto extends Model
{
    use SoftDeletes;
    protected $table = "despachosoldet_invbodegaproducto";
    protected $fillable = [
        'despachosoldet_id',
        'invbodegaproducto_id',
        'cant',
        'cantkg',
        'cantex'
    ];

    //RELACION INVERSA DespachoSolDet
    public function despachosoldet()
    {
        return $this->belongsTo(DespachoSolDet::class);
    }
    //RELACION INVERSA InvBodegaProducto
    public function invbodegaproducto()
    {
        return $this->belongsTo(InvBodegaProducto::class);
    }
    
    //RELACION DE UNO A MUCHOS invmovdet_bodsoldesps
    public function invmovdet_bodsoldesps()
    {
        return $this->hasMany(InvMovDet_BodSolDesp::class,'despachosoldet_invbodegaproducto_id');
    }
    
}
