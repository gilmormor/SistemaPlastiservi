<?php

namespace App\Http\Controllers;

use App\Models\AreaProduccion;
use App\Models\CategoriaProd;
use App\Models\Empresa;
use App\Models\InvBodega;
use App\Models\InvMov;
use App\Models\Producto;
use App\Models\Seguridad\Usuario;
use App\Models\Sucursal;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade as PDF;

class ReportInvStockController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('reporte-stock-inventario');
        $users = Usuario::findOrFail(auth()->id());
        $sucurArray = $users->sucursales->pluck('id')->toArray();
        $tablashtml['sucursales'] = Sucursal::orderBy('id')
                        ->whereIn('sucursal.id', $sucurArray)
                        ->get();
        $tablashtml['invbodegas'] = InvBodega::orderBy('id')->get();
        $tablashtml['areaproduccions'] =  AreaProduccion::areaproduccionxusuario();
        $tablashtml['categoriaprod'] = CategoriaProd::categoriasxUsuario();
        $selecmultprod = 1;
        return view('reportinvstock.index', compact('tablashtml','selecmultprod'));
    }

    /**
     * Kilos/unidades de cada bodega-producto que YA TIENEN DUENO: produccion
     * fabricada para una nota de venta, stock asignado a un item de NV, o stock
     * apartado a mano desde el modulo de Reserva de Stock.
     *
     * Se calcula aparte y se mezcla en PHP a proposito: InvMov::stocksql() la usan
     * doce sitios (los cinco reportes de stock, InvControl, InvBodPesajeaBodProdTerm,
     * el comando ActualizarCachePendientexProducir y Producto) y esta optimizada a
     * mano con STRAIGHT_JOIN. Meterle columnas arrastraria a los otros once usos y
     * podria tirar abajo esa optimizacion.
     *
     * Va en DOS pasos, y el orden importa por rendimiento:
     *
     *   Paso 1 - las entradas con dueno: invmovdetnvdet (lo que escribe
     *            DespachoSolController) e invmovdet_opdetregprod con
     *            notaventadetalle_id (lo que escribe la aprobacion de produccion).
     *            Son tablas chicas, asi que se arranca por ellas y se entra a
     *            invmovdet por su clave primaria.
     *
     *   Paso 2 - las salidas que esas dos tablas NO registran. Ninguna de las dos
     *            guarda la salida que hace la orden de despacho, asi que sin este
     *            paso una bodega de Picking con stock 0 aparecia con 2.000 kg
     *            comprometidos que ya se habian despachado. Esa salida si queda en
     *            invmovdet_bodsoldesp. Es el mismo problema -y la misma solucion-
     *            que documenta despachosol/form.blade.php.
     *            Se acota a las bodegas-producto del paso 1 y se parte desde
     *            invmovdet: metido en el UNION del paso 1 tardaba 3,4 s, asi
     *            tarda 0,18 s con el mismo resultado.
     *
     * El picking NO se cuenta como compromiso aparte: ese stock ya salio
     * fisicamente de la bodega de venta y esta en la bodega de Picking (tipo 1),
     * que es su propia fila del reporte. Contarlo de nuevo seria contarlo dos veces.
     *
     * Se acumula hasta el mes consultado, no solo ese mes: una reserva de octubre
     * sigue vigente en noviembre. Los movimientos de cierre de mes no tienen
     * vinculo a NV, asi que no entran y no hay doble conteo con el saldo inicial.
     */
    /**
     * Como agrupa el reporte: una fila por bodega-producto, o una por producto
     * cuando el usuario marca "AgruparxProducto".
     *
     * La traduccion es la misma que hace InvControlController@invcontrolpage, que
     * es de donde esta pantalla tomaba los datos antes. Se repite aqui para que el
     * reporte use su propia ruta sin perder esa opcion, y sin tocar invcontrolpage,
     * que la comparten invcontrol e invbodpesajeabodprodterm.
     */
    private function agrupacion(Request $request)
    {
        if (isset($request->AgruparxProducto) and $request->AgruparxProducto == 1) {
            return " invmovdet.producto_id ";
        }
        return "invbodegaproducto_id";
    }

    private function comprometido(Request $request)
    {
        $aux_annomes = \App\Models\CategoriaGrupoValMes::annomes($request->mesanno);

        /* MISMOS filtros de bodega que stocksql. Sin esto se sumarian bodegas que
           el reporte no esta mostrando -las de produccion, las de otra sucursal-.
           Los filtros de producto no hacen falta: si el producto no esta en el
           reporte, su clave no se usa. */
        $aux_user   = Usuario::findOrFail(auth()->id());
        $aux_sucUsu = implode(',', $aux_user->sucursales->pluck('id')->toArray());

        if (!isset($request->sucursal_id) or empty($request->sucursal_id) or ($request->sucursal_id == "x")) {
            $aux_condSuc = "false";
        } else {
            $aux_suc = is_array($request->sucursal_id)
                ? implode(',', $request->sucursal_id)
                : $request->sucursal_id;
            $aux_condSuc = " (ib.sucursal_id in ($aux_suc) and ib.sucursal_id in ($aux_sucUsu)) ";
        }

        $aux_condTipo = "true";
        if (isset($request->tipobodega) and !empty($request->tipobodega)) {
            $aux_tipo = is_array($request->tipobodega)
                ? implode(',', $request->tipobodega)
                : $request->tipobodega;
            $aux_condTipo = " ib.tipo in ($aux_tipo) ";
        }

        $aux_condBod = "true";
        if (isset($request->invbodega_id) and !empty($request->invbodega_id)) {
            $aux_bod = is_array($request->invbodega_id)
                ? implode(',', $request->invbodega_id)
                : $request->invbodega_id;
            $aux_condBod = " imd.invbodega_id in ($aux_bod) ";
        }

        /* Los invmovdet con dueno, reutilizados en los dos pasos. */
        $aux_conDueno = "SELECT invmovdet_id FROM invmovdetnvdet
                         UNION
                         SELECT invmovdet_id FROM invmovdet_opdetregprod
                          WHERE notaventadetalle_id IS NOT NULL";

        // ---- Paso 1: entradas con dueno ----
        $sql1 = "SELECT imd.invbodegaproducto_id AS ibp, imd.producto_id AS prod,
                        SUM(imd.cant) AS cant, SUM(imd.cantkg) AS cantkg
                 FROM ($aux_conDueno) v
                 INNER JOIN invmovdet imd ON imd.id = v.invmovdet_id AND ISNULL(imd.deleted_at)
                 INNER JOIN invmov im ON im.id = imd.invmov_id
                      AND ISNULL(im.deleted_at) AND ISNULL(im.staanul)
                      AND im.annomes <= $aux_annomes
                 INNER JOIN invbodega ib ON ib.id = imd.invbodega_id AND ISNULL(ib.deleted_at)
                 WHERE $aux_condSuc AND $aux_condTipo AND $aux_condBod
                 GROUP BY imd.invbodegaproducto_id, imd.producto_id;";

        $aux_filas = \DB::select($sql1);
        if (!count($aux_filas)) {
            return [];
        }

        $aux_res  = [];
        $aux_ibps = [];
        foreach ($aux_filas as $f) {
            $aux_ibps[] = (int) $f->ibp;
            $aux_res[$f->ibp] = [
                'prod'   => $f->prod,
                'cant'   => (float) $f->cant,
                'cantkg' => (float) $f->cantkg,
            ];
        }

        // ---- Paso 2: las salidas que el paso 1 no ve ----
        $aux_lista = implode(',', $aux_ibps);
        $sql2 = "SELECT imd.invbodegaproducto_id AS ibp,
                        SUM(imd.cant) AS cant, SUM(imd.cantkg) AS cantkg
                 FROM invmovdet imd
                 INNER JOIN invmovdet_bodsoldesp b ON b.invmovdet_id = imd.id
                 INNER JOIN despachosoldet_invbodegaproducto z
                         ON z.id = b.despachosoldet_invbodegaproducto_id AND ISNULL(z.deleted_at)
                 INNER JOIN despachosoldet dsd ON dsd.id = z.despachosoldet_id AND ISNULL(dsd.deleted_at)
                 INNER JOIN despachosol ds    ON ds.id  = dsd.despachosol_id  AND ISNULL(ds.deleted_at)
                 INNER JOIN invmov im ON im.id = imd.invmov_id
                      AND ISNULL(im.deleted_at) AND ISNULL(im.staanul)
                      AND im.annomes <= $aux_annomes
                 INNER JOIN invbodega ib ON ib.id = imd.invbodega_id AND ISNULL(ib.deleted_at)
                 WHERE ISNULL(imd.deleted_at)
                   AND imd.invbodegaproducto_id IN ($aux_lista)
                   AND $aux_condSuc AND $aux_condTipo AND $aux_condBod
                   AND imd.id NOT IN ($aux_conDueno)
                 GROUP BY imd.invbodegaproducto_id;";

        foreach (\DB::select($sql2) as $f) {
            if (isset($aux_res[$f->ibp])) {
                $aux_res[$f->ibp]['cant']   += (float) $f->cant;
                $aux_res[$f->ibp]['cantkg'] += (float) $f->cantkg;
            }
        }

        /* ---- Paso 3: el stock apartado a mano (modulo Reserva de Stock) ----
           Es el tercer tipo de compromiso, junto al fabricado para una NV y al
           asignado a un item. Son reservas blandas: el stock sigue en la bodega
           -por eso hay que restarlo del libre- pero ya tiene dueno.

           El picking NO entra aqui: ese stock ya salio fisicamente de la bodega
           de venta y esta en la bodega de Picking, que es su propia fila del
           reporte. Y una reserva que paso a picking queda marcada como cumplida
           (consumida_at), asi que deja de contar por este lado: nunca se suma
           dos veces el mismo compromiso.

           Vigentes() es la unica definicion de "esta reserva descuenta": vive en
           el modelo para que la pantalla de reservas y este reporte no puedan
           quedar en desacuerdo. */
        $aux_reservas = \App\Models\StockReserva::reservadoPorBodegaProducto();
        foreach ($aux_reservas as $aux_ibp => $aux_r) {
            if (!isset($aux_res[$aux_ibp])) {
                /* Una bodega-producto puede tener reservas sin haber pasado
                   nunca por produccion: el stock que entro por carga manual de
                   inventario. Ese es justo el caso que el picking no cubria. */
                $aux_res[$aux_ibp] = ['prod' => null, 'cant' => 0, 'cantkg' => 0];
            }
            $aux_res[$aux_ibp]['cant']   += $aux_r['cant'];
            $aux_res[$aux_ibp]['cantkg'] += $aux_r['cantkg'];
        }

        /* Las filas que solo tienen reserva no traen producto_id de las consultas
           anteriores; se completa para poder consolidar por producto. */
        foreach ($aux_res as $aux_ibp => $aux_fila) {
            if ($aux_fila['prod'] === null) {
                $aux_p = \DB::selectOne("SELECT producto_id FROM invbodegaproducto WHERE id = ?", [$aux_ibp]);
                $aux_res[$aux_ibp]['prod'] = $aux_p ? $aux_p->producto_id : null;
            }
        }

        return $aux_res;
    }

    /**
     * Agrega a cada fila del reporte lo comprometido y lo libre.
     */
    private function agregarComprometido($datas, Request $request, $agrupar = 'invbodegaproducto_id')
    {
        $aux_comp = $this->comprometido($request);

        /* El reporte puede venir agrupado por producto (consolida bodegas). En ese
           caso se suman los invbodegaproducto de ese producto, que ya vienen
           filtrados por las mismas bodegas del reporte. */
        /* Ojo: "invbodegaproducto_id" tambien contiene la palabra "producto", asi
           que la comprobacion es por lo que NO es. */
        $aux_xproducto = (trim($agrupar) != "invbodegaproducto_id");
        if ($aux_xproducto) {
            $aux_porprod = [];
            foreach ($aux_comp as $fila) {
                $aux_k = $fila['prod'];
                if (!isset($aux_porprod[$aux_k])) {
                    $aux_porprod[$aux_k] = ['cant' => 0, 'cantkg' => 0];
                }
                $aux_porprod[$aux_k]['cant']   += $fila['cant'];
                $aux_porprod[$aux_k]['cantkg'] += $fila['cantkg'];
            }
            $aux_comp = $aux_porprod;
        }

        foreach ($datas as $data) {
            $aux_clave = $aux_xproducto ? $data->producto_id : $data->invbodegaproducto_id;
            $aux_fila  = $aux_comp[$aux_clave] ?? null;
            $aux_cant  = $aux_fila ? round($aux_fila['cant'], 2) : 0;

            /* Nunca mas comprometido que lo que hay en la bodega, ni negativo: un
               descuadre de datos no debe mostrarse como stock apartado de mas. Asi
               Comprometido + Libre siempre suma el Stock de la fila. */
            $data->comprometido   = max(min($aux_cant, max($data->stock, 0)), 0);
            $data->comprometidokg = $aux_fila ? round($aux_fila['cantkg'], 2) : 0;
            $data->libre          = round(max($data->stock - $data->comprometido, 0), 2);
        }
        return $datas;
    }

    public function reportinvstockpage(Request $request){
        /* AgruparxProducto se traduce aqui, igual que lo hacia invcontrolpage. */
        $aux_group = $this->agrupacion($request);
        if ($aux_group != "invbodegaproducto_id") {
            /* merge() y no request->add(): en una peticion GET Laravel lee la
               entrada de query, asi que lo anadido a request no se ve. */
            $request->merge(['aux_group' => $aux_group]);
        }
        $datas = InvMov::stocksql($request);
        /* Comprometido / Libre: columnas nuevas, calculadas aparte para no tocar
           stocksql, que es compartida. */
        $datas = $this->agregarComprometido($datas, $request, $aux_group);
        return datatables($datas)->toJson();
/*
        return datatables()
        ->eloquent(InvMov::stock($request))
        ->toJson();
*/
    }

    public function exportPdf(Request $request)
    {
        $aux_group = $this->agrupacion($request);
        if ($aux_group != "invbodegaproducto_id") {
            /* merge() y no request->add(): en una peticion GET Laravel lee la
               entrada de query, asi que lo anadido a request no se ve. */
            $request->merge(['aux_group' => $aux_group]);
        }
        $datas = InvMov::stocksql($request);
        // Mismas columnas que en pantalla, para que el PDF no diga otra cosa.
        $datas = $this->agregarComprometido($datas, $request, $aux_group);
        //$datas = $datas->get();

        $empresa = Empresa::orderBy('id')->get();
        $usuario = Usuario::findOrFail(auth()->id());

        $nombreAreaproduccion = "Todos";
        if($request->areaproduccion_id){
            $areaProduccion = AreaProduccion::findOrFail($request->areaproduccion_id);
            $nombreAreaproduccion=$areaProduccion->nombre;
        }
        if($datas){
            $sucursal = Sucursal::findOrFail($request->sucursal_id);
            $request->request->add(['sucursal_nombre' => $sucursal->nombre]);                        
            if(env('APP_DEBUG')){
                return view('reportinvstock.listado', compact('datas','empresa','usuario','request'));
            }
            
            //return view('notaventaconsulta.listado', compact('notaventas','empresa','usuario','aux_fdesde','aux_fhasta','nomvendedor','nombreAreaproduccion','nombreGiro','nombreTipoEntrega'));
            
            //$pdf = PDF::loadView('reportinvstock.listado', compact('datas','empresa','usuario','request'))->setPaper('a4', 'landscape');
            $pdf = PDF::loadView('reportinvstock.listado', compact('datas','empresa','usuario','request'));
            //return $pdf->download('cotizacion.pdf');
            //return $pdf->stream(str_pad($notaventa->id, 5, "0", STR_PAD_LEFT) .' - '. $notaventa->cliente->razonsocial . '.pdf');
            return $pdf->stream("ReporteStockInv.pdf");
        }else{
            dd('Ningún dato disponible en esta consulta.');
        } 
    }

    public function totalizarindex(Request $request){
        $respuesta = array();
        //$datas = InvMov::stock($request)->get();
        $aux_group = $this->agrupacion($request);
        if ($aux_group != "invbodegaproducto_id") {
            /* merge() y no request->add(): en una peticion GET Laravel lee la
               entrada de query, asi que lo anadido a request no se ve. */
            $request->merge(['aux_group' => $aux_group]);
        }
        $datas = InvMov::stocksql($request);
        $datas = $this->agregarComprometido($datas, $request, $aux_group);
        $aux_totalstock = 0;
        $aux_totalkg = 0;
        $aux_totalcomp = 0;  // total comprometido
        $aux_totallibre = 0; // total libre
        foreach ($datas as $data) {
            //$aux_totalkg += $data->stockkg;
            $aux_totalstock += $data->stock;
            $aux_totalcomp  += $data->comprometido;
            $aux_totallibre += $data->libre;
            if($data->peso <= 0){
                $aux_totalkg += $data->stockkg;
            }else{
                $aux_totalkg += $data->stock * $data->peso;
            }
        }
        $respuesta['aux_totalstock'] = $aux_totalstock;
        $respuesta['aux_totalkg'] = $aux_totalkg;
        $respuesta['aux_totalcomp'] = $aux_totalcomp;
        $respuesta['aux_totallibre'] = $aux_totallibre;
        return $respuesta;
    }
}
