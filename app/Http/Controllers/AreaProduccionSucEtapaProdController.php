<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidarAreaProduccionSucEtapaProd;
use App\Http\Requests\ValidarAreaProduccionSucLinea;
use App\Http\Requests\ValidarEtapaProd;
use App\Models\AreaProduccionSuc;
use App\Models\AreaProduccionSucEtapaProd;
use App\Models\ApsucEtapaProdBodega;
use App\Models\EtapaProd;
use App\Models\InvBodega;
use App\Models\PersonaEtapaProd;
use App\Models\Seguridad\Usuario;
use App\Models\Sucursal;
use App\Models\UnidadMedida;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AreaProduccionSucEtapaProdController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-area-produccion-suc-etapa-prod');
        //$datas = FormaPago::orderBy('id')->get();
        $users = Usuario::findOrFail(auth()->id());
        $sucurArray = $users->sucursales->pluck('id')->toArray();
        $tablas['sucursales'] = Sucursal::orderBy('id')
                        ->whereIn('sucursal.id', $sucurArray)
                        ->get();
        $tablas['unidadmedidas'] = UnidadMedida::orderBy('id')
                        ->get();
        return view('areaproduccionsucetapaprod.index',compact('tablas'));
    }

    public function areaproduccionsucetapaprodpage(Request $request){
        //dd($request);
        $datas = AreaProduccionSuc::report($request);
        return datatables($datas)->toJson();
    }

    public function sucetapaprodpage(Request $request){
        //dd($request);
        $aux_areaproduccionsuc_idCond = " true ";
        if(isset($request->areaproduccionsuc_id) and $request->areaproduccionsuc_id >= 0){
            $aux_areaproduccionsuc_idCond = "areaproduccionsucetapaprod.areaproduccionsuc_id = $request->areaproduccionsuc_id";
        }
        // UM entrada = UM salida de la etapa con orden-1 dentro del mismo areaproduccionsuc_id
        $sql = "SELECT
            areaproduccionsucetapaprod.id,
            etapaprod.nombre as etapaprod_nombre,
            areaproduccionsucetapaprod.unidadmedida_id,
            areaproduccionsucetapaprod.orden,
            areaproduccionsucetapaprod.requiere_kg,
            areaproduccionsucetapaprod.requiere_cc,
            areaproduccionsucetapaprod.usa_matprima,
            (SELECT a2.unidadmedida_id
               FROM areaproduccionsucetapaprod a2
              WHERE a2.areaproduccionsuc_id = areaproduccionsucetapaprod.areaproduccionsuc_id
                AND a2.orden < areaproduccionsucetapaprod.orden
              ORDER BY a2.orden DESC LIMIT 1
            ) as unidadmedida_entrada_id,
            (SELECT u.nombre FROM areaproduccionsucetapaprod a2 LEFT JOIN unidadmedida u ON u.id = a2.unidadmedida_id
              WHERE a2.areaproduccionsuc_id = areaproduccionsucetapaprod.areaproduccionsuc_id
                AND a2.orden < areaproduccionsucetapaprod.orden
              ORDER BY a2.orden DESC LIMIT 1
            ) as unidadmedida_entrada_nombre,
            UNIX_TIMESTAMP(areaproduccionsucetapaprod.updated_at) as updatednum_at,
            areaproduccionsucetapaprod.updated_at
        from areaproduccionsucetapaprod INNER JOIN etapaprod
        ON areaproduccionsucetapaprod.etapaprod_id = etapaprod.id
        where $aux_areaproduccionsuc_idCond
        ORDER BY areaproduccionsucetapaprod.orden;";
        $datas = DB::select($sql);
        return datatables($datas)->toJson();
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function editar($id)
    {
        can('editar-area-produccion-suc-etapa-prod');
        $data = AreaProduccionSuc::findOrFail($id);
        $tablas = array();
        $tablas['etapaprods'] = EtapaProd::orderBy('id')->get();
        $tablas['aux_sta'] = 2;
        return view('areaproduccionsucetapaprod.editar', compact('data','tablas'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function actualizar(ValidarAreaProduccionSucEtapaProd $request, $id)
    {
        can('guardar-area-produccion-suc-etapa-prod');
        $areaproduccionsuc = AreaProduccionSuc::findOrFail($id);
        //dd($request->etapaprod_id);
        //$areaproduccion->update($request->all());

        DB::beginTransaction();

        try {
            // Detectar registros de areaproduccionsucetapaprod que sync() eliminaría
            $etapaprod_ids_nuevos = $request->etapaprod_id ?? [];
            //dd($etapaprod_ids_nuevos);
            $idsAEliminar = AreaProduccionSucEtapaProd::where('areaproduccionsuc_id', $id)
                ->whereNotIn('etapaprod_id', $etapaprod_ids_nuevos)
                ->pluck('id')
                ->toArray();
            //dd($idsAEliminar);
            // Bloquear si alguna etapa a eliminar tiene personas asignadas
            if (!empty($idsAEliminar)) {
                $personasAsignadas = PersonaEtapaProd::whereIn('areaproduccionsucetapaprod_id', $idsAEliminar)->count();
                if ($personasAsignadas > 0) {
                    DB::rollBack();
                    return redirect('areaproduccionsucetapaprod')->with([
                        'mensaje' => "No se puede eliminar la etapa porque tiene {$personasAsignadas} persona(s) asignada(s). Desasigne las personas antes de eliminar la etapa.",
                        'tipo_alert' => 'alert-error'
                    ]);
                }
            }

            $areaproduccionsuc->etapaprods()->sync($request->etapaprod_id);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect('areaproduccionsucetapaprod')->with([
                'mensaje'=> "Error: " . $e->getMessage(),
                'tipo_alert' => 'alert-error'
            ]);
        }
        return redirect('areaproduccionsucetapaprod')->with('mensaje','Area Produccion Sucursal actualizado con exito');
    }

    public function guardarordenetapaprod(Request $request)
    {
        can('guardar-area-produccion-suc-etapa-prod');
        //dd($request);
        $AreaProduccionSucEtapaProd = AreaProduccionSucEtapaProd::findOrFail($request->areaproduccionsucetapaprod_id);
        if(strtotime($AreaProduccionSucEtapaProd->updated_at) != $request->updatednum_at){
            return response()->json([
                'id' => 0,
                'mensaje'=>'Registro no pudo ser Actualizado. Registro Editado por otro usuario. Fecha Hora: '.$AreaProduccionSucEtapaProd->updated_at,
                'tipo_alert' => 'error'
            ]);    
        }
        $AreaProduccionSucEtapaProd->unidadmedida_id = $request->unidadmedida_id;
        $AreaProduccionSucEtapaProd->orden           = $request->orden;
        $AreaProduccionSucEtapaProd->requiere_kg     = $request->requiere_kg  ? 1 : 0;
        $AreaProduccionSucEtapaProd->requiere_cc     = $request->requiere_cc  ? 1 : 0;
        $AreaProduccionSucEtapaProd->usa_matprima    = $request->usa_matprima ? 1 : 0;
        $AreaProduccionSucEtapaProd->updated_at      = date("Y-m-d H:i:s");
        if($AreaProduccionSucEtapaProd->save()){
            return response()->json([
                'id' => $AreaProduccionSucEtapaProd->id,
                'error'=>'0',
                'mensaje'=>'Registro guardo con exito.',
                'updated_at' => date('Y-m-d H:i:s', strtotime($AreaProduccionSucEtapaProd->updated_at)),
                'tipo_alert' => 'success'
            ]);
        } else {
            return response()->json([
                'id' => 0,
                'error'=>'0',
                'mensaje'=>'Ocurrio un error al intenta guardar.',
                'tipo_alert' => 'error'
            ]);
        }
    }

    // ─────────────────────────────────────────────────────────────
    // Gestión de bodegas de inventario por etapa (apsucetapaprod_bodega)
    // ─────────────────────────────────────────────────────────────

    /**
     * Lista las bodegas asignadas a la etapa y las bodegas disponibles de la sucursal.
     */
    public function listarBodegas($id)
    {
        can('editar-area-produccion-suc-etapa-prod');
        $apsuc = AreaProduccionSucEtapaProd::with(['bodegas.invbodega'])->findOrFail($id);

        // Bodegas ya asignadas a esta etapa
        $asignadas = $apsuc->bodegas->map(function($b) {
            return [
                'id'            => $b->id,
                'invbodega_id'  => $b->invbodega_id,
                'bodega_nombre' => $b->invbodega ? $b->invbodega->nombre : '—',
            ];
        });

        // Bodegas disponibles de la sucursal (excluyendo las ya asignadas)
        $sucursal_id = $apsuc->areaproduccionsuc->sucursal_id ?? null;
        $asignadasIds = $apsuc->bodegas->pluck('invbodega_id')->toArray();

        $disponibles = InvBodega::where('sucursal_id', $sucursal_id)
            ->whereNotIn('id', $asignadasIds)
            ->whereNull('deleted_at')
            ->orderBy('nombre')
            ->get(['id', 'nombre']);

        return response()->json([
            'asignadas'   => $asignadas,
            'disponibles' => $disponibles,
        ]);
    }

    /**
     * Agrega una bodega a la etapa.
     */
    public function guardarBodega(Request $request, $id)
    {
        can('editar-area-produccion-suc-etapa-prod');
        $invbodega_id = (int) $request->invbodega_id;
        if (!$invbodega_id) {
            return response()->json(['resp' => 0, 'mensaje' => 'Debe seleccionar una bodega.']);
        }

        // Verificar duplicado
        $existe = ApsucEtapaProdBodega::where('apsucetapaprod_id', $id)
            ->where('invbodega_id', $invbodega_id)
            ->exists();
        if ($existe) {
            return response()->json(['resp' => 0, 'mensaje' => 'La bodega ya está asignada a esta etapa.']);
        }

        ApsucEtapaProdBodega::create([
            'apsucetapaprod_id' => $id,
            'invbodega_id'      => $invbodega_id,
        ]);

        return response()->json(['resp' => 1, 'mensaje' => 'Bodega asignada con éxito.']);
    }

    /**
     * Elimina la asignación de una bodega a la etapa.
     */
    public function eliminarBodega(Request $request, $id)
    {
        can('editar-area-produccion-suc-etapa-prod');
        $bodega = ApsucEtapaProdBodega::findOrFail($id);
        $bodega->delete();
        return response()->json(['resp' => 1, 'mensaje' => 'Bodega eliminada con éxito.']);
    }
}
