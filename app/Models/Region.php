<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Region extends Model
{
    use SoftDeletes;
    protected $table = "region";
    protected $fillable = ['nombre','ordinal','orden','usuariodel_id'];

    public function provincia()
    {
        return $this->hasMany(Provincia::class);
    }
    //Relacion de uno a Muchos con Cliente
    public function clientes()
    {
        return $this->hasMany(Cliente::class,'regionp_id','region_id');
    }

}
