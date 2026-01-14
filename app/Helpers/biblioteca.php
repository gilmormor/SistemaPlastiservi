<?php

use App\Models\Admin\Menu;
use App\Models\Admin\Permiso;
use App\Models\Cliente;
use App\Models\ClienteDesBloqueado;
use App\Models\ClienteDesbloqueadoModulo;
use App\Models\ClienteDesbloqueadoModuloDel;
use App\Models\Dte;
use App\Models\Empresa;
use App\Models\LogCambio;
use App\Models\Modulo;
use Illuminate\Database\Eloquent\Builder;

if (!function_exists('getMenuActivo')) {
    function getMenuActivo($ruta)
    {
        if (request()->is($ruta) || request()->is($ruta . '/*')) {
            return 'active menu-open';
        } else {
            return '';
        }
    }
}
if (!function_exists('canUser')) {
    function can($permiso, $redirect = true)
    {
        if (session()->get('rol_nombre') == 'administrador') {
            return true;
        } else {
            $rolId = session()->get('rol_id');
            /*
            $permisos = cache()->tags('Permiso')->rememberForever("Permiso.rolid.$rolId", function () {
                return Permiso::whereHas('roles', function ($query) {
                    $query->where('rol_id', session()->get('rol_id'));
                })->get()->pluck('slug')->toArray();
            });
            */
            $permisos = Permiso::whereHas('roles', function ($query) {
                $query->where('rol_id', session()->get('rol_id'));
                })->get()->pluck('slug')->toArray();
            //dd($permisos);
            if (!in_array($permiso, $permisos)) {
                if ($redirect) {
                    if (!request()->ajax())
                        return redirect()->route('inicio')->with('mensaje', 'No tienes permisos para entrar en este módulo' . nomPermiso($permiso))->send();
                    return abort(403, 'No tiene permiso para realizar esta acción' . nomPermiso($permiso));
                    /* return response()->json([
                        'error' => 1,
                        'mensaje' => 'No tiene permiso para realizar esta acción' . nomPermiso($permiso),
                        'tipo_alert' => "error"
                    ]); */
                } else {
                    return false;
                }
            }
            return true;
        }
    }
}   

if (!function_exists('urlActual')) {
    function urlActual(){
        $ruta = url()->current();
        //$ruta = url()->current();
        $pos = 0;
        $cont = 0;
        for( $i=0 ; $i < strlen($ruta) ; $i++){
            if((substr($ruta,$i,1) == '/') and $cont < 3){
                $cont++;
                $pos = $i;
            }
        }
        return substr($ruta,$pos+1,(strlen($ruta)-1));

    }
}

if (!function_exists('urlPrevio')) {
    function urlPrevio(){
        $ruta = url()->previous();
        //$ruta = url()->current();
        $pos = 0;
        $cont = 0;
        for( $i=0 ; $i < strlen($ruta) ; $i++){
            if((substr($ruta,$i,1) == '/') and $cont < 3){
                $cont++;
                $pos = $i;
            }
        }
        return substr($ruta,$pos+1,(strlen($ruta)-1));

    }
}

function autoVer($url)
{
  $path = pathinfo($url);
  //dd($url);
  //$ver = '?v=' . filemtime($_SERVER['DOCUMENT_ROOT'].$url);
  $ver = '?v=' . filemtime($url);
  //return $path['dirname'].'/'.$path['basename'].$ver;
  //dd(asset($url).$ver);
  return (asset($url).$ver);
}

function getIniciales($nombre){
    $name = '';
    $explode = explode(' ',$nombre);
    foreach($explode as $x){
        $name .=  $x[0];
    }
    return $name;    
}

