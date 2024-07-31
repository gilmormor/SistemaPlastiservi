<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataCobranzaDet extends Model
{
    protected $table = "datacobranzadet";
    protected $fillable = [
        'datacobranza_id',
        'cliente_id',
        'dte_id',
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

    //RELACION INVERSA Cliente
    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    //RELACION INVERSA DTE
    public function dte()
    {
        return $this->belongsTo(Dte::class);
    }
    
}
