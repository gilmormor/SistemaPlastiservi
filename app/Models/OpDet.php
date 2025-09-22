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
        'apetapaprod_id',
        'obs',
        'kg',
        'cant',
        'saldokg',
        'fechafin',
        'usuariodel_id'
    ];

    //RELACION INVERSA op
    public function op()
    {
        return $this->belongsTo(Op::class)->whereDoesntHave('opanul');
    }
    //RELACION INVERSA otdet
    public function otdet()
    {
        return $this->belongsTo(OtDet::class);
    }
    //RELACION INVERSA areaproduccionetapaprod
    public function areaproduccionetapaprod()
    {
        return $this->belongsTo(AreaProduccionEtapaProd::class,"apetapaprod_id");
    }

    //RELACION UNO A UNO OpDetMaquina
    public function opdetmaquina()
    {
        return $this->hasOne(OpDetMaquina::class,'opdet_id','maquina_id');
    }
    
}
