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
     * Las reasignaciones vigentes de un lote.
     *
     * Un lote puede repartirse entre VARIAS notas de venta: de 600 unidades, 400
     * a una y 200 a otra. Por eso devuelve una coleccion y no una sola fila.
     */
    public static function vigentesDeLote($opdetregprod_id)
    {
        return self::vigentes()
            ->where('opdetregprod_id', (int) $opdetregprod_id)
            ->orderBy('id', 'desc')
            ->get();
    }

    /**
     * Cuanto de un lote ya esta entregado a otros.
     *
     * Es lo que hay que descontar antes de reasignar otra parte, y tambien lo que
     * la nota de venta de origen deja de poder despachar.
     */
    public static function cantReasignadaDeLote($opdetregprod_id)
    {
        return (float) self::vigentes()
            ->where('opdetregprod_id', (int) $opdetregprod_id)
            ->sum('cant');
    }

    /**
     * Cuanto se le quito a un item de nota de venta, lote por lote.
     *
     * Devuelve [opdetregprod_id => cantidad]. La solicitud de despacho lo usa
     * para DESCONTAR esa cantidad del lote, no para esconderlo entero: si de 600
     * se reasignaron 400, a esa NV todavia le quedan 200 suyas que si puede
     * despachar.
     */
    public static function cantQuitadaPorLote($notaventadetalle_id)
    {
        $aux_res = [];
        $aux_filas = self::vigentes()
            ->where('nvdet_origen_id', (int) $notaventadetalle_id)
            ->select('opdetregprod_id', DB::raw('SUM(cant) as cant'))
            ->groupBy('opdetregprod_id')
            ->get();

        foreach ($aux_filas as $f) {
            $aux_res[$f->opdetregprod_id] = (float) $f->cant;
        }
        return $aux_res;
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

    /**
     * Cuanto de cada lote de una bodega-producto esta entregado a otros.
     *
     * Lo usa el calculo de stock libre: ese material tiene dueno aunque no exista
     * una reserva sobre el. Se suma la CANTIDAD reasignada, no el lote entero,
     * porque una reasignacion puede ser parcial.
     */
    public static function cantReasignadaEnBodegaProducto($invbodegaproducto_id, $aux_excluirNvdet = null)
    {
        /* Pesa lo ENTREGADO menos lo que el circuito de despacho ya tomo. El
           estado se deriva y no se guarda, a proposito: la solicitud de despacho
           va y vuelve -se devuelve a edicion, se anula, se modifica- y un estado
           marcado a mano se desincroniza en la primera vuelta.

             - solicitud devuelta a edicion: sus filas de lote siguen ahi, el lote
               sigue comprometido con ella y la pantalla de edicion lo muestra
             - lote quitado al editar: las filas se borran y vuelve a contar
             - solicitud anulada: queda fuera por el filtro de despachosolanul
             - guia emitida: el stock ya salio de la bodega, no hay que descontar

           Asi el numero sale bien en los cuatro casos sin tocar nada. */
        /* Con $aux_excluirNvdet se deja fuera lo entregado a ESE item: sirve para
           preguntar "cuanto esta comprometido con OTROS", que es lo que no puede
           tomar quien esta despachando. */
        $aux_condNvdet = $aux_excluirNvdet
            ? ' AND (r.nvdet_destino_id IS NULL OR r.nvdet_destino_id <> ' . (int) $aux_excluirNvdet . ') '
            : '';

        $aux_filas = DB::select("
            SELECT r.opdetregprod_id, r.nvdet_destino_id, SUM(r.cant) AS entregado,
                   IFNULL((SELECT SUM(dsop.cant)
                             FROM despachosoldet_opdetregprod dsop
                             INNER JOIN despachosoldet dsd ON dsd.id = dsop.despachosoldet_id
                                                          AND ISNULL(dsd.deleted_at)
                             INNER JOIN despachosol ds ON ds.id = dsd.despachosol_id
                                                      AND ISNULL(ds.deleted_at)
                            WHERE dsop.opdetregprod_id = r.opdetregprod_id
                              AND dsd.notaventadetalle_id = r.nvdet_destino_id
                              AND ds.id NOT IN (SELECT despachosol_id FROM despachosolanul
                                                 WHERE ISNULL(deleted_at))), 0) AS tomado
              FROM prodreasignacion r
             WHERE ISNULL(r.deleted_at) AND ISNULL(r.anulada_at)
               $aux_condNvdet
               AND EXISTS (SELECT 1 FROM invmovdet_opdetregprod l
                             INNER JOIN invmovdet d ON d.id = l.invmovdet_id
                            WHERE l.opdetregprod_id = r.opdetregprod_id
                              AND d.invbodegaproducto_id = ?
                              AND ISNULL(d.deleted_at))
             GROUP BY r.opdetregprod_id, r.nvdet_destino_id
        ", [$invbodegaproducto_id]);

        $aux_total = 0;
        foreach ($aux_filas as $f) {
            $aux_total += max(0, (float) $f->entregado - (float) $f->tomado);
        }
        return $aux_total;
    }

    /**
     * Lotes que este item de nota de venta RECIBIO de otra, con lo que puede
     * despachar de cada uno en una bodega concreta.
     *
     * Devuelve filas con la misma forma que las de la consulta de lotes de
     * despachosol, para poder fusionarlas sin que la vista tenga que distinguir
     * de donde salio cada una.
     *
     * Lo que se puede despachar es lo REASIGNADO, no lo que tiene el lote: si el
     * lote tiene 600 en bodega pero a esta NV se le entregaron 400, se pueden
     * despachar 400 y el resto sigue siendo de otro. Y se resta lo que esta misma
     * NV ya despacho de ese lote, o lo despacharia dos veces.
     */
    public static function lotesRecibidosEnBodega($notaventadetalle_id, $invbodegaproducto_id)
    {
        $aux_filas = DB::select("
            SELECT r.opdetregprod_id,
                   SUM(r.cant) AS cant_entregada,
                   (SELECT IFNULL(SUM(d.cant),0) FROM invmovdet_opdetregprod l
                      INNER JOIN invmovdet d ON d.id = l.invmovdet_id
                     WHERE l.opdetregprod_id = r.opdetregprod_id
                       AND d.invbodegaproducto_id = ?
                       AND ISNULL(d.deleted_at)) AS cant_lote,
                   (SELECT IFNULL(SUM(dk.cantkg),0) FROM invmovdet_opdetregprod lk
                      INNER JOIN invmovdet dk ON dk.id = lk.invmovdet_id
                     WHERE lk.opdetregprod_id = r.opdetregprod_id
                       AND dk.invbodegaproducto_id = ?
                       AND ISNULL(dk.deleted_at)) AS cantkg_lote,
                   IFNULL((SELECT SUM(dsop.cant)
                             FROM despachosoldet_opdetregprod dsop
                             INNER JOIN despachosoldet dsd ON dsd.id = dsop.despachosoldet_id
                                                          AND ISNULL(dsd.deleted_at)
                             INNER JOIN despachosol ds ON ds.id = dsd.despachosol_id
                                                      AND ISNULL(ds.deleted_at)
                            WHERE dsop.opdetregprod_id = r.opdetregprod_id
                              AND dsd.notaventadetalle_id = r.nvdet_destino_id
                              AND ds.id NOT IN (SELECT despachosol_id FROM despachosolanul
                                                 WHERE ISNULL(deleted_at))), 0) AS cant_despachada
              FROM prodreasignacion r
             WHERE r.nvdet_destino_id = ?
               AND ISNULL(r.deleted_at) AND ISNULL(r.anulada_at) AND ISNULL(r.consumida_at)
               AND EXISTS (SELECT 1 FROM invmovdet_opdetregprod l3
                             INNER JOIN invmovdet d3 ON d3.id = l3.invmovdet_id
                            WHERE l3.opdetregprod_id = r.opdetregprod_id
                              AND d3.invbodegaproducto_id = ?
                              AND ISNULL(d3.deleted_at))
             GROUP BY r.opdetregprod_id
             ORDER BY r.opdetregprod_id ASC
        ", [$invbodegaproducto_id, $invbodegaproducto_id, (int) $notaventadetalle_id, $invbodegaproducto_id]);

        $aux_res = [];
        foreach ($aux_filas as $f) {
            $aux_disp = max(0, (float) $f->cant_entregada - (float) $f->cant_despachada);

            /* Los kilos, proporcionales a lo que se despacha: el lote puede tener
               600 unidades y 36 kg, y de ahi salen los kilos de las 400. */
            $aux_kg = ((float) $f->cant_lote > 0)
                ? ($aux_disp / (float) $f->cant_lote) * (float) $f->cantkg_lote
                : 0;

            $aux_res[] = (object) [
                'opdetregprod_id'   => $f->opdetregprod_id,
                'cant_lote'         => (float) $f->cant_entregada,
                'cantkg_lote'       => $aux_kg,
                'cant_despachada'   => (float) $f->cant_despachada,
                'cant_reasignada'   => 0,
                'cant_disponible'   => $aux_disp,
                'cantkg_disponible' => $aux_kg,
                /* Marca para que la pantalla pueda decir de donde viene este lote,
                   que no se fabrico para este cliente. */
                'es_recibido'       => true,
            ];
        }
        return $aux_res;
    }

    /**
     * Cierra la reasignacion cuando el destinatario ya despacho lo que le
     * entregaron.
     *
     * Una reasignacion abierta descuenta del stock libre, igual que una reserva.
     * Si no se cerrara al despachar, seguiria comprometiendo material que ya
     * salio de la bodega y el disponible quedaria corto para siempre.
     *
     * Se cierra solo cuando lo despachado cubre lo entregado: una entrega de 400
     * despachada en dos veces de 200 sigue abierta hasta la segunda. No se toca
     * nada mas -ni el vinculo con la nota de venta de origen, ni el historial-:
     * la reasignacion sigue contando lo que paso.
     */
    public static function cerrarSiDespachada($opdetregprod_id, $notaventadetalle_id)
    {
        if (!$opdetregprod_id or !$notaventadetalle_id) {
            return;
        }

        $aux_abiertas = self::vigentes()
            ->where('opdetregprod_id', (int) $opdetregprod_id)
            ->where('nvdet_destino_id', (int) $notaventadetalle_id)
            ->get();

        if (!count($aux_abiertas)) {
            return;   // el lote no venia de una reasignacion: nada que cerrar
        }

        $aux_entregado = (float) $aux_abiertas->sum('cant');

        $aux_despachado = (float) DB::table('despachosoldet_opdetregprod as dsop')
            ->join('despachosoldet as dsd', 'dsd.id', '=', 'dsop.despachosoldet_id')
            ->join('despachosol as ds', 'ds.id', '=', 'dsd.despachosol_id')
            ->where('dsop.opdetregprod_id', (int) $opdetregprod_id)
            ->where('dsd.notaventadetalle_id', (int) $notaventadetalle_id)
            ->whereNull('dsd.deleted_at')
            ->whereNull('ds.deleted_at')
            ->whereRaw('ds.id NOT IN (SELECT despachosol_id FROM despachosolanul WHERE ISNULL(deleted_at))')
            ->sum('dsop.cant');

        if ($aux_despachado + 0.01 >= $aux_entregado) {
            foreach ($aux_abiertas as $aux_r) {
                $aux_r->consumida_at = date('Y-m-d H:i:s');
                $aux_r->save();
            }
        }
    }

    /**
     * Bodegas-producto que tienen alguna reasignacion vigente.
     *
     * El reporte de stock la usa para no dejar fuera las que solo tienen
     * reasignacion: un lote de OT libre entregado a una nota de venta no tiene
     * reserva ni produccion con dueno, y sin esto su compromiso no se veria.
     */
    public static function ibpsConReasignacionVigente()
    {
        return DB::table('prodreasignacion as r')
            ->join('invmovdet_opdetregprod as l', 'l.opdetregprod_id', '=', 'r.opdetregprod_id')
            ->join('invmovdet as d', 'd.id', '=', 'l.invmovdet_id')
            ->whereNull('r.deleted_at')
            ->whereNull('r.anulada_at')
            ->whereNull('d.deleted_at')
            ->distinct()
            ->pluck('d.invbodegaproducto_id')
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
