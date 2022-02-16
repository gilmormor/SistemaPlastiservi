<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvStockMes extends Model
{
    use SoftDeletes;
    protected $table = "invstockmes";
    protected $fillable = [
        'invstock_id',
        'annomes',
        'stockini',
        'stockfin',
        'stockkgini',
        'stockkgfin',
        'usuariodel_id'
    ];
    //RELACION INVERSA INVSTOCK
    public function invstock()
    {
        return $this->belongsTo(InvStock::class);
    }
}
