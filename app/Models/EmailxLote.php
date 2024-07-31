<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmailxLote extends Model
{
    use SoftDeletes;
    protected $table = "emailxlote";
    protected $fillable = [
        'nombre',
        'desc',
        'usuario_id',
        'usuariodel_id'
    ];

    //RELACION MUCHO A MUCHOS CON USUARIO A TRAVES DE emailxlote_persona
    public function personas()
    {
        return $this->belongsToMany(Persona::class, 'emailxlote_persona','emailxlote_id')->withTimestamps();
    }
}
