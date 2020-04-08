<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CategoriaProd extends Model
{
    use SoftDeletes;
    protected $table = "categoriaprod";
    protected $fillable = [
        'nombre',
        'descripcion',
        'precio',
        'areaproduccion_id',
        'sta_precioxkilo',
        'unidadmedida_id',
        'unidadmedidafact_id',
        'usuariodel_id'
    ];

    public function productos()
    {
        return $this->hasMany(Producto::class);
    }
    public function sucursales()
    {
        return $this->belongsToMany(Sucursal::class, 'categoriaprodsuc','categoriaprod_id');
    }
    public function claseprods()
    {
        return $this->hasMany(ClaseProd::class,'categoriaprod_id');
    }
    public function grupoprods()
    {
        return $this->hasMany(GrupoProd::class,'categoriaprod_id');
    }
    //Relacion inversa a AreaProduccion
    public function areaproduccion()
    {
        return $this->belongsTo(AreaProduccion::class);
    }

    //Relacion inversa a UnidadMedida
    public function unidadmedida()
    {
        return $this->belongsTo(UnidadMedida::class);
    }

    //Relacion inversa a UnidadMedida
    public function unidadmedidafact()
    {
        return $this->belongsTo(UnidadMedida::class,'unidadmedidafact_id');
    }
    
    
}
