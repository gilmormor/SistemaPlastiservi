<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RechazoNC extends Model
{
    use SoftDeletes;
    protected $table = "rechazonc";
    protected $fillable = [
        'fecha',
        'accioninmediata',
        'accioninmediatafec',
        'analisiscausa',
        'analisisdecausafec',
        'accioncorrectiva',
        'accorrecfec',
        'fechacompromiso',
        'fechacompromisofec',
        'fechaguardado',
        'cumplimiento',
        'fechacumplimiento',
        'aprobpaso2',
        'fecaprobpaso2',
        'noconformidad_id',
        'usuariodel_id'
    ];

    //RELACION DE UNO A UNO CON noconformidad
    public function noconformidad()
    {
        return $this->hasOne(NoConformidad::class);
    }

    //RELACION INVERSA noconformidad PADRE
    public function noconformidadinv()
    {
        return $this->belongsTo(NoConformidad::class);
    }

}
