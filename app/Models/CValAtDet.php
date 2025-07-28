<?php

namespace App\Models;

use App\Models\Seguridad\Usuario;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CValAtDet extends Model
{
    use SoftDeletes;
    protected $table = "cvalatdet";
    protected $fillable = [
        'cvalat_id',
        'nombre',
        'desc',
        'orden',
        'usuario_id',
        'usuariodel_id'
    ];
    //Relacion inversa a CVALAT
    public function cvalat()
    {
        return $this->belongsTo(CValAt::class);
    }

    //Relacion inversa a Usuario
    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }
}
