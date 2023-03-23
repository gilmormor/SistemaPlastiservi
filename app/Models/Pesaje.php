<?php

namespace App\Models;

use App\Models\Seguridad\Usuario;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pesaje extends Model
{
    use SoftDeletes;
    protected $table = "pesaje";
    protected $fillable = [
        'invmov_id',
        'fechahora',
        'desc',
        'obs',
        'annomes',
        'staanul',
        'invmovmodulo_id',
        'invmovtipo_id',
        'sucursal_id',
        'staaprob',
        'fechahoraaprob',
        'obsaprob',
        'usuario_id',
        'usuariodel_id'
    ];
    //RELACION UNO A MUCHOS PesajeDet
    public function pesajedets()
    {
        return $this->hasMany(PesajeDet::class);
    }
    //RELACION INVERSA InvMovModulo
    public function invmovmodulo()
    {
        return $this->belongsTo(InvMovModulo::class);
    }
    //RELACION INVERSA InvMovModulo
    public function invmovtipo()
    {
        return $this->belongsTo(InvMovTipo::class);
    }
    //RELACION INVERSA Sucursal
    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }
    //RELACION INVERSA Usuario
    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }
}
