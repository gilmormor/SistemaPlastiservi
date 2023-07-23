<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Factura extends Model
{
    use SoftDeletes;
    protected $table = "factura";
    protected $fillable = [
        'nrodocto',
        'fechahora',
        'termpagoglosa',
        'fchvenc',
        'sucursal_id',
        'cliente_id',
        'comuna_id',
        'vendedor_id',    
        'obs',
        'fchemis',
        'tipodespacho',
        'indtraslado',
        'ciudadrecep',
        'mntneto',
        'tasaiva',
        'iva',
        'mnttotal',
        'kgtotal',
        'tipoentrega_id',
        'lugarentrega',
        'comunaentrega_id',
        'centroeconomico_id',
        'aprobstatus',
        'aprobusu_id',
        'aprobfechahora',
        'usuario_id',
        'usuariodel_id'
    ];


    //RELACION DE UNO A MUCHOS GuiaDespDet
    public function facturadets()
    {
        return $this->hasMany(FacturaDet::class,'factura_id');
    }

    //RELACION INVERSA Cliente
    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    //Relacion inversa a Usuario
    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }

    //Relacion inversa a User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function comunaentrega()
    {
        return $this->belongsTo(Comuna::class,'comunaentrega_id');
    }
    //Relacion inversa a TipoEntrega
    public function tipoentrega()
    {
        return $this->belongsTo(TipoEntrega::class);
    }

    //RELACION DE UNO A MUCHOS FacturaAnul
    public function facturaanuls()
    {
        return $this->hasOne(FacturaAnul::class,'factura_id');
    }
    
    //RELACION MUCHO A MUCHOS CON GuiaDesp A TRAVES DE factura_guiadesp
    public function guiadesps()
    {
        return $this->belongsToMany(FacturaGuiaDesp::class, 'factura_guiadesp')->withTimestamps();
    }
    

}
