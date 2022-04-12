<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvMovModulo extends Model
{
    use SoftDeletes;
    protected $table = "invmovmodulo";
    protected $fillable = [
        'nombre',
        'desc',
        'cod',
        'usuario_id',
        'usuariodel_id'
    ];

    //RELACION DE UNO A VARIOS InvMovDet
    public function invmovdets()
    {
        return $this->hasMany(InvMovDet::class);
    }

    public function invmovmodulobodsals()
    {
        return $this->belongsToMany(InvBodega::class, 'invmovmodulobodsal','invmovmodulo_id','invbodega_id')->withTimestamps();
    }

    public function invmovmodulobodents()
    {
        return $this->belongsToMany(InvBodega::class, 'invmovmodulobodent','invmovmodulo_id','invbodega_id')->withTimestamps();
    }


}
