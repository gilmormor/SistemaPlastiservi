<?php

namespace App\Models;

use App\Models\Seguridad\Usuario;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransVeh extends Model
{
    use SoftDeletes;

    protected $table = 'transveh';
    protected $fillable = [
        'patente', 
        'usuario_id', 
        'usuariodel_id'
    ];
    // Relación con el usuario que lo creó
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }
}
