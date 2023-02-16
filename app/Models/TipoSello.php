<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TipoSello extends Model
{
    use SoftDeletes;
    protected $table = "tiposello";
    protected $fillable = [
                    'desc',
                    'usuariodel_id'
                ];

    //RELACION DE UNO A MUCHOS Acuerdotecnico
    public function acuerdotecnicos()
    {
        return $this->hasMany(AcuerdoTecnico::class);
    }
    //RELACION DE UNO A MUCHOS Acuerdotecnicotemp
    public function acuerdotecnicotemps()
    {
        return $this->hasMany(AcuerdoTecnicoTemp::class);
    }
            
}
