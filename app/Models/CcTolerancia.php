<?php

namespace App\Models;

/**
 * Traduce la tolerancia guardada en el acuerdo técnico a un rango numérico
 * utilizable por Control de Calidad.
 *
 * El AT guarda la tolerancia como texto ("±3 µ", "±2 CM") en los campos
 * at_espesordesv / at_anchodesv / at_largodesv / at_fuelledesv, calculados al
 * crear el acuerdo. Esta clase los interpreta y arma el rango aceptable
 * alrededor del valor objetivo del propio AT.
 *
 * Escala del espesor: el AT guarda el espesor en la escala 0,010–7 mientras que
 * su tolerancia viene expresada en micras (±3 µ). Son escalas distintas, con un
 * factor de 1000 entre ellas. Como CC teclea el espesor en la misma escala del
 * AT (0,050), la tolerancia se divide por 1000 para poder compararlas.
 * Ancho, largo y fuelle están en centímetros igual que su tolerancia: factor 1.
 */
class CcTolerancia
{
    /**
     * Campos del AT que se pueden usar como objetivo de un parámetro CC.
     * Clave = valor guardado en ccparam_apsucetapaprod.at_campo.
     * 'desv'        = campo del AT con el texto de la tolerancia.
     * 'factor'      = divisor a aplicar a la tolerancia para llevarla a la escala del valor.
     * 'admite_prod' = si el objetivo puede venir del valor de producción en vez del AT.
     *                 Solo el espesor lo admite: otdet no guarda ancho ni largo de producción.
     */
    public static function camposDisponibles()
    {
        return [
            'at_espesor' => ['etiqueta' => 'Espesor del acuerdo técnico', 'desv' => 'at_espesordesv', 'factor' => 1000, 'admite_prod' => true],
            'at_ancho'   => ['etiqueta' => 'Ancho del acuerdo técnico',   'desv' => 'at_anchodesv',   'factor' => 1,    'admite_prod' => false],
            'at_largo'   => ['etiqueta' => 'Largo del acuerdo técnico',   'desv' => 'at_largodesv',   'factor' => 1,    'admite_prod' => false],
            'at_fuelle'  => ['etiqueta' => 'Fuelle del acuerdo técnico',  'desv' => 'at_fuelledesv',  'factor' => 1,    'admite_prod' => false],
        ];
    }

    /**
     * Tolerancia que le corresponde a una medida de ancho o largo, según la tabla
     * de Control de Calidad. Es el equivalente en PHP de desvAnchoLargo() de
     * general.js, que es la que graba el texto al crear o editar un acuerdo técnico.
     *
     * Se usa para detectar acuerdos cuya tolerancia guardada quedó desactualizada
     * (comando acuerdotecnico:corregir-tolerancias). Si algún día cambia la tabla,
     * hay que tocar los dos lugares — mientras cotización siga calculándola en el
     * navegador, no hay una sola fuente posible.
     */
    public static function tramoAnchoLargo($valor)
    {
        $v = (float) $valor;
        if ($v <= 0)   return null;
        if ($v <= 50)  return '±1 CM';
        if ($v <= 150) return '±2 CM';
        if ($v <= 300) return '±3 CM';
        return '±4 CM';
    }

    /**
     * Extrae el número de un texto de tolerancia. "±3 µ" => 3.0 ; "±2 CM" => 2.0
     * Devuelve null si el texto no se puede interpretar: en ese caso el parámetro
     * no se valida, nunca se rechaza por un problema de lectura del sistema.
     */
    public static function interpretar($texto)
    {
        if ($texto === null || trim($texto) === '') {
            return null;
        }
        // Se toma el primer número del texto, admitiendo coma o punto decimal.
        if (!preg_match('/([0-9]+(?:[.,][0-9]+)?)/', $texto, $m)) {
            return null;
        }
        $valor = (float) str_replace(',', '.', $m[1]);
        return $valor > 0 ? $valor : null;
    }

