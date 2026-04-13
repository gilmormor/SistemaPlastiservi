<?php

namespace App\Models;

use App\Models\Seguridad\Usuario;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransEmp extends Model
{
    use SoftDeletes;

    protected $table = 'transemp';
    protected $fillable = [
        'rut', 
        'nombre', 
        'usuario_id', 
        'usuariodel_id'
    ];
    
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }
}
