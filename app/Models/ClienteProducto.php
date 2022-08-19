<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClienteProducto extends Model
{
    use SoftDeletes;
    protected $table = "cliente_producto";
    protected $fillable = ['cliente_id','producto_id','usuariodel_id'];

    //RELACION INVERSA A Cliente
    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }
    //RELACION INVERSA A SUCURSAL
    public function producto()
    {
        return $this->belongsTo(Sucursal::class);
    }
}
