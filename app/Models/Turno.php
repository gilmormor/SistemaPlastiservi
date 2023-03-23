<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Turno extends Model
{
    use SoftDeletes;
    protected $table = "turno";
    protected $fillable = [
        'nombre',
        'desc',
        'turno',
        'ini',
        'fin',
    ];

    //RELACION UNO A MUCHOS PesajeDet
    public function pesajedets()
    {
        return $this->hasMany(PesajeDet::class);
    }
}
