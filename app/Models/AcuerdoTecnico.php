<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AcuerdoTecnico extends Model
{
    protected $table = "acuerdotecnico";
    protected $fillable = [
        'producto_id',
        'at_desc',
        'at_entmuestra',
        'at_color_id',
        'at_colordesc',
        'at_npantone',
        'at_translucidez',
        'at_materiaprima_id',
        'at_materiaprimaobs',
        'at_usoprevisto',
        'at_uv',
        'at_uvobs',
        'at_antideslizante',
        'at_antideslizanteobs',
        'at_antiestatico',
        'at_antiestaticoobs',
        'at_antiblock',
        'at_antiblockobs',
        'at_aditivootro',
        'at_aditivootroobs',
        'at_ancho',
        'at_anchoum_id',
        'at_anchodesv',
        'at_largo',
        'at_largoum_id',
        'at_largodesv',
        'at_fuelle',
        'at_fuelleum_id',
        'at_fuelledesv',
        'at_espesor',
        'at_espesorum_id',
        'at_espesordesv',
        'at_impreso',
        'at_impresoobs',
        'at_sfondo',
        'at_sfondoobs',
        'at_slateral',
        'at_slateralobs',
        'at_sprepicado',
        'at_sprepicadoobs',
        'at_slamina',
        'at_slaminaobs',
        'at_sfunda',
        'at_sfundaobs',
        'at_feunidxpaq',
        'at_feunidxpaqobs',
        'at_feunidxcont',
        'at_feunidxcontobs',
        'at_fecolorcont',
        'at_fecolorcontobs',
        'at_feunitxpalet',
        'at_feunitxpaletobs',
        'at_etiqplastiservi',
        'at_etiqplastiserviobs',
        'at_etiqotro',
        'at_etiqotroobs',
        'at_certificados',
        'at_otrocertificado',
        'usuariodel_id'
    ];

    //RELACION DE UNO A MUCHOS PRODUCTO
    public function productos()
    {
        return $this->hasMany(Producto::class);
    }
    
}
