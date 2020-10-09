<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DespachoSol extends Model
{
    use SoftDeletes;
    protected $table = "despachosol";
    protected $fillable = [
        'notaventa_id',
        'usuario_id',
        'fecha',
        'obs',
        'usuariodel_id'
    ];

    //RELACION DE UNO A MUCHOS DespachoSolDet
    public function despachosoldets()
    {
        return $this->hasMany(DespachoSolDet::class);
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
}
