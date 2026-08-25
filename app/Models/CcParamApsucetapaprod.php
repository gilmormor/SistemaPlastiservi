<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CcParamApsucetapaprod extends Model
{
    use SoftDeletes;
    protected $table = 'ccparam_apsucetapaprod';
    protected $fillable = [
        'apsucetapaprod_id',
        'ccparam_id',
        'valor_min',
        'valor_max',
        // Campo del acuerdo técnico contra el que se valida (ver CcTolerancia).
        // Vacío = rango fijo valor_min / valor_max, como siempre.
        'at_campo',
        'requerido',
        'orden',
        'usuariodel_id',
    ];

    public function apsucetapaprod()
    {
        return $this->belongsTo(AreaProduccionSucEtapaProd::class, 'apsucetapaprod_id');
    }

    public function ccparam()
    {
        return $this->belongsTo(CcParam::class, 'ccparam_id');
    }

    public function ccregistmuestradet()
    {
        return $this->hasMany(CcRegistMuestraDet::class, 'ccparam_apsucetapaprod_id');
    }
}
