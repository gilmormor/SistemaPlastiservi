<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PesajeCarro extends Model
{
    use SoftDeletes;
    protected $table = "pesajecarro";
    protected $fillable = [
        'sucursal_id',
        'nombre',
        'obs',
        'tara',
        'activo'
    ];

    //RELACION UNO A MUCHOS PesajeDet
    public function pesajedets()
    {
        return $this->hasMany(PesajeDet::class);
    }
}
