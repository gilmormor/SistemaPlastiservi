<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DteGuiaDesp extends Model
{
    use SoftDeletes;
    protected $table = "dteguiadesp";
    protected $fillable = [
        'dte_id',
        'despachoord_id',
        'notaventa_id',
        'tipoentrega_id',
        'comunaentrega_id',
        'lugarentrega',
        'ot',
    ];

    //RELACION INVERSA DTE
    public function dte()
    {
        return $this->belongsTo(Dte::class);
    }

    //Relacion inversa a DespachoOrd
    public function despachoord()
    {
        return $this->belongsTo(DespachoOrd::class);
    }
    

    //Relacion inversa a NotaVenta
    public function notaventa()
    {
        return $this->belongsTo(NotaVenta::class);
    }

    public function comunaentrega()
    {
        return $this->belongsTo(Comuna::class,'comunaentrega_id');
    }
    //Relacion inversa a TipoEntrega
    public function tipoentrega()
    {
        return $this->belongsTo(TipoEntrega::class);
    }
}
