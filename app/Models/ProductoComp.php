<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductoComp extends Model
{
    use SoftDeletes;
    protected $table = "productocomp";
    protected $fillable = [
        'producto_id',
        'productocomp_id',
        'cant',
        'obs',
        'usuario_id',
        'usuariodel_id'
    ];

    //RELACION INVERSA PARA BUSCAR EL PADRE
    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }
    //RELACION INVERSA PARA BUSCAR EL PADRE
    public function productocomp()
    {
        return $this->belongsTo(Producto::class,"productocomp_id");
    }
}
