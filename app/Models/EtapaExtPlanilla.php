<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Seguridad\Usuario;   // la clase Usuario vive en el namespace Seguridad

/**
 * Planilla impresa para el proceso que se ejecuta fuera del sistema entre una
 * etapa de produccion y la siguiente. Ver la migracion de la tabla para el
 * contexto completo. El ancla es el lote (opdetregprod_id).
 */
class EtapaExtPlanilla extends Model
{
    use SoftDeletes;

    protected $table = "etapaextplanilla";
    protected $dates = ['deleted_at'];
    protected $fillable = [
        'opdetregprod_id',
        'etapaprod_id',
        'maquina_id',
        'operario_id',
        'nrobolsas',
        'obs',
        'usuario_id',
        'usuariodel_id'
    ];

    // Lote de origen que se va a procesar
    public function opdetregprod()
    {
        return $this->belongsTo(OpDetRegProd::class, 'opdetregprod_id');
    }

    // Etapa en la que se procesa (la siguiente del lote)
    public function etapaprod()
    {
        return $this->belongsTo(EtapaProd::class, 'etapaprod_id');
    }

    // Maquina asignada por el supervisor para procesar
    public function maquina()
    {
        return $this->belongsTo(Maquina::class, 'maquina_id');
    }

    // Responsable del equipo
    public function operario()
    {
        return $this->belongsTo(Operario::class, 'operario_id');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    // Tiradas de etiquetas impresas a partir de esta planilla
    public function etiquetas()
    {
        return $this->hasMany(EtapaExtEtiqueta::class, 'etapaextplanilla_id');
    }
}
