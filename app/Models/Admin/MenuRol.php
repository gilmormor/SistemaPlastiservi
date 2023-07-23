<?php

namespace App\Models\Admin;

use App\Models\Seguridad\UsuarioRol;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MenuRol extends Model
{
    use SoftDeletes;
    protected $table = "menu_rol";
    protected $fillable = [
        'rol_id',
        'menu_id',
        'usuariodel_id'
    ];
    //public $timestamps = false; 
    //RELACION UNO A MUCHOS usuariorol
    public function usuarioroles()
    {
        return $this->hasMany(UsuarioRol::class,"rol_id","rol_id");
    }
    
}
