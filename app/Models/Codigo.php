<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Codigo extends Model
{
    use SoftDeletes;
    protected $table = "codigo";
    protected $fillable = [
        'desc',
        'usuario_id',
        'usuariodel_id'
    ];
    //RELACION UNO A MUCHOS CodigoDet
    public function codigodet()
    {
        return $this->hasMany(CodigoDet::class);
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
