<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClaseProd extends Model
{
    use SoftDeletes;
    protected $table = "claseprod";
    protected $fillable = [
        'cla_nombre',
        'cla_descripcion',
        'cla_longitud'
    ];

    //RELACION DE UNO A MUCHOS PRODUCTO
    public function productos()
    {
        return $this->hasMany(Producto::class);
    }
    

    //RELACION INVERSA PARA BUSCAR CategoriaProd PADRE DE UNA CLASE
    public function categoriaprod()
    {
        return $this->belongsTo(CategoriaProd::class);
    }

    //RELACION DE UNO A MUCHOS acuerdotecnico
    public function acuerdotecnicos()
    {
        return $this->hasMany(AcuerdoTecnico::class,"at_claseprod_id");
    }

    //RELACION DE UNO A MUCHOS acuerdoTecnicoTemp
    public function acuerdotecnicotemps()
    {
        return $this->hasMany(AcuerdoTecnicoTemp::class,"at_claseprod_id");
    }
    
}
