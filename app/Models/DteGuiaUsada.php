<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DteGuiaUsada extends Model
{
    use SoftDeletes;
    protected $table = "dteguiausada";
    protected $fillable = [
        'dte_id',
        'usuario_id',
        'usuariodel_id',
    ];

    //RELACION INVERSA Dte
    public function dte()
    {
        return $this->belongsTo(Dte::class);
    }
}
