<?php

namespace App\Models;

use App\Models\Seguridad\Usuario;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class AreaProduccionSuc extends Model
{
    use SoftDeletes;
    protected $table = "areaproduccionsuc";
    protected $fillable = [
        'sucursal_id',
        'areaproduccion_id',
        'usuariodel_id'
    ];
    //RELACION UNO A MUCHOS AreaProduccionSucLinea
    public function areaproduccionsuclineas()
    {
        return $this->hasMany(AreaProduccionSucLinea::class,'areaproduccionsuc_id');
    }

    //RELACION INVERSA PARA BUSCAR EL PADRE
    public function areaproduccion()
    {
        return $this->belongsTo(AreaProduccion::class,'areaproduccion_id');
    }
    //RELACION INVERSA PARA BUSCAR EL PADRE
    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class,'sucursal_id');
    }

    public static function report($request){
        if(!isset($request->areaproduccion_id) or empty($request->areaproduccion_id)){
            $aux_condareaproduccion_id = " true";
        }else{
            $aux_condareaproduccion_id = "areaproduccionsuc.areaproduccion_id='$request->areaproduccion_id'";
        }
        $user = Usuario::findOrFail(auth()->id());
        //dd($aux_condfoliocontrol_id);
        $sucurArray = $user->sucursales->pluck('id')->toArray();
        $sucurcadena = implode(",", $sucurArray);
        $aux_condsucurArray = "areaproduccionsuc.sucursal_id  in ($sucurcadena)";
        if(!isset($request->sucursal_id) or empty($request->sucursal_id) or ($request->sucursal_id == "")){
            $aux_sucursal_idCond = "true";
        }else{
            $aux_sucursal_idCond = "areaproduccionsuc.sucursal_id in ($request->sucursal_id)";
        }        
        $sql = "SELECT areaproduccionsuc.id,areaproduccion.nombre as areaproduccion_nombre,
        sucursal.nombre as sucursal_nombre,
        UNIX_TIMESTAMP(areaproduccionsuc.updated_at) as updatednum_at,
        UNIX_TIMESTAMP(sucursal.updated_at) as sucursal_updatednum_at,
        UNIX_TIMESTAMP(areaproduccion.updated_at) as areaproduccion_updatednum_at
        FROM areaproduccionsuc INNER JOIN sucursal
        ON areaproduccionsuc.sucursal_id = sucursal.id
        INNER JOIN areaproduccion
        ON areaproduccionsuc.areaproduccion_id = areaproduccion.id
        WHERE $aux_condareaproduccion_id
        AND $aux_sucursal_idCond
        AND isnull(areaproduccionsuc.deleted_at)
        AND isnull(areaproduccion.deleted_at)
        AND isnull(sucursal.deleted_at);";

        $datas = DB::select($sql);

        return $datas;
    }

}
