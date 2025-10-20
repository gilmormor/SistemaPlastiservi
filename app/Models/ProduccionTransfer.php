<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProduccionTransfer extends Model
{
    protected $table = "producciontransfer";
    protected $fillable = [
        'opdet_id',
        'produccion_id',
        'usuario_id'
    ];
    //RELACION INVERSA opdet
    public function opdet()
    {
        return $this->belongsTo(opdet::class);
    }
    //RELACION INVERSA produccion
    public function produccion()
    {
        return $this->belongsTo(Produccion::class);
    }
}
