<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

/**
 * Un lote fabricado para una nota de venta se entrega a otro cliente o a otra
 * nota de venta.
 *
 * El principio que gobierna todo este modelo: NADA SE BORRA, SE SUMA UN HECHO
 * NUEVO. El lote se fabrico para su nota de venta de origen y eso sigue siendo
 * cierto; lo que cambia es a quien se le entrega. Las dos cosas se pueden
 * consultar por separado, y por eso el vinculo original
 * (invmovdet_opdetregprod.notaventadetalle_id) no se reescribe nunca.
 *
 * Aqui vive la unica definicion de "a quien pertenece hoy este lote", para que
 * la pantalla de reasignacion, la solicitud de despacho y cualquier reporte no
 * puedan quedar en desacuerdo.
 */
class ProdReasignacion extends Model
{
    use SoftDeletes;

    protected $table = "prodreasignacion";

    protected $fillable = [
        'opdetregprod_id',
        'nvdet_origen_id',
        'nvdet_destino_id',
        'cliente_destino_id',
        'cant',
        'motivo',
        'usuario_id',
    ];

    protected $dates = ['anulada_at', 'deleted_at'];

    public function opdetregprod()
    {
        return $this->belongsTo(OpDetRegProd::class);
    }

    public function nvdetOrigen()
    {
        return $this->belongsTo(NotaVentaDetalle::class, 'nvdet_origen_id');
    }

    public function nvdetDestino()
    {
        return $this->belongsTo(NotaVentaDetalle::class, 'nvdet_destino_id');
    }

    public function clienteDestino()
    {
        return $this->belongsTo(Cliente::class, 'cliente_destino_id');
    }

    /** Las que estan en pie: ni deshechas ni eliminadas. */
    public function scopeVigentes($query)
    {
        return $query->whereNull('deleted_at')->whereNull('anulada_at');
    }

    /**
     * La reasignacion vigente de un lote, si la tiene.
     *
     * Un lote puede haber sido reasignado, devuelto y reasignado otra vez: lo
     * que manda es la ultima que sigue en pie.
     */
    public static function vigenteDeLote($opdetregprod_id)
    {
        return self::vigentes()
            ->where('opdetregprod_id', (int) $opdetregprod_id)
            ->orderBy('id', 'desc')
            ->first();
    }

    /**
     * Lotes que se le QUITARON a un item de nota de venta.
     *
     * Es lo que necesita la solicitud de despacho para avisarle a la ejecutiva
     * que el material que esperaba ya no va a llegar.
     */
    public static function quitadasANvdet($notaventadetalle_id)
    {
        return DB::table('prodreasignacion as r')
            ->leftJoin('notaventadetalle as nvd', 'nvd.id', '=', 'r.nvdet_destino_id')
            ->leftJoin('cliente as cd', 'cd.id', '=', 'r.cliente_destino_id')
            ->leftJoin('notaventa as nv', 'nv.id', '=', 'nvd.notaventa_id')
            ->leftJoin('cliente as cnv', 'cnv.id', '=', 'nv.cliente_id')
            ->leftJoin('usuario as u', 'u.id', '=', 'r.usuario_id')
            ->whereNull('r.deleted_at')
            ->whereNull('r.anulada_at')
            ->where('r.nvdet_origen_id', (int) $notaventadetalle_id)
            ->orderBy('r.id', 'desc')
            ->select(
                'r.id', 'r.opdetregprod_id', 'r.cant', 'r.motivo', 'r.created_at',
                'nvd.notaventa_id as destino_nv',
                DB::raw('COALESCE(cd.razonsocial, cnv.razonsocial) as destino_cliente'),
                'u.usuario as usuario_nombre'
            )
            ->get();
    }

    /**
     * Lotes que un item de nota de venta RECIBIO de otra.
     *
     * El reverso de lo anterior: material que esta NV puede despachar aunque no
     * se haya fabricado para ella.
     */
    public static function recibidasPorNvdet($notaventadetalle_id)
    {
        return self::vigentes()
            ->where('nvdet_destino_id', (int) $notaventadetalle_id)
            ->pluck('opdetregprod_id')
            ->toArray();
    }

    /**
     * Lotes que YA NO pertenecen a este item, aunque se hayan fabricado para el.
     *
     * La solicitud de despacho tiene que excluirlos: si los dejara a la vista, la
     * ejecutiva podria despachar un material que ya es de otro cliente y el
     * problema se descubriria recien en bodega, o peor, en la calle.
     */
    public static function quitadasIdsDeNvdet($notaventadetalle_id)
    {
        return self::vigentes()
            ->where('nvdet_origen_id', (int) $notaventadetalle_id)
            ->pluck('opdetregprod_id')
            ->toArray();
    }

    /** Texto de a quien se le entrego, para mostrar en pantalla. */
    public function getDestinoAttribute()
    {
        if ($this->nvdet_destino_id and $this->nvdetDestino) {
            $aux_txt = 'NV ' . $this->nvdetDestino->notaventa_id;
            if ($this->nvdetDestino->notaventa and $this->nvdetDestino->notaventa->cliente) {
                $aux_txt .= ' — ' . $this->nvdetDestino->notaventa->cliente->razonsocial;
            }
            return $aux_txt;
        }
        if ($this->cliente_destino_id and $this->clienteDestino) {
            return $this->clienteDestino->razonsocial . ' (sin nota de venta)';
        }
        return '—';
    }
}
