<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvMovDet extends Model
{
    use SoftDeletes;
    protected $table = "invmovdet";
    protected $fillable = [
        'annomes',
        'invmov_id',
        'invstock_id',
        'cant',
        'cantkg',
        'unidadmedida_id',
        'producto_id',
        'invbodega_id',
        'invmovtipo_id',
        'usuariodel_id'
    ];

    //RELACION INVERSA InvMov
    public function invmov()
    {
        return $this->belongsTo(InvMov::class);
    }

    //RELACION INVERSA InvMovTipo
    public function invmovtipo()
    {
        return $this->belongsTo(InvMovTipo::class);
    }
    //RELACION INVERSA InvMovStock
    public function invmovStock()
    {
        return $this->belongsTo(invmovStock::class);
    }
    
    
}
