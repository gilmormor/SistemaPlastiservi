<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ImportClienteSucursal extends Model
{
    use SoftDeletes;
    protected $table = "importclientesucursal";
    protected $fillable = ['cliente_id','sucursal_id','usuariodel_id'];
}
