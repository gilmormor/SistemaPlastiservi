<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Modulo extends Model
{
    use SoftDeletes;
    protected $table = "modulo";
    protected $fillable = [
        'nombre',
        'desc',
        'stanvdc',
        'usuario_id',
        'usuariodel_id'
    ];

}
