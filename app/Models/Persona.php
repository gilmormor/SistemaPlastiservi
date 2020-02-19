<?php

namespace App\Models;

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
        'usuario_id'
    ];
    public function jefaturasucursalareas()
    {
        return $this->belongsToMany(JefaturaSucursalArea::class, 'jefatura_sucursal_area_persona','persona_id');
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
}
