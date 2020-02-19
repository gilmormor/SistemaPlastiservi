<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SucursalClienteDirec extends Model
{
    use SoftDeletes;
    protected $table = "sucursalclientedirec";
    protected $fillable = ['sucursal_id','clientedirec_id','vendedor_id'];

    //RELACION INVERSA A VENDEDOR
    public function vendedor()
    {
        return $this->belongsTo(Vendedor::class);
    }
    //RELACION INVERSA A SUCURSAL
    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }

}
