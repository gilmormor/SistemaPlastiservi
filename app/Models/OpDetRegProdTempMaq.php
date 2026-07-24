<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OpDetRegProdTempMaq extends Model
{
    protected $table = "opdetregprodtempmaq";
    protected $fillable = [
        'opdetregprodtemp_id',
        'maquina_id'
    ];
    //RELACION INVERSA OpDetRegProdTemp
    public function opdetregprodtemp()
    {
        return $this->belongsTo(OpDetRegProdTemp::class);
    }

}
