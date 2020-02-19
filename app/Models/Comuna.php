<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comuna extends Model
{
    use SoftDeletes;
    protected $table = "comuna";
    protected $fillable = ['nombre','provincia_id','usuariodel_id'];

    //RELACION INVERSA PARA BUSCAR EL PADRE
    public function provincia()
    {
        return $this->belongsTo(Provincia::class);
    }
    //Relacion de uno a Muchos con Cliente
    public function clientes()
    {
        return $this->hasMany(Cliente::class,'comunap_id','comuna_id');
    }
    //Relacion de uno a Muchos con ClienteTemp
    public function clientetemps()
    {
        return $this->hasMany(ClienteTemp::class,'comunap_id','comuna_id');
    }

}
