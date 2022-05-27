<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvStock extends Model
{
    use SoftDeletes;
    protected $table = "invstock";
    protected $fillable = [
        'producto_id',
        'invbodega_id',
        'stock',
        'stockkg',
        'usuariodel_id'
    ];

    //RELACION INVERSA Producto
    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }
    //RELACION INVERSA InvBodega
    public function invbodega()
    {
        return $this->belongsTo(InvBodega::class);
    }    
}
