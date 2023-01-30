<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DteNcNd extends Model
{
    protected $table = "dtencnd";
    protected $fillable = [
        'dte_id',
        'codref'
    ];

    //RELACION de uno a uno dtefac
    public function dtefac()
    {
        return $this->hasOne(DteFac::class);
    }
    
    //RELACION INVERSA Dte
    public function dte()
    {
        return $this->belongsTo(Dte::class);
    }
}
