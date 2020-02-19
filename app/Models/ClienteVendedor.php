<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClienteVendedor extends Model
{
    use SoftDeletes;
    protected $table = "cliente_vendedor";
    protected $fillable = ['cliente_id','vendedor_id','usuariodel_id'];

    
    //RELACION INVERSA PARA BUSCAR EL PADRE
    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }
    //RELACION INVERSA PARA BUSCAR EL PADRE
    public function vendedor()
    {
        return $this->belongsTo(Vendedor::class);
    }
}
