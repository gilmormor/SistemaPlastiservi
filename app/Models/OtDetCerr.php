<?php

namespace App\Models;

use App\Models\Seguridad\Usuario;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OtDetCerr extends Model
{
    use SoftDeletes;
    protected $table = "otdetcerr";
    protected $fillable = [
        'otdet_id',
        'obs',
        'usuario_id',
        'usuariodel_id',
        'tipo' // 1=Cerrado al programar o al enviar a OP, 2=Cerrado directamente sin hacer programacion o envio a OP.
    ];

    //RELACION INVERSA otdet
    public function otdet()
    {
        return $this->belongsTo(OtDet::class);
    }

    //Relacion inversa a Usuario
    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }
}
