<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SolDespachoDet extends Model
{
    use SoftDeletes;
    protected $table = "soldespachodet";
    protected $fillable = [
        'soldespacho_id',
        'notaventadetalle_id',
        'obs',
        'cantsoldesp',
        'cantdesp',
        'usuariodel_id'
    ];

    //RELACION INVERSA SolDespacho
    public function soldespacho()
    {
        return $this->belongsTo(SolDespacho::class);
    }
    //RELACION INVERSA NotaVentaDetalle
    public function notaventadetalle()
    {
        return $this->belongsTo(NotaVentaDetalle::class);
    }
    
}
