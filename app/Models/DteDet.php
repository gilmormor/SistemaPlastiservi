<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DteDet extends Model
{
    use SoftDeletes;
    protected $table = "dtedet";
    protected $fillable = [
        'dte_id',
        'despachoorddet_id',
        'notaventadetalle_id',
        'producto_id',
        'nrolindet',
        'vlrcodigo',
        'nmbitem',
        'dscitem',
        'qtyitem',
        'unmditem',
        'unidadmedida_id',
        'prcitem',
        'montoitem',
        'obsdet',
        'itemkg',
        'usuariodel_id'
    ];

    //RELACION INVERSA Dte
    public function dte()
    {
        return $this->belongsTo(Dte::class);
    }

    //RELACION INVERSA Producto
    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

    //RELACION de uno a uno DteDet_DespachoOrdDet
    public function dtedet_despachoorddet()
    {
        return $this->hasOne(DteDet_DespachoOrdDet::class,"dtedet_id");
    }
    
    //RELACION A LA MISTA TABLA PARA SABER SI EL REGISTRO ES DE UNA FACTURA Y SU ORIGEN ES GUIA DE DESPACHO U OTRO REGISTRO
    public function dtedet()
    {
        return $this->belongsTo(DteDet::class,"dtedet_id");
    }

    //RELACION DE UNO A MUCHOS dtedets
    public function dtedets()
    {
        return $this->hasMany(DteDet::class,'dtedet_id');
    }

    
    
}
