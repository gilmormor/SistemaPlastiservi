<?php

namespace App\Http\Controllers;

use App\Models\Certificado;
use App\Models\FormaDeteccionNC;
use App\Models\JefaturaSucursalArea;
use App\Models\MotivoNc;
use App\Models\NoConformidad;
use App\Models\Seguridad\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NoConformidadValidarController extends Controller
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
        $sql = "SELECT noconformidad.id,noconformidad.fechahora,DATE_ADD(fechahora, INTERVAL 1 DAY) AS cfecha,noconformidad.hallazgo,
        noconformidad.accioninmediata,accioninmediatafec,
        usuario_idmp2
        FROM noconformidad
        WHERE !(accioninmediata is null) 
        and noconformidad.deleted_at is null 
        ORDER BY noconformidad.id;";

        $datas = DB::select($sql); //Area responsable
        //dd($arearesps);


        $motivoncs = MotivoNc::orderBy('id')->pluck('descripcion', 'id')->toArray();
        $formadeteccionncs = FormaDeteccionNC::orderBy('id')->pluck('descripcion', 'id')->toArray();
        $jefaturasucursalareas = JefaturaSucursalArea::orderBy('id')->get();
        $jefaturasucursalareasR = JefaturaSucursalArea::orderBy('id')
                                ->whereNotNull('updated_at')
                                ->get();
        $certificados = Certificado::orderBy('id')->get();
        $usuario_id = $usuario->persona->id;
        $funcvalidarai = 'class="tooltipsC" title="Validar Accion Inmediata No conformidad" onclick="validarai()"';
        //dd($sql);
        return view('noconformidadvalidar.index', compact('datas','motivoncs','formadeteccionncs','jefaturasucursalareas','jefaturasucursalareasR','usuario_id','funcvalidarai'));
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
        $data = NoConformidad::findOrFail($id);

        $funcvalidarai = $sta_val;
        
        return view('noconformidadvalidar.editar',compact('data','funcvalidarai'));
    }
    
    
}
