<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AcuerdoTecnicoTemp_Cliente extends Model
{
    use SoftDeletes;
    protected $table = "acuerdotecnicotemp_cliente";
    protected $fillable = [
        'acuerdotecnicotemp_id',
        'cliente_id'
    ];
    
    //RELACION INVERSA PARA BUSCAR EL PADRE
    public function acuerdotecnico()
    {
        return $this->belongsTo(AcuerdoTecnicoTemp::class);
    }

    //RELACION INVERSA PARA BUSCAR EL PADRE
    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

}
