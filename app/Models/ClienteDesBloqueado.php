<?php

namespace App\Models;

use App\Models\Seguridad\Usuario;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClienteDesBloqueado extends Model
{
    use SoftDeletes;
    protected $table = "clientedesbloqueado";
    protected $fillable = [
        'obs',
        'cliente_id',
        'usuario_id',
        'usuariodel_id'
    ];

    //RELACION INVERSA CLIENTE
    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    //Relacion inversa a Usuario
    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }

    //Relacion inversa a User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
