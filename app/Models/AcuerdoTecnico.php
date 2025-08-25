<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use SplFileInfo;

class AcuerdoTecnico extends Model
{
    use SoftDeletes;
    protected $table = "acuerdotecnico";
    protected $fillable = [
        'at_notaventadetalle_id',
        'producto_id',
        'at_claseprod_id',
        'at_grupoprod_id',
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
        'at_formatofilm',
        'at_firmado',
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
    public function notaventadetalle()
    {
        return $this->belongsTo(NotaVentaDetalle::class,'at_notaventadetalle_id');
    }

    //RELACION DE UNO A MUCHOS acuerdotecnico_cliente
    public function acuerdotecnico_cliente()
    {
        return $this->hasMany(AcuerdoTecnico_Cliente::class);
    }

    //RELACION INVERSA PARA BUSCAR EL PADRE
    public function anchounidadmedida()
    {
        return $this->belongsTo(UnidadMedida::class,"at_anchoum_id");
    }
    //RELACION INVERSA PARA BUSCAR EL PADRE
    public function largounidadmedida()
    {
        return $this->belongsTo(UnidadMedida::class,"at_largoum_id");
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
    
    //RELACION INVERSA PARA BUSCAR EL PADRE
    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

    //RELACION DE UNO A MUCHOS acuerdotecnicocvalatdet
    public function acuerdotecnicocvalatdets()
    {
        return $this->hasMany(AcuerdoTecnicoCValAtDet::class,"acuerdotecnico_id");
    }

    public static function buscaratxcampos($request)
    {
        $request = new Request($request);
        //dd($request);
        //dd($request);
        $aux_ta_fuelleCond = " true ";
        if(!is_null($request->at_fuelle)){
            $aux_ta_fuelleCond = "if(isnull(at_fuelle),'',at_fuelle) = $request->at_fuelle";
        }
        $aux_ta_largoCond = " true ";
        if(!is_null($request->at_fuelle)){
            $aux_ta_largoCond = "if(isnull(at_largo),'',at_largo) = $request->at_largo";
        }
        $aux_at_feunidxpaq = $request->at_feunidxpaq;
        if(is_null($request->at_feunidxpaq)){
            $aux_at_feunidxpaq = "";
        }
        $aux_at_feunidxcont = $request->at_feunidxcont;
        if(is_null($request->at_feunidxcont)){
            $aux_at_feunidxcont = "";
        }
        $aux_at_feunitxpalet = $request->at_feunitxpalet;
        if(is_null($request->at_feunitxpalet)){
            $aux_at_feunitxpalet = "";
        }
        $aux_Condat_formatofilm = "at_formatofilm = $request->at_formatofilm";
        if(is_null($request->at_formatofilm) or empty($request->at_formatofilm) or $request->at_formatofilm == ""){
            $aux_Condat_formatofilm = "at_formatofilm = 0";
        }    
        $json = json_decode($request->objtxt);
        $sql = "SELECT acuerdotecnico.*, producto.nombre as producto_nombre
        FROM acuerdotecnico INNER JOIN producto
        on acuerdotecnico.producto_id = producto.id
        WHERE at_claseprod_id = $request->at_claseprod_id
        and at_materiaprima_id = $request->at_materiaprima_id
        and at_color_id = $request->at_color_id
        and at_pigmentacion = $request->at_pigmentacion
        and at_translucidez = $request->at_translucidez
        and at_uv = $request->at_uv
        and at_antideslizante = $request->at_antideslizante
        and at_antiestatico = $request->at_antiestatico
        and at_antiblock = $request->at_antiblock
        and at_aditivootro = $request->at_aditivootro
        and at_ancho = $request->at_ancho
        and $aux_ta_fuelleCond
        and $aux_ta_largoCond
        and at_espesor = $request->at_espesor
        and at_impreso = $request->at_impreso
        and at_tiposello_id = '$request->at_tiposello_id'
        and if(isnull(at_feunidxpaq),'',at_feunidxpaq) = '$aux_at_feunidxpaq' 
        and if(isnull(at_feunidxcont),'',at_feunidxcont) = '$aux_at_feunidxcont' 
        and if(isnull(at_feunitxpalet),'',at_feunitxpalet) = '$aux_at_feunitxpalet' 
        and at_unidadmedida_id = $request->at_unidadmedida_id
        and $aux_Condat_formatofilm
        and at_etiqplastiservi = $request->at_etiqplastiservi
        and isnull(acuerdotecnico.deleted_at)";
        //dd($sql);
        $datas = DB::select($sql);
        //dd($datas);
        return $datas;
        //return datatables($datas)->toJson();


        //return $respuesta;
        //return response()->json($productos->get());
    }

    public static function setImagen($request){
        //dd($request);
        //dd(isset($request->at_fileimpresofoto));
        /* if (!is_null($request->namefileold) and $request->namefileold != "") {
            Storage::disk('public')->delete("$request->url"."$request->namefileold");
        }

        if ($request->hasFile($request->nombrecampofile) && $request->file($request->nombrecampofile)->isValid()) {
            $file = $request->file($request->nombrecampofile);
            $nombre = $file->getClientOriginalName();
            $info = new SplFileInfo($nombre);
            $ext = strtolower($info->getExtension()); //Obtener extencion de un archivo
            $imageName = $request->namefilenew . '.' . $ext;
            $file->move(public_path() . "/storage" . "$request->url" , $imageName);
            return $imageName;
        } */

        // Ruta completa para eliminación
        $rutaCompleta = $request->url . $request->namefileold;
        //dd($rutaCompleta . " del: " . $request->file_del);
        if($request->file_del == "1"){
            Storage::disk('public')->delete($rutaCompleta);           
        }
        // 1. Si el usuario NO envió un archivo nuevo:
        /* if (!$request->hasFile($request->nombrecampofile)) {
            // Si existía una imagen previa, eliminarla
            if (!is_null($request->namefileold) && $request->namefileold != "" and $request->at_fileimpresofoto_deleted == "1") {
                Storage::disk('public')->delete($rutaCompleta);
                return null; // Indicamos que se eliminó
            } else {
                return null; // No hay nada que hacer
            }
        } */
        // 2. Si se envió un archivo nuevo y es válido
        if ($request->file_loader == "1" and $request->file($request->nombrecampofile)->isValid()) {
            // Procesar nuevo archivo
            $file = $request->file($request->nombrecampofile);
            $nombre = $file->getClientOriginalName();
            $ext = strtolower((new \SplFileInfo($nombre))->getExtension());

            $imageName = $request->namefilenew . '.' . $ext;

            // Mover archivo
            $file->move(public_path() . "/storage" . $request->url, $imageName);
            //$file->move(public_path("storage" . $request->url), $imageName);

            return $imageName;
        }else{
            if($request->file_del == "1"){
                return "del";
            }
            
        }

        return null; // No se cargó ni se eliminó nada
    }

        public static function setAtFirmado($foto,$id,$request,$at_imagen,$imagen, $actual = false){
        //dd($foto);
        if ($foto) {
            if ($actual) {
                Storage::disk('public')->delete("imagenes/attempfirm/$actual");
            }
            //dd($at_imagen);
            $file = $request->file($at_imagen);
            $nombre = $file->getClientOriginalName();
            $info = new SplFileInfo($nombre);
            $ext = strtolower($info->getExtension()); //Obtener extencion de un archivo
            //$imageName = Str::random(10) . '.jpg';
            $imageName = 'attempfirm' . $id . '.' . $ext;
            $file->move(public_path() . "/storage/imagenes/attempfirm/" , $imageName);
            //$request->file('')
            return $imageName;
        } else {
            if ($actual and ($imagen == "" or is_null($imagen))) {
                Storage::disk('public')->delete("imagenes/attempfirm/$actual");
                return "del";
            }else{
                return false;
            }
        }
    }

}
