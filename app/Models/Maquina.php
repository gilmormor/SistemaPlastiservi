<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Maquina extends Model
{
    use SoftDeletes;
    protected $table = "maquina";
    protected $fillable = [
        'sucursal_id',
        'maquinagrupo_id',
        'nombre',
        'desc',
        'usuario_id',
        'usuariodel_id'
    ];

    //Relacion inversa a Sucursal
    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }
    //Relacion inversa a MaquinaGrupo
    public function maquinagrupo()
    {
        return $this->belongsTo(MaquinaGrupo::class);
    }

    //RELACION MUCHO A MUCHOS CON etapaprod A TRAVES DE maquinaetapaprod
    public function etapaprods()
    {
        return $this->belongsToMany(EtapaProd::class, 'maquinaetapaprod','maquina_id','etapaprod_id')->withTimestamps();
    }

    
}