//FUNCION REEMPLAZA CARACTERES ESPECIALES
if (!function_exists('sanear_string')) {
    function sanear_string($string){
        $string = trim($string);
 
        $string = str_replace(
            array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'),
            array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'),
            $string
        );
     
        $string = str_replace(
            array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'),
            array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'),
            $string
        );
     
        $string = str_replace(
            array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'),
            array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'),
            $string
        );
     
        $string = str_replace(
            array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'),
            array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'),
            $string
        );
     
        $string = str_replace(
            array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'),
            array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'),
            $string
        );
     
        $string = str_replace(
            array('ñ', 'Ñ', 'ç', 'Ç'),
            array('n', 'N', 'c', 'C',),
            $string
        );
     
        //Esta parte se encarga de eliminar cualquier caracter extraño
        $string = str_replace(
            array(
                "¨", "º", "~",
                "#", "@", "|", "!",
                "·", "$", "%", "&",
                "(", ")", "?", "¡",
                "¿", "[", "^", "<code>", "]",
                "+", "}", "{", "¨", "´",
                ">", "<"
            ),
            ' ',
            $string
        );

        /*
        $string = str_replace(
            array(
                "\\", "¨", "º", "-", "~",
                "#", "@", "|", "!", "\"",
                "·", "$", "%", "&", "/",
                "(", ")", "?", "'", "¡",
                "¿", "[", "^", "<code>", "]",
                "+", "}", "{", "¨", "´",
                ">", "<", ";", ",", ":",
                "."
            ),
            '',
            $string
        );
        */
     
     
        return $string;
    }
}

if (!function_exists('cadVacia')) { //VALIDAR CADENA VACIA O NULL
    function cadVacia($cad)
    {
        if($cad == "" OR is_null($cad)){
            return true;
        }
        return false;
    }
}

if (!function_exists('urlRaiz')) {
    function urlRaiz(){
        $ruta = url()->current();
        $currentUrl = url()->current();

        $parsedUrl = parse_url($currentUrl);
        // Obtener el dominio
        $domain = $parsedUrl['host'];
        // Construir la URL deseada
        $desiredUrl = 'https://' . $domain;
        return $desiredUrl;
    }
}

if (!function_exists('usuariosConAccesoMenuURL')) {
    function usuariosConAccesoMenuURL($tabla,$url){
        $menu = Menu::where("url","=",$url)->get(); //BUSCO EN LA TABLA MENU EL REGISTRO QUE CONTIENE EL URL notaventaaprobar
        $menu = Menu::findOrFail($menu[0]->id); //LUEGO BUSCO EL ID EN MENU PARA TENER EL OBJETO COMPLETO CON SUS TABLAS HIJAS
        $arrayUsuarios = []; //ARRAY PARA ALMACENAR LOS USUARIOS
        //LUEGO RECORRO Y BUSCO TODOS LOS USUARIO QUE TIENEN ACCESO A ESTE URL PARA ENVIARLES EL CORREO
        //PERO SOLO LOS USUARIOS TIENEN ACCESO A LA MISMA SUCURSAL DE LA NOTA DE VENTA, PUEDEN SER VARIOS USUARIOS UE TENGAN ACCESO A APROBAR NOTAS DE VENTA
        foreach ($menu->menuroles as $menurol) {
            if($menurol->rol_id != 1){
                foreach ($menurol->usuarioroles as $usuariorol){
                    foreach ($usuariorol->usuario->sucursales as $sucursal) {
                        if($tabla->sucursal_id == $sucursal->id){
                            $arrayUsuarios[] = [
                                "usuario_id" =>$usuariorol->usuario->id,
                                "nombre" => $usuariorol->usuario->nombre,
                                "email" => $usuariorol->usuario->email
                            ];
                        }
                    }
                }
            }
        }
        return $arrayUsuarios;
    }
}
if (!function_exists('formatearRUT')) {
    function formatearRUT($rut){
        $rut = preg_replace('/[^0-9Kk]/', '', $rut); // Eliminar caracteres no numéricos ni K/k

        if (strlen($rut) < 2) {
            return $rut; // No se puede formatear si el RUT es demasiado corto
        }
    
        $digitoVerificador = strtoupper(substr($rut, -1)); // Último dígito (puede ser K/k)
        $restoRUT = substr($rut, 0, -1); // Resto del RUT
    
        $rutFormateado = $restoRUT . '-' . $digitoVerificador;
    
        return $rutFormateado;
    }
}
//Sucursales fisicas por Usuario. Sucursales a las que pertenese el Usuario a traves de tabla persona
if (!function_exists('sucFisXUsu')) {
    function sucFisXUsu($persona){
        $arraySuc = [];
        foreach ($persona->jefaturasucursalareas as $jefaturasucursalarea) {
            $arraySuc[] = $jefaturasucursalarea->sucursal_area->sucursal_id;
        }            
        return $arraySuc;
    }
}

