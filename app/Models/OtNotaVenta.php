<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OtNotaVenta extends Model
{
    use SoftDeletes;
    protected $table = "otnotaventa";
    protected $fillable = [
        'ot_id',
        'notaventa_id'
    ];

    //RELACION INVERSA Ot
    public function ot()
    {
        return $this->belongsTo(Ot::class);
    }
    //RELACION INVERSA NotaVenta
    public function notaventa()
    {
        return $this->belongsTo(NotaVenta::class);
    }
    
}
