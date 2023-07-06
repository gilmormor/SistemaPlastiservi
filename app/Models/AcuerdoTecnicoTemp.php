<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use SplFileInfo;

class AcuerdoTecnicoTemp extends Model
{
    use SoftDeletes;
    protected $table = "acuerdotecnicotemp";
    protected $fillable = [
        'at_cotizaciondetalle_id',
        'at_claseprod_id',
        'at_grupoprod_id',
        'at_desc',
        'at_entmuestra',
        'at_color_id',
        'at_npantone',
        'at_translucidez',
        'at_materiaprima_id',
        'at_materiaprimaobs',
        'at_usoprevisto',
        'at_pigmentacion',
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
        'at_unidadmedida',
        'at_impreso',
        'at_impresoobs',
        'at_tiposello_id',
        'at_tiposelloobs',
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
        'at_embalajeplastservi',
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
        'at_aprobado'
    ];

    //RELACION INVERSA PARA BUSCAR EL PADRE
    public function color()
    {
        return $this->belongsTo(Color::class,'at_color_id');
    }
    //RELACION INVERSA PARA BUSCAR EL PADRE
    public function materiaprima()
    {
        return $this->belongsTo(MateriaPrima::class,'at_materiaprima_id');
    }
    
    //RELACION DE UNO A MUCHOS cotizaciondetalle
    public function cotizaciondetalles()
    {
        return $this->hasMany(CotizacionDetalle::class);
    }
    //RELACION INVERSA PARA BUSCAR EL PADRE
    public function tiposello()
    {
        return $this->belongsTo(TipoSello::class,'at_tiposello_id');
    }

    //RELACION INVERSA PARA BUSCAR EL PADRE
    public function claseprod()
    {
        return $this->belongsTo(ClaseProd::class,"at_claseprod_id");
    }

    //RELACION INVERSA PARA BUSCAR EL PADRE
    public function grupoprod()
    {
        return $this->belongsTo(GrupoProd::class,"at_grupoprod_id");
    }

    //RELACION INVERSA PARA BUSCAR EL PADRE
    public function cotizaciondetalle()
    {
        return $this->belongsTo(CotizacionDetalle::class,'at_cotizaciondetalle_id');
    }
    
    //RELACION DE UNO A MUCHOS acuerdotecnicotemp_cliente
    public function acuerdotecnicotemp_cliente()
    {
        return $this->hasMany(AcuerdoTecnicoTemp_Cliente::class,"acuerdotecnicotemp_id");
    }
    //RELACION INVERSA PARA BUSCAR EL PADRE
    public function anchounidadmedida()
    {
        return $this->belongsTo(UnidadMedida::class,"at_anchoum_id");
    }
    //RELACION INVERSA PARA BUSCAR EL PADRE
    public function largounidadmedida()
    {
        return $this->belongsTo(UnidadMedida::class,"at_anchoum_id");
    }
    //RELACION INVERSA PARA BUSCAR EL PADRE
    public function fuelleunidadmedida()
    {
        return $this->belongsTo(UnidadMedida::class,"at_fuelleum_id");
    }
    //RELACION INVERSA PARA BUSCAR EL PADRE
    public function espesorunidadmedida()
    {
        return $this->belongsTo(UnidadMedida::class,"at_espesorum_id");
    }
    
    public static function setImagen($foto,$id,$request,$at_imagen,$imagen, $actual = false){
        //dd($foto);
        if ($foto) {
            if ($actual) {
                Storage::disk('public')->delete("imagenes/attemp/$actual");
            }
            //dd($at_imagen);
            $file = $request->file($at_imagen);
            $nombre = $file->getClientOriginalName();
            $info = new SplFileInfo($nombre);
            $ext = strtolower($info->getExtension()); //Obtener extencion de un archivo
            //$imageName = Str::random(10) . '.jpg';
            $imageName = 'attemp' . $id . '.' . $ext;
            //dd($imageName);
            //      $imagen = Image::make($foto)->encode('jpg', 75);
            //$imagen->fit(530, 470); //Fit() SUpuestamente mantiene la proporcion de la imagen
            /*$imagen->resize(530, 470, function ($constraint) {
                $constraint->upsize();
            });*/
            //Storage::disk('public')->put("imagenes/attemp/$imageName", $imagen->stream());
            //Storage::disk('public')->put("imagenes/attemp/$imageName", $file);
            $file->move(public_path() . "/storage/imagenes/attemp/" , $imageName);
            //$request->file('')
            return $imageName;
        } else {
            if ($actual and ($imagen == "" or is_null($imagen))) {
                Storage::disk('public')->delete("imagenes/attemp/$actual");
                return "del";
            }else{
                return false;
            }
        }
    }

    
}
