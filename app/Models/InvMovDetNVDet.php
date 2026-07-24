<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvMovDetNVDet extends Model
{
    protected $table = "invmovdetnvdet";
    protected $fillable = [
        'invmovdet_id',
        'notaventadetalle_id',
    ];

    //RELACION INVERSA InvMovDet
    public function invmovdet()
    {
        return $this->belongsTo(InvMovDet::class);
    }

    public function notaventadetalle()
    {
        return $this->belongsTo(NotaVentaDetalle::class);
    }
}
