<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class CategoriaGrupoValMes extends Model
{
    use SoftDeletes;
    protected $table = "categoriagrupovalmes";
    protected $fillable = [
        'grupoprod_id',
        'unidadmedida_id',
        'annomes',
        'costo',
        'metacomerkg'
    ];

    //RELACION INVERSA PARA BUSCAR EL PADRE DE UNA CLASE
    public function grupoprod()
    {
        return $this->belongsTo(GrupoProd::class);
    }
    
    public static function annomes($mesanno){
        $arraymeanno = explode(" ", $mesanno);
        switch ($arraymeanno[0]) {
            case "Enero":
                $mes = "01";
                break;
            case "Febrero":
                $mes = "02";
                break;
            case "Marzo":
                $mes = "03";
                break;
            case "Abril":
                $mes = "04";
                break;
            case "Mayo":
                $mes = "05";
                break;
            case "Junio":
                $mes = "06";
                break;
            case "Julio":
                $mes = "07";
                break;
            case "Agosto":
                $mes = "08";
                break;
            case "Septiembre":
                $mes = "09";
                break;
            case "Octubre":
                $mes = "10";
                break;
            case "Noviembre":
                $mes = "11";
                break;
            case "Diciembre":
                $mes = "12";
                break;
        }
        $resultado = $arraymeanno[1].$mes;
        return $resultado;
    }

    public static function mesanno($annomes){
        $mes = substr($annomes,4,2);
        switch ($mes) {
            case "01":
                $mes = "Enero";
                break;
            case "02":
                $mes = "Febrero";
                break;
            case "03":
                $mes = "Marzo";
                break;
            case "04":
                $mes = "Abril";
                break;
            case "05":
                $mes = "Mayo";
                break;
            case "06":
                $mes = "Junio";
                break;
            case "07":
                $mes = "Julio";
                break;
            case "08":
                $mes = "Agosto";
                break;
            case "09":
                $mes = "Septiembre";
                break;
            case "10":
                $mes = "Octubre";
                break;
            case "11":
                $mes = "Noviembre";
                break;
            case "12":
                $mes = "Diciembre";
                break;
        }
        $resultado = $mes . " " . substr($annomes,0,4);
        return $resultado;
    }

    public static function catgrupNoCreados($request){
        if(empty($request['categoriaprod_id'])){
            $cond_categoriaprod_id = " true ";
        }else{
            $categoriaprod_id = $request['categoriaprod_id'];    
            $cond_categoriaprod_id = "categoriaprod.id=$categoriaprod_id";        
        }
        if(empty($request['annomes'])){
            $cond_annomes = " true ";

        }else{
            $annomes = $request['annomes'];
            $cond_annomes = "annomes='$annomes'";

        }
        if(empty($request['id'])){
            $cond_categoriagrupovalmes = " true ";
        }else{
            $id = $request['id'];
            $cond_categoriagrupovalmes = " categoriagrupovalmes.id!=$id";
        }
        $sql = "
            SELECT grupoprod.id,grupoprod.gru_nombre
            FROM grupoprod INNER JOIN categoriaprod
            ON grupoprod.categoriaprod_id=categoriaprod.id
            WHERE $cond_categoriaprod_id
            and grupoprod.id NOT IN (SELECT grupoprod_id 
                                        FROM categoriagrupovalmes 
                                        WHERE $cond_annomes and $cond_categoriagrupovalmes
                                        and isnull(categoriagrupovalmes.deleted_at))
            and ISNULL(categoriaprod.deleted_at) AND ISNULL(grupoprod.deleted_at)
            order BY categoriaprod.id
        ";
        //dd($sql);
        $datas = DB::select($sql);
        //dd($sql);
        return $datas;
    }
}
