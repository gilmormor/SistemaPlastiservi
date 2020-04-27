<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Producto extends Model
{
    use SoftDeletes;
    protected $table = "producto";
    protected $fillable = [
        'nombre',
        'descripcion',
        'codintprod',
        'codbarra',
        'diamextmm',
        'diamextpg',
        'espesor',
        'long',
        'peso',
        'tipounion',
        'precioneto',
        'foto',
        'categoriaprod_id',
        'claseprod_id',
        'grupoprod_id',
        'color_id',
        'usuariodel_id'
    ];

    //RELACION INVERSA PARA BUSCAR EL PADRE
    public function claseprod()
    {
        return $this->belongsTo(ClaseProd::class);
    }
    //RELACION INVERSA PARA BUSCAR EL PADRE
    public function categoriaprod()
    {
        return $this->belongsTo(CategoriaProd::class);
    }
    //RELACION UNO A MUCHOS CotizacionDetalle
    public function cotizaciondetalles()
    {
        return $this->hasMany(CotizacionDetalle::class);
    }
    //RELACION INVERSA PARA BUSCAR EL PADRE
    public function grupoprod()
    {
        return $this->belongsTo(GrupoProd::class);
    }
    //RELACION INVERSA PARA BUSCAR EL PADRE
    public function color()
    {
        return $this->belongsTo(Color::class);
    }

}
