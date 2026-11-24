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
        return $query->whereNull('deleted_at')
            ->whereNull('anulada_at')
            ->whereNull('consumida_at')
            ->whereDate('fechavence', '>=', date('Y-m-d'));
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
     * Libre = stock de la bodega - lo ya reservado vigente. Es la cifra contra la
     * que se valida una reserva nueva: nadie puede apartar lo que otro ya aparto.
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

        $q = self::vigentes()->where('invbodegaproducto_id', $invbodegaproducto_id);
        if ($aux_excluir) {
            $q->where('id', '<>', $aux_excluir);
        }
        $aux_reservado = (float) $q->sum('cant');

        /* Nunca negativo: si el stock bajo por debajo de lo reservado -se
           despacho sin liberar la reserva- lo que queda para reservar es cero,
           no un numero negativo. */
        return max((float) $ibp->stock - $aux_reservado, 0);
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
