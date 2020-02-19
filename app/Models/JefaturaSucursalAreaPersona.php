<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JefaturaSucursalAreaPersona extends Model
{
    use SoftDeletes;
    protected $table = "jefatura_sucursal_area_persona";
    protected $fillable = ['jefatura_sucursal_area_id','persona_id'];

    //RELACION INVERSA PARA BUSCAR EL PADRE
    public function persona()
    {
        return $this->belongsTo(Persona::class);
    }
    //RELACION INVERSA PARA BUSCAR EL PADRE
    public function jefatura_sucursal_area()
    {
        return $this->belongsTo(JefaturaSucursalArea::class);
    }
}
