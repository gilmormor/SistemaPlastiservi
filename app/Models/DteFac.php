<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DteFac extends Model
{
    protected $table = "dtefac";
    protected $fillable = [
        'dte_id',
        'hep',
        'formapago_id',
        'fchvenc'
    ];

    //RELACION INVERSA Dte
    public function dte()
    {
        return $this->belongsTo(Dte::class);
    }

    //RELACION INVERSA formapago
    public function formapago()
    {
        return $this->belongsTo(FormaPago::class);
    }
    
}
