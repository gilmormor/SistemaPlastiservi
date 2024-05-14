<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NoConformidad_Responsable extends Model
{
    use SoftDeletes;
    protected $table = "noconformidad_responsable";
    protected $fillable = [
        'noconformidad_id',
        'jefatura_sucursal_area_id',
        'usuariodel_id'
    ];

    //RELACION INVERSA PARA BUSCAR EL PADRE NOCONFORMIDAD
    public function noconformidad()
    {
        return $this->belongsTo(NoConformidad::class,'noconformidad_id');
    }
    
    //RELACION INVERSA PARA BUSCAR EL PADRE CERTIFICADO
    public function jefaturasucursalarea()
    {
        return $this->belongsTo(JefaturaSucursalArea::class,'jefatura_sucursal_area_id');
    }

}
