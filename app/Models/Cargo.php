<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cargo extends Model
{
    use SoftDeletes;
    protected $table = "cargo";
    protected $fillable = [
        'nombre',
        'descripcion'
    ];
    //RELACION UNO A MUCHOS PERSONA
    public function personals()
    {
        return $this->hasMany(Persona::class);
    }
}
