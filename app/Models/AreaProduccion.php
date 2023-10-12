<?php

namespace App\Models;

use App\Models\Seguridad\Usuario;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AreaProduccion extends Model
{
    use SoftDeletes;
    protected $table = "areaproduccion";
    protected $fillable = [
        'nombre',
        'descripcion',
        'stapromkg',
        'usuariodel_id'
    ];
    //RELACION UNO A MUCHOS CATEGORIAPROD
    public function categoriaprods()
    {
        return $this->hasMany(CategoriaProd::class);
    }
    //Relacion inversa a Sucursal
    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }
    public function sucursales()
    {
        return $this->belongsToMany(Sucursal::class, 'areaproduccionsuc','areaproduccion_id')->withTimestamps();
    }
    //RELACION UNO A MUCHOS AreaProduccionSuc
    public function areaproduccionsucs()
    {
        return $this->hasMany(AreaProduccionSuc::class,'areaproduccion_id');
    }

    public static function areaproduccionxusuario(){
        $user = Usuario::findOrFail(auth()->id());
        $sucurArray = $user->sucursales->pluck('id')->toArray(); //$clientesArray['sucurArray'];
        //SUCURSAL DONDE ESTA FISICAMENTE LA PERSONA
        //PUEDER SER QUE LA PERSONA ESTE EN MAS DE UNA SUCURSAL FISICAMENTE 
        $sucurJefAreaFis = Persona::sucursalFisica(auth()->id());
        $areaproduccion = AreaProduccion::orderBy('areaproduccion.id')
                            ->join('areaproduccionsuc', 'areaproduccion.id', '=', 'areaproduccionsuc.areaproduccion_id')
                            ->whereIn('areaproduccionsuc.sucursal_id', $sucurArray)
                            ->whereIn('areaproduccionsuc.sucursal_id', $sucurJefAreaFis)
                            ->select([
                                'areaproduccion.id',
                                'areaproduccion.nombre',
                                'areaproduccionsuc.sucursal_id'
                            ])
                            ->groupBy('areaproduccion.id')
                            ->get();
        return $areaproduccion;
    }
}