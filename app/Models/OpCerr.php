<?php

namespace App\Models;

use App\Models\Seguridad\Usuario;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OpCerr extends Model
{
    use SoftDeletes;
    protected $table = "opcerr";
    protected $fillable = [
        'op_id',
        'obs',
        'usuario_id',
        'usuariodel_id'
    ];

    //RELACION INVERSA op
    public function op()
    {
        return $this->belongsTo(Op::class);
    }
    //Relacion inversa a Usuario
    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }
    //Relacion inversa a User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
