<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cliente extends Model
{
    use SoftDeletes;
    protected $table = "cliente";
    protected $fillable = [
        'rut',
        'razonsocial',
        'direccion',
        'telefono',
        'email',
        'nombrefantasia',
        'sta_temp',
        'giro_id',
        'regionp_id',
        'provinciap_id',
        'comunap_id',
        'formapago_id',
        'plazopago_id',
        'contactonombre',
        'contactoemail',
        'contactotelef',
        'mostrarguiasfacturas',
        'finanzascontacto',
        'finanzanemail',
        'finanzastelefono',
        'observaciones'
    ];

    //RELACION DE UNO A MUCHOS Cotizacion
    public function cotizacion()
    {
        return $this->hasMany(Cotizacion::class);
    }
    
    public function clientedirecs()
    {
        return $this->hasMany(ClienteDirec::class);
    }
    //Relacion inversa a Vendedor
    public function vendedor()
    {
        return $this->belongsTo(Vendedor::class);
    }
    public function clientebloqueados()
    {
        return $this->hasMany(ClienteBloqueado::class);
    }
    //RELACION MUCHO A MUCHOS CON USUARIO A TRAVES DE cliente_vendedor
    public function vendedores()
    {
        return $this->belongsToMany(Vendedor::class, 'cliente_vendedor');
    }
    //Relacion inversa a Giros
    public function giro()
    {
        return $this->belongsTo(Giro::class);
    }
    //RELACION MUCHO A MUCHOS CON USUARIO A TRAVES DE cliente_sucursal
    public function sucursales()
    {
        return $this->belongsToMany(Sucursal::class, 'cliente_sucursal');
    }
    //Relacion inversa a Region
    public function region()
    {
        return $this->belongsTo(Region::class,'regionp_id');
    }
    //Relacion inversa a Provincia
    public function provincia()
    {
        return $this->belongsTo(Provincia::class,'provinciap_id');
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
    
}
