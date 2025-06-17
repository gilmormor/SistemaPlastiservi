<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OtDetNVDet extends Model
{
    protected $table = "otdetnvdet";
    protected $fillable = [
        'otdet_id',
        'notaventadetalle_id'
    ];

    //RELACION INVERSA OtDet
    public function otdet()
    {
        return $this->belongsTo(OtDet::class);
    }
    //RELACION INVERSA NotaVentaDetalle
    public function notaventadetalle()
    {
        return $this->belongsTo(NotaVentaDetalle::class);
    }
}
