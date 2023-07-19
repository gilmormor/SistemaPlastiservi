<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DteGuiaDespNV extends Model
{
    protected $table = "dteguiadespnv";
    protected $fillable = [
        'dte_id',
        'notaventa_id'
    ];

    //RELACION INVERSA DTE
    public function dte()
    {
        return $this->belongsTo(Dte::class);
    }
    //Relacion inversa a NotaVenta
    public function notaventa()
    {
        return $this->belongsTo(NotaVenta::class);
    }    
}
