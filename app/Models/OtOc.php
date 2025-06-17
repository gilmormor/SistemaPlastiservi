<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OtOc extends Model
{
    protected $table = "otoc";
    protected $fillable = [
        'ot_id',
        'oc_id'
    ];

    //RELACION INVERSA OT
    public function ot()
    {
        return $this->belongsTo(Ot::class);
    }

}
