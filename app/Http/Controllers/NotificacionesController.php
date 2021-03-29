<?php

namespace App\Http\Controllers;

use App\Models\NoConformidad;
use App\Models\Notificaciones;
use App\Models\Seguridad\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotificacionesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
    public function update(Request $request, $id)
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

    
    public function notificaciones()
    {
        $totalNotif = 0;
        $contRecepNC = 0;
        $contnotivalai = 0;
        $contnoticumpl = 0;
        $contnoRevSGI = 0;
        $usuario = Usuario::with('roles')->findOrFail(auth()->id());
        //$fecha = date("d-m-Y H:i:s",strtotime(noconformidad.fecha . "+ 1 days"));
        $fecha = date("d-m-Y H:i:s");
        if($usuario->persona){
            $notfNoConformidades = consultaSQL(1,$usuario->persona->id);

            foreach ($notfNoConformidades as $NC) {
                if($NC->notirecep === null){
                    if ((NOW()<= date("Y-m-d H:i:s",strtotime($NC->fechahora."+ 1 days")) 
                    OR (!is_null($NC->accioninmediata) and $NC->accioninmediata!=''))){
                        $aux_mostrar = false;
                        if(is_null($NC->usuario_idmp2)){
                            $aux_mostrar = true;
                        }else{
                            //Esta ultima validacion es para que cuando se rachazada la NC por el dueño permita mostrarla sin importar la fecha que fue hecha a accion inmediata -> or ($data->cumplimiento <= 0 and !is_null($data->cumplimiento))
                            $aux_mostrarCP = false;
                            if($NC->cumplimiento==1 or ($NC->cumplimiento <= 0 and !is_null($NC->cumplimiento))){
                                $aux_mostrarCP = true;
                            }
                            if($NC->aprobpaso2==1 or ($NC->aprobpaso2 <= 0 and !is_null($NC->aprobpaso2))){
                                $aux_mostrarCP = true;
                            }
                            if(($NC->usuario_idmp2==auth()->id()) AND ( $NC->accioninmediatafec<= date("Y-m-d H:i:s",strtotime($NC->fechahora."+ 1 days")) or $aux_mostrarCP )){
                                $aux_mostrar = true;
                            }
                        }
                        if ($aux_mostrar){
                            $contRecepNC++;
                        }
                    }

                }
            }
            $arearesps = consultaSQL(2,$usuario->persona->id);
            foreach ($arearesps as $data){
                if($data->notirecep === null){
                    if ((NOW()>= date("Y-m-d H:i:s",strtotime($data->fechahora."+ 1 days")) 
                    AND (is_null($data->accioninmediata) or $data->accioninmediata=='')))
                    {
                        $contRecepNC++;
                    }
                else
                    if ($data->usuario_idmp2==auth()->id()){
                        $contRecepNC++;
                    }

                }
            }
            $datas = NoConformidad::orderBy('id')
                ->where('usuario_id','=',auth()->id())
                ->get();
            $datas = consultaSQL(1,$usuario->persona->id);
            foreach ($datas as $data){
                if(!empty($data->fechaguardado) and empty($data->cumplimiento)){
                    if($data->noticumpl===null){ //($NC->fechacompromiso != null and $NC->cumplimiento === null){
                        $contnoticumpl++;
                    }    
                }
            }

            $datas = consultaSQL(3,$usuario->persona->id);
            //$datas = DB::select($sql); //Area responsable
            foreach ($datas as $data){
                if($data->accioninmediata != null and $data->stavalai === null){
                    $contnotivalai++;
                }
            }
            if(can('listar-validar-ai-nc',false)){
                $datas = consultaSQL(4,$usuario->persona->id);
                foreach($datas as $data){
                    if($data->cumplimiento != null and $data->aprobpaso2 === null){
                    $contnoRevSGI++;
                    } 
                }
                
            }
        }
        $datas = consulta(auth()->id());
        $contadornot = 0;
        if($datas){
            foreach($datas as $data){
                $contadornot += $data->contador;
            }
        }
        $totalNotif = $contRecepNC + $contnotivalai + $contnoticumpl + $contnoRevSGI + $contadornot;
        $htmlNotif = "";
        if ($totalNotif > 0){
            $htmlNotif="
            <li class='header'>Tienes $totalNotif Notificaciones</li>
            <li>
                <!-- inner menu: contains the actual data -->
                <ul class='menu'>";
                    foreach($datas as $data){
                        $aux_ruta = route('vista_notificaciones',['id' => $data->id]);
                        $htmlNotif .= "
                        <li class='tooltipsC' title='$data->mensajetitle'>
                            <a href='$aux_ruta'>
                            <i class='$data->icono'></i> $data->contador $data->mensaje
                            </a>
                        </li>";
                    }
            
                    if($contnoticumpl>0){
                        $htmlNotif .= "
                        <li>
                            <a href='" . route('noconformidadrecep') ."'>
                            <i class='fa fa-user text-green'></i> $contnoticumpl nuevas Validar Cumplimiento
                            </a>
                        </li>";
                    }
                    if($contRecepNC>0){
                        $htmlNotif .= "
                        <li>
                            <a href='" . route('noconformidadrecep') ."'>
                            <i class='fa fa-thumbs-o-down text-aqua'></i> $contRecepNC nuevas Recep no conformidades
                            </a>
                        </li>";
                    }
                    if($contnotivalai>0){
                        $htmlNotif .= "
                        <li>
                            <a href='" . route('ncvalidar') ."'>
                            <i class='fa fa-warning text-red'></i> $contnotivalai nuevas Validar Acción Inmediata
                            </a>
                        </li>";
                    }
                    if($contnoRevSGI>0){
                        $htmlNotif .= "
                        <li>
                            <a href='" . route('ncvalidar') ."'>
                            <i class='fa fa-warning text-green'></i> $contnoRevSGI nuevas Revisión SGI
                            </a>
                        </li>";
                    }
            $htmlNotif .= "
                </ul>
            </li>";

            $htmlNotif .= "
            <li class='footer'>
                <a href='#'>Fin</a>
            </li>";
        }



        /*
            <li>
                <a href='#'>
                <i class='fa fa-users text-red'></i> 5 new members joined
                </a>
            </li>
            <li>
                <a href='#'>
                <i class='fa fa-shopping-cart text-green'></i> 25 sales made
                </a>
            </li>
            <li>
                <a href='#'>
                <i class='fa fa-user text-red'></i> You changed your username
                </a>
            </li>
        */


        return response()->json([
                                    'mensaje' => 'ok',
                                    'htmlNotif' => $htmlNotif,
                                    'totalNotif' => $totalNotif
                                ]);


        //dd($sql);
        //return view('noconformidadrecep.index', compact('datas','arearesps','motivoncs','formadeteccionncs','jefaturasucursalareas','jefaturasucursalareasR','usuario_id','funcvalidarai'));
    }

    public function vista($id)
    {
        $notificacion = Notificaciones::findOrFail($id);
        $notificaciones = Notificaciones::where('mensaje','=',$notificacion->mensaje)
                                          ->where('icono','=',$notificacion->icono)
                                          ->update(['status' => 2]);
        return redirect($notificacion->rutadestino);
    }
}



