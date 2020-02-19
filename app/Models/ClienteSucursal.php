<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClienteSucursal extends Model
{
    use SoftDeletes;
    protected $table = "cliente_sucursal";
    protected $fillable = ['cliente_id','sucursal_id','usuariodel_id'];

    //RELACION INVERSA A Cliente
    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }
    //RELACION INVERSA A SUCURSAL
    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }
}
