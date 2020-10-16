<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DespachoOrd extends Model
{
    use SoftDeletes;
    protected $table = "despachoord";
    protected $fillable = [
        'despachosol_id',
        'notaventa_id',
        'usuario_id',
        'fecha',
        'obs',
        'usuariodel_id'
    ];

    //RELACION DE UNO A MUCHOS DespachoOrdDet
    public function despachoorddets()
    {
        return $this->hasMany(DespachoOrdDet::class);
    }

    //Relacion inversa a DespachoSol
    public function despachosol()
    {
        return $this->belongsTo(DespachoSol::class);
    }
    
    //Relacion inversa a NotaVenta
    public function notaventa()
    {
        return $this->belongsTo(NotaVenta::class);
    }

    //Relacion inversa a Usuario
    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }

    //Relacion inversa a User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comunaentrega()
    {
        return $this->belongsTo(Comuna::class,'comunaentrega_id');
    }

}
