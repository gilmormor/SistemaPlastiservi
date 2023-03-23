<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AreaProduccionSuc extends Model
{
    use SoftDeletes;
    protected $table = "areaproduccionsuc";
    protected $fillable = [
        'sucursal_id',
        'areaproduccion_id',
        'usuariodel_id'
    ];
    //RELACION UNO A MUCHOS AreaProduccionSucLinea
    public function areaproduccionsuclineas()
    {
        return $this->hasMany(AreaProduccionSucLinea::class,'areaproduccionsuc_id');
    }

    //RELACION INVERSA PARA BUSCAR EL PADRE
    public function areaproduccion()
    {
        return $this->belongsTo(AreaProduccion::class,'areaproduccion_id');
    }
    //RELACION INVERSA PARA BUSCAR EL PADRE
    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class,'sucursal_id');
    }

}
