<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SucursalArea extends Model
{
    use SoftDeletes;
    protected $table = "sucursal_area";
    protected $fillable = ['area_id','sucursal_id'];

    public function jefaturas()
    {
        return $this->belongsToMany(Jefatura::class, 'jefatura_sucursal_area')->withTimestamps();
    }
    //RELACION INVERSA PARA BUSCAR EL PADRE
    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }
    //RELACION INVERSA PARA BUSCAR EL PADRE
    public function area()
    {
        return $this->belongsTo(Area::class);
    }

}
