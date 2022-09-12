<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GuiaDespachoDet extends Model
{
    use SoftDeletes;
    protected $table = "guiadespachodet";
    protected $fillable = [
        'guiadespacho_id',
        'despachoorddet_id',
        'notaventadetalle_id',
        'cantdesp',
        'usuariodel_id'
    ];

    //RELACION INVERSA DespachoOrd
    public function guiadespacho()
    {
        return $this->belongsTo(GuiaDespacho::class);
    }

    //RELACION INVERSA DespachoOrdDet
    public function despachoorddet()
    {
        return $this->belongsTo(DespachoOrdDet::class);
    }
  
    //RELACION INVERSA NotaVentaDetalle
    public function notaventadetalle()
    {
        return $this->belongsTo(NotaVentaDetalle::class);
    }
/*
    //RELACION DE UNO A MUCHOS DespachoOrdRecDet
    public function despachoordrecdets()
    {
        return $this->hasMany(DespachoOrdRecDet::class,'despachoorddet_id');
    }
*/
}
