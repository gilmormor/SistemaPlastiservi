<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DespachoOrdRecMotivo extends Model
{
    use SoftDeletes;
    protected $table = "despachoordrecmotivo";
    protected $fillable = [
        'nombre',
        'desc',
        'usuariodel_id'
    ];

    //RELACION DE UNO A MUCHOS DespachoOrdRec
    public function despachoordrecs()
    {
        return $this->hasMany(DespachoOrdRec::class,'despachoordrec_id');
    }
}
