<?php

namespace App\Models\Seguridad;

use App\Models\Admin\Rol;
use App\Models\NoConformidad;
use App\Models\Persona;
use App\Models\Sucursal;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class Usuario extends Authenticatable
{
    use SoftDeletes;
    protected $remember_token = false;
    protected $table = 'usuario';
    protected $fillable = ['usuario', 'nombre', 'email', 'password', 'foto'];

    public static function setFotoUsuario($foto,$usuario, $actual = false){
        //dd($foto);
        if ($foto) {
            if ($actual) {
                Storage::disk('public')->delete("imagenes/usuario/$actual");
            }
            //$imageName = Str::random(20) . '.jpg';
            $imageName = $usuario . '.jpg';
            $imagen = Image::make($foto)->encode('jpg', 75);
            $imagen->resize(530, 470, function ($constraint) {
                $constraint->upsize();
            });
            Storage::disk('public')->put("imagenes/usuario/$imageName", $imagen->stream());
            //$request->file('')
            return $imageName;
        } else {
            return false;
        }
    }

    public function roles()
    {
        return $this->belongsToMany(Rol::class, 'usuario_rol');
    }

    public function setSession($roles)
    {
        Session::put([
            'usuario' => $this->usuario,
            'usuario_id' => $this->id,
            'nombre_usuario' => $this->nombre,
            'foto_usuario' => $this->foto
        ]);
        if (count($roles) == 1) {
            Session::put(
                [
                    'rol_id' => $roles[0]['id'],
                    'rol_nombre' => $roles[0]['nombre']
                ]
            );
        }else{
            Session::put('roles', $roles);
        }
    }
    public function setPasswordAttribute($pass)
    {
        $this->attributes['password'] = Hash::make($pass);
    }

    //RELACION MUCHO A MUCHOS CUN SUCURSAL A TRAVES DE SUCURSAL_USUARIO
    public function sucursales()
    {
        return $this->belongsToMany(Sucursal::class, 'sucursal_usuario');
    }

    //Relacion uno a uno con persona
    public function persona()
    {
        return $this->hasOne(Persona::class);
    }

    //RELACION DE UNO A MUCHOS noconformidad
    public function noconformidad()
    {
        return $this->hasMany(NoConformidad::class);
    }
}
