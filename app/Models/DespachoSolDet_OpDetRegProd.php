<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DespachoSolDet_OpDetRegProd extends Model
{
    protected $table = "despachosoldet_opdetregprod";
    protected $fillable = [
        'despachosoldet_id',
        // De que bodega sale esta fila. Un item puede repartirse entre varias
        // bodegas y sin este dato no se sabia a cual pertenecia cada lote.
        'invbodegaproducto_id',
        // Admite nulo: la fila "sin lote" es material de la bodega que no
        // proviene de un lote de produccion (carga manual de inventario).
        'opdetregprod_id',
        'cant',
        'cantkg',
    ];

    // Relación inversa: la bodega-producto de la que sale esta fila
    public function invbodegaproducto()
    {
        return $this->belongsTo(InvBodegaProducto::class);
    }

    // Relación inversa: pertenece a un ítem de solicitud de despacho
    public function despachosoldet()
    {
        return $this->belongsTo(DespachoSolDet::class);
    }

    // Relación inversa: pertenece a un registro de producción aprobado
    public function opdetregprod()
    {
        return $this->belongsTo(OpDetRegProd::class);
    }
}
