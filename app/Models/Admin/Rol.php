<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Rol extends Model
{
    use SoftDeletes;
    protected $table = "rol";
    protected $fillable = ['nombre']; 
    protected $guarded = ['id']; 
}
