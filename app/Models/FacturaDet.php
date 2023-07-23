<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FacturaDet extends Model
{
    use SoftDeletes;
    protected $table = "facturadet";
    protected $fillable = [
        'factura_id',
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

    //RELACION INVERSA Factura
    public function factura()
    {
        return $this->belongsTo(Factura::class);
    }
}
