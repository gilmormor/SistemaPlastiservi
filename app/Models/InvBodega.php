<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvBodega extends Model
{
    use SoftDeletes;
    protected $table = "invbodega";
    protected $fillable = [
        'nombre',
        'nomabre',
        'desc',
        'activo',
        'tipo',
        'sucursal_id',
        'usuario_id',
        'usuariodel_id'
    ];

    //RELACION DE UNO A VARIOS invbodegaproducto
    public function invbodegaproductos()
    {
        return $this->hasMany(InvBodegaProducto::class);
    }

    //RELACION INVERSA Sucursal
    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }
    
    public function categoriaprods()
    {
        return $this->belongsToMany(CategoriaProd::class, 'categoriaprod_invbodega','invbodega_id','categoriaprod_id');
    }

}
