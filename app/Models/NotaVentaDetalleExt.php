<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotaVentaDetalleExt extends Model
{
    protected $table = "notaventadetalleext";
    protected $fillable = [
        'notaventadetalle_id',
        'cantext',
    ];
    
    //RELACION INVERSA NotaVenta
    public function notaventadetalle()
    {
        return $this->belongsTo(NotaVentaDetalle::class);
    }    
}
