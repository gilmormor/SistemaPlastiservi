<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Notificaciones extends Model
{
    use SoftDeletes;
    protected $table = "notificaciones";
    protected $fillable = [
        'usuarioorigen_id',
        'usuariodestino_id',
        'vendedor_id',
        'status',
        'nombretabla',
        'mensaje',
        'mensajetitle',
        'nombrepantalla',
        'rutaorigen',
        'rutadestino',
        'tabla_id',
        'accion',
        'icono'
    ];

    //RELACION INVERSA Usuario
    public function usuarioorigen()
    {
        return $this->belongsTo(Usuario::class,'usuarioorigen_id');
    }
    //RELACION INVERSA Usuario
    public function usuariodestino()
    {
        return $this->belongsTo(Usuario::class,'usuariodestino_id');
    }
    //Relacion inversa a Vendedor
    public function vendedor()
    {
        return $this->belongsTo(Vendedor::class);
    }
    
    
}
