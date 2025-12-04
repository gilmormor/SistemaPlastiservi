<?php

namespace App\Models;

use App\Models\Seguridad\Usuario;
use Illuminate\Database\Eloquent\Model;

class LogCambio extends Model
{
    protected $table = "logcambio";
    protected $fillable = [
        'tabla',
        'tabla_id',
        'operacion',
        'cambios',
        'ip',
        'usuario_id'
    ];
    //Relacion inversa a Usuario
    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }
}
