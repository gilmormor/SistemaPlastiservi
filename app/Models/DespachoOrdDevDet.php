<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DespachoOrdDevDet extends Model
{
    use SoftDeletes;
    protected $table = "despachoorddevdet";
    protected $fillable = [
        'despachoorddev_id',
        'despachoorddet_id',
        'cantdev',
        'obsdet',
        'usuariodel_id'
    ];

    //RELACION INVERSA DespachoOrd
    public function despachoord()
    {
        return $this->belongsTo(DespachoOrd::class);
    }

    //RELACION INVERSA DespachoSolDet
    public function despachosoldet()
    {
        return $this->belongsTo(DespachoSolDet::class);
    }
  
}
