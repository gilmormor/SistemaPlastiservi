<?php

namespace App\Models;

use App\Models\Seguridad\Usuario;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\User;

class Op extends Model
{
    use SoftDeletes;
    protected $table = "op";
    protected $fillable = [
        'otdet_id',
        'cantprod',
        'kgprod',
        'prioridad',
        'obs',
        'usuario_id',
        'usuariodel_id'
    ];

    //RELACION DE UNO A MUCHOS NotaVentaDetalle
    public function opdets()
    {
        return $this->hasMany(OpDet::class,'op_id');
    }

    //RELACION INVERSA OtDet
    public function otdet()
    {
        return $this->belongsTo(OtDet::class);
    }

    //RELACION DE UNO A uno OpAnul
    public function opanul()
    {
        return $this->hasOne(OpAnul::class,'op_id');
    }
    //Relacion inversa a Usuario
    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }

    //Relacion inversa a User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
