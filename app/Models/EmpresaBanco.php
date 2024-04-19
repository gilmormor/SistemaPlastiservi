<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmpresaBanco extends Model
{
    use SoftDeletes;
    protected $table = "empresa_banco";
    protected $fillable = [
        'empresa_id',
        'banco_id',
        'usuariodel_id'];

    
    //RELACION INVERSA PARA BUSCAR EL PADRE
    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }
    //RELACION INVERSA PARA BUSCAR EL PADRE
    public function banco()
    {
        return $this->belongsTo(Banco::class);
    }

}
