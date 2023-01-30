<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AcuerdoTecnico_Cliente extends Model
{
    use SoftDeletes;
    protected $table = "acuerdotecnico_cliente";
    protected $fillable = [
        'acuerdotecnico_id',
        'cliente_id'
    ];
    
    //RELACION INVERSA PARA BUSCAR EL PADRE
    public function acuerdotecnico()
    {
        return $this->belongsTo(AcuerdoTecnico::class);
    }

    //RELACION INVERSA PARA BUSCAR EL PADRE
    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }
}
