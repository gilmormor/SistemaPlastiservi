<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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

}
