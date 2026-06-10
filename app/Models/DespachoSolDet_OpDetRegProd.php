<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DespachoSolDet_OpDetRegProd extends Model
{
    protected $table = "despachosoldet_opdetregprod";
    protected $fillable = [
        'despachosoldet_id',
        'opdetregprod_id',
        'cant',
        'cantkg',
    ];

    // Relación inversa: pertenece a un ítem de solicitud de despacho
    public function despachosoldet()
    {
        return $this->belongsTo(DespachoSolDet::class);
    }

    // Relación inversa: pertenece a un registro de producción aprobado
    public function opdetregprod()
    {
        return $this->belongsTo(OpDetRegProd::class);
    }
}
