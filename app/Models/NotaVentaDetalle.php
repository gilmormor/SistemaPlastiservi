<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NotaVentaDetalle extends Model
{
    use SoftDeletes;
    protected $table = "notaventadetalle";
    protected $fillable = [
        'producto_id',
        'notaventa_id',
        'cotizaciondetalle_id',
        'cant',
        'unidadmedida_id',
        'preciounit',
        'peso',
        'precioneto',
        'iva',
        'total',
        'usuariodel_id',
        'precioxkiloreal'
    ];
    
    //RELACION INVERSA NotaVenta
    public function notaventa()
    {
        return $this->belongsTo(NotaVenta::class);
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
    //Relacion inversa a CotizacionDetalle
    public function cotizaciondetalle()
    {
        return $this->belongsTo(CotizacionDetalle::class);
    }

    //RELACION DE UNO A MUCHOS DespachoSolDet
    public function despachosoldets()
    {
        return $this->hasMany(DespachoSolDet::class);
    }
    //RELACION DE UNO A MUCHOS DespachoOrdDet
    public function despachoorddets()
    {
        return $this->hasMany(DespachoOrdDet::class);
    }
    
}
