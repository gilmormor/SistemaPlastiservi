<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GuiaDespAnul extends Model
{
    use SoftDeletes;
    protected $table = "guiadespanul";
    protected $fillable = [
        'guiadesp_id',
        'obs',
        'motanul_id',
        'moddevgiadesp_id',
        'usuario_id',
        'usuariodel_id'
    ];

    //RELACION INVERSA DespachoOrd
    public function guiadesp()
    {
        return $this->belongsTo(GuiaDesp::class);
    }
}