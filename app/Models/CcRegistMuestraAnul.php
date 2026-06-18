<?php

namespace App\Models;

use App\Models\Seguridad\Usuario;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CcRegistMuestraAnul extends Model
{
    use SoftDeletes;
    protected $table = 'ccregistmuestraanul';
    protected $fillable = [
        'ccregistmuestra_id',
        'motivo',
        'usuario_id',
        'usuariodel_id',
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
