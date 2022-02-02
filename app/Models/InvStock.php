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
        'usuariodel_id'
    ];

    //RELACION DE UNO A VARIOS InvMovDet
    public function invmovdets()
    {
        return $this->hasMany(InvMovDet::class);
    }

    //RELACION DE UNO A VARIOS despachoorddetbodega
    public function despachoorddetbodegas()
    {
        return $this->hasMany(DespachoOrdDetBodega::class);
    }

    //RELACION INVERSA Producto
    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }
    
    
}
