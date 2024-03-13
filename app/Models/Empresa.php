<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Empresa extends Model
{
    use SoftDeletes;
    protected $table = "empresa";
    protected $fillable = [
        'rut',
        'nombre',
        'razonsocial',
        'giro',
        'iva',
        'sucursal_id',
        'acteco',
        'moneda_id',
        'actsiscob',
        'stabloxdeusiscob',
        'diasprorrogacob',
        'usuariodel_id'
    ];

    //Relacion inversa a Sucursal
    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }
    //Relacion inversa a Moneda
    public function moneda()
    {
        return $this->belongsTo(Moneda::class);
    }
    
}
