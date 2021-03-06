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
        return $this->hasMany(ClienteDirec::class,'formapago_id');
    }
    //RELACION UNO A MUCHOS Cotizacion
    public function cotizacions()
    {
        return $this->hasMany(Cotizacion::class,'formapago_id');
    }
    //RELACION UNO A MUCHOS NotaVenta
    public function notaventas()
    {
        return $this->hasMany(NotaVenta::class,'formapago_id');
    }
    //RELACION UNO A MUCHOS Cliente
    public function clientes()
    {
        return $this->hasMany(Cliente::class,'formapago_id');
    }
    public function clientetemps()
    {
        return $this->hasMany(ClienteTemp::class,'formapago_id');
    }

}
