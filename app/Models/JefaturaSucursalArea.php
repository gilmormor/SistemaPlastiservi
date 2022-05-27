<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JefaturaSucursalArea extends Model
{
    use SoftDeletes;
    protected $table = "jefatura_sucursal_area";
    protected $fillable = ['sucursal_area_id','jefatura_id','persona_id'];

    //RELACION INVERSA PARA BUSCAR EL PADRE
    public function jefatura()
    {
        return $this->belongsTo(Jefatura::class);
    }
    //RELACION INVERSA PARA BUSCAR EL PADRE
    public function sucursal_area()
    {
        return $this->belongsTo(SucursalArea::class);
    }

    //RELACION INVERSA PARA BUSCAR EL PADRE
    public function persona()
    {
        return $this->belongsTo(Persona::class);
    }

    //RELACION MUCHOS A MUCHOS A TRAVES DE noconformidad_jefsucarea
    public function noconformidades()
    {
        return $this->belongsToMany(NoConformidad::class, 'noconformidad_jefsucarea')->withTimestamps();
    }

    //RELACION MUCHOS A MUCHOS A TRAVES DE noconformidad_responsable JEFE DE DPTO
    public function noconformidad_responsables()
    {
        return $this->belongsToMany(NoConformidad::class, 'noconformidad_responsable')->withTimestamps();
    }
    
}
