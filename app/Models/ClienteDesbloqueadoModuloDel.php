<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClienteDesbloqueadoModuloDel extends Model
{
    //
    protected $table = "clientedesbloqueadomodulodel";
    protected $fillable = [
            'clientedesbloqueado_id',
            'clientedesbloqueadomodulo_id',
            'modulo_id',
            'cliente_id',
            'notaventa_id',
            'usuario_id'
        ];
    //RELACION INVERSA PARA BUSCAR EL PADRE
    public function clientedesbloqueado()
    {
        return $this->belongsTo(ClienteDesBloqueado::class);
    }
    //RELACION INVERSA PARA BUSCAR EL PADRE
    public function modulo()
    {
        return $this->belongsTo(Modulo::class);
    }
    //RELACION INVERSA PARA BUSCAR EL PADRE
    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }
    //RELACION INVERSA PARA BUSCAR EL PADRE
    public function notaventa()
    {
        return $this->belongsTo(NotaVenta::class);
    }
}
