<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidarNoCAccionCorrectiva;
use App\Http\Requests\ValidarNoCAccionInmediata;
use App\Http\Requests\ValidarNoCAnalisisDeCausa;
use App\Http\Requests\ValidarNoCFechaCompromiso;
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
        $noconformidad = NoConformidad::findOrFail(1);
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
        noconformidad.accioninmediata,
        jefatura_sucursal_area.persona_id,usuario_idmp2
        FROM noconformidad INNER JOIN noconformidad_responsable
        ON noconformidad.id=noconformidad_responsable.noconformidad_id
        INNER JOIN jefatura_sucursal_area
        ON noconformidad_responsable.jefatura_sucursal_area_id=jefatura_sucursal_area.id
        WHERE jefatura_sucursal_area.persona_id=" . $usuario->persona->id .
        " ORDER BY noconformidad.id;";

/*
WHERE jefatura_sucursal_area.persona_id=" .$usuario->persona->id .
" AND (NOW()<=DATE_ADD(fechahora, INTERVAL 1 DAY)
OR (!ISNULL(accioninmediata) and accioninmediata!=''))
*/

        $datas = DB::select($sql);

        $sql = "SELECT noconformidad.id,noconformidad.fechahora,DATE_ADD(fechahora, INTERVAL 1 DAY) AS cfecha,noconformidad.hallazgo,
        noconformidad.accioninmediata,
        jefatura_sucursal_area.persona_id,usuario_idmp2
        FROM noconformidad INNER JOIN noconformidad_jefsucarea
        ON noconformidad.id=noconformidad_jefsucarea.noconformidad_id
        INNER JOIN jefatura_sucursal_area
        ON noconformidad_jefsucarea.jefatura_sucursal_area_id=jefatura_sucursal_area.id
        WHERE jefatura_sucursal_area.persona_id=" . $usuario->persona->id .
        " ORDER BY noconformidad.id;";

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

        //dd($sql);
        return view('noconformidadrecep.index', compact('datas','arearesps','motivoncs','formadeteccionncs','jefaturasucursalareas','jefaturasucursalareasR','usuario_id'));
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
