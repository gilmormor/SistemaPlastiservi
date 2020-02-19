<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClienteDirec extends Model
{
    use SoftDeletes;
    protected $table = "clientedirec";
    protected $fillable = [
        'direcciondetalle',
        'cliente_id',
        'region_id',
        'provincia_id',
        'comuna_id'
    ];

    public function sucursals()
    {
        return $this->belongsToMany(Sucursal::class, 'sucursalclientedirec','clientedirec_id');
    }

    //RELACION INVERSA CLIENTE
    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }
    //RELACION INVERSA FORMAPAGO
    public function formapago()
    {
        return $this->belongsTo(FormaPago::class);
    }
    //RELACION INVERSA PLAZOPAGO
    public function plazopago()
    {
        return $this->belongsTo(PlazoPago::class);
    }
    //RELACION INVERSA REGION
    public function region()
    {
        return $this->belongsTo(Region::class);
    }
    //RELACION INVERSA PROVINCIA
    public function provincia()
    {
        return $this->belongsTo(Provincia::class);
    }
    //RELACION INVERSA COMUNA
    public function comuna()
    {
        return $this->belongsTo(Comuna::class);
    }
    //RELACION UNO A MUCHOS acuerdotectemps
    public function acuerdotectemps()
    {
        return $this->hasMany(AcuerdoTecTemp::class);
    }
    //RELACION UNO A MUCHOS Cotizacion
    public function cotizacions()
    {
        return $this->hasMany(Cotizacion::class);
    }
}
