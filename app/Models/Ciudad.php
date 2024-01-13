<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ciudad extends Model
{
    use SoftDeletes;
    protected $table = "ciudad";
    protected $fillable = [
        'nombre',
        'usuariodel_id'
    ];
    //RELACION UNO A MUCHOS comuna
    public function comunas()
    {
        return $this->hasMany(Comuna::class);
    }
    //RELACION UNO A MUCHOS cliente
    public function clientes()
    {
        return $this->hasMany(Cliente::class);
    }

    //RELACION UNO A MUCHOS DTE
    public function dtes()
    {
        return $this->hasMany(Dte::class);
    }
    
}
