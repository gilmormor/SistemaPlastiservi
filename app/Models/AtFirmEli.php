<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AtFirmEli extends Model
{
    protected $table = "atfirmeli";
    protected $fillable = [
        'acuerdotecnico_id',
        'at_firmado',
        'usuario_id'
    ];
                        
    //RELACION INVERSA PARA BUSCAR EL PADRE
    public function acuerdotecnico()
    {
        return $this->belongsTo(AcuerdoTecnico::class);
    }
}
