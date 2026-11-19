<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Seguridad\Usuario;   // la clase Usuario vive en el namespace Seguridad

/**
 * Tirada de etiquetas de paquete del proceso que se ejecuta fuera del sistema.
 * Una fila por tirada (cantidad = copias impresas), no una por etiqueta.
 * kilos y unidades son POR PAQUETE. Ver la migracion para el contexto.
 */
class EtapaExtEtiqueta extends Model
{
    use SoftDeletes;

    protected $table = "etapaextetiqueta";
    protected $dates = ['deleted_at', 'fecha'];
    protected $fillable = [
        'etapaextplanilla_id',
        'opdetregprod_id',
        'etapaprod_id',
        'maquina_id',
        'operario_id',
        'fecha',
        'turno',
        'kilos',
        'unidades',
        'obs',
        'cantidad',
        'usuario_id',
        'usuariodel_id'
    ];

    // Planilla de la que salio. Puede ser null: imprimir etiquetas no exige
    // que antes se haya impreso la planilla (ver nota en la migracion).
    public function etapaextplanilla()
    {
        return $this->belongsTo(EtapaExtPlanilla::class, 'etapaextplanilla_id');
    }

    public function opdetregprod()
    {
        return $this->belongsTo(OpDetRegProd::class, 'opdetregprod_id');
    }

    public function etapaprod()
    {
        return $this->belongsTo(EtapaProd::class, 'etapaprod_id');
    }

    public function maquina()
    {
        return $this->belongsTo(Maquina::class, 'maquina_id');
    }

    public function operario()
    {
        return $this->belongsTo(Operario::class, 'operario_id');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }
}
