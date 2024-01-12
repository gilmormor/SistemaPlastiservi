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
    //RELACION UNO A MUCHOS PERSONA
    public function comuna()
    {
        return $this->hasMany(Comuna::class);
    }
}
