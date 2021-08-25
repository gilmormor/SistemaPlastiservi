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

    public static function productosxUsuario(){
        $users = Usuario::findOrFail(auth()->id());
        $sucurArray = $users->sucursales->pluck('id')->toArray();
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
                ->orderBy('producto.id', 'asc')
                ->get();
        return $productos;
    }

}
