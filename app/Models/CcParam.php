<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CcParam extends Model
{
    use SoftDeletes;
    protected $table = 'ccparam';
    protected $fillable = [
        'nombre',
        'etiqueta',
        'tipo',
        'unidad',
        'decimales',
        'orden',
        'usuariodel_id',
    ];

    public function ccparamApsucetapaprod()
    {
        return $this->hasMany(CcParamApsucetapaprod::class, 'ccparam_id');
    }
}
