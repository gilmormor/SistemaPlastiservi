<?php

namespace App\Models\Seguridad;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UsuarioRol extends Model
{
    use SoftDeletes;
    protected $table = "usuario_rol";
    protected $fillable = [
                            'rol_id',
                            'usuario_id',
                            'usuariodel_id'
                        ];

    //Relacion inversa a Producto
    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }
    

}
