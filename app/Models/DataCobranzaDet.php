<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataCobranzaDet extends Model
{
    protected $table = "datacobranzadet";
    protected $fillable = [
        'datacobranza_id',
        'nrofav',
        'fecfact',
        'fecvenc',
        'mnttot',
        'deuda',
        'stavencida'
    ];

    //RELACION INVERSA DataCobranza
    public function datacobranza()
    {
        return $this->belongsTo(DataCobranza::class);
    }
    
}
