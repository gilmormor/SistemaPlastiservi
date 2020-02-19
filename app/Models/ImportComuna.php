<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ImportComuna extends Model
{
    use SoftDeletes;
    protected $table = "importcomuna";
    protected $fillable = 
    [
        'comuna_id',
        'direccion',
        'comuna_id',
        'formadepago_id',
        'formadepago',
        'plazodepago_id',
        'plazodepago',
        'nombrecontacto',
        'emailcontacto',
        'telefonocontacto',
        'nombrecontactofinanzas',
        'emailContactofinanzas',
        'telefonocontactofinanzas',
        'nombrefantasia',
        'mostrarguiasfacturas',
        'observaciones',
        'usuariodel_id' 
    ];
}
