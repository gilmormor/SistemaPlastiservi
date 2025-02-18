<?php

namespace App\Models;

use App\Models\Seguridad\Usuario;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DteAnul extends Model
{
    use SoftDeletes;
    protected $table = "dteanul";
    protected $fillable = [
        'dte_id',
        'obs',
        'motanul_id',
        'moddevgiadesp_id',
        'usuario_id',
        'usuariodel_id'
    ];

    //RELACION INVERSA DespachoOrd
    public function dte()
    {
        return $this->belongsTo(Dte::class);
    }

    //Relacion inversa a Usuario
    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }
    
}
