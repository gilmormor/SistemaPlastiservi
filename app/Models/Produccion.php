<?php

namespace App\Models;

use App\Models\Seguridad\Usuario;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Produccion extends Model
{
    use SoftDeletes;
    protected $table = "produccion";
    protected $fillable = [
        'opdet_id',
        'etapaprod_id',
        'producto_id',
        'sucursal_id',
        'cant',
        'kg',
        'kgscrap',
        'mtslineal',
        'obs',
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
    //RELACION UNO A MUCHOS ProduccionTransfer
    public function producciontransfers()
    {
        return $this->hasMany(ProduccionTransfer::class);
    }

}
