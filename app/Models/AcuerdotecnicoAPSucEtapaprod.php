<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AcuerdotecnicoAPSucEtapaprod extends Model
{
    protected $table = "acuerdotecnicoapsucetapaprod";
    protected $fillable = [
        'acuerdotecnico_id',
        'apsucetapaprod_id',
        'obs'
    ];

    //RELACION INVERSA PARA BUSCAR EL PADRE
    public function acuerdotecnico()
    {
        return $this->belongsTo(AcuerdoTecnico::class,'acuerdotecnico_id');
    }
}
