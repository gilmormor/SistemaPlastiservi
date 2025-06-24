<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OtDet extends Model
{
    use SoftDeletes;
    protected $table = "otdet";
    protected $fillable = [
        'ot_id',
        'producto_id',
        'cant',
        'cantprod',
        'unidadmedida_id',
        'espesorprod',
        'preciounit',
        'precioxkilo',
        'precioxkiloreal',
        'kg',
        'kgprod',
        'subtotal',
        'requiere_fabricacion',
        'obs',
        'usuariodel_id'
    ];

    //RELACION INVERSA ot
    public function ot()
    {
        return $this->belongsTo(Ot::class)->whereDoesntHave('otanul');
    }
    //Relacion inversa a Producto
    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }
    //Relacion inversa a UnidadMedida
    public function unidadmedida()
    {
        return $this->belongsTo(UnidadMedida::class);
    }
    //RELACION de uno a uno otdetnvdet
    public function otdetnvdet()
    {
        return $this->hasOne(OtDetNVDet::class,"otdet_id");
    }    
}