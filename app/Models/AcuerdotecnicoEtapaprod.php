<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AcuerdotecnicoEtapaprod extends Model
{
    protected $table = "acuerdotecnicoetapaprod";
    protected $fillable = [
        'acuerdotecnico_id',
        'etapaprod_id',
        'obs'
    ];    

    //RELACION INVERSA PARA BUSCAR EL PADRE
    public function acuerdotecnico()
    {
        return $this->belongsTo(AcuerdoTecnico::class,'acuerdotecnico_id');
    }

    //RELACION INVERSA PARA BUSCAR EL PADRE
    public function etapaprod()
    {
        return $this->belongsTo(EtapaProd::class,'etapaprod_id');
    }

}
