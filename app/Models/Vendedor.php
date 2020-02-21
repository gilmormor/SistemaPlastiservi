<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vendedor extends Model
{
    use SoftDeletes;
    protected $table = "vendedor";
    protected $fillable = [
        'persona_id',
        'usuariodel_id',
        'sta_activo'
    ];

    public function clientess()
    {
        return $this->hasMany(Cliente::class);
    }
    public function sucursalclientedirecs()
    {
        return $this->hasMany(SucursalClienteDirec::class);
    }

    //Relacion inversa a Persona
    public function persona()
    {
        return $this->belongsTo(Persona::class);
    }
    //RELACION UNO A MUCHOS Cotizacion
    public function cotizacions()
    {
        return $this->hasMany(Cotizacion::class);
    }

    //RELACION MUCHO A MUCHOS CON USUARIO A TRAVES DE cliente_vendedor
    public function clientes()
    {
        return $this->belongsToMany(Cliente::class, 'cliente_vendedor');
    }

    public function clientetemps()
    {
        return $this->hasMany(ClienteTemp::class);
    }

}
