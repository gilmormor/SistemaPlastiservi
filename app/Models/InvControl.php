<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvControl extends Model
{
    use SoftDeletes;
    protected $table = "invcontrol";
    protected $fillable = [
        'annomes',
        'status',
        'sucursal_id',
        'status',
        'usuario_id',
        'usuariodel_id'
    ];

    //RELACION INVERSA Sucursal
    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }
    
    //RELACION INVERSA User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    //RELACION INVERSA User
    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }
}
