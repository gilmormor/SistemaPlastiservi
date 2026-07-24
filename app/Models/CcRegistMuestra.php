<?php

namespace App\Models;

use App\Models\Seguridad\Usuario;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CcRegistMuestra extends Model
{
    use SoftDeletes;
    protected $table = 'ccregistmuestra';
    protected $fillable = [
        'opdetregprod_id',
        'fechahora',
        'usuario_id',
        'status',
        'observacion',
        'sta_env',
        'sta_env_obs',
        'fechahora_env',
        'usuariostaenv_id',
        'usuariodel_id',
    ];

    public function opdetregprod()
    {
        return $this->belongsTo(OpDetRegProd::class, 'opdetregprod_id');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    public function usuarioStaenv()
    {
        return $this->belongsTo(Usuario::class, 'usuariostaenv_id');
    }

    public function dets()
    {
        return $this->hasMany(CcRegistMuestraDet::class, 'ccregistmuestra_id');
    }

    public function anulacion()
    {
        return $this->hasOne(CcRegistMuestraAnul::class, 'ccregistmuestra_id');
    }

    public function desbloqueo()
    {
        return $this->hasOne(CcRegistMuestraDesbloqueo::class, 'ccregistmuestra_id');
    }

    /**
     * Calcula el status automáticamente a partir de los detalles.
     * 1=Aprobado, 2=Aprobado con obs, 3=Rechazado.
     * Usa el peor resultado entre todos los detalles.
     */
    public function calcularStatus()
    {
        $peor = 1;
        foreach ($this->dets as $det) {
            if ($det->resultado > $peor) {
                $peor = $det->resultado;
            }
        }
        return $peor;
    }
}