if (!function_exists('nomPermiso')) {
    function nomPermiso($permiso){
        $permisoT = Permiso::where("slug",$permiso)->get();
        $nombre_permiso = "";
        if(count($permisoT) > 0){
            $nombre_permiso = ": " . $permisoT[0]->nombre;
        }
        return $nombre_permiso;
    }
}

if (!function_exists('dtetipotraslado')) {
    function dtetipotraslado($indtraslado){
        $arraytipotraslado = "";
        if($indtraslado == 1){
            $arraytipotraslado = [
                "desc" => "Venta",
                "letra" => "V"
            ];
        }
        if($indtraslado == 2){
            $arraytipotraslado = [
                "desc" => "Ventas por efectuar",
                "letra" => "VPE"
            ];
        }
        if($indtraslado == 3){
            $arraytipotraslado = [
                "desc" => "Consignaciones",
                "letra" => "C"
            ];
        }
        if($indtraslado == 4){
            $arraytipotraslado = [
                "desc" => "Entrega gratuita",
                "letra" => "C"
            ];
        }
        if($indtraslado == 5){
            $arraytipotraslado = [
                "desc" => "Traslados internos",
                "letra" => "TI"
            ];
        }
        if($indtraslado == 6){
            $arraytipotraslado = [
                "desc" => "Traslado",
                "letra" => "T"
            ];
        }
        if($indtraslado == 7){
            $arraytipotraslado = [
                "desc" => "Guía de devolución",
                "letra" => "GDev"
            ];
        }
        if($indtraslado == 8){
            $arraytipotraslado = [
                "desc" => "Traslado para exportación",
                "letra" => "TExp"
            ];
        }
        if($indtraslado == 9){
            $arraytipotraslado = [
                "desc" => "Venta para exportación",
                "letra" => "VExp"
            ];
        }
        return $arraytipotraslado;
    }
}

