<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvMovTipo extends Model
{
    use SoftDeletes;
    protected $table = "invmovtipo";
    protected $fillable = [
        'nombre',
        'desc',
        'tipomov',
        'stacieinimes',
        'usuario_id',
        'usuariodel_id'
    ];

    //RELACION DE UNO A VARIOS InvMovDet
    public function invmovdets()
    {
        return $this->hasMany(InvMovDet::class);
    }



}
