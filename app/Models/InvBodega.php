<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvBodega extends Model
{
    use SoftDeletes;
    protected $table = "invbodega";
    protected $fillable = [
        'bod_desc',
        'sucursal_id',
        'usuariodel_id'
    ];

    //RELACION DE UNO A VARIOS
    public function invstocks()
    {
        return $this->hasMany(InvStock::class);
    }

    //RELACION INVERSA Sucursal
    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }
    


}
