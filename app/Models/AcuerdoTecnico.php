<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

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
        and isnull(acuerdotecnico.deleted_at)";
        $datas = DB::select($sql);
        //dd($datas);
        return $datas;
        //return datatables($datas)->toJson();


        //return $respuesta;
        //return response()->json($productos->get());
    }

}
