<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MenuRol extends Model
{
    use SoftDeletes;
    protected $table = "menu_rol";
    public $timestamps = false; 
}
