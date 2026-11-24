<?php

namespace App\Http\Controllers;

use App\Models\AreaProduccion;
use App\Models\CategoriaGrupoValMes;
use App\Models\Empresa;
use App\Models\InvBodega;
use App\Models\Producto;
use App\Models\Seguridad\Usuario;
use App\Models\Sucursal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade as PDF;

class ReportInvMovController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('reporte-movimiento-inventario');
        $users = Usuario::findOrFail(auth()->id());
        $sucurArray = $users->sucursales->pluck('id')->toArray();
        $sucursales = Sucursal::orderBy('id')
                        ->whereIn('sucursal.id', $sucurArray)
                        ->get();
        $tablashtml['sucursales'] = $sucursales;
        $tablashtml['areaproduccions'] =  AreaProduccion::areaproduccionxusuario();
        $tablashtml['invbodegas'] = InvBodega::orderBy('id')->get();
        /* Modulos que originan movimientos (Produccion, Entrada y Salida Inv,
           Guia de Despacho...). Sirve para aislar, por ejemplo, lo que entro por
           produccion de lo que se cargo a mano al inventario. */
        $tablashtml['invmovmodulos'] = \DB::table('invmovmodulo')
            ->whereNull('deleted_at')->orderBy('nombre')->get();
        $selecmultprod = 1;
        return view('reportinvmov.index', compact('tablashtml','selecmultprod'));
    }

    public function reporte(Request $request){
    
        if($request->ajax()){
            //dd($request);
            $datas = consultainvmov($request);
            return datatables($datas)->toJson();
        }
    }

    public function totalizarRep(Request $request){
        $respuesta = array();
        $datas = consultainvmov($request);
        $aux_totalcant = 0;
        //$aux_totaldinero = 0;
        foreach ($datas as $data) {
            $aux_totalcant += $data->cant;
            //$aux_totaldinero += $data->subtotal;
        }
        $respuesta['aux_totalcant'] = $aux_totalcant;
        //$respuesta['aux_totaldinero'] = $aux_totaldinero;
        return $respuesta;
    }

    public function exportPdf(Request $request)
    {
        $datas = consultainvmov($request);

        $empresa = Empresa::orderBy('id')->get();
        $usuario = Usuario::findOrFail(auth()->id());
       
        if($datas){
            
            if(env('APP_DEBUG')){
                return view('reportinvmov.listado', compact('datas','empresa','usuario','request'));
            }
            
            //return view('notaventaconsulta.listado', compact('notaventas','empresa','usuario','aux_fdesde','aux_fhasta','nomvendedor','nombreAreaproduccion','nombreGiro','nombreTipoEntrega'));
            
            $pdf = PDF::loadView('reportinvmov.listado', compact('datas','empresa','usuario','request'));
            $pdf->setPaper('a4', 'landscape'); // Horizontal para que entren todas las columnas
            //return $pdf->download('cotizacion.pdf');
            //return $pdf->stream(str_pad($notaventa->id, 5, "0", STR_PAD_LEFT) .' - '. $notaventa->cliente->razonsocial . '.pdf');
            return $pdf->stream("ReporteMovInv.pdf");
        }else{
            dd('Ningún dato disponible en esta consulta.');
        } 
    }


}

