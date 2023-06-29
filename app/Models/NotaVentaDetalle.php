<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Session;


class NotaVentaDetalle extends Model
{
    use SoftDeletes;
    protected $table = "notaventadetalle";
    protected $fillable = [
        'producto_id',
        'notaventa_id',
        'cotizaciondetalle_id',
        'cant',
        'cantgrupo',
        'cantxgrupo',
        'unidadmedida_id',
        'descuento',
        'preciounit',
        'peso',
        'precioneto',
        'iva',
        'total',
        'usuariodel_id',
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
        'usuariodel_id'
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

    //RELACION de uno a uno acuerdotecnico
    public function acuerdotecnico()
    {
        return $this->hasOne(AcuerdoTecnico::class,"at_notaventadetalle_id");
    }

    //RELACION de uno a uno acuerdotecnicotemp
    public function acuerdotecnicotempunoauno()
    {
        return $this->hasOne(AcuerdoTecnicoTemp::class,"at_cotizaciondetalle_id","cotizaciondetalle_id");
    }
    //Relacion uno a uno con notaventadetalleext
    public function notaventadetalleext()
    {
        return $this->hasOne(NotaVentaDetalleExt::class,"notaventadetalle_id");
    }
}