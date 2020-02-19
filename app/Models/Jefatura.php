<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Jefatura extends Model
{
    use SoftDeletes;
    protected $table = "jefatura";
    protected $fillable = ['nombre','descripcion'];

    public function sucursalAreas()
    {
        return $this->belongsToMany(SucursalArea::class, 'jefatura_sucursal_area');
    }
}
