<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DteTrans extends Model
{
    use SoftDeletes;

    protected $table = 'dtetrans';
    protected $fillable = [
        'dte_id', 
        'fchsalida', 
        'fchllegada', 
        'dirdest', 
        'comuna_id', 
        'cmnadest',
        'ruttrans', 
        'rutchofer', 
        'nombrechofer', 
        'patente',
        'patentecarro',
    ];

    // Relaciones
    public function dte()
    {
        return $this->belongsTo(Dte::class, 'dte_id');
    }

    public function comuna()
    {
        return $this->belongsTo(Comuna::class, 'comuna_id');
    }
}
