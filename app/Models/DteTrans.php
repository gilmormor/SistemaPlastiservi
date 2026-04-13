<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DteTrans extends Model
{
    use SoftDeletes;

    protected $table = 'dtetrans';
    protected $fillable = [
        'dte_id', 
        'fchsalida', 
        'fchllegada', 
        'dirdest', 
        'comuna_id', 
        'cmnadest',
        'ruttrans', 
        'rutchofer', 
        'nombrechofer', 
        'patente',
        'patentecarro',
        'indtraslado',
    ];

    // Relaciones
    public function dte()
    {
        return $this->belongsTo(Dte::class, 'dte_id');
    }

    public function comuna()
    {
        return $this->belongsTo(Comuna::class, 'comuna_id');
    }

    /**
     * Indicador de traslado al que pertenece este registro.
     * La unión es por el campo indtraslado (no por id).
     */
    public function indtrasladoRel()
    {
        return $this->belongsTo(TipoTraslado::class, 'indtraslado', 'indtraslado');
    }
}
