<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProduccionMezclado extends Model
{
    protected $table = "produccionmezclado";
    protected $fillable = [
        'produccion_id',
        'bin_id',
        'cant',
        'kg',
    ];
    //RELACION INVERSA Produccion
    public function produccion()
    {
        return $this->belongsTo(Produccion::class);
    }
    //RELACION INVERSA Bin
    public function bin()
    {
        return $this->belongsTo(Bin::class);
    }

}
