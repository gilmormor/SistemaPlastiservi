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
        // Rango contra el que se validó, congelado al momento de medir. Solo se
        // llenan cuando el rango viene del acuerdo técnico (ver CcTolerancia).
        'valor_objetivo',
        'tolerancia',
        'rango_min',
        'rango_max',
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
     *
     * Se mantiene la firma original para no romper las llamadas existentes; delega
     * en evaluar(), que es la que además devuelve el rango usado.
     */
    public static function calcularResultado($valor, CcParamApsucetapaprod $param, $at = null, $espesorProd = null)
    {
        $e = self::evaluar($valor, $param, $at, $espesorProd);
        return $e['resultado'];
    }

    /**
     * Evalúa un valor medido y devuelve tanto el resultado como el rango contra el
     * que se comparó, para poder guardarlo junto al detalle de la muestra.
     *
     * Si el parámetro tiene at_campo configurado, el rango sale del acuerdo técnico
     * del producto (objetivo ± tolerancia). Si no lo tiene, o si el AT no permite
     * calcularlo (sin acuerdo técnico, campo vacío o tolerancia ilegible), se usa
     * el rango fijo valor_min / valor_max de siempre. Nunca se rechaza por no poder
     * leer la tolerancia.
     *
     * $espesorProd es el espesor con el que realmente se fabricó (otdet.espesorprod);
     * cuando viene, manda sobre el del acuerdo técnico para el parámetro de espesor.
     *
     * @return array ['resultado','objetivo','tolerancia','min','max']
     */
    public static function evaluar($valor, CcParamApsucetapaprod $param, $at = null, $espesorProd = null)
    {
        $salida = [
            'resultado'  => 1,
            'objetivo'   => null,
            'tolerancia' => null,
            'min'        => null,
            'max'        => null,
        ];

        if ($valor === null || $valor === '') {
            return $salida;
        }

        // Cumple / No Cumple: "No Cumple" (0) es un rechazo. Antes este tipo no se
        // evaluaba y siempre devolvia OK, con lo que marcar "No Cumple" aprobaba igual.
        if ($param->ccparam->tipo === 'boolean') {
            $salida['resultado'] = ((string) $valor === '0') ? 3 : 1;
            return $salida;
        }

        // Texto libre: no hay criterio con el que compararlo, no se evalua.
        if ($param->ccparam->tipo !== 'number') {
            return $salida;
        }

        $v = (float) str_replace(',', '.', $valor);

        // Rango desde el acuerdo técnico, si el parámetro está configurado así.
        $rango = null;
        if (!empty($param->at_campo)) {
            $rango = CcTolerancia::rango($at, $param->at_campo, $espesorProd);
        }

        if ($rango) {
            $salida['objetivo']   = $rango['objetivo'];
            $salida['tolerancia'] = $rango['tolerancia'];
            $salida['min']        = $rango['min'];
            $salida['max']        = $rango['max'];
        } else {
            // Comportamiento original: rango fijo configurado en la etapa.
            $salida['min'] = $param->valor_min !== null ? (float) $param->valor_min : null;
            $salida['max'] = $param->valor_max !== null ? (float) $param->valor_max : null;
        }

        if ($salida['min'] !== null && $v < $salida['min']) $salida['resultado'] = 3;
        if ($salida['max'] !== null && $v > $salida['max']) $salida['resultado'] = 3;

        return $salida;
    }
}
