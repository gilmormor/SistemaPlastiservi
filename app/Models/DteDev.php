<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DteDev extends Model
{
    use SoftDeletes;
    protected $table = "dtedev";
    protected $fillable = [
        'dte_id',
        'obs',
        'usuario_id',
        'usuariodel_id'
    ];
   
    //RELACION INVERSA Dte
    public function dte()
    {
        return $this->belongsTo(Dte::class);
    }
}
