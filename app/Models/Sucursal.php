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
        return $this->belongsToMany(Area::class, 'sucursal_area')->withTimestamps();
    }

    //RELACION MUCHO A MUCHOS CON USUARIO A TRAVES DE SUCURSAL_USUARIO
    public function usuarios()
    {
        return $this->belongsToMany(Usuario::class, 'sucursal_usuario')->withTimestamps();
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
        return $this->belongsToMany(Cliente::class, 'cliente_sucursal')->withTimestamps();
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

    //RELACION DE UNO A MUCHOS EstadisticaVentaGI Guia Interna
    public function estadisticaventagi()
    {
        return $this->hasMany(EstadisticaVentaGI::class);
    }

    //RELACION DE UNO A MUCHOS InvBodega
    public function invbodegas()
    {
        return $this->hasMany(InvBodega::class);
    }

    //RELACION UNO A UNO CON CENTRO ECONOMICO
    public function centroeconomico()
    {
        return $this->hasOne(CentroEconomico::class);
    }

    //Relacion inversa a Comuna
    public function comuna()
    {
        return $this->belongsTo(Comuna::class);
    }
    
    public function categorias()
    {
        return $this->belongsToMany(CategoriaProd::class, 'categoriaprodsuc','sucursal_id','categoriaprod_id')->withTimestamps();
    }
 

}
