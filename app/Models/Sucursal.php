<?php

namespace App\Models;

use App\Models\Seguridad\Usuario;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sucursal extends Model
{
    use SoftDeletes;
    protected $table = "sucursal";
    protected $fillable = [
            'nombre',
            'abrev',
            'region_id',
            'provincia_id',
            'comuna_id',
            'direccion',
            'telefono1',
            'telefono2',
            'telefono3',
            'email',
            'usuariodel_id'
        ];

    public function empresa()
    {
        return $this->hasOne(Empresa::class);
    }

    public function areas()
    {
        return $this->belongsToMany(Area::class, 'sucursal_area');
    }

    //RELACION MUCHO A MUCHOS CON USUARIO A TRAVES DE SUCURSAL_USUARIO
    public function usuarios()
    {
        return $this->belongsToMany(Usuario::class, 'sucursal_usuario');
    }
    //RELACION DE UNO A MUCHOS Cotizacion
    public function cotizaciones()
    {
        return $this->hasMany(Cotizacion::class);
    }
    //RELACION DE UNO A MUCHOS NotaVenta
    public function notaventas()
    {
        return $this->hasMany(NotaVenta::class);
    }
    //RELACION MUCHO A MUCHOS CON USUARIO A TRAVES DE cliente_sucursal
    public function clientes()
    {
        return $this->belongsToMany(Cliente::class, 'cliente_sucursal');
    }
    //RELACION DE UNO A MUCHOS ClienteTemp
    public function clientetemps()
    {
        return $this->hasMany(ClienteTemp::class);
    }
    //RELACION DE UNO A MUCHOS EstadisticaVenta
    public function estadisticaventa()
    {
        return $this->hasMany(EstadisticaVenta::class);
    }

}
