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
        'mostrarnv',
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

    //RELACION DE UNO A MUCHOS DTE
    public function dtes()
    {
        return $this->hasMany(Dte::class);
    }
    
    //RELACION DE UNO A MUCHOS NotaVenta
    public function notaventas()
    {
        return $this->hasMany(NotaVenta::class);
    }
    
}