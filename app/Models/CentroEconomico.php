<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CentroEconomico extends Model
{
    use SoftDeletes;
    protected $table = "centroeconomico";
    protected $fillable = [
        'sucursal_id',
        'nombre',
        'desc',
        'usuario_id',
        'usuariodel_id'
    ];

    //Relacion inversa a Sucursal
    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }

    //RELACION UNO A MUCHOS PERSONA
    public function guiadesps()
    {
        return $this->hasMany(GuiaDesp::class);
    }
    
    
}