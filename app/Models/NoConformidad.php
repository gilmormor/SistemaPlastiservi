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
        'fechahora',
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
        'aprobpaso2',
        'fecaprobpaso2',
        'rechazonc_id',
        'rechazoresmedtom_id',
        'resmedtom',
        'adjresmedtom',
        'acepresmedtom',
        'cierreaccorr',
        'adjcierreaccorr',
        'feccierreaccorr',
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

    //RELACION INVERSA rechazoresmedtom PADRE
    public function rechazoresmedtom()
    {
        return $this->belongsTo(RechazoResMedTom::class);
    }
    //RELACION DE UNO A MUCHOS rechazoresmedtom
    public function rechazoresmedtoms()
    {
        return $this->hasMany(RechazoResMedTom::class);
    }

    //RELACION MUCHOS A MUCHOS A TRAVES DE noconformidad_certificado
    public function certificados()
    {
        return $this->belongsToMany(Certificado::class, 'noconformidad_certificado','noconformidad_id');
    }
    
    //RELACION MUCHOS A MUCHOS A TRAVES DE noconformidad_jefsucarea
    public function jefaturasucursalareas()
    {
        return $this->belongsToMany(JefaturaSucursalArea::class, 'noconformidad_jefsucarea','noconformidad_id');
    }

    //RELACION MUCHOS A MUCHOS A TRAVES DE noconformidad_responsable JEFE DE DPTO
    public function jefaturasucursalarearesponsables()
    {
        return $this->belongsToMany(JefaturaSucursalArea::class, 'noconformidad_responsable','noconformidad_id');
    }
}