if (!function_exists('clienteBloqueado')) {
    function clienteBloqueado($cliente_id,$aux_consultadeuda = 0,$request = null){
        //dd($request);
        $cliente = Cliente::findOrFail($cliente_id);
        //dd($cliente);
        $staBloqueo = [];
        $staBloqueo ["bloqueo"] = null;
        $empresa = Empresa::findOrFail(1);        
        //SI stabloxdeusiscob = 0 BUSCO SI ESTA BLOQUEADO DE FORMA MANUAL, SI NO ESTA BLOQUEADO SIMPLEMENTe SIGUE SIN BLOQUEO
        if($empresa->stabloxdeusiscob == 0 and $aux_consultadeuda == 0){
            if(isset($cliente->clientebloqueado)){
                $staBloqueo ["bloqueo"]= $cliente->clientebloqueado->descripcion;
            }
            return $staBloqueo;
        }
        //VALIDAR DESBLOQUEO TOTAL
        if(isset($cliente->clientedesbloqueadopro) and $aux_consultadeuda == 0){
            return $staBloqueo;
        }

        //dd(isset($request->modulo_id) and $aux_consultadeuda == 0);
        //SI LA SOLICITUD VIENE DE NOTA DE VENTA, BUSCO SI EL CLIENTE ESTA DESBLOQUEADO PARA NOTA DE VENTA
        if(isset($request->modulo_id) and $aux_consultadeuda == 0){
            $modulo = Modulo::findOrFail($request->modulo_id);
            if($modulo->stamodapl == 0){
                $clientedesbloqueados = ClienteDesBloqueado::where("cliente_id",$cliente_id)
                                        ->whereNull("notaventa_id")
                                        ->whereNull("cotizacion_id")
                                        ->get();
                //dd($clientedesbloqueados);
            }
            if($modulo->stamodapl == 1){
                $clientedesbloqueados = ClienteDesBloqueado::where("cliente_id",$cliente_id)
                                        ->where("notaventa_id",$request->notaventa_id)
                                        ->whereNull("cotizacion_id")
                                        ->get();
            }
            if($modulo->stamodapl == 2){
                $clientedesbloqueados = ClienteDesBloqueado::where("cliente_id",$cliente_id)
                                        ->where("cotizacion_id",$request->cotizacion_id)
                                        ->whereNull("notaventa_id")
                                        ->get();
            }
            //dd($clientedesbloqueados);
            //dd($clientedesbloqueados[0]->modulos);
            //dd($aux_consultadeuda);
            //dd($clientedesbloqueados[0]->clientedesbloqueadomodulos);
            if(count($clientedesbloqueados) > 0){
                foreach ($clientedesbloqueados as $clientedesbloqueado) {
                    $aux_desbloqueo = false;
                    foreach ($clientedesbloqueado->clientedesbloqueadomodulos as $clientedesbloqueadomodulo) {
                        //dd($clientedesbloqueadomodulo);
                        if($clientedesbloqueadomodulo->modulo_id == $request->modulo_id){
                            //dd("Desbloqueado en NV");
                            if(isset($request->deldesbloqueo) and $request->deldesbloqueo == 1){ //SOLO ENTRA AQUI SI ENVIO LA PROPIEDAD EN EL REQUEST
                                if ($clientedesbloqueadomodulo->delete()) {
                                    //Despues de eliminar actualizo el campo usuariodel_id=usuario que elimino el registro
                                    /* $clientedesbloqueadomodulo = ClienteDesbloqueadoModulo::withTrashed()->findOrFail($clientedesbloqueadomodulo->id);
                                    $clientedesbloqueadomodulo->usuariodel_id = auth()->id();
                                    $clientedesbloqueadomodulo->save(); */
                                    $clientedesbloqueadomodulodel = new ClienteDesbloqueadoModuloDel();
                                    $clientedesbloqueadomodulodel->clientedesbloqueado_id = $clientedesbloqueado->id;
                                    $clientedesbloqueadomodulodel->clientedesbloqueadomodulo_id = $clientedesbloqueadomodulo->id;
                                    $clientedesbloqueadomodulodel->modulo_id = $clientedesbloqueadomodulo->modulo_id;
                                    $clientedesbloqueadomodulodel->cliente_id = $clientedesbloqueado->cliente_id;
                                    $clientedesbloqueadomodulodel->notaventa_id = $clientedesbloqueado->notaventa_id;
                                    $clientedesbloqueadomodulodel->usuario_id = auth()->id();
                                    $clientedesbloqueadomodulodel->save();
                                }    
                            }
                            $aux_desbloqueo = true;
                            break;
                        }
                    }
                    $clientedesbloqueado = ClienteDesBloqueado::findOrFail($clientedesbloqueado->id);
                    if(count($clientedesbloqueado->clientedesbloqueadomodulos) == 0){
                        if (ClienteDesBloqueado::destroy($clientedesbloqueado->id)) {
                            //Despues de eliminar actualizo el campo usuariodel_id=usuario que elimino el registro
                            $clientedesbloqueado = ClienteDesBloqueado::withTrashed()->findOrFail($clientedesbloqueado->id);
                            $clientedesbloqueado->usuariodel_id = auth()->id();
                            $clientedesbloqueado->save();
                        }    
                    }
                    if($aux_desbloqueo){
                        return $staBloqueo;
                    }
                }
            }
        }else{ // DE LO CONTRARIO BUSCO SI ESTA DESBLOQUEADO PARA TODOS LOS DEMAS PROCESOS
            /* if($cliente->clientedesbloqueado and $aux_consultadeuda == 0){
                return $staBloqueo;
            } */    
        }
        //SI EL plazopago_id=1 DEL CLIENTE ES CONTADO ENTONCES SE DEBE BLOQUEAR DE FORMA PREDETERMINADA
        if($cliente->plazopago_id == 1 and $aux_consultadeuda == 0){
            $staBloqueo ["bloqueo"]= "Plazo de pago CONTADO.\nSolo se permite hacer Cotizaciones.";
            return $staBloqueo;
        }
        $aux_respuestadatacobranza = [];
        if($aux_consultadeuda == 1){
            $aux_respuestadatacobranza = datacobranza($staBloqueo,$cliente,$request,1);
            //$dataCobranza = Dte::deudaClienteSisCobranza($cliente->rut);
        }
        if(isset($cliente->clientebloqueado)){
            $staBloqueo ["bloqueo"]= $cliente->clientebloqueado->descripcion;
        }else{
            if($empresa->stabloxdeusiscob == 1){
                if($aux_consultadeuda == 0){
                    $aux_respuestadatacobranza = datacobranza($staBloqueo,$cliente,$request,0);
                }
                /* $dataCobranza = Dte::deudaClienteSisCobranza($cliente->rut);
                //dd($dataCobranza);
                $staBloqueo["datacobranza"] = $dataCobranza;
                if($dataCobranza["TDeuda"] > 0 and $dataCobranza["TDeuda"] >= $dataCobranza["limitecredito"]){
                    $staBloqueo ["bloqueo"]= "Supero limite de Crédito: " . number_format($dataCobranza["limitecredito"], 0, ',', '.') . "\nDeuda: " . number_format($dataCobranza["TDeuda"], 0, ',', '.');
                }else{
                    if($dataCobranza["TDeudaFec"] > 0){
                        $staBloqueo ["bloqueo"]=  "Facturas Vencidas:\n" . $dataCobranza["NroFacDeu"] . ".";
                    }
                } */
            }
        }
        $staBloqueo["bloqueoreal"] = $staBloqueo ["bloqueo"];
        if(isset($aux_respuestadatacobranza["error"])){
            return $aux_respuestadatacobranza;
        }
        return $staBloqueo;
    }
}

