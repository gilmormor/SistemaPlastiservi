<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClienteTemp extends Model
{
    use SoftDeletes;
    protected $table = "clientetemp";
    protected $fillable = [
        'rut',
        'razonsocial',
        'direccion',
        'telefono',
        'email',
        'vendedor_id',
        'giro_id',
        'giro',
        'comunap_id',
        'formapago_id',
        'plazopago_id',
        'contactonombre',
        'contactoemail',
        'contactotelef',
        'finanzascontacto',
        'finanzanemail',
        'finanzastelefono',
        'sucursal_id',
        'observaciones',
        'usuariodel_id'
    ];
    //RELACION DE UNO A MUCHOS Cotizacion
    public function cotizacion()
    {
        return $this->hasMany(Cotizacion::class);
    }
    
    //Relacion inversa a Vendedor
    public function vendedor()
    {
        return $this->belongsTo(Vendedor::class);
    }
    //Relacion inversa a Giros
    public function giro()
    {
        return $this->belongsTo(Giro::class);
    }
    //Relacion inversa a Comuna
    public function comuna()
    {
        return $this->belongsTo(Comuna::class,'comunap_id');
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
    /*
    //Relacion inversa a ClienteTemp
    public function clientetemp()
    {
        return $this->belongsTo(ClienteTemp::class);
    }*/

}
