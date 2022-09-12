<?php

namespace App\Models;

use App\Models\Seguridad\Usuario;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Producto extends Model
{
    use SoftDeletes;
    protected $table = "producto";
    protected $fillable = [
        'nombre',
        'descripcion',
        'estado',
        'codintprod',
        'codbarra',
        'diametro',
        'diamextmm',
        'diamextpg',
        'espesor',
        'long',
        'peso',
        'tipounion',
        'precioneto',
        'foto',
        'categoriaprod_id',
        'claseprod_id',
        'grupoprod_id',
        'color_id',
        'tipoprod',
        'stockmin',
        'stockmax',
        'usuariodel_id'
    ];

    //RELACION INVERSA PARA BUSCAR EL PADRE
    public function claseprod()
    {
        return $this->belongsTo(ClaseProd::class);
    }
    //RELACION INVERSA PARA BUSCAR EL PADRE
    public function categoriaprod()
    {
        return $this->belongsTo(CategoriaProd::class);
    }
    //RELACION UNO A MUCHOS CotizacionDetalle
    public function cotizaciondetalles()
    {
        return $this->hasMany(CotizacionDetalle::class);
    }
    //RELACION UNO A MUCHOS NotaventaDetalle
    public function notaventadetalles()
    {
        return $this->hasMany(NotaVentaDetalle::class);
    }

    //RELACION INVERSA PARA BUSCAR EL PADRE
    public function grupoprod()
    {
        return $this->belongsTo(GrupoProd::class);
    }
    //RELACION INVERSA PARA BUSCAR EL PADRE
    public function color()
    {
        return $this->belongsTo(Color::class);
    }

    //RELACION UNO A MUCHOS invbodegaproducto
    public function invbodegaproductos()
    {
        return $this->hasMany(InvBodegaProducto::class);
    }
    
    //RELACION UNO A MUCHOS InvmovDet
    public function invmovdets()
    {
        return $this->hasMany(InvMovDet::class);
    }

    public static function productosxUsuario($sucursal_id = false){
        $users = Usuario::findOrFail(auth()->id());
        if($sucursal_id){
            $sucurArray = [$sucursal_id];
        }else{
            $sucurArray = $users->sucursales->pluck('id')->toArray();
        }
        //Filtrando las categorias por sucursal, dependiendo de las sucursales asignadas al usuario logueado
        //******************* */
        $productos = CategoriaProd::join('categoriaprodsuc', 'categoriaprod.id', '=', 'categoriaprodsuc.categoriaprod_id')
        ->join('sucursal', 'categoriaprodsuc.sucursal_id', '=', 'sucursal.id')
        ->join('producto', 'categoriaprod.id', '=', 'producto.categoriaprod_id')
        ->join('claseprod', 'producto.claseprod_id', '=', 'claseprod.id')
        ->select([
                'producto.id',
                'producto.nombre',
                'claseprod.cla_nombre',
                'producto.codintprod',
                'producto.diamextmm',
                'producto.diamextpg',
                'producto.diametro',
                'producto.espesor',
                'producto.long',
                'producto.peso',
                'producto.tipounion',
                'producto.precioneto',
                'categoriaprod.precio',
                'categoriaprodsuc.sucursal_id',
                'categoriaprod.unidadmedida_id'
                ])
        ->whereIn('categoriaprodsuc.sucursal_id', $sucurArray)
        ->where('producto.deleted_at','=',null)
        ->groupBy('producto.id')
        ->orderBy('producto.id', 'asc')
        ->get();
        return $productos;
    }

    public static function productosxClienteTemp($request){ //Esta funcion es temporal mientras fusiono las 2 ramas de Santa Ester y San Bernardo
        //dd($request);
        $cliente_idCond = "true";
        if($request->cliente_id and $request->cliente_id != "undefined"){
            $cliente_idCond = " TRUE ";
        }
        $users = Usuario::findOrFail(auth()->id());
        if($request->sucursal_id and $request->sucursal_id !=  "undefined"){
            $sucurArray = [$request->sucursal_id];
        }else{
            $sucurArray = $users->sucursales->pluck('id')->toArray();
        }
        $sucurcadena = implode(",", $sucurArray);

        $sql = "SELECT producto.id,producto.nombre,claseprod.cla_nombre,producto.codintprod,producto.diamextmm,producto.diamextpg,
                producto.diametro,producto.espesor,producto.long,producto.peso,producto.tipounion,producto.precioneto,categoriaprod.precio,
                categoriaprodsuc.sucursal_id,categoriaprod.unidadmedida_id,producto.tipoprod,'' as acuerdotecnico_id
                from producto inner join categoriaprod
                on producto.categoriaprod_id = categoriaprod.id and isnull(producto.deleted_at) and isnull(categoriaprod.deleted_at)
                INNER JOIN claseprod
                on producto.claseprod_id = claseprod.id and isnull(claseprod.deleted_at)
                INNER JOIN categoriaprodsuc
                on categoriaprod.id = categoriaprodsuc.categoriaprod_id
                INNER JOIN sucursal
                ON categoriaprodsuc.sucursal_id = sucursal.id
                WHERE sucursal.id in ($sucurcadena)
                and $cliente_idCond
                GROUP BY producto.id
                ORDER BY producto.id asc;";
        //dd($sql);
        $datas = DB::select($sql);
        return $datas;
    }


}
