<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RechazoResMedTom extends Model
{
    use SoftDeletes;
    protected $table = "rechazoresmedtom";
    protected $fillable = [
        'accorrec',
        'accorrecfec',
        'fechacompromiso',
        'fechacompromisofec',
        'fechaguardado',
        'cumplimiento',
        'fechacumplimiento',
        'aprobpaso2',
        'aprobpaso2',
        'fecaprobpaso2',
        'resmedtom',
        'fecharesmedtom',
        'fecha',
        'descripcion',
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
