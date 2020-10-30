<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DespachoObs extends Model
{
    use SoftDeletes;
    protected $table = "despachoobs";
    protected $fillable = [
        'nombre',
        'descripcion',
        'usuariodel_id'
    ];

    //RELACION DE UNO A MUCHOS DespachoOrd
    public function despachoords()
    {
        return $this->hasMany(DespachoOrd::class);
    }
}
