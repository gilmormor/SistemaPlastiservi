<?php

namespace App\Models;

use App\Models\Seguridad\Usuario;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UsuAprobModulo extends Model
{
    use SoftDeletes;
    protected $table = "usuaprobmodulo";
    protected $fillable = [
        'usuario_id',
        'modulo',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    // Usuarios creadores que el usuario_id (aprobador restringido) puede aprobar para este modulo
    public function usuariosPermitidos()
    {
        return $this->belongsToMany(Usuario::class, 'usuaprobmoduledet', 'usuaprobmodulo_id', 'usuario_creador_id')->withTimestamps();
    }

    // Bodegas que el usuario_id (aprobador restringido) puede aprobar. Solo aplica al modulo inventsalaprobar.
    // Opcional: si no tiene ninguna bodega asociada, no hay restriccion por bodega (puede aprobar cualquiera).
    public function bodegasAprobEntSalInv()
    {
        return $this->belongsToMany(InvBodega::class, 'usuaprobmodulobodega', 'usuaprobmodulo_id', 'invbodega_id')->withTimestamps();
    }

    /**
     * Determina si $usuarioAprobadorId puede aprobar un registro creado por $usuarioCreadorId
     * en el modulo (url) indicado. Si el usuario aprobador no tiene restriccion configurada
     * para ese modulo, puede aprobar todo (comportamiento por defecto).
     */
    public static function puedeAprobar($usuarioAprobadorId, $modulo, $usuarioCreadorId)
    {
        $restriccion = self::where('usuario_id', $usuarioAprobadorId)
            ->where('modulo', $modulo)
            ->first();

        if (!$restriccion) {
            return true;
        }

        return $restriccion->usuariosPermitidos()->where('usuario.id', $usuarioCreadorId)->exists();
    }

    /**
     * Determina si $usuarioAprobadorId puede aprobar un movimiento cuyas lineas pertenecen
     * a las bodegas $bodegaIds, en el modulo indicado (pensado para inventsalaprobar).
     * Si el usuario no tiene restriccion configurada, o si tiene restriccion pero no configuro
     * ninguna bodega, puede aprobar cualquier bodega (sin restriccion). Si configuro bodegas,
     * TODAS las bodegas de $bodegaIds deben estar dentro de las permitidas.
     */
    public static function puedeAprobarBodegas($usuarioAprobadorId, $modulo, array $bodegaIds)
    {
        $restriccion = self::where('usuario_id', $usuarioAprobadorId)
            ->where('modulo', $modulo)
            ->first();

        if (!$restriccion) {
            return true;
        }

        $bodegasPermitidas = $restriccion->bodegasAprobEntSalInv()->pluck('invbodega.id')->toArray();
        if (empty($bodegasPermitidas)) {
            return true;
        }

        return count(array_diff($bodegaIds, $bodegasPermitidas)) === 0;
    }
}
