<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvEntSal extends Model
{
    use SoftDeletes;
    protected $table = "inventsal";
    protected $fillable = [
        'fechahora',
        'desc',
        'obs',
        'staanul',
        'invmov_id',
        'invmovmodulo_id',
        'invmovtipo_id',
        'usuario_id',
        'usuariodel_id'
    ];

    //RELACION DE UNO A VARIOS InvEntSalDet
    public function inventsaldets()
    {
        return $this->hasMany(InvEntSalDet::class,'inventsal_id');
    }

    //RELACION INVERSA InvMovModulo
    public function invmovmodulo()
    {
        return $this->belongsTo(InvMovModulo::class);
    }
    //RELACION INVERSA InvMovTipo
    public function invmovtipo()
    {
        return $this->belongsTo(InvMovTipo::class);
    }
    //RELACION INVERSA User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    //RELACION INVERSA User
    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }
}
