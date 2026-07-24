<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CcRegistMuestraDet extends Model
{
    protected $table = 'ccregistmuestradet';
    protected $fillable = [
        'ccregistmuestra_id',
        'ccparam_apsucetapaprod_id',
        'valor',
        'resultado',
    ];

    public function ccregistmuestra()
    {
        return $this->belongsTo(CcRegistMuestra::class, 'ccregistmuestra_id');
    }

    public function ccparamApsucetapaprod()
    {
        return $this->belongsTo(CcParamApsucetapaprod::class, 'ccparam_apsucetapaprod_id');
    }

    /**
     * Calcula el resultado comparando el valor ingresado con los rangos min/max del parámetro.
     * 1=Ok, 2=Observación (sin rango definido pero con valor), 3=Fuera de rango.
     */
    public static function calcularResultado($valor, CcParamApsucetapaprod $param)
    {
        if ($param->ccparam->tipo === 'number' && $valor !== null && $valor !== '') {
            $v = (float) str_replace(',', '.', $valor);
            $tieneMin = $param->valor_min !== null;
            $tieneMax = $param->valor_max !== null;
            if ($tieneMin && $v < (float) $param->valor_min) return 3;
            if ($tieneMax && $v > (float) $param->valor_max) return 3;
        }
        return 1;
    }
}
