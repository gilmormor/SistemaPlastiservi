<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DteDte extends Model
{
    use SoftDeletes;
    protected $table = "dtedte";
    protected $fillable = [
        'dte_id',
        'dter_id',
        'dtefac_id',
    ];

    //RELACION INVERSA Dte
    public function dte()
    {
        return $this->belongsTo(Dte::class);
    }

    //RELACION INVERSA Dte
    public function dter()
    {
        return $this->belongsTo(Dte::class,"dter_id");
    }

    //RELACION INVERSA Dte
    public function dtefac()
    {
        return $this->belongsTo(Dte::class,"dtefac_id");
    }
    
}
