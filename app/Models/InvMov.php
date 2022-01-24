<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvMov extends Model
{
    use SoftDeletes;
    protected $table = "invmov";
    protected $fillable = [
        'fechahora',
        'obs',
        'staanul',
        'invmovmod_id',
        'usuariodel_id'
    ];

    //RELACION DE UNO A VARIOS InvMovDet
    public function invmovdets()
    {
        return $this->hasMany(InvMovDet::class);
    }

    //RELACION INVERSA InvMovModulo
    public function invmovmodulo()
    {
        return $this->belongsTo(InvMovModulo::class);
    }
    
}
