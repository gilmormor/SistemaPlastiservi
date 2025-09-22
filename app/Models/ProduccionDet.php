<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProduccionDet extends Model
{
    protected $table = "producciondet";
    protected $fillable = [
        'produccion_id',
        'opdet_id',
        'cant',
        'kg',
    ];
    //RELACION INVERSA Produccion
    public function produccion()
    {
        return $this->belongsTo(Produccion::class);
    }

    //RELACION INVERSA OpDet
    public function opdet()
    {
        return $this->belongsTo(OpDet::class);
    }

}
