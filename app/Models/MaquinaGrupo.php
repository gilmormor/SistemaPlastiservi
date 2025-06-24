<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaquinaGrupo extends Model
{
    use SoftDeletes;
    protected $table = "maquinagrupo";
    protected $fillable = [
        'nombre',
        'desc',
        'usuario_id',
        'usuariodel_id'
    ];

    //RELACION DE UNO A MUCHOS maquina
    public function maquina()
    {
        return $this->hasMany(Maquina::class);
    }    
    
}
