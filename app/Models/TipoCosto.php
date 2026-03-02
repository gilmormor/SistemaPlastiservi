<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TipoCosto extends Model
{
    use SoftDeletes;
    protected $table = "tipocosto";
    protected $fillable = [
                    'nombre',
                    'desc',
                    'usuario_id',
                    'usuariodel_id'
                ];

    //RELACION DE UNO A MUCHOS Insumo
    public function insumos()
    {
        return $this->hasMany(Insumo::class,"tipocosto_id");
    }
}
