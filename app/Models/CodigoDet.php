<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\SoftDeletes;
use ReflectionClass;
use ReflectionMethod;

class CodigoDet extends Model
{
    use SoftDeletes;
    protected $table = "codigodet";
    protected $fillable = [
        'codigo_id',
        'descdet',
        'usuario_id',
        'usuariodel_id'
    ];

    //RELACION INVERSA NotaVenta
    public function codigo()
    {
        return $this->belongsTo(Codigo::class);
    }
    //RELACION INVERSA User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    //RELACION INVERSA User
    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }

    //RELACION INVERSA acuerdotecnicotempcodigodet
    public function acuerdotecnicotempcodigodet()
    {
        return $this->belongsTo(AcuerdoTecnicoTempCodigoDet::class);
    }

    //RELACION INVERSA acuerdotecnicocodigodet
    public function acuerdotecnicocodigodet()
    {
        return $this->belongsTo(AcuerdoTecnicoCodigoDet::class);
    }

    public function hasRelationships()
    {
        $relationships = [];
        $model = new static;
        
        // Obtener todos los métodos públicos del modelo
        $methods = (new ReflectionClass($model))->getMethods(ReflectionMethod::IS_PUBLIC);
        
        foreach ($methods as $method) {
            if ($method->class != get_class($model) || 
                !empty($method->getParameters()) || 
                $method->getName() == __FUNCTION__) {
                continue;
            }
            
            // Verificar si el método devuelve una relación
            try {
                $return = $method->invoke($model);
                
                if ($return instanceof Relation) {
                    if ($return->exists()) {
                        return true;
                    }
                }
            } catch (\Exception $e) {
                continue;
            }
        }
        
        return false;
    }

}
