<?php

namespace App\Models;

use App\Models\Seguridad\Usuario;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OpDetRegProd extends Model
{
    use SoftDeletes;
    protected $table = "opdetregprod";
    protected $fillable = [
        'opdetregprodtemp_id',
        'opdet_id',
        'etapaprod_id',
        'producto_id',
        'sucursal_id',
        'cant',
        'kg',
        'kgscrap',
        'mtslineal',
        'obs',
        'operario_id',
        'aprobstatus',
        'aprobusu_id',
        'aprobfechahora',
        'aprobobs',
        'usuario_id',
        'usuariodel_id',
    ];
    //RELACION INVERSA opdet
    public function opdet()
    {
        return $this->belongsTo(OpDet::class);
    }
    //RELACION INVERSA Usuario
    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }
}
