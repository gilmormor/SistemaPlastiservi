<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NotaVentaCerrada extends Model
{
    use SoftDeletes;
    protected $table = "notaventacerrada";
    protected $fillable = [
        'notaventa_id',
        'observacion',
        'motcierre_id',
        'usuario_id',
        'usuariodel_id'
    ];

    //RELACION INVERSA NotaVenta
    public function notaventa()
    {
        return $this->belongsTo(NotaVenta::class);
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
