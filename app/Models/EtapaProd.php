<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EtapaProd extends Model
{
    use SoftDeletes;
    protected $table = "etapaprod";
    protected $fillable = [
        'nombre',
        'desc',
        'usuario_id',
        'usuariodel_id'
    ];
}
