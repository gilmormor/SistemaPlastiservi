<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Trazabilidad entre etapas — DEFINITIVA (lado aprobado).
 * Cada fila indica de qué lote aprobado de la etapa anterior tomó material
 * un registro de producción aprobado, y cuánto (kg/cant).
 * Se crea al aprobar el supervisor, copiando opdetregprodtemp_origen.
 */
class OpDetRegProdOrigen extends Model
{
    protected $table = 'opdetregprod_origen';

    protected $fillable = [
        'opdetregprod_id',
        'opdetregprod_origen_id',
        'kg',
        'scrap',
        'cant',
    ];

    // Registro aprobado de la etapa actual (hijo)
    public function opdetregprod()
    {
        return $this->belongsTo(OpDetRegProd::class, 'opdetregprod_id');
    }

    // Lote aprobado de la etapa anterior (padre/origen)
    public function origen()
    {
        return $this->belongsTo(OpDetRegProd::class, 'opdetregprod_origen_id');
    }
}
