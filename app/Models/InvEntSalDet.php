<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvEntSalDet extends Model
{
    use SoftDeletes;
    protected $table = "inventsaldet";
    protected $fillable = [
        'inventsal_id',
        'invbodegaproducto_id',
        'invbodega_id',
        'producto_id',
        'sucursal_id',
        'invmovtipo_id',
        'cant',
        'cantgrupo',
        'cantxgrupo',
        'peso',
        'cantkg',
        'unidadmedida_id',
        'usuariodel_id'
    ];

    //RELACION INVERSA InvEntSal
    public function inventsal()
    {
        return $this->belongsTo(InvEntSal::class);
    }

    //RELACION INVERSA InvMovTipo
    public function invmovtipo()
    {
        return $this->belongsTo(InvMovTipo::class);
    }
    //RELACION INVERSA InvMovTipo
    public function invbodega()
    {
        return $this->belongsTo(InvBodega::class);
    }
    //RELACION INVERSA InvMovStock
    public function invmovStock()
    {
        return $this->belongsTo(invmovStock::class);
    }
    //Relacion inversa a UnidadMedida
    public function unidadmedida()
    {
        return $this->belongsTo(UnidadMedida::class);
    }
    //Relacion inversa a Producto
    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }
    
}
