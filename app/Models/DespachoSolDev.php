<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DespachoSolDev extends Model
{
    protected $table = "despachosoldev";
    protected $fillable = 
    [
        'despachosol_id',
        'usuario_id',
        'obs',
        'status'
    ];

    //Relacion inversa a DespachoSol
    public function despachosol()
    {
        return $this->belongsTo(DespachoSol::class);
    }
}
