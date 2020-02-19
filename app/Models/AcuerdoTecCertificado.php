<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AcuerdoTecCertificado extends Model
{
    use SoftDeletes;
    protected $table = "acuerdotectemp_certificado";
    protected $fillable = ['acuerdotecnicotemp_id','certificado_id','usuariodel_id'];

    //RELACION INVERSA PARA BUSCAR EL PADRE
    public function acuerdotectemp()
    {
        return $this->belongsTo(AcuerdoTecTemp::class,'acuerdotecnicotemp_id');
    }
    //RELACION INVERSA PARA BUSCAR EL PADRE
    public function certificado()
    {
        return $this->belongsTo(Certificado::class,'certificado_id');
    }
}