function consultaSQL($idconsulta,$usuario_id){
    switch ($idconsulta) {
        case 1:
            $sql = "SELECT noconformidad.id,noconformidad.fechahora,DATE_ADD(fechahora, INTERVAL 1 DAY) AS cfecha,noconformidad.hallazgo,
            noconformidad.accioninmediata,accioninmediatafec,
            jefatura_sucursal_area.persona_id,usuario_idmp2,noconformidad.cumplimiento,noconformidad.aprobpaso2,
            noconformidad.fechacompromiso,notirecep,notivalai,noticumpl,notiresgi,
            noconformidad.fechaguardado
            FROM noconformidad INNER JOIN noconformidad_responsable
            ON noconformidad.id=noconformidad_responsable.noconformidad_id
            INNER JOIN jefatura_sucursal_area
            ON noconformidad_responsable.jefatura_sucursal_area_id=jefatura_sucursal_area.id
            WHERE jefatura_sucursal_area.persona_id=" . $usuario_id .
            " and noconformidad.deleted_at is null 
            ORDER BY noconformidad.id;";
        break;
        case 2:
            $sql = "SELECT noconformidad.id,noconformidad.fechahora,DATE_ADD(fechahora, INTERVAL 1 DAY) AS cfecha,noconformidad.hallazgo,
            noconformidad.accioninmediata,accioninmediatafec,
            jefatura_sucursal_area.persona_id,usuario_idmp2,noconformidad.cumplimiento,noconformidad.aprobpaso2,
            noconformidad.fechacompromiso,notirecep,notivalai,noticumpl,notiresgi
            FROM noconformidad INNER JOIN noconformidad_jefsucarea
            ON noconformidad.id=noconformidad_jefsucarea.noconformidad_id
            INNER JOIN jefatura_sucursal_area
            ON noconformidad_jefsucarea.jefatura_sucursal_area_id=jefatura_sucursal_area.id
            WHERE jefatura_sucursal_area.persona_id=" . $usuario_id .
            " and noconformidad.deleted_at is null 
            ORDER BY noconformidad.id;";
        break;
        case 3:
            $sql = "SELECT noconformidad.id,noconformidad.fechahora,DATE_ADD(fechahora, INTERVAL 1 DAY) AS cfecha,noconformidad.hallazgo,
                noconformidad.accioninmediata,accioninmediatafec,stavalai,
                usuario_idmp2
                FROM noconformidad
                WHERE !(accioninmediata is null) 
                and isnull(noconformidad.notivalai)
                and noconformidad.deleted_at is null 
                ORDER BY noconformidad.id;";
        break;
        case 4:
            $sql = "SELECT noconformidad.id,noconformidad.fechahora,DATE_ADD(fechahora, INTERVAL 1 DAY) AS cfecha,noconformidad.hallazgo,
                noconformidad.accioninmediata,accioninmediatafec,stavalai,notivalai,
                usuario_idmp2,cumplimiento,aprobpaso2
                FROM noconformidad
                WHERE !(accioninmediata is null) 
                and noconformidad.deleted_at is null 
                ORDER BY noconformidad.id;";
        break;
    }

    $datas = DB::select($sql);
    return $datas;

    
}

function consulta($usuario_id){
    $sql = "SELECT id,count(*) as contador,mensaje,mensajetitle,rutadestino,icono
    FROM notificaciones
    WHERE usuariodestino_id = $usuario_id
    and status = 1
    and isnull(notificaciones.deleted_at)
    GROUP BY mensaje,icono;";

    $datas = DB::select($sql);
    return $datas;
}