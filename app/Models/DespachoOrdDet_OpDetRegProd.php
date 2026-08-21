<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DespachoOrdDet_OpDetRegProd extends Model
{
    protected $table = "despachoorddet_opdetregprod";
    protected $fillable = [
        'despachoorddet_id',
        'opdetregprod_id',
        'cant',
        'cantkg',
    ];

    // Relación inversa: pertenece a un ítem de orden de despacho
    public function despachoorddet()
    {
        return $this->belongsTo(DespachoOrdDet::class);
    }

    // Relación inversa: pertenece a un registro de producción aprobado
    public function opdetregprod()
    {
        return $this->belongsTo(OpDetRegProd::class);
    }
}
