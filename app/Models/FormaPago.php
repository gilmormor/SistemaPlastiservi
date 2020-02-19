<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FormaPago extends Model
{
    use SoftDeletes;
    protected $table = "formapago";
    protected $fillable = [
        'descripcion'
    ];

    //RELACION UNO A MUCHOS ClienteDirec
    public function clientedirecs()
    {
        return $this->hasMany(ClienteDirec::class);
    }
    //RELACION UNO A MUCHOS Cotizacion
    public function cotizacions()
    {
        return $this->hasMany(Cotizacion::class);
    }
    public function clientes()
    {
        return $this->hasMany(Cliente::class);
    }
    public function clientetemps()
    {
        return $this->hasMany(ClienteTemp::class);
    }

}
