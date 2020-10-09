<?php

namespace App\Models;

use App\Models\Seguridad\Usuario;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SolDespacho extends Model
{
    use SoftDeletes;
    protected $table = "soldespacho";
    protected $fillable = [
        'notaventa_id',
        'usuario_id',
        'fecha',
        'obs',
        'usuariodel_id'
    ];

    //RELACION DE UNO A MUCHOS SolDespachoDet
    public function soldespachodets()
    {
        return $this->hasMany(SolDespachoDet::class);
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
