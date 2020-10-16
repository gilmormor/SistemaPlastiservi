<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DespachoSolDet extends Model
{
    use SoftDeletes;
    protected $table = "despachosoldet";
    protected $fillable = [
        'despachosol_id',
        'notaventadetalle_id',
        'cantsoldesp',
        'cantdesp',
        'usuariodel_id'
    ];

    //RELACION DE UNO A MUCHOS DespachoOrdDet
    public function despachoorddets()
    {
        return $this->hasMany(DespachoOrdDet::class);
    }

    //RELACION INVERSA DespachoSol
    public function despachosol()
    {
        return $this->belongsTo(DespachoSol::class);
    }
    //RELACION INVERSA NotaVentaDetalle
    public function notaventadetalle()
    {
        return $this->belongsTo(NotaVentaDetalle::class);
    }
}
