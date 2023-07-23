<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FacturaAnul extends Model
{
    use SoftDeletes;
    protected $table = "facturaanul";
    protected $fillable = [
        'factura_id',
        'obs',
        'motanul_id',
        'moddevgiadesp_id',
        'usuario_id',
        'usuariodel_id'
    ];

    //RELACION INVERSA Factura
    public function factura()
    {
        return $this->belongsTo(Factura::class);
    }
}
