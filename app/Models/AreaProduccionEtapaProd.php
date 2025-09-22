<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AreaProduccionEtapaProd extends Model
{
    protected $table = "areaproduccionetapaprod";
    protected $fillable = [
        'areaproduccion_id',
        'etapaprod_id',
        'orden'
    ];

    //RELACION INVERSA PARA BUSCAR EL PADRE
    public function areaproduccion()
    {
        return $this->belongsTo(AreaProduccion::class,'areaproduccion_id');
    }

    //RELACION INVERSA PARA BUSCAR EL PADRE
    public function etapaprod()
    {
        return $this->belongsTo(EtapaProd::class,'etapaprod_id');
    }

}
