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
        'at_glosa',
        'at_desc',
        'at_complementonomprod',
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
        'at_unidadmedida_id',
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
        'at_aprobado',
        'at_formatofilm',
        'at_peso',
        'at_cantxunimed',
        'at_firmado',
        'at_stacorregirat',
        'usuariodel_id'
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

    //RELACION INVERSA PARA BUSCAR EL PADRE
    public function unidadmedida()
    {
        return $this->belongsTo(UnidadMedida::class,"at_unidadmedida_id");
    }
    //RELACION DE UNO A MUCHOS acuerdotecnicotempvalat
    public function acuerdotecnicotempcvalatdets()
    {
        return $this->hasMany(AcuerdoTecnicoTempCValAtDet::class,"acuerdotecnicotemp_id");
    }
    //RELACION DE UNO A MUCHOS acuerdotecnicotempvalat
    public function acuerdotecnicocvalatdets()
    {
        return $this->hasMany(AcuerdoTecnicoTempCValAtDet::class,"acuerdotecnicotemp_id");
    }

    //RELACION INVERSA PARA BUSCAR EL PADRE
    public function producto()
    {
        return $this->belongsTo(Producto::class);
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

        public static function setAtFirmado($foto,$id,$request,$at_imagen,$imagen, $actual = false){
        //dd($foto);
        if ($foto) {
            if ($actual) {
                Storage::disk('public')->delete("imagenes/atfirmtemp/$actual");
            }
            //dd($at_imagen);
            $file = $request->file($at_imagen);
            $nombre = $file->getClientOriginalName();
            $info = new SplFileInfo($nombre);
            $ext = strtolower($info->getExtension()); //Obtener extencion de un archivo
            //$imageName = Str::random(10) . '.jpg';
            $imageName = 'atfirmtemp' . $id . '.' . $ext;
            $file->move(public_path() . "/storage/imagenes/atfirmtemp/" , $imageName);
            //$request->file('')
            return $imageName;
        } else {
            if ($actual and ($imagen == "" or is_null($imagen))) {
                Storage::disk('public')->delete("imagenes/atfirmtemp/$actual");
                return "del";
            }else{
                return false;
            }
        }
    }

    protected static function boot()
    {
        parent::boot();

        // Calcula at_peso automáticamente al guardar si viene vacío o en 0.
        // Cubre todos los flujos de creación (cotización, NV, etc.) sin tocar controladores.
        // Si at_peso ya trae valor (ej: asignado por código de la rama de producción/módulo producción) no se pisa.
        static::saving(function (AcuerdoTecnicoTemp $acuerdo) {
            if (empty($acuerdo->at_peso) && function_exists('pesounitattemp')) {
                try {
                    $acuerdo->at_peso = pesounitattemp($acuerdo);
                } catch (\Exception $e) {
                    // Nunca bloquear el guardado por un fallo del cálculo de peso
                }
            }
        });
    }

    // Agregar accesor para producto_nombre
    public function getProductoNombreAttribute()
    {
        // Verificar si el modelo tiene un código de producto
        if (isset($this->id)) {
            // Llamar a la función estática para obtener los atributos
            $productoArray = self::atributosProducto();

            // Retornar el nombre si existe en el arreglo
            return $productoArray['nombre'] ?? null;
        }

        // Retornar null si no se cumple la condición
        return null;
    }
    

    public function atributosProducto(){
        $producto = $this->cotizaciondetalle->producto;
        //$producto = AcuerdoTecnicoTemp::findOrFail($cotizaciondetalle_id);
        $aux_nombreprod = $producto->nombre;
        $aux_cla_nombre = "";
        $at_espesor = "";
        $aux_tipounion = "";
        $aux_atribAcuTec = "";
        $at_anchoT = "";
        $at_largoT = "";
        $aux_color = "";
        $aux_unidmed = "";
        $unidadmedida_id = "";
        if(isset($this->id)){
            $at_ancho = $this->at_ancho;
            $at_largo = $this->at_largo;
            $at_espesor = $this->at_espesor;
            $at_ancho = empty($at_ancho) ? "0,00" : $at_ancho;
            $at_largo = empty($at_largo) ? "0,00" : $at_largo;
            $at_espesor = empty($at_espesor) ? "0,00" : $at_espesor;
            //$aux_nombreprod = $aux_nombreprod . " " . $at_ancho . "x" . $at_largo . "x" . $at_espesor;

            $AcuTec = $this;
            $aux_formatofilm = $AcuTec->at_formatofilm > 0 ? " " . number_format($AcuTec->at_formatofilm, 2, ',', '.') . "Kg." : "";
            $aux_color =  empty($AcuTec->color->descripcion) ? "" : " " . $AcuTec->color->descripcion;
            $aux_at_complementonomprod = empty($AcuTec->at_complementonomprod) ? "" : " " . $AcuTec->at_complementonomprod;
            $aux_atribAcuTec = $AcuTec->materiaprima->descfact . $aux_color . $aux_at_complementonomprod . $aux_formatofilm;
            //CONCATENAR TODO LOS CAMPOS NECESARIOS PARA QUE SE FORME EL NOMBRE DEL RODUCTO EN LA GUIA
            $aux_nombreprod = nl2br($producto->categoriaprod->nombre . " " . $aux_atribAcuTec . " " . $at_ancho . "x" . $at_largo . "x" . number_format($AcuTec->at_espesor, 3, ',', '.'));
            $at_materiaprima = $this->materiaprima->nombre;
            $aux_unidmed = $this->unidadmedida->nombre;
            $unidadmedida_id = $this->unidadmedida->id;
            $aux_cla_nombre = $this->claseprod->cla_nombre;
        }
        $atributoProducto = [
            "nombre" => $aux_nombreprod,
            "at_ancho" => $at_ancho,
            "at_largo" => $at_largo,
            "at_espesor" => $at_espesor,
            "at_anchoT" => $at_anchoT,
            "at_largoT" => $at_largoT,
            "cla_nombre" => $aux_cla_nombre,
            "tipounion" => $aux_tipounion,
            "at_materiaprima" => $at_materiaprima,
            "at_color" => $aux_color,
            "at_unidmed" => $aux_unidmed,
            "unidadmedida_id" => $unidadmedida_id
            
        ];
        return $atributoProducto;
    }
}
