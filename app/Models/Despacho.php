<?php

namespace App\Models;

use App\Models\Seguridad\Usuario;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Despacho extends Model
{
    use SoftDeletes;
    protected $table = "despacho";
    protected $fillable = [
        'soldespacho_id',
        'notaventa_id',
        'usuario_id',
        'fecha',
        'obs',
        'usuariodel_id'
    ];

    //RELACION DE UNO A MUCHOS DespachoDet
    public function despachodets()
    {
        return $this->hasMany(DespachoDet::class);
    }

    //Relacion inversa a SolDespacho
    public function soldespacho()
    {
        return $this->belongsTo(SolDespacho::class);
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
