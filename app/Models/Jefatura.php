<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Jefatura extends Model
{
    use SoftDeletes;
    protected $table = "jefatura";
    protected $fillable = [
                            'nombre',
                            'abrev',
                            'descripcion'
                        ];

    //RELACION MUCHOS A MUCHOS A TRAVES DE jefatura_sucursal_area
    public function sucursalAreas()
    {
        return $this->belongsToMany(SucursalArea::class, 'jefatura_sucursal_area');
    }
}
