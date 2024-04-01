<?php

namespace App\Models;

use App\Models\Seguridad\Usuario;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ErrorSubirDte extends Model
{
    use SoftDeletes;
    protected $table = "errorsubirdte";
    protected $fillable = [
        'dte_id',
        'stasubsii',
        'errorsii',
        'xmlsii',
        'stasubcob',
        'errorcob',
        'xmlcob',
        'usuario_id'        
    ];


    //RELACION INVERSA Dte
    public function dte()
    {
        return $this->belongsTo(Dte::class);
    }
    //Relacion inversa a Usuario
    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }
    

}
