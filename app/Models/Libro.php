<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Libro extends Model
{
    use SoftDeletes;
    protected $table = "libro";
    protected $fillable = ['titulo','isbn','autor','cantidad','editorial','foto']; 
    protected $guarded = ['id']; 
}
