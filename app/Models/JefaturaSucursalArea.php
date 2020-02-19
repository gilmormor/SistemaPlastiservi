<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JefaturaSucursalArea extends Model
{
    use SoftDeletes;
    protected $table = "jefatura_sucursal_area";

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
}
