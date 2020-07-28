<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidarNoCAccionCorrectiva;
use App\Http\Requests\ValidarNoCAccionInmediata;
use App\Http\Requests\ValidarNoCAnalisisDeCausa;
use App\Http\Requests\ValidarNoCAprobpaso2;
use App\Http\Requests\ValidarNoCCumplimiento;
use App\Http\Requests\ValidarNoCFechaCompromiso;
use App\Http\Requests\ValidarNoCFechaGuardado;
use App\Http\Requests\ValidarNoCIncumplimiento;
use App\Http\Requests\ValidarNoCobsvalai;
use App\Models\Certificado;
use App\Models\FormaDeteccionNC;
use App\Models\JefaturaSucursalArea;
use App\Models\MotivoNc;
use App\Models\NoConformidad;
use App\Models\Seguridad\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NoConformidadRecepController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-recepcion-no-conformidad');
        $usuario = Usuario::with('roles')->findOrFail(auth()->id());
        //$fecha = date("d-m-Y H:i:s",strtotime(noconformidad.fecha . "+ 1 days"));
        $fecha = date("d-m-Y H:i:s");
        //dd($fecha);
        $datas = NoConformidad::orderBy('noconformidad.id')
                ->join('noconformidad_responsable', 'noconformidad.id', '=', 'noconformidad_responsable.noconformidad_id')
                ->join('jefatura_sucursal_area', 'noconformidad_responsable.jefatura_sucursal_area_id', '=', 'jefatura_sucursal_area.id')
                ->where('jefatura_sucursal_area.persona_id','=',$usuario->persona->id)
                ->select([
                    'noconformidad.id',
                    'noconformidad.fechahora',
                    'noconformidad.hallazgo'
                    ])
                ->get();
        $sql = "SELECT noconformidad.id,noconformidad.fechahora,DATE_ADD(fechahora, INTERVAL 1 DAY) AS cfecha,noconformidad.hallazgo,
        noconformidad.accioninmediata,accioninmediatafec,
        jefatura_sucursal_area.persona_id,usuario_idmp2,noconformidad.cumplimiento,noconformidad.aprobpaso2
        FROM noconformidad INNER JOIN noconformidad_responsable
        ON noconformidad.id=noconformidad_responsable.noconformidad_id
        INNER JOIN jefatura_sucursal_area
        ON noconformidad_responsable.jefatura_sucursal_area_id=jefatura_sucursal_area.id
        WHERE jefatura_sucursal_area.persona_id=" . $usuario->persona->id .
        " and noconformidad.deleted_at is null 
        ORDER BY noconformidad.id;";

