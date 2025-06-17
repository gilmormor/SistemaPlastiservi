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
        'obs',
        'maquina_id',
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
    
}
