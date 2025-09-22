<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AreaProduccionSucEtapaProd extends Model
{
    protected $table = "areaproduccionsucetapaprod";
    protected $fillable = [
        'areaproduccionsuc_id',
        'etapaprod_id',
        'orden'
    ];

    //RELACION INVERSA PARA BUSCAR EL PADRE
    public function areaproduccionsuc()
    {
        return $this->belongsTo(AreaProduccionSuc::class,'areaproduccionsuc_id');
    }

    //RELACION INVERSA PARA BUSCAR EL PADRE
    public function etapaprod()
    {
        return $this->belongsTo(EtapaProd::class,'etapaprod_id');
    }

}
