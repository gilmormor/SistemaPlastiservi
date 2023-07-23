<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DteDet_DespachoOrdDet extends Model
{
    use SoftDeletes;
    protected $table = "dtedet_despachoorddet";
    protected $fillable = [
        'dtedet_id',
        'despachoorddet_id',
        'notaventadetalle_id',
    ];

    //RELACION INVERSA DteDet
    public function dtedet()
    {
        return $this->belongsTo(DteDet::class);
    }
    
    
    //RELACION INVERSA DespachoOrdDet
    public function despachoorddet()
    {
        return $this->belongsTo(DespachoOrdDet::class);
    }
    
    //RELACION INVERSA NotaVentaDetalle
    public function notaventadetalle()
    {
        return $this->belongsTo(NotaVentaDetalle::class);
    }
    
}
