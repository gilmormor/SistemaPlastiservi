<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AcuerdoTecTemp extends Model
{
    use SoftDeletes;
    protected $table = "acuerdotectemp";
    protected $fillable = [
        'entmuestra',
        'matfrabobs',
        'usoprevisto',
        'uv',
        'uvobs',
        'antideslizante',
        'antideslizanteobs',
        'antiestatico',
        'antiestaticoobs',
        'antiblock',
        'antiblockobs',
        'aditivootro',
        'aditivootroobs',
        'ancho',
        'anchoum',
        'anchodesv',
        'largo',
        'largoum',
        'largodesv',
        'fuelle',
        'fuelleum',
        'fuelledesv',
        'espesor',
        'espesorum',
        'espesordesv',
        'color',
        'npantone',
        'translucidez',
        'impreso',
        'impresofoto',
        'impresocolor',
        'impresoobs',
        'sfondo',
        'sfondoobs',
        'slateral',
        'slateralobs',
        'sprepicado',
        'sprepicadoobs',
        'slamina',
        'slaminaobs',
        'sfunda',
        'sfundaobs',
        'feunidxpaq',
        'feunidxpaqobs',
        'feunidxcont',
        'feunidxcontobs',
        'fecolorcont',
        'fecolorcontobs',
        'feunitxpalet',
        'feunitxpaletobs',
        'etiqplastiservi',
        'etiqplastiserviobs',
        'etiqotro',
        'etiqotroobs',
        'despacharA',
        'fechacuerdocli',
        'clientedirec_id',
        'formapago_id',
        'plazopago_id',
        'vendedor_id',
        'cliente_id',
        'matfrab_id',
        'usuariodel_id'
    ];

    public function certificados()
    {
        return $this->belongsToMany(Certificado::class, 'acuerdotectemp_certificado')->withTimestamps();
    }

    //RELACION INVERSA MatFabr
    public function matfabr()
    {
        return $this->belongsTo(MatFabr::class);
    }
    //RELACION INVERSA Color
    public function color()
    {
        return $this->belongsTo(Color::class);
    }
    //RELACION INVERSA clientedirec
    public function clientedirec()
    {
        return $this->belongsTo(ClienteDirec::class);
    }
    
}
