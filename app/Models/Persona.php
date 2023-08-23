<?php

namespace App\Models;

use App\Models\Seguridad\Usuario;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Persona extends Model
{
    use SoftDeletes;
    protected $table = "persona";
    protected $fillable = [
        'rut',
        'nombre',
        'apellido',
        'direccion',
        'telefono',
        'ext',
        'email',
        'cargo_id',
        'usuario_id',
        'activo'
    ];
    //RELACION DE MUCHOS A MUCHOS TABLA INTERMEDIA jefatura_sucursal_area_persona ENTRE JefaturaSucursalArea Y persona
    public function jefaturasucursalareas()
    {
        return $this->belongsToMany(JefaturaSucursalArea::class, 'jefatura_sucursal_area_persona','persona_id')->withTimestamps();
    }
    //RELACION INVERSA CARGO
    public function cargo()
    {
        return $this->belongsTo(Cargo::class);
    }
    //RELACION INVERSA User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    //Relacion uno a uno con Vendedor
    public function vendedor()
    {
        return $this->hasOne(Vendedor::class);
    }
    //RELACION INVERSA User
    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }
    //RELACION DE UNO A MUCHOS jefaturasucursalareas
    public function jefaturasucursalarea()
    {
        return $this->hasMany(JefaturaSucursalArea::class);
    }
}
