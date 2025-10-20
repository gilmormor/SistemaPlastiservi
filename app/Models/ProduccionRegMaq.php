<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProduccionRegMaq extends Model
{
    protected $table = "produccionregmaq";
    protected $fillable = [
        'produccionreg_id',
        'maquina_id'
    ];
    //RELACION INVERSA ProduccionReg
    public function produccionreg()
    {
        return $this->belongsTo(Produccionreg::class);
    }
}
