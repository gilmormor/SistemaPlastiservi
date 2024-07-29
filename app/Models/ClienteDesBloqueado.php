<?php

namespace App\Models;

use App\Models\Seguridad\Usuario;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClienteDesBloqueado extends Model
{
    use SoftDeletes;
    protected $table = "clientedesbloqueado";
    protected $fillable = [
        'obs',
        'cliente_id',
        'notaventa_id',
        'cotizacion_id',
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

    //RELACION INVERSA NOTAVENTA
    public function notaventa()
    {
        return $this->belongsTo(NotaVenta::class);
    }

    //RELACION INVERSA COTIZACION
    public function cotizacion()
    {
        return $this->belongsTo(Cotizacion::class);
    }
    
    //RELACION MUCHO A MUCHOS CON MODULO A TRAVES DE clientebloqueadomodulo
    public function modulos()
    {
        return $this->belongsToMany(Modulo::class, 'clientedesbloqueadomodulo','clientedesbloqueado_id')
                    ->withTimestamps();
    }
    //RELACION DE UNO A MUCHOS ClienteDesBloqueadoModulo
    public function clientedesbloqueadomodulos()
    {
        return $this->hasMany(ClienteDesbloqueadoModulo::class,'clientedesbloqueado_id');
    }
    
}
