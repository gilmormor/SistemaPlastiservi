<?php

namespace App\Models;

use App\Models\Seguridad\Usuario;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OpDetCerr extends Model
{
    use SoftDeletes;
    protected $table = "opdetcerr";
    protected $fillable = [
        'opdet_id',
        'obs',
        'usuario_id',
        'usuariodel_id'
    ];

    //RELACION INVERSA opdet
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
