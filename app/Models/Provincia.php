<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Provincia extends Model
{
    use SoftDeletes;
    protected $table = "provincia";
    protected $fillable = ['nombre','region_id','usuariodel_id'];

    public function comuna()
    {
        return $this->hasMany(Comuna::class);
    }
    //RELACION INVERSA PARA BUSCAR EL PADRE
    public function region()
    {
        return $this->belongsTo(Region::class);
    }
    //Relacion de uno a Muchos con Cliente
    public function clientes()
    {
        return $this->hasMany(Cliente::class,'provinciap_id','provincia_id');
    }
}
