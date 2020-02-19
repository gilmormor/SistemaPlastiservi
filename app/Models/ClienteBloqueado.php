<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClienteBloqueado extends Model
{
    use SoftDeletes;
    protected $table = "clientebloqueado";
    protected $fillable = [
        'descripcion',
        'cliente_id'
    ];

    //RELACION INVERSA CLIENTE
    public function ciente()
    {
        return $this->belongsTo(Cliente::class);
    }
}
