<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstadisticaVenta extends Model
{
    protected $table = "estadisticaventa";
    protected $fillable = [
        'sucursal_id',
        'tipofact',
        'fechadocumento',
        'tipodocumento',
        'numerodocumento',
        'item',
        'rut',
        'razonsocial',
        'producto',
        'descripcion',
        'ancho',
        'largo',
        'espesor',
        'espesorc',
        'medidas',
        'materiaprima',
        'matprimdesc',
        'descr_prod_mp',
        'unidades',
        'subtotal',
        'kilos',
        'unidadmedida_id',
        'factorconversion',
        'diferenciakilos',
        'conversionkilos',
        'precioxkilo',
        'valorcosto',
        'diferenciaprecio',
        'diferenciaval'
    ];

    //Relacion inversa a Sucursal
    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }
    
    //Relacion inversa a UnidadMedida
    /*public function unidadmedida()
    {
        return $this->belongsTo(UnidadMedida::class);
    }*/

}
