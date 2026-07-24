<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OpDetMaquina extends Model
{
    protected $table = "opdetmaquina";
    protected $fillable = [
        'opdet_id',
        'maquina_id'
    ];

    //RELACION INVERSA otdet
    public function otdet()
    {
        return $this->belongsTo(OtDet::class);
    }

    //RELACION INVERSA otdet
    public function maquina()
    {
        return $this->belongsTo(Maquina::class);
    }

}
