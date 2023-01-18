<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GrupoProd extends Model
{
    use SoftDeletes;
    protected $table = "grupoprod";
    protected $fillable = [
        'gru_nombre',
        'gru_descripcion'
    ];

    //RELACION DE UNO A MUCHOS PRODUCTO
    public function productos()
    {
        return $this->hasMany(Producto::class);
    }
    
    //RELACION INVERSA PARA BUSCAR EL PADRE DE UNA CLASE
    public function categoriaprod()
    {
        return $this->belongsTo(CategoriaProd::class);
    }

    //RELACION UNO A MUCHOS 
    public function categoriagrupovalmes()
    {
        return $this->hasMany(CategoriaGrupoValMes::class);
    }
    
    //RELACION DE UNO A MUCHOS acuerdotecnico
    public function acuerdotecnicos()
    {
        return $this->hasMany(AcuerdoTecnico::class,"at_grupoprod_id");
    }

    //RELACION DE UNO A MUCHOS acuerdoTecnicoTemp
    public function acuerdotecnicotemps()
    {
        return $this->hasMany(AcuerdoTecnicoTemp::class,"at_grupoprod_id");
    }
    
}
