<?php

namespace App\Models;

use App\Models\Seguridad\Usuario;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Insumo extends Model
{
    use SoftDeletes;
    protected $table = "insumo";
    protected $fillable = [
                    'nombre',
                    'desc',
                    'tipocosto_id',
                    'unidadmedida_id',
                    'costounitario',
                    'activo',
                    'usuario_id',
                    'usuariodel_id'
                ];

    //RELACION DE UNO A MUCHOS Insumo
    public function productoinsumos()
    {
        return $this->hasMany(ProductoInsumo::class);
    }
    //Relacion inversa a UnidadMedida
    public function unidadmedida()
    {
        return $this->belongsTo(UnidadMedida::class);
    }
    //RELACION INVERSA User
    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }
    //Relacion inversa a TipoCosto
    public function tipocosto()
    {
        return $this->belongsTo(TipoCosto::class);
    }
}
