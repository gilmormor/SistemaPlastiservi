<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvMovDetOpDetRegProd extends Model
{
    protected $table = "invmovdet_opdetregprod";
    protected $fillable = [
        'invmovdet_id',
        'opdetregprod_id',
        'notaventadetalle_id', // FK a notaventadetalle; NULL cuando OT no viene de NV
    ];

    // Relación inversa → InvMovDet
    public function invmovdet()
    {
        return $this->belongsTo(InvMovDet::class);
    }

    // Relación inversa → OpDetRegProd
    public function opdetregprod()
    {
        return $this->belongsTo(OpDetRegProd::class);
    }

    // Relación inversa → NotaVentaDetalle (nullable)
    public function notaventadetalle()
    {
        return $this->belongsTo(NotaVentaDetalle::class);
    }
}
