<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GrupoCatPromCategoriaProd extends Model
{
    protected $table = "grupocatPromcategoriaprod";
    protected $fillable = [
        'grupocatprom_id',
        'categoriaprod_id'
    ];

    //RELACION INVERSA A GrupoCatProm
    public function grupocatprom()
    {
        return $this->belongsTo(GrupoCatProm::class);
    }
    //RELACION INVERSA A CategoriaProd
    public function categoriaprod()
    {
        return $this->belongsTo(CategoriaProd::class);
    }

}
