<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DespachoOrdRecDet extends Model
{
    use SoftDeletes;
    protected $table = "despachoordrecdet";
    protected $fillable = [
        'despachoordrec_id',
        'despachoorddet_id',
        'cantrec',
        'obsdet',
        'usuariodel_id'
    ];

    //RELACION INVERSA DespachoOrdRec
    public function despachoordrec()
    {
        return $this->belongsTo(DespachoOrdRec::class);
    }


    //RELACION INVERSA DespachoOrd
    public function despachoord()
    {
        return $this->belongsTo(DespachoOrd::class);
    }

    //RELACION INVERSA DespachoOrdDet
    public function despachoorddet()
    {
        return $this->belongsTo(DespachoOrdDet::class);
    }

    //RELACION DE UNO A MUCHOS despachoordrecdet_invbodegaproducto
    public function despachoordrecdet_invbodegaproductos()
    {
        return $this->hasMany(DespachoOrdRecDet_InvBodegaProducto::class,'despachoordrecdet_id');
    }
    
}
