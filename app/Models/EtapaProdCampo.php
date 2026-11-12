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
        'mapea_campo', // campo estándar de opdetregprodtemp que recibe este valor (nullable)
    ];

    protected $casts = [
        'requerido'  => 'boolean',
        'decimales'  => 'integer',
        'orden'      => 'integer',
    ];

    /**
     * Variables que una formula puede nombrar ademas de los otros campos de
     * la misma etapa. Son las que el formulario de registro carga en
     * window._etapaCtx (ver opdetregprodtemp/_campos_adicionales.blade.php) y
     * las dos cantidades que el operario escribe en el formulario principal.
     *
     * Esta lista es la unica fuente: la usa la validacion al guardar una
     * formula y la ayuda que se muestra al configurarla. Si se agrega una
     * variable al evaluador hay que agregarla aqui tambien, si no la
     * validacion la va a rechazar.
     */
    const VARIABLES_CONTEXTO = [
        'ancho'         => 'Ancho del producto segun su acuerdo tecnico',
        'largo'         => 'Largo del producto segun su acuerdo tecnico',
        'espesor'       => 'Espesor de PRODUCCION si la OT lo define; si no, el del acuerdo tecnico',
        'pe'            => 'Peso especifico de la materia prima',
        'peso_unitario' => 'Kilos que pesa una unidad, ya calculado con el espesor de produccion',
        'doble'         => 'Capas de lamina de una unidad: 2 si va doblada, 1 si es lamina',
        'kgprod'        => 'Kilos producidos que el operario escribe en el formulario',
        'kgscrap'       => 'Kilos de scrap que el operario escribe en el formulario',
    ];

    /**
     * Campos estandar del registro de produccion a los que un campo puede
     * volcar su valor.
     */
    const CAMPOS_ESTANDAR = ['cantprod', 'kgprod', 'kgscrap', 'cantunid'];

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