if (!function_exists('datacobranza')) {
    function datacobranza(&$staBloqueo,$cliente,$request,$importarDataSisCob = 0){
        if($importarDataSisCob == 1){
            $dataCobranza = Dte::deudaClienteSisCobranza($cliente->rut,$request);
        }else{
            $dataCobranza = Dte::deudaClienteSisCobranzaLocal($cliente->rut,$request);
        }
        //$dataCobranza = Dte::deudaClienteSisCobranza($cliente->rut,$request);
        //$dataCobranza = Dte::deudaClienteSisCobranzaLocal($cliente->rut,$request);
        //dd($dataCobranza);
        if(isset($dataCobranza["error"])){
            //dd($dataCobranza);
            return $dataCobranza;
        }
        //dd($dataCobranza);
        $staBloqueo["datacobranza"] = $dataCobranza;
        //dd($dataCobranza);
        $aux_TotalNVPendDesp = 0;
        if(isset($dataCobranza["TotalNVPendDesp"])){
            $aux_TotalNVPendDesp = $aux_TotalNVPendDesp;
        }
        $aux_deuda = $dataCobranza["TDeuda"] + $aux_TotalNVPendDesp;
        $aux_mensajesobrecupo = "";
        if($aux_deuda > 0 and $aux_deuda >= $dataCobranza["limitecredito"]){
            $staBloqueo ["titulo"] = "Limite de crédito superado. ";
            $aux_nvpenddesp = "";
            $aux_mensajesobrecupo = "\n\nGESTIONAR SOBRECUPO.";
            if ($aux_TotalNVPendDesp > 0){
                $aux_nvpenddesp = "\nNV Pendiente Desp: " . number_format($aux_TotalNVPendDesp, 0, ',', '.')
                . "\nNro. NV: " . $dataCobranza["IDsNVPendDesp"];
            }
            $staBloqueo ["bloqueo"] = "Limite de crédito superado. "
            . "\nLimite de crédito: " . number_format($dataCobranza["limitecredito"], 0, ',', '.') 
            . $aux_nvpenddesp
            . "\nDeuda vencida a la fecha: " . number_format($dataCobranza["TDeudaFec"], 0, ',', '.')
            . "\nTotal Deuda: " . number_format($dataCobranza["TDeuda"], 0, ',', '.')
            . $aux_mensajesobrecupo;
        }else{
            if($dataCobranza["TDeudaFec"] > 0){
                $staBloqueo ["titulo"] = "Facturas Vencidas.";
                $staBloqueo ["bloqueo"]=  "Facturas Vencidas:\n" . $dataCobranza["NroFacDeu"] . ".";
            }
        }
    }
}