function consultainvmov($request){
    //dd($request->annomes);
    $aux_annomes = CategoriaGrupoValMes::annomes($request->annomes);
    $aux_condannomes = "invmov.annomes='$aux_annomes'";
    $aux_condsucursal_id = " true";
    if(!empty($request->sucursal_id)){
        $aux_condsucursal_id = "invbodega.sucursal_id='$request->sucursal_id'";
    }
    if(empty($request->fechad) or empty($request->fechah)){
        $aux_condFecha = " true";
    }else{
        $fecha = date_create_from_format('d/m/Y', $request->fechad);
        $fechad = date_format($fecha, 'Y-m-d')." 00:00:00";
        $fecha = date_create_from_format('d/m/Y', $request->fechah);
        $fechah = date_format($fecha, 'Y-m-d')." 23:59:59";
        $aux_condFecha = "invmov.fechahora>='$fechad' and invmov.fechahora<='$fechah'";
        $aux_condannomes = " true "; //SI LAS FECHAS CONTIENEN VALOR ELIMINO LA VALIDACION DE BUSQUEDA POR MES AÑO
    }
    $aux_condareaproduccion_id = " true";
    if(!empty($request->areaproduccion_id)){
        $aux_condareaproduccion_id = "categoriaprod.areaproduccion_id='$request->areaproduccion_id'";
    }
    $aux_condproducto_idPxP = " true";
    if(!empty($request->producto_idPxP)){
        $aux_codprod = explode(",", $request->producto_idPxP);
        $aux_codprod = implode ( ',' , $aux_codprod);
        $aux_condproducto_idPxP = "invmovdet.producto_id in ($aux_codprod)";
    }
    $aux_condinvbodega_id = " true";
    if(!empty($request->invbodega_id)){
        $aux_condinvbodega_id = "invmovdet.invbodega_id='$request->invbodega_id'";
    }
    $aux_condbuscarxdesc = " true";
    if(!empty($request->buscarxdesc)){
        $aux_condbuscarxdesc = "invmov.desc LIKE '%$request->buscarxdesc%'";
    }

    /* Modulo de origen. Varios a la vez, igual que bodega y area. Los ids pasan
       por intval porque el valor entra directo al SQL. */
    $aux_condmodulo = " true";
    if(!empty($request->invmovmodulo_id)){
        $aux_modulos = is_array($request->invmovmodulo_id)
            ? $request->invmovmodulo_id
            : explode(',', $request->invmovmodulo_id);
        $aux_modulos = array_filter(array_map('intval', $aux_modulos));
        if(count($aux_modulos)){
            $aux_condmodulo = " invmov.invmovmodulo_id in (" . implode(',', $aux_modulos) . ") ";
        }
    }

    /* Para quien es el movimiento. La pregunta de fondo era poder separar lo que
       entro para una nota de venta de lo que entro para stock -por una OT libre
       o por carga manual de inventario-, que es justo lo que no se podia ver.

       El vinculo con la NV puede estar en dos tablas: invmovdetnvdet (la escribe
       la solicitud de despacho) o invmovdet_opdetregprod.notaventadetalle_id (la
       escribe la aprobacion de produccion). Se usa EXISTS y no un JOIN porque un
       movimiento puede tener varias filas en esas tablas y un JOIN duplicaria la
       fila del reporte, inflando el total de cantidades. */
    $aux_existeNV = "(EXISTS (SELECT 1 FROM invmovdetnvdet mn WHERE mn.invmovdet_id = invmovdet.id)
                      OR EXISTS (SELECT 1 FROM invmovdet_opdetregprod mo
                                  WHERE mo.invmovdet_id = invmovdet.id
                                    AND mo.notaventadetalle_id IS NOT NULL))";
    $aux_conddestino = " true";
    if(isset($request->destino) and $request->destino === 'nv'){
        $aux_conddestino = $aux_existeNV;
    }
    if(isset($request->destino) and $request->destino === 'stock'){
        $aux_conddestino = " NOT " . $aux_existeNV;
    }
    $sql = "SELECT STRAIGHT_JOIN invmov.id,invmovdet.id as invmovdet_id,
            usuario.usuario,
            invmov.fechahora,invmov.annomes,invmov.desc,invmov.obs,invbodegaproducto.producto_id,
            invmov.idmovmod,invmovmodulo.nombre as invmovmodulo_nombre,
            CONCAT (invbodega.nombre, ' / ' , sucursal.abrev) as invbodega_nombre,sucursal.nombre as sucursal_nombre,
            producto.glosa as producto_nombre,categoriaprod.nombre as categoriaprod_nombre,invmovdet.cant,
            invmovdet.cantkg,
            unidadmedida.nombre as unidadmedida_nombre,
            invmov.invmovmodulo_id,acuerdotecnico.id as acuerdotecnico_id,
            /* Nota de venta a la que pertenece el movimiento, por cualquiera de
               las dos vias. Subconsultas y no JOIN: un JOIN duplicaria la fila
               cuando hay mas de un vinculo, y el total de cantidades saldria mal. */
            (SELECT nvd.notaventa_id FROM invmovdetnvdet mn
               INNER JOIN notaventadetalle nvd ON nvd.id = mn.notaventadetalle_id
              WHERE mn.invmovdet_id = invmovdet.id LIMIT 1) as nv_por_nvdet,
            (SELECT nvd2.notaventa_id FROM invmovdet_opdetregprod mo
               INNER JOIN notaventadetalle nvd2 ON nvd2.id = mo.notaventadetalle_id
              WHERE mo.invmovdet_id = invmovdet.id LIMIT 1) as nv_por_lote
            FROM invmov INNER JOIN invmovdet
            ON invmov.id = invmovdet.invmov_id and isnull(invmov.deleted_at) and isnull(invmov.staanul) and isnull(invmovdet.deleted_at)
            INNER JOIN invbodegaproducto
            on invmovdet.invbodegaproducto_id = invbodegaproducto.id and isnull(invbodegaproducto.deleted_at)
            INNER JOIN producto
            on invbodegaproducto.producto_id = producto.id and isnull(producto.deleted_at)
            INNER JOIN invbodega
            on invbodegaproducto.invbodega_id = invbodega.id and isnull(invbodega.deleted_at)
            INNER JOIN sucursal
            on invbodega.sucursal_id = sucursal.id and isnull(sucursal.deleted_at)
            INNER JOIN categoriaprod
            on categoriaprod.id=producto.categoriaprod_id and isnull(categoriaprod.deleted_at)
            INNER JOIN invmovmodulo
            on invmovmodulo.id=invmov.invmovmodulo_id and isnull(invmovmodulo.deleted_at)
            LEFT JOIN acuerdotecnico
            ON producto.id = acuerdotecnico.producto_id and isnull(acuerdotecnico.deleted_at)
            INNER JOIN usuario
            ON usuario.id = invmov.usuario_id and isnull(usuario.deleted_at)
            INNER JOIN unidadmedida
            ON unidadmedida.id = invmovdet.unidadmedida_id and isnull(unidadmedida.deleted_at)
            WHERE $aux_condannomes
            and $aux_condsucursal_id
            and $aux_condFecha
            and $aux_condareaproduccion_id
            and $aux_condproducto_idPxP
            and $aux_condinvbodega_id
            and $aux_condbuscarxdesc
            and $aux_condmodulo
            and $aux_conddestino
            ORDER BY invmov.id,invbodega.orden;";
    //dd($sql);
    $datas = DB::select($sql);

    /* Texto del dueno, resuelto una sola vez aqui para que la pantalla, el PDF y
       el Excel digan lo mismo. "Stock" no significa huerfano: significa que ese
       movimiento no se hizo para una nota de venta concreta -una OT libre, una
       carga manual de inventario- y por lo tanto queda disponible para asignar. */
    foreach ($datas as $d) {
        $aux_nv = $d->nv_por_nvdet ?: $d->nv_por_lote;
        $d->notaventa_destino = $aux_nv ?: null;
        $d->destino_txt = $aux_nv ? ('NV ' . $aux_nv) : 'Stock';
    }

    return $datas;
}