<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AcuerdoTecnicoCValAtDet extends Model
{
    protected $table = "acuerdotecnicocvalatdet";
    protected $fillable = [
        'acuerdotecnico_id',
        'cvalatdet_id',
        'valor'
    ];

    //RELACION INVERSA ACUERDOTECNICO TEMPORAL
    public function acuerdotecnico()
    {
        return $this->belongsTo(AcuerdoTecnico::class, 'acuerdotecnico_id');
    }

    //RELACION INVERSA CODIGODET
    public function cvalatdet()
    {
        return $this->belongsTo(CValAtDet::class, 'cvalatdet_id');
    }
}
