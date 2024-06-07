<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClienteDesbloqueadoModulo extends Model
{
    //use SoftDeletes;
    protected $table = "clientedesbloqueadomodulo";
    protected $fillable = [
            'clientedesbloqueado_id',
            'modulo_id',
            'usuario_id'
        ];
    //RELACION INVERSA PARA BUSCAR EL PADRE
    public function clientedesbloqueado()
    {
        return $this->belongsTo(ClienteDesBloqueado::class,'clientedesbloqueado_id');
    }
    //RELACION INVERSA PARA BUSCAR EL PADRE
    public function modulo()
    {
        return $this->belongsTo(Modulo::class,'modulo_id');
    }
}
