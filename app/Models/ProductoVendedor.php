<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductoVendedor extends Model
{
    protected $table = "producto_vendedor";
    protected $fillable = [
        'producto_id',
        'vendedor_id'
    ];

    
    //RELACION INVERSA PARA BUSCAR EL PADRE
    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }
    //RELACION INVERSA PARA BUSCAR EL PADRE
    public function vendedor()
    {
        return $this->belongsTo(Vendedor::class);
    }
}
