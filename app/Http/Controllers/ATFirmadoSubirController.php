<?php

namespace App\Http\Controllers;

use App\Models\AcuerdoTecnico;
use App\Models\AreaProduccion;
use App\Models\AtFirmEli;
use App\Models\CategoriaProd;
use App\Models\Certificado;
use App\Models\Color;
use App\Models\Empresa;
use App\Models\MateriaPrima;
use App\Models\Producto;
use App\Models\Seguridad\Usuario;
use App\Models\Sucursal;
use App\Models\TipoSello;
use App\Models\UnidadMedida;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ATFirmadoSubirController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-reporte-productos');
        $users = Usuario::findOrFail(auth()->id());
        $sucurArray = $users->sucursales->pluck('id')->toArray();
        $tablashtml['sucursales'] = Sucursal::orderBy('id')
                        ->whereIn('sucursal.id', $sucurArray)
                        ->get();
        $tablashtml['areaproduccions'] =  AreaProduccion::areaproduccionxusuario();
        $tablashtml['categoriaprod'] = CategoriaProd::categoriasxUsuario();
        $selecmultprod = 1;
        $tablas['empresa'] = Empresa::findOrFail(1);

        $aux_sta=1;

        return view('atfirmadosubir.index', compact('tablashtml','selecmultprod','tablas','aux_sta'));
    }

    public function atfirmadosubirpage(Request $request){
        $datas = Producto::productosxUsuarioRep($request);
        return datatables($datas)->toJson();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function editar($id)
    {
        can('editar-subir-at-firmado');
        //dd($id);
        //dd(session('aux_paginaredirect'));
        $data = Producto::findOrFail($id);
        if(!isset($data->acuerdotecnico)){
            return redirect('ateditar')->with([
                'mensaje'=> "Producto $id no tiene Acuerdo Tecnico",
                'tipo_alert' => 'alert-error'
            ]);
        }
        $tablas['atributoProd'] = Producto::atributosProducto($id);
        $tablas['empresa'] = Empresa::findOrFail(1);

        session(['editaracutec' => '1']);
        $aux_sta=1;

        return view('atfirmadosubir.editar', compact('data','tablas','aux_sta'));
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
        //dd($request->ip());
        can('guardar-subir-at-firmado');

        $acuerdotecnico = AcuerdoTecnico::findOrFail($request->acuerdotecnico_id);
        // Antes de los updates
        $acuerdotecnicoOriginal = AcuerdoTecnico::with('acuerdotecnicocvalatdets')
            ->findOrFail($request->acuerdotecnico_id);
        
        DB::beginTransaction();
        try {
            $request->merge([
                'id' => $request->acuerdotecnico_id,
                'namefilenew' => 'atfirm' . $request->acuerdotecnico_id,
                'nombrecampofile' => "at_filefirmado",
                'file_del' => $request->at_filefirmado_deleted,
                'namefileold' => $acuerdotecnico->at_firmado,
                'url' => '/imagenes/atfirm/',
                'file_loader' => isset($request->at_filefirmado) ? "1" : "0"
            ]); 
            
            $foto = AcuerdoTecnico::setImagen($request);
            if(isset($foto)){
                if($foto=="del"){
                    $foto = null;
                }
                $acuerdotecnico->at_firmado = $foto;
                $acuerdotecnico->save();
            }

            // Recargar el modelo actualizado con relaciones
            $acuerdotecnicoActualizado = AcuerdoTecnico::with('acuerdotecnicocvalatdets')
                ->findOrFail($request->acuerdotecnico_id);

            // Guardar log comparando original con actualizado
            $aux_resp = guardarLogCambioModelo(
                $acuerdotecnicoActualizado,
                ['acuerdotecnicocvalatdets'],
                $acuerdotecnicoOriginal // <- se lo pasamos como estado original
            );

            if ($aux_resp != null and $acuerdotecnicoActualizado->at_firmado == null and $acuerdotecnico->at_firmado != null) {
                AtFirmEli::create([
                            'acuerdotecnico_id' => $request->acuerdotecnico_id,
                            'at_firmado' => $acuerdotecnico->at_firmado,
                            'usuario_id' => auth()->id(),
                        ]);
            }


            //guardarLogCambio('acuerdotecnico', $atorig, $atnew, $acuerdotecnico->id);

            DB::commit();
            /* return redirect('ateditar/3880/editar')->with([
                'mensaje'=> 'Acuerdo Tecnico actualizado con éxito',
                'tipo_alert' => 'alert-success'
            ]);  */           
            return redirect('atfirmadosubir')->with([
                'mensaje'=> 'Acuerdo Tecnico actualizado con éxito',
                'tipo_alert' => 'alert-success'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect('atfirmadosubir')->with([
                'mensaje'=> 'Error: ' . $e->getMessage(),
                'tipo_alert' => 'alert-error'
            ]);
        }

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
}