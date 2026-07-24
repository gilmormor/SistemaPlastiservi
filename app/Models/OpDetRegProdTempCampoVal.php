<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Valores de campos adicionales ingresados por el operario
 * en un registro de producción temporal (antes de aprobación supervisora).
 */
class OpDetRegProdTempCampoVal extends Model
{
    protected $table = 'opdetregprodtemp_campoval';

    protected $fillable = [
        'opdetregprodtemp_id',
        'etapaprod_campo_id',
        'valor',
    ];

    public function opdetregprodtemp()
    {
        return $this->belongsTo(OpDetRegProdTemp::class, 'opdetregprodtemp_id');
    }

    public function campo()
    {
        return $this->belongsTo(EtapaProdCampo::class, 'etapaprod_campo_id');
    }
}
