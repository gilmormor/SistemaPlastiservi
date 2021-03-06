<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NoConformidad_Certificado extends Model
{
    use SoftDeletes;
    protected $table = "noconformidad_certificado";
    protected $fillable = [
        'noconformidad_id',
        'certificado_id',
        'usuariodel_id'
    ];

    //RELACION INVERSA PARA BUSCAR EL PADRE NOCONFORMIDAD
    public function noconformidad()
    {
        return $this->belongsTo(NoConformidad::class,'noconformidad_id');
    }
    
    //RELACION INVERSA PARA BUSCAR EL PADRE CERTIFICADO
    public function certificado()
    {
        return $this->belongsTo(Certificado::class,'certificado_id');
    }
    
}