    /**
     * Arma el rango aceptable para un campo del AT.
     *
     * Espesor: el AT guarda el espesor comprometido con el cliente, pero producción
     * fabrica con un espesor algo menor para ahorrar materia prima (otdet.espesorprod).
     * Control de Calidad mide contra el espesor realmente fabricado, así que ese es el
     * objetivo cuando se recibe. La tolerancia sigue siendo la del acuerdo técnico,
     * tal cual quedó guardada, aunque el espesor de producción caiga en otro tramo.
     * Ancho, largo y fuelle no tienen equivalente productivo: siempre van por el AT.
     *
     * @param  object|null $at          acuerdo técnico del producto del lote
     * @param  string      $at_campo    at_espesor | at_ancho | at_largo | at_fuelle
     * @param  float|null  $objetivoAlt espesor de producción, solo aplica a at_espesor
     * @return array|null  null si no se puede calcular
     */
    public static function rango($at, $at_campo, $objetivoAlt = null)
    {
        $campos = self::camposDisponibles();
        if (!$at || !$at_campo || !isset($campos[$at_campo])) {
            return null;
        }

        $cfg = $campos[$at_campo];

        // Valor comprometido en el acuerdo técnico (lo que ve el cliente).
        $objetivoAt = isset($at->{$at_campo}) ? $at->{$at_campo} : null;
        $objetivoAt = ($objetivoAt === null || $objetivoAt === '') ? null : (float) $objetivoAt;

        // Objetivo efectivo: el de producción si vino y el campo lo admite.
        $usaProd  = $cfg['admite_prod'] && $objetivoAlt !== null && (float) $objetivoAlt > 0;
        $objetivo = $usaProd ? (float) $objetivoAlt : $objetivoAt;

        // Sin valor objetivo no hay nada contra qué comparar (ej. productos sin largo).
        if ($objetivo === null || $objetivo <= 0) {
            return null;
        }

        $tolTexto = isset($at->{$cfg['desv']}) ? $at->{$cfg['desv']} : null;
        $tol = self::interpretar($tolTexto);
        if ($tol === null) {
            return null;
        }

        // Se lleva la tolerancia a la escala en que está guardado el valor objetivo.
        $tol = $tol / $cfg['factor'];

        return [
            'objetivo'    => $objetivo,
            'objetivo_at' => $objetivoAt,
            'usa_prod'    => $usaProd,
            'tolerancia'  => $tol,
            'min'         => $objetivo - $tol,
            'max'         => $objetivo + $tol,
            'texto'       => $tolTexto,
        ];
    }

    /**
     * Espesor de producción del ítem de OT del que proviene un lote.
     * Es el que el jefe de producción fijó al enviar el ítem a Programación.
     * Devuelve null si no está cargado, y entonces se usa el del acuerdo técnico.
     */
    public static function espesorProdDeLote($opdetregprod_id)
    {
        if (!$opdetregprod_id) {
            return null;
        }
        $r = \Illuminate\Support\Facades\DB::selectOne("
            SELECT otdet.espesorprod
            FROM   opdetregprod odrp
            INNER  JOIN opdet ON opdet.id = odrp.opdet_id
            INNER  JOIN op    ON op.id    = opdet.op_id
            INNER  JOIN otdet ON otdet.id = op.otdet_id
            WHERE  odrp.id = ?
            LIMIT  1
        ", [$opdetregprod_id]);

        return ($r && $r->espesorprod > 0) ? (float) $r->espesorprod : null;
    }

    /**
     * Decimales necesarios para que un conjunto de valores no se vea redondeado.
     * El espesor se maneja en milésimas y los parámetros CC suelen estar configurados
     * con 2 decimales, con lo que 0,025 y 0,031 se mostraban ambos como "0,03".
     */
    public static function decimalesNecesarios(array $valores, $minimo = 2, $tope = 4)
    {
        $dec = $minimo;
        foreach ($valores as $v) {
            if ($v === null || $v === '') continue;
            // Decimales significativos del valor, sin ceros finales.
            $txt = rtrim(rtrim(number_format((float) $v, $tope, '.', ''), '0'), '.');
            $pos = strpos($txt, '.');
            if ($pos !== false) {
                $dec = max($dec, strlen($txt) - $pos - 1);
            }
        }
        return min($dec, $tope);
    }

    /**
     * Acuerdo técnico vigente de un producto, o null si no tiene.
     */
    public static function atDeProducto($producto_id)
    {
        if (!$producto_id) {
            return null;
        }
        return AcuerdoTecnico::where('producto_id', $producto_id)
            ->whereNull('deleted_at')
            ->orderBy('id', 'desc')
            ->first();
    }
}
