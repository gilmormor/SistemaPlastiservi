<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PesajeDet extends Model
{
    use SoftDeletes;
    protected $table = "pesajedet";
    protected $fillable = [
        'pesaje_id',
        'invbodegaproducto_id',
        'producto_id',
        'invbodega_id',
        'sucursal_id',
        'unidadmedida_id',
        'invmovtipo_id',
        'turno_id',
        'pesajecarro_id',
        'areaproduccionsuclinea_id',
        'cant',
        'cantgrupo',
        'cantxgrupo',
        'peso',
        'pesounitnom',
        'cantkg',
        'tara',
        'pesobaltotal',
        'usuariodel_id'
    ];
    //RELACION UNO A MUCHOS PesajeDet
    public function pesajedets()
    {
        return $this->hasMany(PesajeDet::class);
    }
    //RELACION INVERSA InvMovModulo
    public function invmovmodulo()
    {
        return $this->belongsTo(InvMovModulo::class);
    }
    //RELACION INVERSA InvMovTipo
    public function invmovtipo()
    {
        return $this->belongsTo(InvMovTipo::class);
    }
    //RELACION INVERSA InvMovTipo
    public function invbodega()
    {
        return $this->belongsTo(InvBodega::class);
    }
    //Relacion inversa a UnidadMedida
    public function unidadmedida()
    {
        return $this->belongsTo(UnidadMedida::class);
    }
    //Relacion inversa a Producto
    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }
    //RELACION INVERSA invbodegaproducto
    public function invbodegaproducto()
    {
        return $this->belongsTo(InvBodegaProducto::class);
    }
    //RELACION INVERSA Turno
    public function turno()
    {
        return $this->belongsTo(Turno::class);
    }
    //RELACION INVERSA PesajeCarro
    public function pesajecarro()
    {
        return $this->belongsTo(PesajeCarro::class);
    }
    //RELACION INVERSA AreaProduccionSucLinea
    public function areaproduccionsuclinea()
    {
        return $this->belongsTo(AreaProduccionSucLinea::class);
    }    
}
