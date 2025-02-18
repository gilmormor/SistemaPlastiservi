<?php

namespace App\Models;

use App\Models\Seguridad\Usuario;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DespachoSolAnul extends Model
{
    use SoftDeletes;
    protected $table = "despachosolanul";
    protected $fillable = [
        'despachosol_id',
        'usuario_id',
        'usuariodel_id'
    ];

    //RELACION INVERSA DespachoSol
    public function despachosol()
    {
        return $this->belongsTo(DespachoSol::class);
    }

    //Relacion inversa a Usuario
    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }
    
}
