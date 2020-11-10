<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DespachoOrdAnulGuiaFact extends Model
{
    use SoftDeletes;
    protected $table = "despachoordanulguiafact";
    protected $fillable = [
        'despachoord_id',
        'guiadespacho',
        'guiadespachofec',
        'numfactura',
        'fechafactura',
        'numfacturafec',
        'observacion',
        'usuario_id',
        'usuariodel_id'
    ];

    //RELACION INVERSA despachoord
    public function despachoord()
    {
        return $this->belongsTo(DespachoOrd::class);
    }
}
