<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailxLotePersona extends Model
{
    protected $table = "emailxlote_persona";
    protected $fillable = [
        'emailxlote_id',
        'persona_id'
    ];

    
    //RELACION INVERSA PARA BUSCAR EL PADRE
    public function emailxlote()
    {
        return $this->belongsTo(EmailxLote::class);
    }
    //RELACION INVERSA PARA BUSCAR EL PADRE
    public function persona()
    {
        return $this->belongsTo(Persona::class);
    }
}
