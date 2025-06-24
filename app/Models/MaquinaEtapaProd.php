<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaquinaEtapaProd extends Model
{
    protected $table = "maquinaetapaprod";
    protected $fillable = [
        'maquina_id',
        'etapaprod_id',
    ];

    //RELACION INVERSA A Maquina
    public function maquina()
    {
        return $this->belongsTo(Maquina::class);
    }
    //RELACION INVERSA A EtapaProd
    public function etapaprod()
    {
        return $this->belongsTo(EtapaProd::class);
    }

}
