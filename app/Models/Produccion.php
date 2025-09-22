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
        'operario_id',
        'cantprod',
        'kgprod',
        'obs',
        'aprobstatus',
        'aprobusu_id',
        'aprobfechahora',
        'aprobobs',
        'usuario_id',
        'usuariodel_id',
    ];
    //RELACION UNO A MUCHOS ProduccionDet
    public function producciondets()
    {
        return $this->hasMany(ProduccionDet::class);
    }
    //RELACION INVERSA Usuario
    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }

}
