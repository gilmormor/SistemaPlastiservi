<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClienteInternoSucursal extends Model
{
    use SoftDeletes;
    protected $table = "clienteinterno_sucursal";
    protected $fillable = ['clienteinterno_id','sucursal_id','usuariodel_id'];

    //RELACION INVERSA A ClienteInterno
    public function clienteinterno()
    {
        return $this->belongsTo(ClienteInterno::class);
    }
    //RELACION INVERSA A SUCURSAL
    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }
}
