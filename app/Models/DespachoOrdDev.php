<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DespachoOrdDev extends Model
{
    use SoftDeletes;
    protected $table = "despachoorddev";
    protected $fillable = [
        'despachoord_id',
        'usuario_id',
        'fechahora',
        'despachoorddevmotivo_id',
        'obs',
        'anulada',
        'usuariodel_id'
    ];

    //RELACION DE UNO A MUCHOS DespachoOrdDevDet
    public function despachoorddevdets()
    {
        return $this->hasMany(DespachoOrdDevDet::class,'despachoorddev_id');
    }
    //Relacion inversa a despachoord
    public function despachoord()
    {
        return $this->belongsTo(DespachoOrd::class);
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
    //Relacion inversa a DespachoOrdDevMotivo
    public function despachoorddevmotivo()
    {
        return $this->belongsTo(DespachoOrdDevMotivo::class);
    }

}
