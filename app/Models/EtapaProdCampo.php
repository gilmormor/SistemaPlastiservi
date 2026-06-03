<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Campos adicionales configurados para una etapa de producción concreta.
 * Cada registro define un campo extra (number, text o calculated) que el
 * operario deberá llenar al registrar producción en esa etapa.
 */
class EtapaProdCampo extends Model
{
    use SoftDeletes;

    protected $table = 'etapaprod_campo';

    protected $fillable = [
        'apsucetapaprod_id',
        'nombre',
        'etiqueta',
        'tipo',
        'formula',
        'unidad',
        'decimales',
        'requerido',
        'orden',
    ];

    protected $casts = [
        'requerido'  => 'boolean',
        'decimales'  => 'integer',
        'orden'      => 'integer',
    ];

    // ——— Relaciones ———

    public function apsucetapaprod()
    {
        return $this->belongsTo(AreaProduccionSucEtapaProd::class, 'apsucetapaprod_id');
    }

    // Valores guardados en registros temporales
    public function campovalsTemp()
    {
        return $this->hasMany(OpDetRegProdTempCampoVal::class, 'etapaprod_campo_id');
    }

    // Valores guardados en registros aprobados
    public function campovalsProd()
    {
        return $this->hasMany(OpDetRegProdCampoVal::class, 'etapaprod_campo_id');
    }
}
