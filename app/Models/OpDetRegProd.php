<?php

namespace App\Models;

use App\Models\Seguridad\Usuario;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OpDetRegProd extends Model
{
    use SoftDeletes;
    protected $table = "opdetregprod";
    /**
     * Campos de cantidad/UM — semantica (identica a opdetregprodtemp):
     *   cantent              Cantidad entrada al proceso (en unidadmedidaent_id)
     *   unidadmedidaent_id   UM de entrada (tipicamente kg)
     *   kgent                Kg entrada al proceso (input de la etapa anterior / MP)
     *   kgprod               Kg producidos (salida real) = kgent - kgscrap
     *   kgscrap              Kg descartados en este registro
     *   cantprod             Cantidad producida (en unidadmedidasal_id)
     *   unidadmedidasal_id   UM de salida (rollo, bolsa, pieza, kg, etc.)
     *
     * Invariante: kgent = kgprod + kgscrap
     *
     * Los triggers vtrg_kgregprod_after_* de esta tabla suman cantprod/kgprod/kgscrap
     * a los totales agregados de opdet (opdet.cantprod, opdet.kgprod, opdet.kgscrap).
     */
    protected $fillable = [
        'opdetregprodtemp_id',
        'opdet_id',
        'etapaprod_id',
        'producto_id',
        'sucursal_id',
        'cantent',
        'unidadmedidaent_id',
        'kgent',
        'kgprod',
        'kgscrap',
        'cantprod',
        'unidadmedidasal_id',
        'mtslineal',
        'obs',
        'operario_id',
        'aprobstatus',
        'aprobusu_id',
        'aprobfechahora',
        'aprobobs',
        'usuario_id',
        'usuariodel_id',
    ];
    //RELACION INVERSA opdet
    public function opdet()
    {
        return $this->belongsTo(OpDet::class);
    }
    //RELACION INVERSA Usuario
    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }
    //RELACION UM entrada
    public function unidadmedidaent()
    {
        return $this->belongsTo(UnidadMedida::class, 'unidadmedidaent_id');
    }
    //RELACION UM salida
    public function unidadmedidasal()
    {
        return $this->belongsTo(UnidadMedida::class, 'unidadmedidasal_id');
    }
}
