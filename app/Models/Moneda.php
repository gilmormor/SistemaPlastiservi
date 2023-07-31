<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Moneda extends Model
{
    use SoftDeletes;
    protected $table = "moneda";
    protected $fillable = [
        'nombre',
        'desc',
        'simbolo',
        'valor',
        'usuario_id',
        'usuariodel_id'
    ];

    //RELACION DE UNO A MUCHOS cotizacion
    public function cotizacion()
    {
        return $this->hasMany(Cotizacion::class);
    }

}
