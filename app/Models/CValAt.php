<?php

namespace App\Models;

use App\Models\Seguridad\Usuario;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CValAt extends Model
{
    use SoftDeletes;
    protected $table = "cvalat";
    protected $fillable = [
        'nombre',
        'desc',
        'orden',
        'usuario_id',
        'usuariodel_id'
    ];
    //RELACION UNO A MUCHOS CVALATDET
    public function cvalatdets()
    {
        return $this->hasMany(CValAtDet::class,'cvalat_id');
    }

    //Relacion inversa a Usuario
    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }


}
