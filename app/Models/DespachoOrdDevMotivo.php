<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DespachoOrdDevMotivo extends Model
{
    use SoftDeletes;
    protected $table = "despachoorddevmotivo";
    protected $fillable = [
        'nombre',
        'desc',
        'usuariodel_id'
    ];

    //RELACION DE UNO A MUCHOS DespachoOrdDev
    public function despachoorddevs()
    {
        return $this->hasMany(DespachoOrdDev::class,'despachoorddev_id');
    }
}
