<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClienteBloqueado extends Model
{
    use SoftDeletes;
    protected $table = "clientebloqueado";
    protected $fillable = [
        'descripcion',
        'cliente_id'
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
