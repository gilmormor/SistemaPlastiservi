<?php

namespace App\Models;

use App\Models\Seguridad\Usuario;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bitacora extends Model
{
    use SoftDeletes;
    protected $table = "bitacora";
    protected $fillable = [
        'empresa_id',
        'menu_id',
        'usuario_id',
        'codmov',
        'desc',
        'ip',
        'dispositivo'
    ];

    //Relacion inversa a Usuario
    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }
    //Relacion inversa a Empresa
    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }
    //Relacion inversa a Menu
    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }
}
