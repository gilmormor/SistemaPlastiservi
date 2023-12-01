<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CodigoDet extends Model
{
    use SoftDeletes;
    protected $table = "codigodet";
    protected $fillable = [
        'codigo_id',
        'descdet',
        'usuario_id',
        'usuariodel_id'
    ];

    //RELACION INVERSA NotaVenta
    public function codigo()
    {
        return $this->belongsTo(Codigo::class);
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
