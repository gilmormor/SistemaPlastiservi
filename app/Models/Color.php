<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Color extends Model
{
    use SoftDeletes;
    protected $table = "color";
    protected $fillable = [
        'nombre',
        'descripcion',
        'codcolor'
    ];

    public function acuerdotectemps()
    {
        return $this->hasMany(AcuerdoTecTemp::class);
    }
    public function acuerdotectempImps()
    {
        return $this->hasMany(AcuerdoTecTemp::class,'impresocolor_id');
    }
    public function producto()
    {
        return $this->hasMany(Producto::class);
    }
}
