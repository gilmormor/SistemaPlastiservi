<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ImportComunaGilmerEproplas extends Model
{
    use SoftDeletes;
    protected $table = "importcomunagilmereproplas";
    protected $fillable = ['comuna_id','comuna_id_eproplas','usuariodel_id'];
}
