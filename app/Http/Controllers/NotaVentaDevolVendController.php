<?php

namespace App\Http\Controllers;

use App\Mail\MailNotaVentaDevuelta;
use App\Models\DespachoSol;
use App\Models\DespachoSolAnul;
use App\Models\NotaVenta;
use App\Models\NotaVentaCerrada;
use App\Models\Notificaciones;
use App\Models\Seguridad\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class NotaVentaDevolVendController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {        
        can('listar-devolver-nota-venta-vendedor');
        return view('notaventadevolvend.index');
    }

    public function notaventadevolvervenpage(){
        $datas = consulta("");
        return datatables($datas)->toJson();
    }

    public function anularnotaventapage()
    {
        $datas = consulta("");
        return datatables($datas)->toJson(); 
    }

    public function indexanular()
    {        
        can('listar-anular-nota-venta');

        return view('notaventaanular.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function actualizar(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function actualizarreg(Request $request)
    {
        can('guardar-devolver-nota-venta-vendedor');
        if ($request->ajax()) {
            $notaventa = NotaVenta::findOrFail($request->id);
            if($notaventa->updated_at != $request->updated_at){
                return [
                    'mensaje' => 'No se pudo modificar. Registro Editado por otro usuario. Fecha Hora: '.$notaventa->updated_at,
                    'tipo_alert' => 'alert-error'
                ];
            }
            /* Opt: anti-join reemplaza NOT IN; evita full scan de despachosolanul por cada fila */
            $sql = "SELECT notaventa_id
                        FROM despachosol
                        LEFT JOIN despachosolanul
                        ON despachosolanul.despachosol_id = despachosol.id AND ISNULL(despachosolanul.deleted_at)
                        WHERE notaventa_id = $request->id
                        AND ISNULL(despachosol.deleted_at)
                        AND despachosolanul.despachosol_id IS NULL;";
            $datas = DB::select($sql);
            if(count($datas) > 0){
                return [
                    'mensaje' => 'No se pudo modificar. Registro Editado por otro usuario. Tiene Solicitud de Despacho asociado a Nota de Venta.',
                    'tipo_alert' => 'alert-error'
                ];
            }

            $datas = consulta($request->id);
            //dd(count($datas));

            $notaventa->aprobstatus = null;
            $notaventa->aprobusu_id = null;
            $notaventa->aprobfechahora = null;
            $notaventa->aprobobs = null;
            
            if(count($datas) > 0){
                if ($notaventa->save()) {
                    $notificaciones = new Notificaciones();
                    $notificaciones->usuarioorigen_id = auth()->id();
                    $aux_email = $notaventa->vendedor->persona->email;
                    if($notaventa->vendedor->persona->usuario){
                        $notificaciones->usuariodestino_id = $notaventa->vendedor->persona->usuario->id;
                        $aux_email = $notaventa->vendedor->persona->usuario->email;
                    }
                    $notificaciones->vendedor_id = $notaventa->vendedor_id;
                    $notificaciones->status = 1;                    
                    $notificaciones->nombretabla = 'notaventa';
                    $notificaciones->mensaje = 'Nota Venta Devuelta';
                    $notificaciones->nombrepantalla = 'notaventadevolvend.index';
                    $notificaciones->rutaorigen = 'notaventadevolvend';
                    $notificaciones->rutadestino = 'notaventa';
                    $notificaciones->tabla_id = $request->id;
                    $notificaciones->accion = 'Nota Venta devuelta al vendedor.';
                    $notificaciones->icono = 'fa fa-fw fa-reply text-yellow';
                    $notificaciones->save();
                    //$usuario = Usuario::findOrFail(auth()->id());
                    $asunto = 'Nota de Venta Devuelta';
                    $cuerpo = "Nota de Venta Devuelta: Nro. $request->id";
    
                    // ValidarEmailAntesDeSendListener valida formato y DNS antes del envío.
                    // Si el correo es inválido, el envío se cancelará y se notificará a los administradores.
                    Mail::to($aux_email)->send(new MailNotaVentaDevuelta($notificaciones,$asunto,$cuerpo));
                    return response()->json(['mensaje' => 'ok']);
                } else {
                    return response()->json(['mensaje' => 'ng']);
                }    
            }else{
                return response()->json(['mensaje' => 'ng']);
            }
        } else {
            abort(404);
        }
    }
}


function consulta($id){
    $aux_condid = "true";
    if($id!=""){
        $aux_condid = "notaventa.id=$id";
    }
    //Consultar registros que estan sin aprobar por vendedor null o 0 y los rechazados por el supervisor rechazado por el supervisor=4

    $user = Usuario::findOrFail(auth()->id());
    $sucurArray = implode ( ',' , $user->sucursales->pluck('id')->toArray());
    $aux_condsucursal_id = " notaventa.sucursal_id in ($sucurArray)";
    $sql = "SELECT notaventa.id,DATE_FORMAT(notaventa.fechahora,'%d/%m/%Y %h:%i %p') as fechahora,
            notaventa.cotizacion_id,razonsocial,aprobstatus,aprobobs,oc_id,oc_file,
            CONCAT(persona.nombre, ' ', persona.apellido) as nombrevendedor,
                (SELECT COUNT(*) 
                FROM notaventadetalle 
                WHERE notaventadetalle.notaventa_id=notaventa.id and 
                notaventadetalle.precioxkilo < notaventadetalle.precioxkiloreal) AS contador,
            notaventa.updated_at
            FROM notaventa inner join cliente
            on notaventa.cliente_id = cliente.id
            inner join vendedor
            on notaventa.vendedor_id=vendedor.id
            inner join persona
            on vendedor.persona_id=persona.id
            /* Opt: anti-join reemplaza NOT IN; evita full scan de notaventacerrada por cada fila */
            LEFT JOIN notaventacerrada
            ON notaventacerrada.notaventa_id = notaventa.id AND ISNULL(notaventacerrada.deleted_at)
            where $aux_condid
            and isnull(notaventa.findespacho)
            and isnull(anulada)
            and (aprobstatus=1 or aprobstatus=3)
            and $aux_condsucursal_id
            /* Opt: NOT EXISTS con anti-join interno reemplaza NOT IN anidado; despachosol puede tener N filas por notaventa */
            AND NOT EXISTS (SELECT 1 FROM despachosol
                            LEFT JOIN despachosolanul ON despachosolanul.despachosol_id = despachosol.id AND ISNULL(despachosolanul.deleted_at)
                            WHERE despachosol.notaventa_id = notaventa.id
                            AND ISNULL(despachosol.deleted_at)
                            AND despachosolanul.despachosol_id IS NULL)
            AND notaventacerrada.notaventa_id IS NULL
            and isnull(notaventa.deleted_at)
            /* Opt: NOT EXISTS con anti-join interno reemplaza NOT IN anidado; dteguiadespnv puede tener N filas por notaventa */
            AND NOT EXISTS (SELECT 1 FROM dteguiadespnv
                            LEFT JOIN dteanul ON dteanul.dte_id = dteguiadespnv.dte_id AND ISNULL(dteanul.deleted_at)
                            WHERE dteguiadespnv.notaventa_id = notaventa.id
                            AND dteanul.dte_id IS NULL)
            order by notaventa.id desc;";
    $datas = DB::select($sql);
    return $datas;

}