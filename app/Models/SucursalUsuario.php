<?php

namespace App\Models;

use App\Models\Seguridad\Usuario;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SucursalUsuario extends Model
{
    use SoftDeletes;
    protected $table = "sucursal_usuario";
    protected $fillable = ['sucursal_id','usuario_id','usuariodel_id'];

    //RELACION INVERSA A USARIO
    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }
    //RELACION INVERSA A SUCURSAL
    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }
}
