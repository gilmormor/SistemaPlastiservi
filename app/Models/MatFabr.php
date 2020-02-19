<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MatFabr extends Model
{
    use SoftDeletes;
    protected $table = "matfabr";
    protected $fillable = [
        'nombre',
        'descripcion'
    ];

    public function acuerdotectemps()
    {
        return $this->hasMany(AcuerdoTecTemp::class);
    }
}
