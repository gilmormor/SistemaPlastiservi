<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidarNoConformidad;
use App\Models\Certificado;
use App\Models\FormaDeteccionNC;
use App\Models\JefaturaSucursalArea;
use App\Models\MotivoNc;
use App\Models\NoConformidad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use SplFileInfo;

class NoConformidadController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-no-conformidad');
        $datas = NoConformidad::orderBy('id')
                ->where('usuario_id','=',auth()->id())
                ->get();
        return view('noconformidad.index', compact('datas'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function crear()
    {
        can('crear-no-conformidad');
        $motivoncs = MotivoNc::orderBy('id')->pluck('descripcion', 'id')->toArray();
        $formadeteccionncs = FormaDeteccionNC::orderBy('id')->pluck('descripcion', 'id')->toArray();
        $jefaturasucursalareas = JefaturaSucursalArea::orderBy('id')->get();
        $jefaturasucursalareasR = JefaturaSucursalArea::orderBy('id')
                                ->whereNotNull('updated_at')
                                ->get();
        $certificados = Certificado::orderBy('id')->get();
        return view('noconformidad.crear',compact('motivoncs','formadeteccionncs','jefaturasucursalareas','certificados','jefaturasucursalareasR'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function guardar(ValidarNoConformidad $request)
    {
        can('guardar-no-conformidad');
        //dd($request);
        $request->request->add(['usuario_id' => auth()->id()]);
        $hoy = date("Y-m-d H:i:s");
        $request->request->add(['fechahora' => $hoy]);
        $noconformidad = NoConformidad::create($request->all());
        $noconformidad->jefaturasucursalareas()->sync($request->jefatura_sucursal_area_id);
        $noconformidad->jefaturasucursalarearesponsables()->sync($request->jefatura_sucursal_areaR_id);
        $noconformidad->certificados()->sync($request->certificado_id);
        return redirect('noconformidad')->with('mensaje','Creado con exito');
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
    public function editar($id)
    {
        can('editar-no-conformidad');
        $data = NoConformidad::findOrFail($id);
        if($data->usuario_id == auth()->id()){
            $motivoncs = MotivoNc::orderBy('id')->pluck('descripcion', 'id')->toArray();
            $formadeteccionncs = FormaDeteccionNC::orderBy('id')->pluck('descripcion', 'id')->toArray();
            $jefaturasucursalareas = JefaturaSucursalArea::orderBy('id')->get();
            $jefaturasucursalareasR = JefaturaSucursalArea::orderBy('id')
                                    ->whereNotNull('persona_id')
                                    ->get();
            $certificados = Certificado::orderBy('id')->get();
            //dd($jefaturasucursalareasR);
            return view('noconformidad.editar',compact('data','motivoncs','formadeteccionncs','jefaturasucursalareas','jefaturasucursalareasR','certificados'));
        }else{
            return redirect('noconformidad')->with('mensaje','Registro creado por otro Usuario.'); 
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function actualizar(ValidarNoConformidad $request, $id)
    {
        $noconformidad = NoConformidad::findOrFail($id);
        $noconformidad->update($request->all());
        $noconformidad->jefaturasucursalareas()->sync($request->jefatura_sucursal_area_id);
        $noconformidad->jefaturasucursalarearesponsables()->sync($request->jefatura_sucursal_areaR_id);
        $noconformidad->certificados()->sync($request->certificado_id);
        return redirect('noconformidad')->with('mensaje','Actualizado con exito');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function eliminar(Request $request, $id)
    {
        if ($request->ajax()) {
            if (NoConformidad::destroy($id)) {
                return response()->json(['mensaje' => 'ok']);
            } else {
                return response()->json(['mensaje' => 'ng']);
            }
        } else {
            abort(404);
        }
    }

    public function prevImagen($id,$sta_val)
    {
        $directory = "storage/imagenes/noconformidad/"; //Storage::url("imagenes/noconformidad/");
        //dd($directory);     
        $images = glob($directory . $id."-*.*");
        //dd($images);
        $initialPreview = array();
        $initialPreviewConfig = array();
        $infoImagenesSubidas = array();
        $i = 0;
        foreach($images as $image){
            $initialPreview = Storage::url("imagenes/noconformidad/$image");
            $tamano = filesize($image);
            $infoImagenes=explode("/",$image);
            //dd($infoImagenes);
            $infoImagenesSubidas[$i]=array("type"=>"pdf","caption"=>"$infoImagenes[3]","size"=>"$tamano","height"=>"120px","width"=>"120px","url"=> route('delImagen_noconformidad', ['id' => $id]) ,"key"=>$infoImagenes[3]);
            $ImagenesSubidas[$i]=array("/$directory$infoImagenes[3]");
            $i++;
        }
        if($i > 0){
            $arr = [
                "i"=>$i,
                "file_id"=>0,
                "overwriteInitial"=>true,
                "initialPreviewConfig"=>$infoImagenesSubidas,
                "initialPreview"=>$ImagenesSubidas,
                "mensaje"=>"img"
                ];
        }else{
            $arr = [
                "i"=>$i,
                "mensaje"=>"img"
                ];
        }
        //dd($arr);
        return response()->json($arr);
    }



    public function actualizarImagen(Request $request, $id,$sta_val)
    {
        //dd($request);
        if($sta_val=="0") //Si es 0 no puede guardar archivo
        {
            //dd($request);
            $carpetaAdjunta="storage/imagenes/noconformidad/";
            // Contar envían por el plugin
            $Imagenes =count(isset($_FILES['imagenes']['name'])?$_FILES['imagenes']['name']:0);
            //$infoImagenesSubidas = array();
            for($i = 0; $i < $Imagenes; $i++) {
                // El nombre y nombre temporal del archivo que vamos para adjuntar
                $nombreArchivo=isset($_FILES['imagenes']['name'][$i])?$id.'-'.$_FILES['imagenes']['name'][$i]:null;
                $nombreTemporal=isset($_FILES['imagenes']['tmp_name'][$i])?$_FILES['imagenes']['tmp_name'][$i]:null;
                
                $rutaArchivo=$carpetaAdjunta.$nombreArchivo;
                
                move_uploaded_file($nombreTemporal,$rutaArchivo);
                /*
                $tamano = filesize($image);
                $infoImagenesSubidas[$cont]=array("type"=>"pdf","caption"=>"$nombreArchivo","size"=>"$tamano","height"=>"120px","width"=>"120px","url"=> route('delImagen_noconformidad', ['id' => $id]) ,"key"=>$nombreArchivo);
                //$ImagenesSubidas[$i]="<img  height='120px'  src='/$rutaArchivo' class='file-preview-image'>";
                $ImagenesSubidas[$cont]=array("/$rutaArchivo");
                $cont++;
                */
            }
        }
        $directory = "storage/imagenes/noconformidad/";
        $images = glob($directory . $id."-*.*");
        $infoImagenesSubidas = array();
        $i = 0;
        foreach($images as $image){
            $infoImagenes=explode("/",$image);
            $info = new SplFileInfo($infoImagenes[3]);
            $extension = $info->getExtension();
            $tamano = filesize($image);
            $infoImagenesSubidas[$i]=array("type"=>"pdf","caption"=>"$infoImagenes[3]","size"=>"$tamano","height"=>"120px","width"=>"120px","url"=> route('delImagen_noconformidad', ['id' => $id]) ,"key"=>$infoImagenes[3]);
            $ImagenesSubidas[$i]=array("/$directory$infoImagenes[3]");
            $i++;
        }
        $arr = array("file_id"=>0,"overwriteInitial"=>true,"initialPreviewConfig"=>$infoImagenesSubidas,
                     "initialPreview"=>$ImagenesSubidas);
        echo json_encode($arr);
    }

    public function delImagen($id)
    {
        //dd($id);
        $carpetaAdjunta="storage/imagenes/noconformidad/";

        //if($_SERVER['REQUEST_METHOD']=="DELETE"){
            //dd($id);

			parse_str(file_get_contents("php://input"),$datosDELETE);

			$key= $datosDELETE['key'];

			unlink($carpetaAdjunta.$key);
			
			echo 0;
        //}
    }

    public function actualizarImagens(Request $request, $id)
    {
        //dd($request);
        // COMPROBACIÓN INICIAL ANTES DE CONTINUAR CON EL PROCESO DE UPLOAD
        // **********************************************************************

        // Si no se ha llegado ha definir el array global $_FILES, cancelaremos el resto del proceso
        if (empty($_FILES['file-ess'])) {
            // Devolvemos un array asociativo con la clave error en formato JSON como respuesta	
            echo json_encode(['error'=>'No hay ficheros para realizar upload.']); 
            // Cancelamos el resto del script
            return; 
        }

        // DEFINICIÓN DE LAS VARIABLES DE TRABAJO (CONSTANTES, ARRAYS Y VARIABLES)
        // ************************************************************************

        // Definimos la constante con el directorio de destino de las descargas
        define('DIR_DESCARGAS',public_path() . "/storage/imagenes/noconformidad/");
        // Obtenemos el array de ficheros enviados
        $ficheros = $_FILES['file-ess'];
        // Establecemos el indicador de proceso correcto (simplemente no indicando nada)
        $estado_proceso = NULL;
        // Paths para almacenar
        $paths= array();
        // Obtenemos los nombres de los ficheros
        $nombres_ficheros = $ficheros['name'];
        $noconformidad = NoConformidad::findOrFail($id);
        $noconformidad->adjaccorrec = implode ( ',' , $nombres_ficheros );
        $noconformidad->save();

        //$respuestas = ['nombres_ficheros'=>$nombres_ficheros];
        //$aux_tipofalla = implode ( ',' , $nombres_ficheros );
        //dd($nombres_ficheros);

        // LÍNEAS ENCARGADAS DE REALIZAR EL PROCESO DE UPLOAD POR CADA FICHERO RECIBIDO
        // ****************************************************************************

        // Si no existe la carpeta de destino la creamos
        if(!file_exists(DIR_DESCARGAS)) @mkdir(DIR_DESCARGAS);
        // Sólo en el caso de que exista esta carpeta realizaremos el proceso
        if(file_exists(DIR_DESCARGAS)) {
            // Recorremos el array de nombres para realizar proceso de upload
            for($i=0; $i < count($nombres_ficheros); $i++){
                // Extraemos el nombre y la extensión del nombre completo del fichero
                $nombre_extension = explode('.', basename($nombres_ficheros[$i]));
                // Obtenemos la extensión
                $extension=array_pop($nombre_extension);
                // Obtenemos el nombre
                $nombre=array_pop($nombre_extension);
                // Creamos la ruta de destino
                $archivo_destino = DIR_DESCARGAS . DIRECTORY_SEPARATOR .$id.'-'. utf8_decode($nombre) . '.' . $extension;
                // Mover el archivo de la carpeta temporal a la nueva ubicación
                if(move_uploaded_file($ficheros['tmp_name'][$i], $archivo_destino)) {
                    // Activamos el indicador de proceso correcto
                    $estado_proceso = true;
                    // Almacenamos el nombre del archivo de destino
                    $paths[] = $archivo_destino;
                } else {
                    // Activamos el indicador de proceso erroneo		
                    $estado_proceso = false;
                    // Rompemos el bucle para que no continue procesando ficheros
                    break;
                }
            }
        }
        // PREPARAR LAS RESPUESTAS SOBRE EL ESTADO DEL PROCESO REALIZADO
        // **********************************************************************

        // Definimos un array donde almacenar las respuestas del estado del proceso
        $respuestas = array();
        // Comprobamos si el estado del proceso a finalizado de forma correcta
        if ($estado_proceso === true) {
            /* Podríamos almacenar información adicional en una base de datos
            con el resto de los datos enviados por el método POST */

            // Como mínimo tendremos que devolver una respuesta correcta por medio de un array vacio.
            $respuestas = array();
            $respuestas = ['dirupload' => basename(DIR_DESCARGAS), 'total'=>count($paths)]; 
            /* Podemos devolver cualquier otra información adicional que necesitemos por medio de un array asociativo
            Por ejemplo, prodríamos devolver la lista de ficheros subidos de esta manera: 
                $respuestas = ['ficheros' => $paths]; 
            Posteriormente desde el evento fileuploaded del plugin iríamos mostrando el array de ficheros utilizando la propiedad response
            del parámetro data: 
                respuesta = data.response; 
                respuesta.ficheros.forEach(function(nombre) {alert(nombre); });
            */
        } elseif ($estado_proceso === false) {
            $respuestas = ['error'=>'Error al subir los archivos. Póngase en contacto con el administrador del sistema'];
            // Eliminamos todos los archivos subidos
            foreach ($paths as $fichero) {
                unlink($fichero);
            }
        // Si no se han llegado a procesar ficheros $estado_proceso seguirá siendo NULL
        } else {
            $respuestas = ['error'=>'No se ha procesado ficheros.'];
        }

        // RESPUESTA DEVUELTA POR EL SCRIPT EN FORMATO JSON
        // **********************************************************************

        // Devolvemos el array asociativo en formato JSON como respuesta
        echo json_encode($respuestas);
    }


    public function ver($id,$sta_val)
    {
        can('editar-no-conformidad');
        $data = NoConformidad::findOrFail($id);
        $funcvalidarai = $sta_val;
        $directory = "storage/imagenes/noconformidad/";      
        $images = glob($directory . "*.*");
        return view('noconformidad.editarver',compact('data','funcvalidarai','images'));
    }

}
