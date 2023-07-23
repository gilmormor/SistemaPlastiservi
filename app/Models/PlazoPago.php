<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PlazoPago extends Model
{
    use SoftDeletes;
    protected $table = "plazopago";
    protected $fillable = [
        'descripcion',
        'dias'
    ];

    public function clientedirecs()
    {
        return $this->hasMany(ClienteDirec::class);
    }
    public function clientes()
    {
        return $this->hasMany(Cliente::class);
    }
    //RELACION UNO A MUCHOS Cotizacion
    public function cotizacions()
    {
        return $this->hasMany(Cotizacion::class);
    }
    public function clientetemps()
    {
        return $this->hasMany(ClienteTemp::class);
    }
}