//FUNCION REEMPLAZA CARACTERES ESPECIALES
if (!function_exists('sanear_stringSisCob')) {
    function sanear_stringSisCob($string){
        $string = trim($string);
 
        $string = str_replace(
            array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'),
            array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'),
            $string
        );
     
        $string = str_replace(
            array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'),
            array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'),
            $string
        );
     
        $string = str_replace(
            array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'),
            array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'),
            $string
        );
     
        $string = str_replace(
            array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'),
            array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'),
            $string
        );
     
        $string = str_replace(
            array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'),
            array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'),
            $string
        );
     
        $string = str_replace(
            array('ñ', 'Ñ', 'ç', 'Ç'),
            array('n', 'N', 'c', 'C',),
            $string
        );
     
        //Esta parte se encarga de eliminar cualquier caracter extraño
        $string = str_replace(
            array(
                "¨", "º", "~",
                "#", "@", "|", "!",
                "·", "$", "%", "&",
                "(", ")", "?", "¡",
                "¿", "[", "^", "<code>", "]",
                "+", "}", "{", "¨", "´",
                ">", "<", "'", "\""
            ),
            ' ',
            $string
        );

        /*
        $string = str_replace(
            array(
                "\\", "¨", "º", "-", "~",
                "#", "@", "|", "!", "\"",
                "·", "$", "%", "&", "/",
                "(", ")", "?", "'", "¡",
                "¿", "[", "^", "<code>", "]",
                "+", "}", "{", "¨", "´",
                ">", "<", ";", ",", ":",
                "."
            ),
            '',
            $string
        );
        */
     
     
        return $string;
    }
}

// Función de comparación para ordenar por 'producto_id'
if (!function_exists('compararProductoId')) {
    function compararProductoId($a, $b) {
        return $a->producto_id <=> $b->producto_id;
    }    
}

// Función devuelve stabloxdeusiscob de la tabla EMPRESA 
if (!function_exists('stabloxdeusiscob')) {
    function stabloxdeusiscob() {
        $empresa = Empresa::findOrFail(1);
        return  $empresa->stabloxdeusiscob;
    }    
}

// Función BUSCA EN FUNCION A CIERTAS CONDICIONES LOS CLIENTES QUE ESTAN BLOQUEADOS Y LOS INSERTA EN UN NUEVO ARRAY
if (!function_exists('filtrarclientesbloqueados')) {
    function filtrarclientesbloqueados($request,&$datas) {
        //dd($request);
        if(isset($request->statusBloqueo) and $request->statusBloqueo){
            $aux_stabloxdeusiscob = stabloxdeusiscob();
            $aux_data = [];
            foreach ($datas as $data) {
                //dd($data);
                if($data->clientedesbloqueadopro_obs == ""){
                    if(($data->modulo_id === null) and ((($data->datacobranza_tdeudafec > 0) OR ($data->datacobranza_tdeuda > $data->limitecredito)  OR $data->clientebloqueado_desc !== null))){
                        if(($data->modulo_id === null)){
                            if($data->clientebloqueado_desc !== null){
                                $aux_data[] = $data;
                                continue;
                            }
                        }
                        if($aux_stabloxdeusiscob == "1"){
                            if($data->datacobranza_tdeuda > $data->limitecredito OR $data->datacobranza_tdeudafec > 0){
                                $aux_data[] = $data;
                            }
                        }
                    }
                }        
            }
            $datas = $aux_data;
        }    
    }    
}

