<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Valores de campos adicionales copiados al aprobar un registro de producción.
 * Espejo definitivo de OpDetRegProdTempCampoVal una vez que el supervisor aprueba.
 */
class OpDetRegProdCampoVal extends Model
{
    protected $table = 'opdetregprod_campoval';

    protected $fillable = [
        'opdetregprod_id',
        'etapaprod_campo_id',
        'valor',
    ];

    public function opdetregprod()
    {
        return $this->belongsTo(OpDetRegProd::class, 'opdetregprod_id');
    }

    public function campo()
    {
        return $this->belongsTo(EtapaProdCampo::class, 'etapaprod_campo_id');
    }
}
