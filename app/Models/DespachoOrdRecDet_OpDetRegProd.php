<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DespachoOrdRecDet_OpDetRegProd extends Model
{
    protected $table = "despachoordrecdet_opdetregprod";
    protected $fillable = [
        'despachoordrecdet_id',
        'opdetregprod_id',
        'cant',
        'cantkg',
    ];

    // Relación inversa: pertenece a un ítem de rechazo de orden de despacho
    public function despachoordrecdet()
    {
        return $this->belongsTo(DespachoOrdRecDet::class);
    }

    // Relación inversa: pertenece a un registro de producción aprobado (lote)
    public function opdetregprod()
    {
        return $this->belongsTo(OpDetRegProd::class);
    }
}