// Función de comparación para ordenar por 'producto_id'
if (!function_exists('ordenarArrayxCampo')) {
    function ordenarArrayxCampo($campo, $orden = 'asc') {
        return function($a, $b) use ($campo, $orden) {

            $resultado = $a->$campo <=> $b->$campo; // Comparar los dos objetos en base al campo

            // Si el orden es descendente, invertimos el resultado
            return $orden === 'desc' ? -$resultado : $resultado;
        };
    }
}

// Funcion que me calcula el peso unitario de un producto en la tabla acuerdo tecnico
if (!function_exists('pesounitattemp')) {
    function pesounitattemp($at) {
        $aux_doble = 2;
        if($at->producto->categoriaprod_id == 13){
            $aux_doble = 1;
        }
        if($at->at_formatofilm > 0 ){
            return $at->at_formatofilm;
        }
        if($at->at_unidadmedida_id == 7 ){
            return 1;
        }
        if($at->at_espesor == 0 or $at->at_peso > 0){
            return $at->at_peso;
        }
        /* if($at->at_unidadmedida_id != 5 ){
            return 0;
        } */
        return round((($at->at_ancho * $at->at_largo * $at->at_espesor * $at->materiaprima->pe) / 1000) * $aux_doble,10 );
    }
}
if (!function_exists('guardarLogCambio')) {
    function guardarLogCambio($modeloNombre, $registroOriginal, $registroNuevo, $idTabla){
        unset($registroOriginal['updated_at'], $registroNuevo['updated_at']);
        // Comparar los arrays para detectar qué campos cambiaron
        $diferencias = [];
        foreach ($registroOriginal as $campo => $valorOriginal) {
            $valorNuevo = $registroNuevo[$campo] ?? null;

            // Comparamos sólo si cambiaron (puedes usar trim o strtolower si quieres comparar sin espacios o mayúsculas)
            if ($valorOriginal != $valorNuevo) {
                $diferencias[$campo] = [
                    'anterior' => $valorOriginal,
                    'nuevo' => $valorNuevo
                ];
            }
        }

        // Si no hay diferencias, no se guarda nada
        if (empty($diferencias)) {
            return;
        }

        // Guardamos el log
        LogCambio::create([
            'usuario_id' => auth()->id(),
            'ip' => ObtenerRealIP(),
            'tabla' => $modeloNombre,
            'tabla_id' => $idTabla,
            'operacion' => 'm',
            'cambios' => json_encode($diferencias)
        ]);
    }
}

