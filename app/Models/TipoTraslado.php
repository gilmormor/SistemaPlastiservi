<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoTraslado extends Model
{
    protected $table = 'tipotraslado';

    protected $fillable = [
        'indtraslado',
        'desc',
        'activo',
    ];

    /**
     * Un tipo de traslado puede estar en muchos registros dtetrans.
     * La unión es por el campo indtraslado (no por id).
     */
    public function dtetrans()
    {
        return $this->hasMany(DteTrans::class, 'indtraslado', 'indtraslado');
    }
}
