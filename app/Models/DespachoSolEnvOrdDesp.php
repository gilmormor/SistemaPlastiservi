<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DespachoSolEnvOrdDesp extends Model
{
    use SoftDeletes;
    protected $table = "despachosolenvorddesp";
    protected $fillable = [
        'despachosol_id',
        'usuario_id',
        'usuariodel_id'
    ];

    //Relacion inversa a DespachoSol
    public function despachosol()
    {
        return $this->belongsTo(DespachoSol::class);
    }
    
    public static function delsolenvord($despachosol){
        if(isset($despachosol->despachosolenvorddesp->id)){ //SI EXISTE EN despachosolenvorddesp SE ELIMINA
            $despachosolenvorddesp_id = $despachosol->despachosolenvorddesp->id;
            if (DespachoSolEnvOrdDesp::destroy($despachosolenvorddesp_id)) {
                $despachosolenvorddesp = DespachoSolEnvOrdDesp::withTrashed()->findOrFail($despachosolenvorddesp_id);
                $despachosolenvorddesp->usuariodel_id = auth()->id();
                $despachosolenvorddesp->save();
                return true;
            }   
        }
        return false;
    }

}
