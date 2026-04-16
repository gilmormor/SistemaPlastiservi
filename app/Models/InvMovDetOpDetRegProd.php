<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvMovDetOpDetRegProd extends Model
{
    protected $table = "invmovdet_opdetregprod";
    protected $fillable = [
        'invmovdet_id',
        'opdetregprod_id',
    ];

    // Relación inversa → InvMovDet
    public function invmovdet()
    {
        return $this->belongsTo(InvMovDet::class);
    }

    // Relación inversa → OpDetRegProd
    public function opdetregprod()
    {
        return $this->belongsTo(OpDetRegProd::class);
    }
}
