<?php

namespace App\Models;

use App\Models\Seguridad\Usuario;
use DateTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


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

    //RELACION DE UNO A MUCHOS datacobranzadet
    public function datacobranzadets()
    {
        return $this->hasMany(DataCobranzaDet::class,'datacobranza_id');
    }
    

    public static function llenartabla($request){
        if(isset($request->cliente_id)){
            $clientes = Cliente::
                        where("id",$request->cliente_id)
                        ->get();
        }else{
            if(isset($request->clienteid_ini)){
                //PARA TAREA QUE SE EJECUTA EN EL CPANEL A TRAVES DE CRON
                $clientes = Cliente::where("id",">=",$request->clienteid_ini)
                            ->where("id","<=",$request->clienteid_fin)
                            ->get();
            }else{
                $clientes = Cliente::get();
            }
        }
        //dd($clientes);
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
                        if(isset($datacobranza->datacobranzadets)){
                            $datacobranza->datacobranzadets()->delete();
                        }
                    }else{
                        $datacobranza = new DataCobranza();
                    }
                    //dd($clibloq["datacobranza"]["datosTodasFacDeuda"]);
                    $datacobranza->cliente_id = $cliente->id;
                    $datacobranza->tfac = $clibloq["datacobranza"]["TFac"];
                    $datacobranza->tdeuda = $clibloq["datacobranza"]["TDeuda"];
                    $datacobranza->tdeudafec = $clibloq["datacobranza"]["TDeudaFec"];
                    $datacobranza->nrofacdeu = $clibloq["datacobranza"]["NroFacDeu"];
                    $datacobranza->save();
                    foreach ($clibloq["datacobranza"]["datosTodasFacDeuda"] as $datosTodasFacDeudas) {
                        //dd($datosTodasFacDeudas);
                        $dtefac = Dte::where("nrodocto",$datosTodasFacDeudas["NroFAV"])
                                        ->whereIn("foliocontrol_id",[1,7]) //AQUI SE VA A PRESENTAR EL PROBLEMA CUANDO COINCIDAN LOS NUMEROS ENTRE FACT Y FACT EXENTA
                                        ->get();
                        $mnttotal = 0;
                        $datacobranzadet = new DataCobranzaDet();
                        if(count($dtefac) > 0){
                            $datacobranzadet->dte_id = $dtefac[0]->id;
                        }
                        $datacobranzadet->datacobranza_id = $datacobranza->id;
                        $datacobranzadet->cliente_id = $cliente->id;
                        $datacobranzadet->nrofav = $datosTodasFacDeudas["NroFAV"];
                        $datacobranzadet->fecfact = $datosTodasFacDeudas["fecfact"];
                        $datacobranzadet->fecvenc = $datosTodasFacDeudas["fecvenc"];
                        $datacobranzadet->mnttot = $datosTodasFacDeudas["mnttot"];
                        $datacobranzadet->deuda = $datosTodasFacDeudas["Deuda"];
                        $aux_stavenc = $datosTodasFacDeudas["staVencida"] ? 1 : 0;
                        $datacobranzadet->stavencida = $aux_stavenc;
                        $datacobranzadet->save();
                    }
                }
            }else{
                if(isset($cliente->datacobranza)){
                    $datacobranza = $cliente->datacobranza;
                    $datacobranza->tfac = 0;
                    $datacobranza->tdeuda = 0;
                    $datacobranza->tdeudafec = 0;
                    $datacobranza->nrofacdeu = "";
                    $datacobranza->save();
                    if(isset($datacobranza->datacobranzadets)){
                        $datacobranza->datacobranzadets()->delete();
                    }
                }
            }    
        }
        if(isset($request->cliente_id)){
            return $clibloq;
        }

    }

    public static function EnviarEmailFactxVencer($persona,$sucursal_id){
        // Crear un objeto DateTime con la fecha actual
        $fecha_act = date("Y-m-d");
        $fecha_actual = new DateTime();

        // Sumar 10 días a la fecha actual
        $fecha_actual->modify('+10 days');

        // Guardar el resultado en una variable
        $fecha_10dias = $fecha_actual->format('Y-m-d');

        if(isset($persona->vendedor)){
            $vendedor_id=$persona->vendedor->id;
            $vendedorcond = "cliente.id in (SELECT cliente_vendedor.cliente_id 
            FROM cliente_vendedor
            WHERE cliente_vendedor.cliente_id = cliente.id
            AND cliente_vendedor.vendedor_id = $vendedor_id)";
        }else{
            $vendedorcond = " true ";
        }
        //$arraySucFisxUsu = implode(",", sucFisXUsu($persona));
        /* $arraySucFisxUsu = "1,2,3";
        $sucurcadena = "1,2,3"; */
        if(is_null($sucursal_id)){
            $aux_sucursales = sucFisXUsu($persona);
            $arraySucFisxUsu = implode(",", sucFisXUsu($persona));
            $aux_codsucursal_id = "ISNULL(dte.sucursal_id)
            AND cliente.id IN (SELECT cliente_sucursal.cliente_id
                                FROM cliente_sucursal
                                WHERE cliente_sucursal.cliente_id = cliente.id
                                AND cliente_sucursal.sucursal_id IN ($arraySucFisxUsu))";
            $aux_uniondte = "LEFT";
        }else{
            $aux_codsucursal_id = "dte.sucursal_id = $sucursal_id";
            $aux_uniondte = "INNER";
        }


        $sql = "SELECT dte.sucursal_id,cliente.rut,cliente.razonsocial,cliente.limitecredito,
        datacobranza.cliente_id,nrofav,fecfact,fecvenc,mnttot,deuda
        FROM datacobranzadet INNER JOIN datacobranza
        ON datacobranzadet.datacobranza_id = datacobranza.id AND ISNULL(datacobranza.deleted_at)
        INNER JOIN cliente
        ON cliente.id = datacobranza.cliente_id
        $aux_uniondte JOIN dte
        ON dte.nrodocto = datacobranzadet.nrofav
        WHERE $aux_codsucursal_id
        AND fecvenc >= '$fecha_act'
        AND fecvenc <= '$fecha_10dias'
        AND $vendedorcond
        GROUP BY datacobranzadet.id
        ORDER BY datacobranzadet.nrofav;";
        //dd($sql);
        return DB::select($sql);

    }
    
}