/*
WHERE jefatura_sucursal_area.persona_id=" .$usuario->persona->id .
" AND (NOW()<=DATE_ADD(fechahora, INTERVAL 1 DAY)
OR (!ISNULL(accioninmediata) and accioninmediata!=''))
*/

        $datas = DB::select($sql);
        //dd($datas);

        $sql = "SELECT noconformidad.id,noconformidad.fechahora,DATE_ADD(fechahora, INTERVAL 1 DAY) AS cfecha,noconformidad.hallazgo,
        noconformidad.accioninmediata,accioninmediatafec,
        jefatura_sucursal_area.persona_id,usuario_idmp2,noconformidad.cumplimiento
        FROM noconformidad INNER JOIN noconformidad_jefsucarea
        ON noconformidad.id=noconformidad_jefsucarea.noconformidad_id
        INNER JOIN jefatura_sucursal_area
        ON noconformidad_jefsucarea.jefatura_sucursal_area_id=jefatura_sucursal_area.id
        WHERE jefatura_sucursal_area.persona_id=" . $usuario->persona->id .
        " and noconformidad.deleted_at is null 
        ORDER BY noconformidad.id;";

        $arearesps = DB::select($sql); //Area responsable
        //dd($arearesps);


        $motivoncs = MotivoNc::orderBy('id')->pluck('descripcion', 'id')->toArray();
        $formadeteccionncs = FormaDeteccionNC::orderBy('id')->pluck('descripcion', 'id')->toArray();
        $jefaturasucursalareas = JefaturaSucursalArea::orderBy('id')->get();
        $jefaturasucursalareasR = JefaturaSucursalArea::orderBy('id')
                                ->whereNotNull('updated_at')
                                ->get();
        $certificados = Certificado::orderBy('id')->get();
        $usuario_id = $usuario->persona->id;
        $funcvalidarai = '';

        //dd($sql);
        return view('noconformidadrecep.index', compact('datas','arearesps','motivoncs','formadeteccionncs','jefaturasucursalareas','jefaturasucursalareasR','usuario_id','funcvalidarai'));
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
    public function editar($id,$sta_val)
    {
        //can('editar-no-conformidad');
        //dd($sta_val);
        $data = NoConformidad::findOrFail($id);
        $funcvalidarai = $sta_val;
        $directory = "storage/imagenes/noconformidad/";      
        $images = glob($directory . "*.*");
        return view('noconformidadrecep.editar',compact('data','funcvalidarai','images'));
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

    public function buscar(Request $request)
    {
        //dd($request);
        if ($request->ajax()) {
            $noconformidad = NoConformidad::findOrFail($request->id);
            
            //dd($noconformidad->jefaturasucursalareas->jefatura->get());
            $jefaturas = array();
            foreach ($noconformidad->jefaturasucursalareas as $jefatura) {
                $jefaturas[] = $jefatura->jefatura->nombre;
            }
            $certificados = array();
            foreach ($noconformidad->certificados as $certificado) {
                $certificados[] = $certificado->descripcion;
            }
            $responsables = array();
            foreach ($noconformidad->jefaturasucursalarearesponsables as $responsable) {
                $responsables[] = $responsable->persona->nombre . " " .$responsable->persona->apellido;
            }
            $feccomp = date("d/m/Y", strtotime( $noconformidad->fechacompromiso));
            
            return response()->json([
                                        'mensaje' => 'ok',
                                        'noconformidad' => $noconformidad,
                                        'motivonc' => $noconformidad->motivonc->descripcion,
                                        'formadeteccionnc' => $noconformidad->formadeteccionnc->descripcion,
                                        'jefaturas' => $jefaturas,
                                        'certificados' => $certificados,
                                        'responsables' => $responsables,
                                        'feccomp' => $feccomp
                                    ]);
        }
    }

    public function actai(ValidarNoCAccionInmediata $request)
    {
        if ($request->ajax()) {
            $noconformidad = NoConformidad::findOrFail($request->id);
            $noconformidad->accioninmediata = $request->accioninmediata;
            $noconformidad->accioninmediatafec = date("Y-m-d H:i:s");
            $noconformidad->usuario_idmp2 = auth()->id();
            if($noconformidad->cumplimiento===0){ //Si es === 0 es porque fue rechazado el cumplimiento de la NC entonces cuando guarda cambia a -1 para la autorizacion de
                $noconformidad->cumplimiento = -1;
            }
            if($noconformidad->aprobpaso2===0){ //Si es === 0 es porque fue rechazada la revision SGI de la NC entonces cuando guarda cambia a -1 para la autorizacion del siguiente valor
                $noconformidad->aprobpaso2 = -1;
            }

            if ($noconformidad->save()) {
                return response()->json(['mensaje' => 'ok']);
            } else {
                return response()->json(['mensaje' => 'ng']);
            }
        } else {
            abort(404);
        }
    }

    public function actobsvalai(ValidarNoCobsvalai $request)
    {
        if ($request->ajax()) {
            $noconformidad = NoConformidad::findOrFail($request->id);
            $noconformidad->stavalai = $request->stavalai;
            $noconformidad->obsvalai = $request->obsvalai;
            $noconformidad->fechavalai = date("Y-m-d H:i:s");
            $noconformidad->usuario_idvalai = auth()->id();
            if($noconformidad->cumplimiento===-1){ //Si es === -1 es porque fue aceptada la modificacion de la accion inmediata que habia sido incumplida y pasa al siguiente nivel analisis de causa
                $noconformidad->cumplimiento = -2;
            }
            if($noconformidad->aprobpaso2===-1){ //Si es === -1 es porque fue aceptada la modificacion de la accion inmediata que habia sido rechazada y pasa al siguiente nivel analisis de causa
                $noconformidad->aprobpaso2 = -2;
            }

            if ($noconformidad->save()) {
                return response()->json(['mensaje' => 'ok']);
            } else {
                return response()->json(['mensaje' => 'ng']);
            }
        } else {
            abort(404);
        }

    }

    public function actacausa(ValidarNoCAnalisisDeCausa $request)
    {
        if ($request->ajax()) {
            $noconformidad = NoConformidad::findOrFail($request->id);
            $noconformidad->analisisdecausa = $request->analisisdecausa;
            $noconformidad->analisisdecausafec = date("Y-m-d H:i:s");
            if($noconformidad->cumplimiento===-2){ //Si es === -2 es porque guardo el analisis de causa y pasa al siguiente nivel que es accion correctiva
                $noconformidad->cumplimiento = -3;
            }
            if($noconformidad->aprobpaso2===-2){ //Si es === -2 es porque guardo el analisis de causa y pasa al siguiente nivel que es accion correctiva
                $noconformidad->aprobpaso2 = -3;
            }

            if ($noconformidad->save()) {
                return response()->json(['mensaje' => 'ok']);
            } else {
                return response()->json(['mensaje' => 'ng']);
            }
        } else {
            abort(404);
        }
    }

    public function actacorr(ValidarNoCAccionCorrectiva $request)
    {
        if ($request->ajax()) {
            $noconformidad = NoConformidad::findOrFail($request->id);
            $noconformidad->accorrec = $request->accorrec;
            $noconformidad->accorrecfec = date("Y-m-d H:i:s");
            if($noconformidad->cumplimiento===-3){ //Si es === -3 es porque guardo Accion Correctiva y pasa al siguiente nivel que es fecha de compromiso.
                $noconformidad->cumplimiento = -4;
            }
            if($noconformidad->aprobpaso2===-3){ //Si es === -3 es porque guardo el analisis de causa y pasa al siguiente nivel que es accion correctiva
                $noconformidad->aprobpaso2 = -4;
            }
            if ($noconformidad->save()) {
                return response()->json(['mensaje' => 'ok']);
            } else {
                return response()->json(['mensaje' => 'ng']);
            }
        } else {
            abort(404);
        }
    }

    public function actfeccomp(ValidarNoCFechaCompromiso $request)
    {
        if ($request->ajax()) {
            $noconformidad = NoConformidad::findOrFail($request->id);
            $dateInput = explode('/',$request->fechacompromiso);
            $request["fechacompromiso"] = $dateInput[2].'-'.$dateInput[1].'-'.$dateInput[0];
            $noconformidad->fechacompromiso = $request->fechacompromiso;
            $noconformidad->fechacompromisofec = date("Y-m-d H:i:s");
            if($noconformidad->cumplimiento===-4){ //Si es === -4 es porque guardo fecha de compromiso y pasa al siguiente nivel que es Guardar y terminar edicion de la NC.
                $noconformidad->cumplimiento = -5;
            }
            if($noconformidad->aprobpaso2===-4){ //Si es === -4 es porque guardo fecha de compromiso y pasa al siguiente nivel que es Guardar y terminar edicion de la NC.
                $noconformidad->aprobpaso2 = -5;
            }
            if ($noconformidad->save()) {
                return response()->json(['mensaje' => 'ok']);
            } else {
                return response()->json(['mensaje' => 'ng']);
            }
        } else {
            abort(404);
        }
    }

    public function actfechaguardado(ValidarNoCFechaGuardado $request)
    {
        if ($request->ajax()) {
            $noconformidad = NoConformidad::findOrFail($request->id);
            $noconformidad->fechaguardado = date("Y-m-d H:i:s");
            if($noconformidad->cumplimiento===-5){ //Si es === -5 es porque guardo y terminó la edición de la NC y pasa al siguiente nivel que es Validar Cumplimiento que esto lo hace el dueño del la NC .
                $noconformidad->cumplimiento = -6;
            }
            if($noconformidad->aprobpaso2===-5){ //Si es === -5 es porque guardo y terminó la edición de la NC y pasa al siguiente nivel que es Validar Cumplimiento que esto lo hace el dueño del la NC .
                $noconformidad->aprobpaso2 = -6;
            }

            if ($noconformidad->save()) {
                return response()->json(['mensaje' => 'ok']);
            } else {
                return response()->json(['mensaje' => 'ng']);
            }
        } else {
            abort(404);
        }
    }

    public function actvalai(Request $request)
    {
        if ($request->ajax()) {
            $noconformidad = NoConformidad::findOrFail($request->id);
            $noconformidad->stavalai = $request->stavalai;
            $noconformidad->obsvalai = $request->obsvalai;
            $noconformidad->fechavalai = date("Y-m-d H:i:s");
            $noconformidad->usuario_idvalai = auth()->id();
            if ($noconformidad->save()) {
                return response()->json(['mensaje' => 'ok']);
            } else {
                return response()->json(['mensaje' => 'ng']);
            }
        } else {
            abort(404);
        }
    }

    public function cumplimiento(ValidarNoCCumplimiento $request)
    {
        if ($request->ajax()) {
            $noconformidad = NoConformidad::findOrFail($request->id);
            $noconformidad->cumplimiento = $request->cumplimiento;
            $noconformidad->fechacumplimiento = date("Y-m-d H:i:s");
            if($noconformidad->aprobpaso2===-6){ //Si es === -6 es porque guardo y paso el cumplimiento y pasa al siguiente nivel que es aceptacion o rechaso de revición SGI .
                $noconformidad->aprobpaso2 = -7;
            }
            if ($noconformidad->save()) {
                return response()->json(['mensaje' => 'ok']);
            } else {
                return response()->json(['mensaje' => 'ng']);
            }
        } else {
            abort(404);
        }
    }

    public function incumplimiento(ValidarNoCIncumplimiento $request)
    {
        if ($request->ajax()) {
            $noconformidad = NoConformidad::findOrFail($request->id);
            $noconformidad->cumplimiento = 0;
            $noconformidad->fechacumplimiento = date("Y-m-d H:i:s");
            if ($noconformidad->save()) {
                return response()->json(['mensaje' => 'ok']);
            } else {
                return response()->json(['mensaje' => 'ng']);
            }
        } else {
            abort(404);
        }
    }


    public function consvalai(Request $request)
    {
        if ($request->ajax()) {
            $noconformidad = NoConformidad::findOrFail($request->id);
            return response()->json([
                                        'mensaje' => 'ok',
                                        'noconformidad' => $noconformidad
                                    ]);
        } else {
            abort(404);
        }
    }

    public function aprobpaso2(ValidarNoCAprobpaso2 $request)
    {
        if ($request->ajax()) {
            $noconformidad = NoConformidad::findOrFail($request->id);
            $noconformidad->aprobpaso2 = $request->aprobpaso2;
            $noconformidad->fecaprobpaso2 = date("Y-m-d H:i:s");
            if($request->aprobpaso2 == 0){
                $noconformidad->cumplimiento = null;
                $noconformidad->fechacumplimiento = null;
                $noconformidad->accioninmediata = $request->accioninmediata;
                $noconformidad->analisisdecausa = $request->analisisdecausa;
                $noconformidad->accorrec = $request->accorrec;
            }
            if ($noconformidad->save()) {
                return response()->json(['mensaje' => 'ok']);
            } else {
                return response()->json(['mensaje' => 'ng']);
            }
        } else {
            abort(404);
        }
    }

}
