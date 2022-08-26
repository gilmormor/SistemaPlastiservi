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
        'acuerdotecnico_id',
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
    //RELACION MUCHO A MUCHOS CON CLIENTE A TRAVES DE cliente_producto
    public function clientes()
    {
        return $this->belongsToMany(Cliente::class, 'cliente_producto')->withTimestamps();
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
    //RELACION UNO A UNO CON ACUERDOTECNICO
    public function acuerdotecnico()
    {
        return $this->hasOne(AcuerdoTecnico::class);
    }

    //RELACION MUCHO A MUCHOS vendedor A TRAVES DE producto_vendedor
    public function vendedores()
    {
        return $this->belongsToMany(Vendedor::class, 'producto_vendedor');
    }
    

    public static function productosxUsuarioSQL($sucursal_id = false){
        $users = Usuario::findOrFail(auth()->id());
        if($sucursal_id){
            $sucurArray = [$sucursal_id];
        }else{
            $sucurArray = $users->sucursales->pluck('id')->toArray();
        }
        $sucurcadena = implode(",", $sucurArray);

        $sql = "SELECT producto.id,producto.nombre,claseprod.cla_nombre,producto.codintprod,producto.diamextmm,producto.diamextpg,
                producto.diametro,producto.espesor,producto.long,producto.peso,producto.tipounion,producto.precioneto,categoriaprod.precio,
                categoriaprodsuc.sucursal_id,categoriaprod.unidadmedida_id
                from producto inner join categoriaprod
                on producto.categoriaprod_id = categoriaprod.id and isnull(producto.deleted_at) and isnull(categoriaprod.deleted_at)
                INNER JOIN claseprod
                on producto.claseprod_id = claseprod.id and isnull(claseprod.deleted_at)
                INNER JOIN categoriaprodsuc
                on categoriaprod.id = categoriaprodsuc.categoriaprod_id
                INNER JOIN sucursal
                ON categoriaprodsuc.sucursal_id = sucursal.id
                WHERE sucursal.id in ($sucurcadena)
                GROUP BY producto.id
                ORDER BY producto.id asc;";
        //dd($sql);
        $datas = DB::select($sql);
        return $datas;
    }

    public static function productosxCliente($request){
        $cliente_idCond = "true";
        if($request->cliente_id){
            $cliente_idCond = "if(categoriaprod.asoprodcli = 1, ((producto.id IN (SELECT producto_id FROM cliente_producto WHERE isnull(cliente_producto.deleted_at)
                                AND cliente_producto.cliente_id = $request->cliente_id)) OR producto.tipoprod = 1), TRUE )";
        }
        $users = Usuario::findOrFail(auth()->id());
        if($request->sucursal_id){
            $sucurArray = [$request->sucursal_id];
        }else{
            $sucurArray = $users->sucursales->pluck('id')->toArray();
        }
        $sucurcadena = implode(",", $sucurArray);

        $sql = "SELECT producto.id,producto.nombre,claseprod.cla_nombre,producto.codintprod,producto.diamextmm,producto.diamextpg,
                producto.diametro,producto.espesor,producto.long,producto.peso,producto.tipounion,producto.precioneto,categoriaprod.precio,
                categoriaprodsuc.sucursal_id,categoriaprod.unidadmedida_id,producto.tipoprod
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

    public static function AsignarProductosAClientes($request){
        //dd($request);
        $cliente_idCond = "false";
        if($request->cliente_id and $request->producto_id){
            $cliente_idCond = "categoriaprod.asoprodcli = 1 and producto.tipoprod = 0
                                and 
                                producto.id NOT IN (SELECT producto_id FROM cliente_producto WHERE isnull(cliente_producto.deleted_at)
                                AND cliente_producto.cliente_id = $request->cliente_id)
                                AND producto.id NOT IN ($request->producto_id)";
        };
        $users = Usuario::findOrFail(auth()->id());
        if($request->sucursal_id){
            $sucurArray = [$request->sucursal_id];
        }else{
            $sucurArray = $users->sucursales->pluck('id')->toArray();
        }
        $sucurcadena = implode(",", $sucurArray);

        $sql = "SELECT producto.id,producto.nombre,claseprod.cla_nombre,producto.codintprod,producto.diamextmm,producto.diamextpg,
                producto.diametro,producto.espesor,producto.long,producto.peso,producto.tipounion,producto.precioneto,categoriaprod.precio,
                categoriaprodsuc.sucursal_id,categoriaprod.unidadmedida_id,producto.tipoprod
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
