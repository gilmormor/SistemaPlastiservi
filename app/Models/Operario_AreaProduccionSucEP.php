<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Operario_AreaProduccionSucEP extends Model
{
    protected $table = "operario_areaproduccionsuc";
    protected $fillable = [
        'operario_id',
        'areaproduccionsucep_id',
        'usuariodel_id'
    ];

    //RELACION INVERSA PARA BUSCAR EL PADRE
    public function operario()
    {
        return $this->belongsTo(Operario::class,'operario_id');
    }
    //RELACION INVERSA PARA BUSCAR EL PADRE
    public function areaproduccionsucep()
    {
        return $this->belongsTo(AreaProduccionSucEtapaProd::class,'areaproduccionsucep_id');
    }
}
