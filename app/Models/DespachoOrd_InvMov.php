<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DespachoOrd_InvMov extends Model
{
    use SoftDeletes;
    protected $table = "despachoord_invmov";
    protected $fillable = [
        'despachoord_id',
        'invmov_id'
    ];

    //RELACION INVERSA DespachoOrd
    public function despachoor()
    {
        return $this->belongsTo(DespachoOrd::class);
    }
    //RELACION INVERSA InvMov
    public function invmov()
    {
        return $this->belongsTo(InvMov::class);
    }
}
