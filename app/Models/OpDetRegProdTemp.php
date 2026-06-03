<?php

namespace App\Models;

use App\Models\Seguridad\Usuario;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OpDetRegProdTemp extends Model
{
    use SoftDeletes;
    protected $table = "opdetregprodtemp";
    /**
     * Campos de cantidad/UM — semantica:
     *   cantent              Cantidad entrada al proceso (en unidadmedidaent_id)
     *   unidadmedidaent_id   UM de entrada (tipicamente kg)
     *   kgent                Kg entrada al proceso (input de la etapa anterior / MP)
     *   kgprod               Kg producidos (salida real) = kgent - kgscrap
     *   kgscrap              Kg descartados en este registro
     *   cantprod             Cantidad producida (en unidadmedidasal_id).
     *                        0 mientras la UM salida (rollo/bolsa/pieza) no se cierra.
     *   unidadmedidasal_id   UM de salida (rollo, bolsa, pieza, kg, etc.)
     *
     * Invariante: kgent = kgprod + kgscrap
     */
    protected $fillable = [
        'opdet_id',
        'etapaprod_id',
        'producto_id',
        'sucursal_id',
        'cantent',
        'unidadmedidaent_id',
        'kgent',
        'kgprod',
        'kgscrap',
        'cantprod',
        'unidadmedidasal_id',
        'mtslineal',
        'obs',
        'operario_id',
        'aprobstatus',
        'aprobusu_id',
        'aprobfechahora',
        'aprobobs',
        'usuario_id',
        'usuariodel_id',
    ];
    //RELACION INVERSA opdet
    public function opdet()
    {
        return $this->belongsTo(OpDet::class);
    }
    //RELACION INVERSA Usuario
    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }

    /**
     * Busca el primer registro bloqueante DENTRO DEL MISMO ROLLO del opdet_id dado.
     *
     * Concepto de "mismo rollo": un rollo es la secuencia de registros que termina cuando
     * aparece uno con cantprod>0 (cerrador). Dos registros estan en el mismo rollo cuando
     * no existe un cerrador entre ellos. El filtro NOT EXISTS implementa exactamente eso.
     *
     * NOTA: Se asume opdet_id => maquina_id 1:1 (hoy hay una sola fila por opdet_id en
     * opdetmaquina). Cuando un opdet pueda tener varias maquinas, habra que agregar
     * maquina_id como campo propio en opdetregprodtemp y filtrar por el.
     *
     * @param int    $opdet_id    opdet_id comun
     * @param int    $id_actual   id del registro en cuestion
     * @param string $direccion   'anterior' o 'posterior'
     * @param \Closure $filtroEstado Aplicar filtros adicionales (ej. aprobstatus)
     * @return OpDetRegProdTemp|null
     */
    public static function buscarEnMismoRollo($opdet_id, $id_actual, $direccion, \Closure $filtroEstado)
    {
        $q = self::where('opdet_id', $opdet_id)->whereNull('deleted_at');
        if ($direccion === 'anterior') {
            $q->where('id', '<', $id_actual);
            // Mismo rollo que $id_actual: no hay cerrador con id >= M.id Y id < $id_actual.
            $q->whereRaw(
                "NOT EXISTS (SELECT 1 FROM opdetregprodtemp C
                    WHERE C.opdet_id = ?
                      AND C.id >= opdetregprodtemp.id
                      AND C.id < ?
                      AND IFNULL(C.cantprod,0) > 0
                      AND C.deleted_at IS NULL)",
                [$opdet_id, $id_actual]
            );
            $q->orderBy('id', 'asc');
        } else { // 'posterior'
            $q->where('id', '>', $id_actual);
            // Mismo rollo: no hay cerrador con id >= $id_actual Y id < M.id.
            $q->whereRaw(
                "NOT EXISTS (SELECT 1 FROM opdetregprodtemp C
                    WHERE C.opdet_id = ?
                      AND C.id >= ?
                      AND C.id < opdetregprodtemp.id
                      AND IFNULL(C.cantprod,0) > 0
                      AND C.deleted_at IS NULL)",
                [$opdet_id, $id_actual]
            );
            $q->orderBy('id', 'desc');
        }
        $filtroEstado($q);
        return $q->first();
    }
}
