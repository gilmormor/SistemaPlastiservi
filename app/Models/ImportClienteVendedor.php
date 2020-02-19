<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ImportClienteVendedor extends Model
{
    //use SoftDeletes;
    protected $table = "importclientevendedor";
    protected $fillable = ['cliente_rut','vendedor_id'];
}
