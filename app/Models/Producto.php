<?php

namespace App\Models;

use App\Models\Seguridad\Usuario;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Producto extends Model
{
    use SoftDeletes;
    protected $table = "producto";
    protected $fillable = [
        'nombre',
        'descripcion',
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
                ->orderBy('producto.id', 'asc')
                ->groupBy('producto.id')
                ->get();
        return $productos;
    }

}
