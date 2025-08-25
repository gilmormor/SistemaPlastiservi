<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AcuerdoTecnicoTempCValAtDet extends Model
{
    protected $table = "acuerdotecnicotempcvalatdet";
    protected $fillable = [
        'acuerdotecnicotemp_id',
        'cvalatdet_id',
        'valor'
    ];

    //RELACION INVERSA ACUERDOTECNICO TEMPORAL
    public function acuerdotecnicotemp()
    {
        return $this->belongsTo(AcuerdoTecnicoTemp::class, 'acuerdotecnicotemp_id');
    }

    //RELACION INVERSA CODIGODET
    public function cvalatdet()
    {
        return $this->belongsTo(CValAtDet::class, 'cvalatdet_id');
    }
}
