<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApsucEtapaProdBodega extends Model
{
    protected $table    = 'apsucetapaprod_bodega';
    protected $fillable = ['apsucetapaprod_id', 'invbodega_id'];

    public function apsucetapaprod()
    {
        return $this->belongsTo(AreaProduccionSucEtapaProd::class, 'apsucetapaprod_id');
    }

    public function invbodega()
    {
        return $this->belongsTo(InvBodega::class);
    }
}
