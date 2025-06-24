<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Atributo extends Model
{
    use SoftDeletes;
    protected $table = "atributo";
    protected $fillable = [
        'nombre',
        'desc',
        'tipodato',
        'longitud',
        'usuario_id',
        'usuariodel_id'
    ];

    //Relacion inversa a Sucursal
    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }
    //Relacion inversa a MaquinaGrupo
    public function maquinagrupo()
    {
        return $this->belongsTo(MaquinaGrupo::class);
    }

    public static function tipoDato(){
        $tipodatos = json_decode(json_encode([
            ['id' => 1, 'nombre' => 'Caracter'],
            ['id' => 2, 'nombre' => 'Numerico']
        ]));
        return $tipodatos;
    }
}
