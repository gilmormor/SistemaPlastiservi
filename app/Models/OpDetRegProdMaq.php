<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OpDetRegProdMaq extends Model
{
    protected $table = "opdetregprodmaq";
    protected $fillable = [
        'opdetregprod_id',
        'maquina_id'
    ];
    //RELACION INVERSA OpDetRegProd
    public function opdetregprod()
    {
        return $this->belongsTo(OpDetRegProd::class);
    }
}
