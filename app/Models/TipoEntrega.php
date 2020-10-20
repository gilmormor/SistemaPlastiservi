<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TipoEntrega extends Model
{
    use SoftDeletes;
    protected $table = "tipoentrega";
    protected $fillable = [
                    'nombre',
                    'abrev',
                    'icono',
                    'usuariodel_id'
                ];

    public function cotizacion()
    {
        return $this->hasOne(Cotizacion::class);
    }
    //RELACION DE UNO A MUCHOS Cotizacion
    public function cotizaciones()
    {
        return $this->hasMany(Cotizacion::class,'tipoentrega_id');
    }
    
    //RELACION DE UNO A MUCHOS NotaVenta
    public function notaventas()
    {
        return $this->hasMany(NotaVenta::class,'tipoentrega_id');
    }

    //RELACION DE UNO A MUCHOS DespachoSol
    public function despachosols()
    {
        return $this->hasMany(DespachoSol::class,'tipoentrega_id');
    }
    //RELACION DE UNO A MUCHOS DespachoOrd
    public function despachoords()
    {
        return $this->hasMany(DespachoOrd::class,'tipoentrega_id');
    }
}
