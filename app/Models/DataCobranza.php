<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;


class DataCobranza extends Model
{
    use SoftDeletes;
    protected $table = "datacobranza";
    protected $fillable = [
        'cliente_id',
        'tfac',
        'tdeuda',
        'tdeudafec',
        'nrofacdeu'
    ];
   
    //RELACION INVERSA Cliente
    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public static function llenartabla($request){
        if(isset($request->cliente_id)){
            $clientes = Cliente::
                        where("id",$request->cliente_id)
                        ->get();
        }else{
            $clientes = Cliente::get();
        }
        //DataCobranza::truncate();
        foreach ($clientes as $cliente) {
            //dd($cliente->razonsocial);
            //$request = new Request();
            //$cliente = Cliente::findOrFail($request->cliente_id);
            $clibloq = clienteBloqueado($cliente->id,1,$request);
            if(!is_null($clibloq["bloqueo"])){
                if(isset($clibloq["datacobranza"])){
                    if(isset($cliente->datacobranza)){
                        $datacobranza = $cliente->datacobranza;
                    }else{
                        $datacobranza = new DataCobranza();
                    }
                    $datacobranza->cliente_id = $cliente->id;
                    $datacobranza->tfac = $clibloq["datacobranza"]["TFac"];
                    $datacobranza->tdeuda = $clibloq["datacobranza"]["TDeuda"];
                    $datacobranza->tdeudafec = $clibloq["datacobranza"]["TDeudaFec"];
                    $datacobranza->nrofacdeu = $clibloq["datacobranza"]["NroFacDeu"];
                    $datacobranza->save();
                }
            }else{
                if(isset($cliente->datacobranza)){
                    $datacobranza = $cliente->datacobranza;
                    $datacobranza->tfac = 0;
                    $datacobranza->tdeuda = 0;
                    $datacobranza->tdeudafec = 0;
                    $datacobranza->nrofacdeu = "";
                    $datacobranza->save();
                }
            }    
        }
        if(isset($request->cliente_id)){
            return $clibloq;
        }

    }
}
