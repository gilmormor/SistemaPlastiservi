<?php

namespace App\Http\Controllers;

use App\Models\AcuerdoTecnico;
use App\Models\AcuerdoTecnicoCValAtDet;
use App\Models\AcuerdoTecnicoEdit;
use App\Models\AreaProduccion;
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

class ATEditarController extends Controller
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
        $tablas['materiPrima'] = MateriaPrima::orderBy('id')->get();
        $tablas['color'] = Color::orderBy('id')->get();
        $tablas['certificado'] = Certificado::orderBy('id')->get();
        $tablas['tipoSello'] = TipoSello::orderBy('id')->get();
        $tablas['empresa'] = Empresa::findOrFail(1);
        $tablas['unidadmedida'] = UnidadMedida::orderBy('id')->where('mostrarfact',1)->get();
        $tablas['unidadmedidaAT'] = UnidadMedida::orderBy('id')->get();

        $aux_sta=1;

        return view('ateditar.index', compact('tablashtml','selecmultprod','tablas','aux_sta'));
    }

    public function ateditarpage(Request $request){
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
        can('editar-cotizacion');
        //dd($id);
        //dd(session('aux_paginaredirect'));
        $data = Producto::findOrFail($id);
        if(!isset($data->acuerdotecnico)){
            return redirect('ateditar')->with([
                'mensaje'=> "Producto $id no tiene Acuerdo Tecnico",
                'tipo_alert' => 'alert-error'
            ]);
        }
        $tablashtml['areaproduccions'] =  AreaProduccion::areaproduccionxusuario();
        $tablashtml['categoriaprod'] = CategoriaProd::categoriasxUsuario();
        $tablas['materiPrima'] = MateriaPrima::orderBy('id')->get();
        $tablas['color'] = Color::orderBy('id')->get();
        $tablas['certificado'] = Certificado::orderBy('id')->get();
        $tablas['tipoSello'] = TipoSello::orderBy('id')->get();
        $tablas['empresa'] = Empresa::findOrFail(1);
        $tablas['unidadmedida'] = UnidadMedida::orderBy('id')->where('mostrarfact',1)->get();
        $tablas['unidadmedidaAT'] = UnidadMedida::orderBy('id')->get();
        $tablas['atributoProd'] = Producto::atributosProducto($id);

        //CREACION DE CAMPOS DINAMICOS GUARDADOS EN acuerdotecnicocvalatdets, PARA QUE ME LOS GUARDE EN $data->acuerdotecnico->$campo
        //Y PODER TENER ACCESO A ELLOS DESDE EL FRONTEND
        $aux_staAT = true;
        foreach ($data->acuerdotecnico->acuerdotecnicocvalatdets as $cvalatdet) {
            $campo = 'at_cvalatdet' . $cvalatdet->cvalatdet_id;
            $valor = $cvalatdet->valor;

            // Agregas el atributo dinámicamente al objeto
            $data->acuerdotecnico->$campo = $valor;
        }
        session(['editaracutec' => '1']);
        $aux_sta=1;

        return view('ateditar.editar', compact('data','tablas','aux_sta'));
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

        $acuerdotecnico = AcuerdoTecnico::findOrFail($request->acuerdotecnico_id);
        $atorig = $acuerdotecnico->toArray();
       
        /* return redirect('ateditar/3880/editar')->with([
                'mensaje'=> 'Acuerdo Tecnico actualizado con éxito',
                'tipo_alert' => 'alert-success'
            ]); */

        DB::beginTransaction();
        try {
            $id = $request->acuerdotecnico_id;
            $acuerdoTecnico = $request->input("acuerdotecnico{$id}");
            $objetAT = json_decode($acuerdoTecnico);
            //dd($objetAT);
            foreach($objetAT as $clave => &$valor) {
                if($valor == ""){
                    $valor = null;
                }
            }
            /*
            if(isset($objetAT->id)){
                $acuerdotecnico_id = $objetAT->id;
            }else{
                $acuerdotecnico_id = "";
            }*/
            unset($objetAT->id);
            unset($objetAT->at_id);
            unset($objetAT->deleted_at);
            unset($objetAT->created_at);
            unset($objetAT->updated_at);
            unset($objetAT->usuariodel_id);
            unset($objetAT->tiposello);
            unset($objetAT->materiaprima);
            unset($objetAT->largounidadmedida);
            unset($objetAT->claseprod);
            unset($objetAT->cotizaciondetalle);
            unset($objetAT->anchounidadmedida);
            unset($objetAT->color);
            unset($objetAT->acuerdotecnico_idEditAct);
            //dd($objetAT);
            $arrayAT = (array) $objetAT;
            $aux_at_pigmentacion = $arrayAT["at_pigmentacion"];
            if($aux_at_pigmentacion == "" or is_null($aux_at_pigmentacion)){
                $arrayAT["at_pigmentacion"] = 0;
            }
            $aux_at_largo = $arrayAT["at_largo"];
            if($aux_at_largo == "" or is_null($aux_at_largo)){
                $arrayAT["at_largo"] = 0;
            }
            $aux_at_fuelle = $arrayAT["at_fuelle"];
            if($aux_at_fuelle == "" or is_null($aux_at_fuelle)){
                $arrayAT["at_fuelle"] = 0;
            }
            $aux_at_formatofilm = $arrayAT["at_formatofilm"];
            if($aux_at_formatofilm == "" or is_null($aux_at_formatofilm)){
                $arrayAT["at_formatofilm"] = 0;
            }
            $array_valats = [];
            foreach ($arrayAT as $key => $value) {
                if (strpos($key, 'at_cvalatdet') === 0) {
                    // Extraer el número del ID
                    $cvalatdet_id = intval(substr($key, strlen('at_cvalatdet')));

                    // Agregar al array de codigodet
                    $array_valats[] = [
                        'cvalatdet_id' => $cvalatdet_id,
                        'valor' => $value
                    ];

                    // Eliminar del array original
                    unset($arrayAT[$key]);
                }
            }
            if(isset($arrayAT["acuerdotecnicocvalatdets"])){
                unset($arrayAT["acuerdotecnicocvalatdets"]);
            }
            foreach ($array_valats as $array_valat) {
                AcuerdoTecnicoCValAtDet::updateOrCreate(
                    [
                        'acuerdotecnico_id' => $request->acuerdotecnico_id,
                        'cvalatdet_id' => $array_valat["cvalatdet_id"]
                    ],
                    [
                        'valor' => $array_valat["valor"]
                    ]
                );
            }

            $aux_at = AcuerdoTecnico::where("id","=",$request->acuerdotecnico_id)
            ->update($arrayAT);

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
            $request->merge([
                'id' => $request->acuerdotecnico_id,
                'namefilenew' => 'at' . $request->acuerdotecnico_id,
                'nombrecampofile' => "at_fileimpresofoto",
                'file_del' => $request->at_fileimpresofoto_deleted,
                'namefileold' => $acuerdotecnico->at_impresofoto,
                'url' => '/imagenes/at/',
                'file_loader' => isset($request->at_fileimpresofoto) ? "1" : "0"
            ]); 
            $foto = AcuerdoTecnico::setImagen($request);
            if(isset($foto)){
                if($foto=="del"){
                    $foto = null;
                }
                $acuerdotecnico->at_impresofoto = $foto;
                $acuerdotecnico->save();
            }

            $acuerdotecnico = AcuerdoTecnico::findOrFail($request->acuerdotecnico_id);
            $atnew = $acuerdotecnico->toArray();
            guardarLogCambio('acuerdotecnico', $atorig, $atnew, $acuerdotecnico->id);
            /* $acuerdotecnico = AcuerdoTecnico::findOrFail($request->acuerdotecnico_id);
            $atorig = $acuerdotecnico->toArray();

            $acuerdotecnico = AcuerdoTecnico::findOrFail($request->acuerdotecnico_id);
            $atnew = $acuerdotecnico->toArray(); */

            /* $acuerdoOrigArray["acuerdotecnico_id"] = $acuerdoOrigArray["id"];
            $acuerdoOrigArray["at_status"] = 1;
            $acuerdoOrigArray["usuarioedit_id"] = auth()->id();

            $arrayAT["acuerdotecnico_id"] = $acuerdoOrigArray["id"];
            $arrayAT["at_status"] = 2;
            $arrayAT["usuarioedit_id"] = auth()->id();

            unset($acuerdoOrigArray["id"]);
            unset($acuerdoOrigArray["at_id"]);
            unset($acuerdoOrigArray["deleted_at"]);
            unset($acuerdoOrigArray["created_at"]);
            unset($acuerdoOrigArray["updated_at"]);
            unset($acuerdoOrigArray["usuariodel_id"]);
            unset($acuerdoOrigArray["tiposello"]);
            unset($acuerdoOrigArray["materiaprima"]);
            unset($acuerdoOrigArray["largounidadmedida"]);
            unset($acuerdoOrigArray["claseprod"]);
            unset($acuerdoOrigArray["cotizaciondetalle"]);
            unset($acuerdoOrigArray["anchounidadmedida"]);
            unset($acuerdoOrigArray["color"]);
            
            $acuerdotecnicoedit = AcuerdoTecnicoEdit::create($acuerdoOrigArray);
            $acuerdotecnicoedit = AcuerdoTecnicoEdit::create($arrayAT); */

            DB::commit();
            /* return redirect('ateditar/3880/editar')->with([
                'mensaje'=> 'Acuerdo Tecnico actualizado con éxito',
                'tipo_alert' => 'alert-success'
            ]);  */           
            return redirect('ateditar')->with([
                'mensaje'=> 'Acuerdo Tecnico actualizado con éxito',
                'tipo_alert' => 'alert-success'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect('ateditar')->with([
                'mensaje'=> 'Error: ' . $e->getMessage(),
                'tipo_alert' => 'alert-error'
            ]);
            /* return redirect('ateditar')->with(
                'error', 'Error: ' . $e->getMessage()
                )->withInput(); */
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
