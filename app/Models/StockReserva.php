<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

/**
 * Reserva de stock: apartar sin mover.
 *
 * Es el paso anterior al picking. El picking saca el stock de la bodega de venta
 * y por eso deja de estar disponible; la reserva no mueve nada, solo declara que
 * ese stock tiene dueno. Sirve para lo que el picking no puede hacer: apartar
 * antes de que exista la nota de venta.
 *
 * Toda la logica de "que esta vigente" vive aqui y en ningun otro lado. Si la
 * regla cambia -por ejemplo, que una reserva vencida siga contando unos dias-
 * se cambia en un solo sitio y el reporte de stock, la pantalla de reservas y
 * cualquier validacion futura siguen de acuerdo.
 */
class StockReserva extends Model
{
    use SoftDeletes;

    protected $table = "stockreserva";

    protected $fillable = [
        'invbodegaproducto_id',
        'opdetregprod_id',
        'cliente_id',
        'notaventadetalle_id',
        'cant',
        'cantkg',
        'fechavence',
        'motivo',
        'usuario_id',
    ];

    protected $dates = ['fechavence', 'anulada_at', 'consumida_at', 'deleted_at'];

    public function invbodegaproducto()
    {
        return $this->belongsTo(InvBodegaProducto::class);
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function notaventadetalle()
    {
        return $this->belongsTo(NotaVentaDetalle::class);
    }

    public function opdetregprod()
    {
        return $this->belongsTo(OpDetRegProd::class);
    }

    /**
     * Reservas que hoy descuentan stock.
     *
     * Vigente = ni liberada a mano, ni ya cumplida (paso a picking/despacho), ni
     * vencida. Las tres condiciones tienen que estar: una reserva vencida que
     * siguiera descontando es como no tener vencimiento, y una ya consumida
     * descontaria dos veces, porque el stock ya se movio.
     */
    public function scopeVigentes($query)
    {
        /* Columnas calificadas con la tabla: este scope se usa tambien en
           consultas con join -el calculo de #stockM une con invbodegaproducto e
           invbodega, que tienen su propio deleted_at- y sin el prefijo MySQL lo
           rechaza por ambiguo. */
        return $query->whereNull('stockreserva.deleted_at')
            ->whereNull('stockreserva.anulada_at')
            ->whereNull('stockreserva.consumida_at')
            ->whereDate('stockreserva.fechavence', '>=', date('Y-m-d'));
    }

    /**
     * Cuanto hay reservado hoy, por bodega-producto.
     *
     * Devuelve [invbodegaproducto_id => ['cant' => n, 'cantkg' => n]]. Se puede
     * acotar a una lista de bodega-producto para no traer toda la tabla cuando
     * el que pregunta solo mira unas pocas filas (el reporte de stock).
     */
    public static function reservadoPorBodegaProducto($aux_ibps = null)
    {
        $q = self::vigentes()
            ->select(
                'invbodegaproducto_id',
                DB::raw('SUM(cant) as cant'),
                DB::raw('SUM(IFNULL(cantkg,0)) as cantkg')
            )
            ->groupBy('invbodegaproducto_id');

        if (is_array($aux_ibps) and count($aux_ibps)) {
            $q->whereIn('invbodegaproducto_id', $aux_ibps);
        }

        $aux_res = [];
        foreach ($q->get() as $fila) {
            $aux_res[$fila->invbodegaproducto_id] = [
                'cant'   => (float) $fila->cant,
                'cantkg' => (float) $fila->cantkg,
            ];
        }
        return $aux_res;
    }

    /**
     * Cuanto queda LIBRE para reservar en una bodega-producto.
     *
     * Libre = stock de la bodega - lo ya reservado vigente - los lotes ya
     * reasignados a otro cliente o nota de venta. Es la cifra contra la que se
     * valida una reserva nueva: nadie puede apartar lo que otro ya aparto, venga
     * ese compromiso de una reserva o de una reasignacion de produccion.
     *
     * El stock sale de invbodegaproducto.stock, que es el saldo que mantiene el
     * propio modulo de inventario; no se recalcula aqui para no tener dos
     * verdades distintas del mismo numero.
     *
     * $aux_excluir permite ignorar una reserva concreta, que es lo que hace falta
     * al EDITAR una existente: si no, se validaria contra si misma.
     */
    public static function libreEnBodegaProducto($invbodegaproducto_id, $aux_excluir = null)
    {
        $ibp = InvBodegaProducto::find($invbodegaproducto_id);
        if (!$ibp) {
            return 0;
        }

        $q = self::vigentes()->where('stockreserva.invbodegaproducto_id', $invbodegaproducto_id);
        if ($aux_excluir) {
            $q->where('stockreserva.id', '<>', $aux_excluir);
        }
        $aux_reservado = (float) $q->sum('stockreserva.cant');

        /* Lo que de los lotes de esta bodega-producto YA se entrego a otro cliente
           o a otra nota de venta tampoco esta libre: ese material tiene dueno
           aunque no exista una reserva sobre el. Sin esto, el mismo lote se podria
           reasignar a una NV y reservar para otro cliente, y el sistema diria que
           es de los dos.

           Se suma la CANTIDAD reasignada y no el movimiento del lote, porque una
           reasignacion puede ser parcial: de 600 se pueden haber entregado 400 y
           las otras 200 siguen libres. */
        $aux_reasignado = \App\Models\ProdReasignacion::cantReasignadaEnBodegaProducto($invbodegaproducto_id);

        /* Nunca negativo: si el stock bajo por debajo de lo comprometido -se
           despacho sin liberar la reserva- lo que queda para reservar es cero,
           no un numero negativo. */
        return max((float) $ibp->stock - $aux_reservado - max($aux_reasignado, 0), 0);
    }

    /**
     * Cuanto puede tomar ESTE item de nota de venta de una bodega-producto.
     *
     * No es lo mismo que el stock libre. El libre es lo que no tiene dueno, y
     * sirve para saber si se puede reservar. Pero a la hora de despachar, un item
     * puede tomar ademas LO SUYO: su propia reserva y lo que le entregaron por
     * una reasignacion de produccion.
     *
     * Si ABR COMERCIAL tiene 200 reservados, para cualquier otro esos 200 no
     * existen, pero para ABR COMERCIAL son justamente lo que vino a buscar.
     *
     * Por eso se calcula al reves: del stock se descuenta lo comprometido con
     * OTROS, y lo que queda es lo que este item puede tomar.
     *
     *   NV 35504 (le entregaron 400, tomo 100):  500 - 200 de ABR      = 300
     *   una NV de ABR COMERCIAL:                 500 - 300 de la 35504 = 200
     *   cualquier otra:                          500 - 200 - 300       = 0
     */
    public static function disponibleParaNvdet($invbodegaproducto_id, $notaventadetalle_id = null)
    {
        $ibp = InvBodegaProducto::find($invbodegaproducto_id);
        if (!$ibp) {
            return 0;
        }
        $aux_stock = (float) $ibp->stock;

        /* Cliente de este item, para reconocer las reservas hechas a su nombre
           cuando todavia no existia la nota de venta. */
        $aux_cliente_id = null;
        if ($notaventadetalle_id) {
            $aux_cliente_id = DB::table('notaventadetalle as nvd')
                ->join('notaventa as nv', 'nv.id', '=', 'nvd.notaventa_id')
                ->where('nvd.id', (int) $notaventadetalle_id)
                ->value('nv.cliente_id');
        }

        /* Reservas de OTROS: ni de este item ni de su cliente. */
        $q = self::vigentes()->where('stockreserva.invbodegaproducto_id', $invbodegaproducto_id);
        if ($notaventadetalle_id) {
            $q->where(function ($q2) use ($notaventadetalle_id, $aux_cliente_id) {
                $q2->where(function ($q3) use ($notaventadetalle_id) {
                    $q3->whereNull('stockreserva.notaventadetalle_id')
                       ->orWhere('stockreserva.notaventadetalle_id', '<>', (int) $notaventadetalle_id);
                });
                if ($aux_cliente_id) {
                    $q2->where(function ($q4) use ($aux_cliente_id) {
                        $q4->whereNull('stockreserva.cliente_id')
                           ->orWhere('stockreserva.cliente_id', '<>', $aux_cliente_id);
                    });
                }
            });
        }
        $aux_resAjena = (float) $q->sum('stockreserva.cant');

        /* Produccion entregada a OTROS items por reasignacion, y todavia no
           tomada por el circuito de despacho. */
        $aux_reaAjena = \App\Models\ProdReasignacion::cantReasignadaEnBodegaProducto(
            $invbodegaproducto_id, $notaventadetalle_id
        );

        return round(max($aux_stock - $aux_resAjena - $aux_reaAjena, 0), 2);
    }

    /**
     * Texto de para quien es la reserva, para mostrar en pantalla.
     */
    public function getDestinoAttribute()
    {
        $aux_txt = [];
        if ($this->cliente_id and $this->cliente) {
            $aux_txt[] = $this->cliente->razonsocial;
        }
        if ($this->notaventadetalle_id and $this->notaventadetalle) {
            $aux_txt[] = 'NV ' . $this->notaventadetalle->notaventa_id;
        }
        return count($aux_txt) ? implode(' — ', $aux_txt) : '—';
    }

    /**
     * Estado para mostrar: el orden de las comprobaciones importa, porque una
     * reserva liberada y ademas vencida se informa como liberada, que es lo que
     * de verdad le paso.
     */
    public function getEstadoAttribute()
    {
        if ($this->anulada_at) {
            return 'Liberada';
        }
        if ($this->consumida_at) {
            return 'Cumplida';
        }
        if ($this->fechavence < date('Y-m-d')) {
            return 'Vencida';
        }
        return 'Vigente';
    }
}
