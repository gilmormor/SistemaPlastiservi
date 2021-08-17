<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GuiaDespIntDetalle extends Model
{
    use SoftDeletes;
    protected $table = "guiadespintdetalle";
    protected $fillable = [
        'guiadespint_id',
        'producto_id',
        'cant',
        'unidadmedida_id',
        'preciounit',
        'peso',
        'precioxkilo',
        'precioxkiloreal',
        'totalkilos',
        'subtotal',
        'producto_nombre',
        'ancho',
        'largo',
        'espesor',
        'diametro',
        'categoriaprod_id',
        'claseprod_id',
        'grupoprod_id',
        'color_id',
        'descuento',
        'obs',
        'usuariodel_id'
    ];

    //RELACION INVERSA GuiaDespInt
    public function guiadespint()
    {
        return $this->belongsTo(GuiaDespInt::class);
    }
    
    //Relacion inversa a Producto
    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }
    //Relacion inversa a CategoriaProd
    public function categoriaprod()
    {
        return $this->belongsTo(CategoriaProd::class);
    }
    //Relacion inversa a ClaseProd
    public function claseprod()
    {
        return $this->belongsTo(ClaseProd::class);
    }
    //Relacion inversa a GrupoProd
    public function grupoprod()
    {
        return $this->belongsTo(GrupoProd::class);
    }
    //Relacion inversa a Color
    public function color()
    {
        return $this->belongsTo(Color::class);
    }
}