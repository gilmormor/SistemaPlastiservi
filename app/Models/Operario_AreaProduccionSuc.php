<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Operario_AreaProduccionSuc extends Model
{
    use SoftDeletes;
    protected $table = "operario_areaproduccionsuc";
    protected $fillable = [
        'operario_id',
        'areaproduccionsuc_id',
        'usuariodel_id'
    ];

    //RELACION DE UNO A MUCHOS producciondet
    public function producciondets()
    {
        return $this->hasMany(ProduccionDet::class,'operarioapsuc_id');
    }

    //RELACION INVERSA PARA BUSCAR EL PADRE
    public function areaproduccionsuc()
    {
        return $this->belongsTo(AreaProduccionSuc::class,'areaproduccionsuc_id');
    }
    //RELACION INVERSA PARA BUSCAR EL PADRE
    public function operario()
    {
        return $this->belongsTo(Operario::class,'operario_id');
    }
}
