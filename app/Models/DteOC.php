<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DteOC extends Model
{
    use SoftDeletes;
    protected $table = "dteoc";
    protected $fillable = [
        'dte_id',
        'oc_id',
        'oc_folder',
        'oc_file',
    ];

    //RELACION INVERSA DTE
    public function dte()
    {
        return $this->belongsTo(Dte::class);
    }
    
}
