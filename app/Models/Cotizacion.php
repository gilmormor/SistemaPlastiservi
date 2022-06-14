<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cotizacion extends Model
{
    use SoftDeletes;
    protected $table = "cotizacion";
    protected $fillable = [
        'sucursal_id',
        'fechahora',
        'direccioncot',
        'cliente_id',
        'clientetemp_id',
        'clientedirec_id',
        'contacto',
        'email',
        'telefono',
        'observacion',
        'formapago_id',
        'vendedor_id',
        'plazoentrega',
        'plaentdias',
        'lugarentrega',
        'plazopago_id',
        'tipoentrega_id',
        'region_id',
        'provincia_id',
        'comuna_id',
        'giro_id',
        'neto',
        'piva',
        'iva',
        'total',
        'usuario_id',
        'aprobstatus',
        'aprobusu_id',
        'aprobfechahora',
        'usuariodel_id'
    ];
    //RELACION DE UNO A MUCHOS CotizacionDetalle
    public function cotizaciondetalles()
    {
        return $this->hasMany(CotizacionDetalle::class);
    }
    //RELACION DE UNO A MUCHOS Nota de Venta
    public function notaventa()
    {
        return $this->hasMany(NotaVenta::class);
    }

    //RELACION INVERSA Cliente
    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }
    //RELACION INVERSA ClienteDirecc
    public function clientedirec()
    {
        return $this->belongsTo(ClienteDirec::class);
    }
    //Relacion inversa a FormaPago
    public function formapago()
    {
        return $this->belongsTo(FormaPago::class);
    }
    //Relacion inversa a Vendedor
    public function vendedor()
    {
        return $this->belongsTo(Vendedor::class);
    }
    //Relacion inversa a PlazoPago
    public function plazopago()
    {
        return $this->belongsTo(PlazoPago::class);
    }
    //Relacion inversa a Sucursal
    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }
    //Relacion inversa a TipoEntrega
    public function tipoentrega()
    {
        return $this->belongsTo(TipoEntrega::class);
    }
    //RELACION INVERSA ClienteTemporal
    public function clientetemp()
    {
        return $this->belongsTo(ClienteTemp::class);
    }
}
