<?php

namespace App\Models;

use App\Models\Seguridad\Usuario;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PersonaEtapaProd extends Model
{
    protected $table = "personaetapaprod";
    protected $fillable = [
        'persona_id',
        'areaproduccionsucetapaprod_id'
    ];
    //RELACION INVERSA Persona
    public function persona()
    {
        return $this->belongsTo(Persona::class);
    }
    //RELACION INVERSA areaproduccionsucetapaprod
    public function areaproduccionsucetapaprod()
    {
        return $this->belongsTo(AreaProduccionSucEtapaProd::class);
    }
    //RELACION INVERSA usuario
    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }

    public static function etapasProdSucAreaProd($sucursal_id = false){
        if(isset($sucursal_id)){
            $sucursal_idCond = "sucursal.id in ($sucursal_id)";
        }else{
            $sucursal_idCond = "true";
        }
        $sql = "SELECT areaproduccionsucetapaprod.id AS areaproduccionsucetapaprod_id,
            areaproduccion.id AS areaproduccion_id,
            areaproduccionsucetapaprod.orden AS areaproduccionsucetapaprod_orden,
            areaproduccion.nombre AS areaproduccion_nombre,
            areaproduccionsucetapaprod.etapaprod_id,
            etapaprod.nombre AS etapaprod_nombre,
            sucursal.nombre AS sucursal_nombre,areaproduccionsuc.sucursal_id
            FROM areaproduccion INNER JOIN areaproduccionsuc
            ON areaproduccion.id = areaproduccionsuc.areaproduccion_id
            INNER JOIN areaproduccionsucetapaprod
            ON areaproduccionsucetapaprod.areaproduccionsuc_id = areaproduccionsuc.id
            INNER JOIN etapaprod
            ON etapaprod.id = areaproduccionsucetapaprod.etapaprod_id
            INNER JOIN sucursal
            ON sucursal.id = areaproduccionsuc.sucursal_id
            WHERE $sucursal_idCond
            and isnull(areaproduccion.deleted_at)
            and isnull(areaproduccionsuc.deleted_at)
            and isnull(etapaprod.deleted_at)
            ORDER BY sucursal.id,areaproduccion.id,areaproduccionsucetapaprod.orden;";
        $datas = DB::select($sql);
        return $datas;
    }
}
