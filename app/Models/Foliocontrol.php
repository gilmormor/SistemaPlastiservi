<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Foliocontrol extends Model
{
    use SoftDeletes;
    protected $table = "foliocontrol";
    protected $fillable = [
        'doc',
        'desc',
        'ultfoliouti',
        'ultfoliohab',
        'activo',
        'bloqueo',
        'signo',
        'maxitemxdoc',
        'folmindisp',
        'usuario_id',
        'usuariodel_id'
    ];
}