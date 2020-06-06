<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TipoEntrega extends Model
{
    use SoftDeletes;
    protected $table = "tipoentrega";
    protected $fillable = [
                    'nombre',
                    'abrev',
                    'icono',
                    'usuariodel_id'
                ];

    public function cotizacion()
    {
        return $this->hasOne(Cotizacion::class);
    }
}
