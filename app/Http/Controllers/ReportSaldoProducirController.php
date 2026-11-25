<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Seguridad\Usuario;
use App\Models\StockReserva;
use App\Models\Sucursal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * SALDO POR PRODUCIR — de donde sale el numero que ve el vendedor.
 *
 * En la nota de venta y en la cotizacion, el campo de stock del modal de producto
 * NO muestra el stock: muestra cuanto falta fabricar para cubrir todo lo
 * comprometido. Un negativo significa que el producto esta SOBREVENDIDO.
 *
 *     stock vendible - pendiente por despachar - reservado
 *
 * Al vendedor se le da solo ese numero, a proposito: si viera que hay 750 en
 * bodega pensaria que puede venderlos y agravaria la sobreventa.
 *
 * Pero entonces nadie puede explicar el numero. Un -29.450 no se reconstruye
 * desde el reporte de stock, porque ese muestra lo que hay HOY (150 libres) y no
 * incluye las 30.000 unidades ya vendidas. Son dos preguntas distintas:
 *
 *     reportinvstock      cuanto hay y cuanto esta libre        -> presente fisico
 *     esta pantalla       cuanto falta fabricar para cumplir    -> saldo comprometido
 *
 * Esta pantalla existe para responder la segunda con el detalle completo: cada
 * bodega, cada nota de venta y cada reserva que forman el numero. No es para
 * vendedores; tiene su propio permiso.
 */
