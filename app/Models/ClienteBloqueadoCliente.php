<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClienteBloqueadoCliente extends Model
{
    protected $table = "vista_clientebloquedocliente";
    protected $fillable = [
        'id',
        'descripcion',
        'cliente_id',
        'razonsocial'
    ];
}
