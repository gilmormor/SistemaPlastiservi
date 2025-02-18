<?php

namespace App\Models;

use App\Models\Seguridad\Usuario;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DespachoOrdAnul extends Model
{
    use SoftDeletes;
    protected $table = "despachoordanul";
    protected $fillable = [
        'despachoord_id',
        'usuario_id',
        'usuariodel_id'
    ];

    //RELACION INVERSA despachoord
    public function despachoord()
    {
        return $this->belongsTo(DespachoOrd::class);
    }

    //Relacion inversa a Usuario
    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }
    
}
