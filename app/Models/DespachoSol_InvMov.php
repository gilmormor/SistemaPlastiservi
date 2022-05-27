<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DespachoSol_InvMov extends Model
{
    use SoftDeletes;
    protected $table = "despachosol_invmov";
    protected $fillable = [
        'despachosol_id',
        'invmov_id'
    ];

    //RELACION INVERSA DespachoSol
    public function despachoor()
    {
        return $this->belongsTo(DespachoSol::class);
    }
    //RELACION INVERSA InvMov
    public function invmov()
    {
        return $this->belongsTo(InvMov::class);
    }
}