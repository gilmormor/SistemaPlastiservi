<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FacturaGuiaDesp extends Model
{
    protected $table = "factura_guiadesp";
    protected $fillable = ['factura_id','guiadesp_id'];

    //RELACION INVERSA A Cliente
    public function factura()
    {
        return $this->belongsTo(Cliente::class);
    }
    //RELACION INVERSA A GuiaDesp
    public function guiadesp()
    {
        return $this->belongsTo(GuiaDesp::class);
    }

}
