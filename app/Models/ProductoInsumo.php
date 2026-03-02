<?php

namespace App\Models;

use App\Models\Seguridad\Usuario;
use Illuminate\Database\Eloquent\Model;

class ProductoInsumo extends Model
{
    protected $table = "productoinsumo";
    protected $fillable = [
                    'producto_id',
                    'insumo_id',
                    'cant',
                    'unidadesproducto',
                    'activo',
                    'obs',
                    'usuario_id',
                    'usuariodel_id'
                ];
    //RELACION DE UNO A MUCHOS ProductoInsumo
    public function insumo()
    {
        return $this->belongsTo(Insumo::class);
    }
    //Relacion inversa a Producto
    public function producto()
    {
        return $this->belongsTo(Producto::class); 
    }
    //RELACION INVERSA User
    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }
}
