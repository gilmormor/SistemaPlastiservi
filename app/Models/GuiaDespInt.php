<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GuiaDespInt extends Model
{
    use SoftDeletes;
    protected $table = "guiadespint";
    protected $fillable = [
        'fechahora',
        'cli_rut',
        'cli_nom',
        'cli_dir',
        'cli_tel',
        'cli_email',
        'observacion',
        'plazoentrega',
        'lugarentrega',
        'comunaentrega_id',
        'total',
        'usuario_id',
        'aprobstatus',
        'aprobusu_id',
        'aprobfechahora',
        'aprobobs',
        'anulada',
        'usuariodel_id',
        'sucursal_id',
        'clienteinterno_id',
        'formapago_id',
        'vendedor_id',
        'plazopago_id',
        'tipoentrega_id',
        'comuna_id'
    ];

    //RELACION DE UNO A MUCHOS GuiaDespInt
    public function guiadespintdetalle()
    {
        return $this->hasMany(GuiaDespIntDetalle::class,'guiadespint_id');
    }

    //RELACION INVERSA Cliente
    public function clienteinterno()
    {
        return $this->belongsTo(ClienteInterno::class);
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
    //Relacion inversa a Comuna
    public function comuna()
    {
        return $this->belongsTo(Comuna::class);
    }
    public function comunaentrega()
    {
        return $this->belongsTo(Comuna::class,'comunaentrega_id');
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

    //RELACION INVERSA User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    //RELACION INVERSA User
    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }
    //RELACION INVERSA aprobusu, Usuario que aprobo la Guia despacho
    public function aprobusu()
    {
        return $this->belongsTo(Usuario::class,'aprobusu_id');
    }    
    
}