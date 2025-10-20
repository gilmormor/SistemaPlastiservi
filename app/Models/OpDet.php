<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OpDet extends Model
{
    use SoftDeletes;
    protected $table = "opdet";
    protected $fillable = [
        'op_id',
        'otdet_id',
        'apsucetapaprod_id',
        'obs',
        'kg',
        'cant',
        'kgprod',
        'cantprod',
        'saldokg',
        'kgscrap',
        'mtslineal',
        'fechafin',
        'usuariodel_id'
    ];

    //RELACION INVERSA op
    public function op()
    {
        return $this->belongsTo(Op::class)->whereDoesntHave('opanul');
    }
    //RELACION INVERSA areaproduccionsucetapaprod
    public function areaproduccionsucetapaprod()
    {
        return $this->belongsTo(AreaProduccionSucEtapaProd::class,"apsucetapaprod_id");
    }

    //RELACION UNO A UNO OpDetMaquina
    public function opdetmaquina()
    {
        return $this->hasOne(OpDetMaquina::class,'opdet_id','maquina_id');
    }
    
}
