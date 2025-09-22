<?php

namespace App\Models;

use App\Models\Seguridad\Usuario;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OtCerr extends Model
{
    use SoftDeletes;
    protected $table = "otcerr";
    protected $fillable = [
        'ot_id',
        'obs',
        'usuario_id',
        'usuariodel_id'
    ];

    //RELACION INVERSA ot
    public function ot()
    {
        return $this->belongsTo(Ot::class);
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
