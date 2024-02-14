<?php

namespace App\Models;

use App\Models\Seguridad\Usuario;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Pesaje extends Model
{
    use SoftDeletes;
    protected $table = "pesaje";
    protected $fillable = [
        'invmov_id',
        'fechahora',
        'desc',
        'obs',
        'annomes',
        'staanul',
        'invmovmodulo_id',
        'invmovtipo_id',
        'sucursal_id',
        'staaprob',
        'fechahoraaprob',
        'obsaprob',
        'usuario_id',
        'usuariodel_id'
    ];
    //RELACION UNO A MUCHOS PesajeDet
    public function pesajedets()
    {
        return $this->hasMany(PesajeDet::class);
    }
    //RELACION INVERSA InvMovModulo
    public function invmovmodulo()
    {
        return $this->belongsTo(InvMovModulo::class);
    }
    //RELACION INVERSA InvMovModulo
    public function invmovtipo()
    {
        return $this->belongsTo(InvMovTipo::class);
    }
    //RELACION INVERSA Sucursal
    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }
    //RELACION INVERSA Usuario
    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }

    public static function pesajeDet($request){
        //dd($request);
        //$aux_annomes = CategoriaGrupoValMes::annomes($request->mesanno);
        if(!isset($request->fechah) or empty($request->fechah)){
            $aux_condFecha = "true";
        }else{
            $fecha = date_create_from_format('d/m/Y', $request->fechah);
            $fechah = date_format($fecha, 'Y-m-d');
            $aux_condFecha = "DATE(pesaje.fechahora) = '$fechah'";
            if($request->statusSumPeriodo == 1){
                $fecha = date_create_from_format('d/m/Y', $request->fechad);
                $fechad = date_format($fecha, 'Y-m-d');    
                $aux_condFecha = "DATE(pesaje.fechahora) >= '$fechad' AND DATE(pesaje.fechahora) <= '$fechah'";
            }

        }
        if(!isset($request->sucursal_id) or empty($request->sucursal_id) or ($request->sucursal_id == "x")){
            $aux_sucursal_idCond = "false";
        }else{
            $aux_sucursal_idCond = "pesaje.sucursal_id = $request->sucursal_id";
        }
        if(!isset($request->categoriaprodgrupo_id) or empty($request->categoriaprodgrupo_id) or ($request->categoriaprodgrupo_id == "")){
            $aux_categoriaprodgrupo_idCond = "true";
        }else{
            $aux_categoriaprodgrupo_idCond = "categoriaprod.categoriaprodgrupo_id = $request->categoriaprodgrupo_id";
        }

        if(!isset($request->producto_id) or empty($request->producto_id)){
            $aux_producto_idCodn = "true";
        }else{
            $aux_producto_idCodn = " pesajedet.producto_id in ($request->producto_id) ";
        }
        $aux_groupby = "GROUP BY pesajedet.id";
        $aux_orderby = "ORDER BY turno.nombre ASC, pesajedet.producto_id ASC";
        if(!isset($request->agrurep_id) or empty($request->agrurep_id)){
        }else{
            if($request->agrurep_id == 2){
                $aux_groupby = "GROUP BY pesajedet.producto_id";
            }
            if($request->agrurep_id == 3){
                $aux_groupby = "GROUP BY categoriaprodgrupo.id";
                $aux_orderby = "ORDER BY categoriaprodgrupo.id ASC";
            }
        }
        if($request->agruFecha == 1){
            $aux_groupby = "GROUP BY pesaje.fechahora";
            $aux_orderby = "ORDER BY pesaje.fechahora ASC";
        }

        if(!isset($request->categoriaprod_id) or empty($request->categoriaprod_id)){
            $aux_condcategoriaprod_id = "true";
        }else{
    
            if(is_array($request->categoriaprod_id)){
                $aux_categoriaprodid = implode ( ',' , $request->categoriaprod_id);
            }else{
                $aux_categoriaprodid = $request->categoriaprod_id;
            }
            $aux_condcategoriaprod_id = " producto.categoriaprod_id in ($aux_categoriaprodid) ";
        }
    

        $sql = "SELECT pesaje.*,pesajedet.*,producto.nombre AS producto_nombre,producto.diametro,claseprod.cla_nombre,
        producto.long,producto.tipounion,pesajecarro.nombre AS pesajecarro_nombre,
        areaproduccionsuclinea.nombre AS areaproduccionsuclinea_nombre,turno.nombre AS turno_nombre,
        categoriaprodgrupo.nombre as categoriaprodgrupo_nombre, 
        sum(pesajedet.tara) as sumtara,
        sum(pesajedet.pesobaltotal) as sumpesobaltotal,
        sum((pesajedet.pesobaltotal - pesajedet.tara) / pesajedet.cant) pesopromunibal,
        sum(pesajedet.pesobaltotal - pesajedet.tara) pesototalprodbal,
        sum(pesajedet.cant * pesajedet.pesounitnom) as pesototalnorma,
        sum(((pesajedet.pesobaltotal - pesajedet.tara)) - (pesajedet.cant * pesajedet.pesounitnom)) AS difkg
        FROM pesaje INNER JOIN pesajedet
        ON pesaje.id = pesajedet.pesaje_id AND ISNULL(pesaje.deleted_at) AND ISNULL(pesajedet.deleted_at)
        INNER JOIN producto
        ON pesajedet.producto_id = producto.id AND ISNULL(producto.deleted_at)
        INNER JOIN claseprod
        ON producto.claseprod_id = claseprod.id AND ISNULL(claseprod.deleted_at)
        INNER JOIN pesajecarro
        ON pesajecarro.id = pesajedet.pesajecarro_id AND ISNULL(pesajecarro.deleted_at)
        INNER JOIN areaproduccionsuclinea
        ON areaproduccionsuclinea.id = pesajedet.areaproduccionsuclinea_id
        INNER JOIN turno
        ON turno.id = pesajedet.turno_id AND ISNULL(turno.deleted_at)
        INNER JOIN categoriaprod
        ON categoriaprod.id = producto.categoriaprod_id
        LEFT JOIN categoriaprodgrupo
        ON categoriaprodgrupo.id = categoriaprod.categoriaprodgrupo_id
        WHERE $aux_sucursal_idCond
        AND $aux_producto_idCodn
        AND $aux_condFecha
        AND $aux_categoriaprodgrupo_idCond
        AND $aux_condcategoriaprod_id
        $aux_groupby
        $aux_orderby;";
        //dd($sql);
        $datas = DB::select($sql);
        return $datas;
    }

}
