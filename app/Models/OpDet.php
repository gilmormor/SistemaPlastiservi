<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OpDet extends Model
{
    use SoftDeletes;
    protected $table = "opdet";
    protected $fillable = [
        'op_id',
        'otdet_id',
        'apsucetapaprod_id',
        'obs',
        'kg',
        'cant',
        'kgprod',
        'cantprod',
        'saldokg',
        'kgscrap',
        'mtslineal',
        'fechafin',
        'usuariodel_id'
    ];

    //RELACION INVERSA op
    public function op()
    {
        return $this->belongsTo(Op::class)->whereDoesntHave('opanul');
    }
    //RELACION INVERSA areaproduccionsucetapaprod
    public function areaproduccionsucetapaprod()
    {
        return $this->belongsTo(AreaProduccionSucEtapaProd::class,"apsucetapaprod_id");
    }

    //RELACION UNO A UNO OpDetMaquina
    public function opdetmaquina()
    {
        return $this->hasOne(OpDetMaquina::class,'opdet_id','maquina_id');
    }

    //RELACION DE UNO A MUCHOS OpDetRegProdTemp
    public function opdetregprodtemps()
    {
        return $this->hasMany(OpDetRegProdTemp::class,'opdet_id');
    }

    //RELACION UNO A UNO OtDetNVDet
    public function otdetnvdet()
    {
        return $this->hasOne(OtDetNVDet::class,'opdet_id');
    }

    /**
     * Obtiene los totales de producción pendientes de aprobación asociados al registro actual de OpDet.
     *
     * Esta función calcula las sumas totales de los campos `cant`, `kg`, `kgscrap` y `mtslineal`
     * en la tabla `opdetregprodtemp`, únicamente considerando los registros que:
     *  - No han sido eliminados (campo `deleted_at` es NULL)
     *  - No han sido aprobados por el supervisor (`aprobstatus` != 2)
     *
     * Se utiliza como un atributo dinámico de Eloquent (Accessor) y permite acceder
     * a los totales pendientes como una propiedad del modelo `OpDet`.
     *
     * Ejemplo de uso:
     * ```php
     * $opdet = OpDet::findOrFail($id);
     * $totales = $opdet->totales_pendientes;
     *
     * echo $totales->total_cant;      // Total de unidades pendientes
     * echo $totales->total_kg;        // Total de kilos pendientes
     * echo $totales->total_kgscrap;   // Total de scrap pendiente
     * echo $totales->total_mtslineal; // Total de metros lineales pendientes
     * ```
     *
     * @return \stdClass  Objeto con los campos:
     *                    - total_cant
     *                    - total_kg
     *                    - total_kgscrap
     *                    - total_mtslineal
     */
    public function getTotalesPendientesAttribute()
    {
        return $this->opdetregprodtemps()
            ->whereNull('deleted_at')                 // Ignorar registros eliminados
            ->where(function($q) {                    // Excluir los aprobados
                $q->where('aprobstatus', '!=', 2)
                ->orWhereNull('aprobstatus');
            })
            ->selectRaw('
                COALESCE(SUM(cant), 0) as total_cant,
                COALESCE(SUM(kg), 0) as total_kg,
                COALESCE(SUM(kgscrap), 0) as total_kgscrap,
                COALESCE(SUM(mtslineal), 0) as total_mtslineal
            ')
            ->first();
    }
}
