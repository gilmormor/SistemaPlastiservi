<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DteTrans extends Model
{
    use SoftDeletes;

    protected $table = 'dtetrans';
    protected $fillable = [
        'dte_id', 
        'fchsalida', 
        'fchllegada', 
        'dirdest', 
        'comuna_id', 
        'transemp_id',
        'transchofer_id',
        'transveh_id',
        'transvehcarro_id',
    ];

    // Relaciones
    public function dte()
    {
        return $this->belongsTo(Dte::class, 'dte_id');
    }

    public function comuna()
    {
        return $this->belongsTo(Comuna::class, 'comuna_id');
    }

    /* Los datos del transportista viven en sus maestros: dtetrans solo los vincula.
       El XML lee de aqui el RUT de la empresa, el RUT y nombre del chofer y las
       patentes del vehiculo y del carro. */
    public function transemp()
    {
        return $this->belongsTo(TransEmp::class, 'transemp_id');
    }

    public function transchofer()
    {
        return $this->belongsTo(TransChofer::class, 'transchofer_id');
    }

    public function transveh()
    {
        return $this->belongsTo(TransVeh::class, 'transveh_id');
    }

    public function transvehcarro()
    {
        return $this->belongsTo(TransVeh::class, 'transvehcarro_id');
    }
}
