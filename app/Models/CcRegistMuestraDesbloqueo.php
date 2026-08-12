<?php

namespace App\Models;

use App\Models\Seguridad\Usuario;
use Illuminate\Database\Eloquent\Model;

class CcRegistMuestraDesbloqueo extends Model
{
    protected $table = 'ccregistmuestra_desbloqueo';

    protected $fillable = [
        'ccregistmuestra_id',
        'observacion',
        'usuario_id',
    ];

    public function ccregistmuestra()
    {
        return $this->belongsTo(CcRegistMuestra::class, 'ccregistmuestra_id');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }
}
