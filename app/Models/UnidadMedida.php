<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UnidadMedida extends Model
{
    use SoftDeletes;
    protected $table = "unidadmedida";
    protected $fillable = [
        'nombre',
        'descripcion'
    ];

    public function acuerdotectempanchos()
    {
        return $this->hasMany(AcuerdoTecTemp::class,'anchoum_id');
    }
    public function acuerdotectemplargo()
    {
        return $this->hasMany(AcuerdoTecTemp::class,'largoum_id');
    }
    public function acuerdotectempfuelle()
    {
        return $this->hasMany(AcuerdoTecTemp::class,'fuelleum_id');
    }
    public function acuerdotectempespesor()
    {
        return $this->hasMany(AcuerdoTecTemp::class,'espesorum_id');
    }
    //RELACION UNO A MUCHOS CotizacionDetalle
    public function cotizaciondetalles()
    {
        return $this->hasMany(CotizacionDetalle::class);
    }

    
}
