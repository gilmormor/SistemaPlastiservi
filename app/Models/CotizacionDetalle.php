<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CotizacionDetalle extends Model
{
    use SoftDeletes;
    protected $table = "cotizaciondetalle";
    protected $fillable = [
        'producto_id',
        'cotizacion_id',
        'cant',
        'unidadmedida_id',
        'preciounit',
        'precioneto',
        'iva',
        'total',
        'usuariodel_id',
        'precioxkiloreal'
    ];
    //RELACION DE UNO A MUCHOS NotaVentaDetalle
    public function notaventadetalles()
    {
        return $this->hasMany(NotaVentaDetalle::class);
    }
    //RELACION INVERSA Cotizacion
    public function cotizacion()
    {
        return $this->belongsTo(Cotizacion::class);
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
