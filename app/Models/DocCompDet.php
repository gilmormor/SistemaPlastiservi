<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocCompDet extends Model
{
    use SoftDeletes;
    protected $table = "doccompdet";
    protected $fillable = [
        'origentipo',
        'origendet_id',
        'productocomp_id',
        'productopadre_id',
        'producto_id',
        'cant',
        'cantxcomp',
        'precio',
        'costo',
        'usuario_id',
        'usuariodel_id'
    ];

    //RELACION INVERSA productoComp
    public function productocomp()
    {
        return $this->belongsTo(ProductoComp::class, 'productocomp_id');
    }

    //RELACION INVERSA ProductoPadre
    public function productopadre()
    {
        return $this->belongsTo(Producto::class, 'productopadre_id');
    }

    //RELACION INVERSA Producto
    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

}