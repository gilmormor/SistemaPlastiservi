<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NoConformidad extends Model
{
    use SoftDeletes;
    protected $table = "noconformidad";
    protected $fillable = [
        'usuario_id',
        'motivonc_id',
        'puntonormativo',
        'hallazgo',
        'formadeteccionnc_id',
        'puntonorma',
        'accioninmediata',
        'analisisdecausa',
        'accioncorrectiva',
        'fechacompromiso',
        'fechaguardado',
        'cumplimiento',
        'fechacumplimiento',
        'aprobado',
        'fechaaprobado',
        'rechazonc_id',
        'usuariodel_id'
    ];

    //RELACION INVERSA motivonc PADRE
    public function motivonc()
    {
        return $this->belongsTo(MotivoNc::class);
    }
    //RELACION INVERSA User PADRE
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    //RELACION INVERSA formadeteccionnc PADRE
    public function formadeteccionnc()
    {
        return $this->belongsTo(FormaDeteccionNC::class);
    }

    //RELACION INVERSA rechazonc PADRE
    public function rechazonc()
    {
        return $this->belongsTo(RechazoNC::class);
    }

    //RELACION DE UNO A MUCHOS rechazonc
    public function rechazoncs()
    {
        return $this->hasMany(RechazoNC::class);
    }

    //RELACION MUCHOS A MUCHOS A TRAVES DE noconformidad_certificado
    public function certificados()
    {
        return $this->belongsToMany(Certificado::class, 'noconformidad_certificado');
    }
    
    //RELACION MUCHOS A MUCHOS A TRAVES DE noconformidad_jefsucarea
    public function jefatura_sucursal_areas()
    {
        return $this->belongsToMany(JefaturaSucursalArea::class, 'noconformidad_jefsucarea');
    }

    //RELACION MUCHOS A MUCHOS A TRAVES DE noconformidad_responsable JEFE DE DPTO
    public function jefatura_sucursal_area_responsables()
    {
        return $this->belongsToMany(JefaturaSucursalArea::class, 'noconformidad_responsable');
    }
}
