<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

// Importaciones para relaciones CC, Campos adicionales y Bodegas de inventario
use App\Models\ApsucEtapaProdBodega;
use App\Models\CcParamApsucetapaprod;
use App\Models\EtapaProdCampo;

class AreaProduccionSucEtapaProd extends Model
{
    protected $table = "areaproduccionsucetapaprod";
    protected $fillable = [
        'areaproduccionsuc_id',
        'etapaprod_id',
        'unidadmedida_id',     // UM de SALIDA de la etapa
        'orden',
        'requiere_kg',
        'requiere_cc',
        'usa_matprima',
    ];

    /**
     * UM de ENTRADA = UM de SALIDA de la etapa con orden-1 dentro
     * del mismo areaproduccionsuc_id. Null si es la primera etapa.
     */
    public function unidadmedidaEntrada()
    {
        $prev = self::where('areaproduccionsuc_id', $this->areaproduccionsuc_id)
            ->where('orden', '<', $this->orden)
            ->orderBy('orden', 'desc')
            ->first();
        if (!$prev) return null;
        return $prev->unidadmedida; // puede ser null si la anterior no tiene UM configurada
    }

    public function getUnidadmedidaEntradaIdAttribute()
    {
        $um = $this->unidadmedidaEntrada();
        return $um ? $um->id : null;
    }

    public function getUnidadmedidaEntradaNombreAttribute()
    {
        $um = $this->unidadmedidaEntrada();
        return $um ? $um->nombre : null;
    }

    /**
     * UM de SALIDA y de ENTRADA de esta etapa PARA UN PRODUCTO.
     *
     * La unidad de salida es propia del producto: de Sellado un producto puede
     * salir en metros y otro en rollos aunque los dos entren en kilos. Por eso
     * manda acuerdotecnicoapsucetapaprod.unidadmedidasal_id y no la UM de la
     * etapa. Si el producto no tiene nada configurado se usa la UM de la etapa,
     * para que los productos que nunca se configuraron sigan funcionando igual.
     *
     * La entrada es la salida de la etapa ANTERIOR *del producto* (la anterior
     * dentro de las etapas asignadas a su acuerdo tecnico), no la anterior del
     * area: un producto puede saltarse etapas del area.
     *
     * Devuelve ['sal_id','sal_nombre','ent_id','ent_nombre']; ent_* en null
     * cuando es la primera etapa.
     */
    public static function umProducto($producto_id, $apsucetapaprod_id)
    {
        $vacio = ['sal_id' => null, 'sal_nombre' => null,
                  'ent_id' => null, 'ent_nombre' => null,
                  'cant_id' => null, 'cant_nombre' => null];

        $etapa = self::find($apsucetapaprod_id);
        if (!$etapa) {
            return $vacio;
        }

        /* Respaldo: la configuracion de la etapa, que es lo que se usaba antes
           de que la unidad pudiera definirse por producto. */
        $sal_id = $etapa->unidadmedida_id;
        $umEntAnterior = $etapa->unidadmedidaEntrada();
        $ent_id = $umEntAnterior ? $umEntAnterior->id : null;

        /* Contar aparte no tiene respaldo por etapa: o esta configurado para
           este producto o no se pide. */
        $cant_id = null;

        $at = DB::table('acuerdotecnico')
            ->where('producto_id', $producto_id)
            ->whereNull('deleted_at')
            ->orderBy('id', 'desc')
            ->first();

        if ($at) {
            /* Etapas asignadas al producto, en el orden en que las recorre. */
            $etapasAt = DB::select(
                "SELECT ap.id, ap.orden, ap.unidadmedida_id AS um_etapa,
                        a.unidadmedidasal_id AS um_at,
                        a.unidadmedidacant_id AS um_cant
                   FROM acuerdotecnicoapsucetapaprod a
                   INNER JOIN areaproduccionsucetapaprod ap ON ap.id = a.apsucetapaprod_id
                  WHERE a.acuerdotecnico_id = ?
                    AND ap.areaproduccionsuc_id = ?
                  ORDER BY ap.orden",
                [$at->id, $etapa->areaproduccionsuc_id]
            );

            $anterior = null;
            foreach ($etapasAt as $e) {
                if ((int) $e->id === (int) $apsucetapaprod_id) {
                    // la UM del producto manda; si quedo sin definir, la de la etapa
                    $sal_id = $e->um_at ?: $e->um_etapa;
                    $ent_id = $anterior ? ($anterior->um_at ?: $anterior->um_etapa) : null;
                    $cant_id = $e->um_cant ?: null;
                    break;
                }
                $anterior = $e;
            }
        }

        return [
            'sal_id'     => $sal_id,
            'sal_nombre' => self::nombreUm($sal_id),
            'ent_id'      => $ent_id,
            'ent_nombre'  => self::nombreUm($ent_id),
            'cant_id'     => $cant_id,
            'cant_nombre' => self::nombreUm($cant_id),
        ];
    }

    /** Nombre de una unidad de medida, o null si no existe. */
    private static function nombreUm($id)
    {
        if (!$id) return null;
        $um = UnidadMedida::find($id);
        return $um ? $um->nombre : null;
    }
    //RELACION INVERSA PARA BUSCAR EL PADRE
    public function areaproduccionsuc()
    {
        return $this->belongsTo(AreaProduccionSuc::class,'areaproduccionsuc_id');
    }

    //RELACION INVERSA PARA BUSCAR EL PADRE
    public function etapaprod()
    {
        return $this->belongsTo(EtapaProd::class,'etapaprod_id');
    }
    //RELACION INVERSA PARA BUSCAR EL PADRE
    public function unidadmedida()
    {
        return $this->belongsTo(UnidadMedida::class,'unidadmedida_id');
    }

    // Campos adicionales configurados para esta etapa (ordenados por 'orden')
    public function campos()
    {
        return $this->hasMany(EtapaProdCampo::class, 'apsucetapaprod_id')
                    ->orderBy('orden');
    }

    // Parámetros CC configurados para esta etapa
    public function ccparamApsucetapaprod()
    {
        return $this->hasMany(CcParamApsucetapaprod::class, 'apsucetapaprod_id')
                    ->orderBy('orden');
    }

    // Bodegas de inventario asignadas a esta etapa (para generar movimientos al aprobar)
    public function bodegas()
    {
        return $this->hasMany(ApsucEtapaProdBodega::class, 'apsucetapaprod_id');
    }

}
