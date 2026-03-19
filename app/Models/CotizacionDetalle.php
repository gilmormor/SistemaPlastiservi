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
        'cantgrupo',
        'cantxgrupo',
        'unidadmedida_id',
        'descuento',
        'preciounit',
        'peso',
        'precioxkilo',
        'precioxkiloreal',
        'totalkilos',
        'subtotal',
        'producto_nombre',
        'ancho',
        'largo',
        'espesor',
        'diametro',
        'categoriaprod_id',
        'claseprod_id',
        'grupoprod_id',
        'color_id',
        'obs',
        'acuerdotecnicotemp_id',
        'usuariodel_id'
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

    //Relacion inversa a acuerdotecnicotemp
    public function acuerdotecnicotemp()
    {
        return $this->belongsTo(AcuerdoTecnicoTemp::class);
    }

    //RELACION de uno a uno acuerdotecnicotemp
    public function acuerdotecnicotempunoauno()
    {
        return $this->hasOne(AcuerdoTecnicoTemp::class,"at_cotizaciondetalle_id");
    }

    public function doccompdets()
    {
        return $this->hasMany(DocCompDet::class, 'origendet_id', 'id')
            ->where('origentipo', 'COT');
    }
    public function docinsumodets()
    {
        return $this->hasMany(DocInsumoDet::class, 'origendet_id', 'id')
            ->where('origentipo', 'COT');
    }
    
}
