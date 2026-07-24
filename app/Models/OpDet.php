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
        return $this->hasOne(OpDetMaquina::class,'opdet_id','id');
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
     * Calcula las sumas totales pendientes (opdetregprodtemp no aprobado) para este opdet.
     * Considera los registros que:
     *  - No han sido eliminados (deleted_at IS NULL)
     *  - No han sido aprobados por supervisor (aprobstatus != 2)
     *
     * @return \stdClass Campos expuestos:
     *  - total_cantprod  Suma de opdetregprodtemp.cantprod (cant. producida en UM salida pendiente)
     *  - total_kgprod    Suma de opdetregprodtemp.kgprod   (kg producidos pendientes)
     *  - total_kgent     Suma de opdetregprodtemp.kgent    (kg entrada comprometidos)
     *  - total_kgscrap   Suma de opdetregprodtemp.kgscrap
     *  - total_mtslineal Suma de opdetregprodtemp.mtslineal
     *
     * Aliases legacy (mantener compatibilidad con codigo que aun usa los nombres antiguos):
     *  - total_cant  ==  total_cantprod  (era opdetregprodtemp.cant, hoy cantprod)
     *  - total_kg    ==  total_kgprod    (era opdetregprodtemp.kg,   hoy kgprod)
     */
    public function getTotalesPendientesAttribute()
    {
        return $this->opdetregprodtemps()
            ->whereNull('deleted_at')
            ->where('es_muestra', 0)  // R1: las muestras físicas no suman a producción
            ->where(function($q) {
                $q->where('aprobstatus', '!=', 2)
                ->orWhereNull('aprobstatus');
            })
            ->selectRaw('
                COALESCE(SUM(cantprod),  0) as total_cantprod,
                COALESCE(SUM(kgprod),    0) as total_kgprod,
                COALESCE(SUM(kgent),     0) as total_kgent,
                COALESCE(SUM(kgscrap),   0) as total_kgscrap,
                COALESCE(SUM(mtslineal), 0) as total_mtslineal,
                COALESCE(SUM(cantprod),  0) as total_cant,
                COALESCE(SUM(kgprod),    0) as total_kg
            ')
            ->first();
    }
}