if (!function_exists('ObtenerRealIP')) {
    function ObtenerRealIP(){

        if (isset($_SERVER["HTTP_CLIENT_IP"])){

            return $_SERVER["HTTP_CLIENT_IP"];

        }elseif (isset($_SERVER["HTTP_X_FORWARDED_FOR"])){

            return $_SERVER["HTTP_X_FORWARDED_FOR"];

        }elseif (isset($_SERVER["HTTP_X_FORWARDED"])){

            return $_SERVER["HTTP_X_FORWARDED"];

        }elseif (isset($_SERVER["HTTP_FORWARDED_FOR"])){

            return $_SERVER["HTTP_FORWARDED_FOR"];

        }elseif (isset($_SERVER["HTTP_FORWARDED"])){

            return $_SERVER["HTTP_FORWARDED"];

        }else{

            return $_SERVER["REMOTE_ADDR"];

        }
    }    
}
if (!function_exists('guardarLogCambioModelo')) {
    function guardarLogCambioModelo($modeloActual, $relaciones = [], $modeloOriginal = null) 
    {
        if (!$modeloOriginal) {
            $modeloOriginal = $modeloActual->fresh()->load($relaciones);
        }

        $original = $modeloOriginal->toArray();
        $nuevo = $modeloActual->toArray();

        unset($original['updated_at'], $original['created_at']);
        unset($nuevo['updated_at'], $nuevo['created_at']);

        $diferenciasPadre = [];
        foreach ($original as $campo => $valorOriginal) {
            if (is_array($valorOriginal)) continue;
            if ($valorOriginal != ($nuevo[$campo] ?? null)) {
                $diferenciasPadre[$campo] = [$valorOriginal, $nuevo[$campo] ?? null];
            }
        }

        $diferenciasHijas = [];

        foreach ($relaciones as $rel) {
            $originalRelacion = collect($original[$rel] ?? [])->map(function ($item) {
                unset($item['created_at'], $item['updated_at']);
                return $item;
            })->toArray();

            $nuevoRelacion = collect($nuevo[$rel] ?? [])->map(function ($item) {
                unset($item['created_at'], $item['updated_at']);
                return $item;
            })->toArray();

            $relDiferencias = [];

            // Modificados o eliminados
            foreach ($originalRelacion as $origItem) {
                $idItem = $origItem['id'] ?? null;
                $nuevoItem = collect($nuevoRelacion)->firstWhere('id', $idItem);

                if ($nuevoItem) {
                    foreach ($origItem as $campo => $valorOriginal) {
                        if ($valorOriginal != ($nuevoItem[$campo] ?? null)) {
                            $relDiferencias[$idItem][$campo] = [$valorOriginal, $nuevoItem[$campo] ?? null];
                        }
                    }
                } else {
                    $relDiferencias['__eliminado'][] = $origItem;
                }
            }

            // Nuevos
            foreach ($nuevoRelacion as $nuevoItem) {
                $idItem = $nuevoItem['id'] ?? null;
                if (!$idItem || !collect($originalRelacion)->firstWhere('id', $idItem)) {
                    $relDiferencias['__nuevo'][] = $nuevoItem;
                }
            }

            if (!empty($relDiferencias)) {
                $diferenciasHijas[$rel] = $relDiferencias;
            }
        }

        if (empty($diferenciasPadre) && empty($diferenciasHijas)) {
            return null;
        }

        $nombreTablaPadre = $modeloActual->getTable();
        $idPadre = $modeloActual->id;

        $diferencias = [
            '1.0padre' => [
                $nombreTablaPadre => [
                    $idPadre => $diferenciasPadre
                ]
            ],
            '1.1hijas' => $diferenciasHijas
        ];

        LogCambio::create([
            'usuario_id' => auth()->id(),
            'ip'         => request()->ip(),
            'tabla'      => $nombreTablaPadre,
            'tabla_id'   => $idPadre,
            'operacion'  => 'm',
            'cambios'    => json_encode($diferencias, JSON_UNESCAPED_UNICODE)
        ]);
        return 1;
    }
}

if (!function_exists('getRealIP')) {
    function getRealIP(){

        if (isset($_SERVER["HTTP_CLIENT_IP"])){

            return $_SERVER["HTTP_CLIENT_IP"];

        }elseif (isset($_SERVER["HTTP_X_FORWARDED_FOR"])){

            return $_SERVER["HTTP_X_FORWARDED_FOR"];

        }elseif (isset($_SERVER["HTTP_X_FORWARDED"])){

            return $_SERVER["HTTP_X_FORWARDED"];

        }elseif (isset($_SERVER["HTTP_FORWARDED_FOR"])){

            return $_SERVER["HTTP_FORWARDED_FOR"];

        }elseif (isset($_SERVER["HTTP_FORWARDED"])){

            return $_SERVER["HTTP_FORWARDED"];

        }else{

            return $_SERVER["REMOTE_ADDR"];

        }
    }
}
?>