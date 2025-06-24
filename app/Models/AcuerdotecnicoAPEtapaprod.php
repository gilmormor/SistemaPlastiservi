<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AcuerdotecnicoAPEtapaprod extends Model
{
    protected $table = "Acuerdotecnicoapetapaprod";
    protected $fillable = [
        'acuerdotecnico_id',
        'apetapaprod_id',
        'obs'
    ];    

    //RELACION INVERSA PARA BUSCAR EL PADRE
    public function acuerdotecnico()
    {
        return $this->belongsTo(AcuerdoTecnico::class,'acuerdotecnico_id');
    }

    //RELACION INVERSA PARA BUSCAR EL PADRE
    public function areaproduccionetapaprod()
    {
        return $this->belongsTo(AreaProduccionEtapaProd::class,'apetapaprod_id');
    }
}
