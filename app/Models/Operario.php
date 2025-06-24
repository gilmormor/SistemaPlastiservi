<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Operario extends Model
{
    use SoftDeletes;
    protected $table = "operario";
    protected $fillable = [
        'nombre',
        'desc',
        'usuario_id',
        'usuariodel_id'
    ];

    //RELACION MUCHO A MUCHOS CON areaproduccionsuc A TRAVES DE operario_areaproduccionsuc
    public function areaproduccionsucs()
    {
        return $this->belongsToMany(AreaProduccionSuc::class, 'operario_areaproduccionsuc','operario_id','areaproduccionsuc_id')->withTimestamps();
    }
    
    
}