class ReportSaldoProducirController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        can('reporte-saldo-producir');

        $users = Usuario::findOrFail(auth()->id());
        $sucurArray = $users->sucursales->pluck('id')->toArray();
        $tablashtml['sucursales'] = Sucursal::orderBy('id')
            ->whereIn('sucursal.id', $sucurArray)->get();

        $selecmultprod = true;
        return view('reportsaldoproducir.index', compact('tablashtml', 'selecmultprod'));
    }

    /**
     * Una fila por producto: las tres piezas y el saldo.
     *
     * Acepta varios productos -el buscador devuelve una lista separada por
     * comas- porque comparar es justo lo que hace falta: ver de un vistazo
     * cuales estan sobrevendidos. El desglose de cada uno se pide aparte.
     */
    public function listadopage(Request $request)
    {
        can('reporte-saldo-producir');

        $aux_sucursal = (int) $request->sucursal_id;
        if (!$aux_sucursal) {
            return datatables([])->toJson();
        }

        $aux_productos = $this->productosPedidos($request);
        if (!count($aux_productos)) {
            return datatables([])->toJson();
        }

        $datas = [];
        foreach ($aux_productos as $aux_prod_id) {
            $aux_d = $this->calcular($aux_prod_id, $aux_sucursal);
            if ($aux_d) {
                $datas[] = $aux_d;
            }
        }

        return datatables($datas)->toJson();
    }

    /** El desglose completo de un producto: bodega por bodega y venta por venta. */
    public function detalle($producto_id, Request $request)
    {
        can('reporte-saldo-producir');

        $aux_sucursal = (int) $request->sucursal_id;
        $aux_prod = Producto::atributosProducto((int) $producto_id);

        return response()->json([
            'ok'       => true,
            'producto' => $aux_prod['nombre'] ?? ('Prod. ' . $producto_id),
            'resumen'  => $this->calcular((int) $producto_id, $aux_sucursal),
            'bodegas'  => $this->detalleBodegas((int) $producto_id, $aux_sucursal),
            'ventas'   => $this->detalleVentas((int) $producto_id, $aux_sucursal),
            'reservas' => $this->detalleReservas((int) $producto_id, $aux_sucursal),
        ]);
    }

    /**
     * Las tres piezas del saldo.
     *
     * Se calculan aqui, en vivo y por bodega, en vez de leer la cache que usa el
     * campo del vendedor: esta pantalla existe justamente para explicar el
     * numero, y una cache de hace cinco minutos no explica nada. Si el saldo de
     * aqui no coincide con el de la nota de venta, es que la cache esta vieja
     * -se refresca con producto:actualizar-cache-pendientexproducir-.
     */
    private function calcular($producto_id, $sucursal_id)
    {
        /* Stock vendible: TODAS las bodegas de tipo 1 (picking) y 2 (venta) de la
           sucursal, no una. Si manana se agrega otra bodega, entra sola. */
        $aux_stock = (float) DB::table('invbodegaproducto as ibp')
            ->join('invbodega as b', 'b.id', '=', 'ibp.invbodega_id')
            ->where('ibp.producto_id', $producto_id)
            ->whereIn('b.tipo', [1, 2])
            ->where('b.sucursal_id', $sucursal_id)
            ->where('b.activo', 1)
            ->whereNull('ibp.deleted_at')
            ->whereNull('b.deleted_at')
            ->sum('ibp.stock');

        $aux_pend = (float) DB::table('notaventadetalle as nvd')
            ->join('notaventa as nv', 'nv.id', '=', 'nvd.notaventa_id')
            ->leftJoin('vista_sumorddespxnvdetid as od', 'od.notaventadetalle_id', '=', 'nvd.id')
            ->where('nvd.producto_id', $producto_id)
            ->where('nv.sucursal_id', $sucursal_id)
            ->whereNull('nvd.deleted_at')
            ->whereNull('nv.deleted_at')
            ->whereNull('nv.anulada')
            ->whereNull('nv.findespacho')
            ->whereIn('nv.aprobstatus', [1, 3])
            ->whereRaw('nvd.notaventa_id NOT IN (SELECT notaventa_id FROM notaventacerrada WHERE ISNULL(deleted_at))')
            ->whereRaw('nvd.cant > IFNULL(od.cantdesp, 0)')
            ->sum(DB::raw('nvd.cant - IFNULL(od.cantdesp, 0)'));

        $aux_res = (float) StockReserva::vigentes()
            ->join('invbodegaproducto as ibp', 'ibp.id', '=', 'stockreserva.invbodegaproducto_id')
            ->join('invbodega as b', 'b.id', '=', 'ibp.invbodega_id')
            ->where('ibp.producto_id', $producto_id)
            ->where('b.sucursal_id', $sucursal_id)
            ->sum('stockreserva.cant');

        if ($aux_stock == 0 and $aux_pend == 0 and $aux_res == 0) {
            return null;   // nada que contar de este producto en esta sucursal
        }

        $aux_prod = Producto::atributosProducto($producto_id);

        /* Para quien esta reservado, en el propio listado: ver "200 reservado"
           sin saber de quien obliga a abrir el detalle para algo que cabe en una
           linea, y es justo el dato que explica por que falta ese material. */
        $aux_resPara = [];
        if ($aux_res > 0) {
            foreach ($this->detalleReservas($producto_id, $sucursal_id) as $aux_r) {
                $aux_txt = $aux_r->razonsocial ?: ('NV ' . $aux_r->notaventa_id);
                $aux_resPara[] = $aux_txt . ' (' . number_format($aux_r->cant, 2, ',', '.') . ')';
            }
        }

        /* Y de quien es lo pendiente: con una sola nota de venta se nombra, y con
           varias se dice cuantas son, que es lo que se puede leer de un vistazo. */
        $aux_ventas = $this->detalleVentas($producto_id, $sucursal_id);
        $aux_pendPara = '';
        if (count($aux_ventas) == 1) {
            $aux_pendPara = 'NV ' . $aux_ventas[0]->notaventa_id . ' — ' . $aux_ventas[0]->razonsocial;
        } elseif (count($aux_ventas) > 1) {
            $aux_pendPara = count($aux_ventas) . ' notas de venta';
        }

        return (object) [
            'producto_id'      => $producto_id,
            'producto_nombre'  => $aux_prod['nombre'] ?? ('Prod. ' . $producto_id),
            'stock'            => round($aux_stock, 2),
            'pendiente'        => round($aux_pend, 2),
            'pendiente_para'   => $aux_pendPara,
            'reservado'        => round($aux_res, 2),
            'reservado_para'   => implode('; ', $aux_resPara),
            'saldo'            => round($aux_stock - $aux_pend - $aux_res, 2),
        ];
    }

    private function detalleBodegas($producto_id, $sucursal_id)
    {
        return DB::table('invbodegaproducto as ibp')
            ->join('invbodega as b', 'b.id', '=', 'ibp.invbodega_id')
            ->where('ibp.producto_id', $producto_id)
            ->whereIn('b.tipo', [1, 2])
            ->where('b.sucursal_id', $sucursal_id)
            ->where('b.activo', 1)
            ->whereNull('ibp.deleted_at')
            ->whereNull('b.deleted_at')
            ->orderBy('b.orden')
            ->select('b.nombre as bodega', 'b.tipo', 'ibp.stock')
            ->get();
    }

    private function detalleVentas($producto_id, $sucursal_id)
    {
        return DB::table('notaventadetalle as nvd')
            ->join('notaventa as nv', 'nv.id', '=', 'nvd.notaventa_id')
            ->join('cliente as c', 'c.id', '=', 'nv.cliente_id')
            ->leftJoin('vista_sumorddespxnvdetid as od', 'od.notaventadetalle_id', '=', 'nvd.id')
            ->where('nvd.producto_id', $producto_id)
            ->where('nv.sucursal_id', $sucursal_id)
            ->whereNull('nvd.deleted_at')
            ->whereNull('nv.deleted_at')
            ->whereNull('nv.anulada')
            ->whereNull('nv.findespacho')
            ->whereIn('nv.aprobstatus', [1, 3])
            ->whereRaw('nvd.notaventa_id NOT IN (SELECT notaventa_id FROM notaventacerrada WHERE ISNULL(deleted_at))')
            ->whereRaw('nvd.cant > IFNULL(od.cantdesp, 0)')
            ->orderBy('nvd.notaventa_id', 'desc')
            ->select(
                'nvd.notaventa_id', 'c.razonsocial', 'nvd.cant',
                DB::raw('IFNULL(od.cantdesp,0) as despachado'),
                DB::raw('nvd.cant - IFNULL(od.cantdesp,0) as pendiente')
            )->get();
    }

    private function detalleReservas($producto_id, $sucursal_id)
    {
        return StockReserva::vigentes()
            ->join('invbodegaproducto as ibp', 'ibp.id', '=', 'stockreserva.invbodegaproducto_id')
            ->join('invbodega as b', 'b.id', '=', 'ibp.invbodega_id')
            ->leftJoin('cliente as c', 'c.id', '=', 'stockreserva.cliente_id')
            ->leftJoin('notaventadetalle as nvd', 'nvd.id', '=', 'stockreserva.notaventadetalle_id')
            ->where('ibp.producto_id', $producto_id)
            ->where('b.sucursal_id', $sucursal_id)
            ->orderBy('stockreserva.id', 'desc')
            ->select(
                'stockreserva.id', 'stockreserva.cant', 'stockreserva.fechavence',
                'stockreserva.motivo', 'b.nombre as bodega',
                'c.razonsocial', 'nvd.notaventa_id'
            )->get();
    }

    /** Los productos que pidio el usuario: uno, varios o ninguno. */
    private function productosPedidos(Request $request)
    {
        if (empty($request->producto_id)) {
            return [];
        }
        $aux_ids = is_array($request->producto_id)
            ? $request->producto_id
            : explode(',', $request->producto_id);

        return array_values(array_unique(array_filter(array_map('intval', $aux_ids))));
    }
}
