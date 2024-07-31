<?php

namespace App\Http\Controllers;

use App\Events\XMLCargaDocManager;
use App\Models\Dte;
use Exception;
use Illuminate\Http\Request;
use SoapClient;
use SoapFault;

class SoapController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        /*
        $soapFolio = new SoapClient('http://bes-cert.bestechnology.cl/wsfactlocal/dtelocal.asmx?wsdl');
        $response = $soapFolio->Solicitar_Folio([
            "RutEmpresa" => "79522140-9",
            "TipoDocto" => "52"
        ]);
        dd($response->Solicitar_FolioResult);
        */


        try{
            /*
            $soapclient = new SoapClient('http://bes-cert.bestechnology.cl/wsfactlocal/dtelocal.asmx?wsdl');
            $response = $soapclient->Carga_TXTDTE([
                "ArchivoTXT" => $ArchivoTXT,
                "TipoArchivo" => $TipoArchivo
            ]);
            */

            //$soapFolio = new SoapClient('http://bes-cert.bestechnology.cl/wsfactlocal/dtelocal.asmx?wsdl'); //AMBIENTE PRUEBA
            $soapFolio = new SoapClient('http://bes-dte.bestechnology.cl/wsfactlocal/dtelocal.asmx?wsdl'); //AMBIENTE PRODUCTIVO
            $response = $soapFolio->Reimprimir_DoctoDTE([
                "RutEmpresa" => "79522140-9",
                "TipoDocto" => "52",
                "NroDocto" => 183
            ]);
            dd($response);
            return $response->Reimprimir_DoctoDTEResult;

            
            //$soapFolio = new SoapClient('http://bes-cert.bestechnology.cl/wsfactlocal/dtelocal.asmx?wsdl'); //AMBIENTE PRUEBA
            $soapFolio = new SoapClient('http://bes-dte.bestechnology.cl/wsfactlocal/dtelocal.asmx?wsdl'); //AMBIENTE PRODUCTIVO
            $response = $soapFolio->Reimprimir_DoctoDTE([
                "RUTEmpresa" => "79522140-9",
                "TipoDocto" => "52",
                "NroDocto" => 71
            ]);
            //dd($response);

            //$soapFolio = new SoapClient('http://bes-cert.bestechnology.cl/wsfactlocal/dtelocal.asmx?wsdl'); //AMBIENTE PRUEBA
            $soapFolio = new SoapClient('http://bes-dte.bestechnology.cl/wsfactlocal/dtelocal.asmx?wsdl'); //AMBIENTE PRODUCTIVO
            $response = $soapFolio->Confirmar_Docto([
                "RUTEmpresa" => "79522140-9",
                "TipoDocto" => "52",
                "NroDocto" => 71
            ]);
            //dd($response);

            //$soapFolio = new SoapClient('http://bes-cert.bestechnology.cl/wsfactlocal/dtelocal.asmx?wsdl'); //AMBIENTE PRUEBA
            $soapFolio = new SoapClient('http://bes-dte.bestechnology.cl/wsfactlocal/dtelocal.asmx?wsdl'); //AMBIENTE PRODUCTIVO
            $response = $soapFolio->Estado_DTE([
                "RUTEmpresa" => "79522140-9",
                "TipoDocto" => "52",
                "NroDocto" => 73
            ]);
            if($response->Estado_DTEResult->Estatus == 3){
                dd($response);
            }
            dd($response);

            for ($i=66; $i < 100; $i++) { 
                //$soapFolio = new SoapClient('http://bes-cert.bestechnology.cl/wsfactlocal/dtelocal.asmx?wsdl'); //AMBIENTE PRUEBA
                $soapFolio = new SoapClient('http://bes-dte.bestechnology.cl/wsfactlocal/dtelocal.asmx?wsdl'); //AMBIENTE PRODUCTIVO
                $response = $soapFolio->Estado_DTE([
                    "RUTEmpresa" => "79522140-9",
                    "TipoDocto" => "52",
                    "NroDocto" => $i
                ]);
                if($response->Estado_DTEResult->Estatus == 3){
                    dd($response);
                }
    
            }

    

            /*
            echo '<br><br><br>';
            $array = json_decode(json_encode($response), true);
            print_r($array);
            */
            /*
             echo '<br><br><br>';
            echo  $array['GetCountriesAvailableResult']['CountryCode']['5']['Description'];
                  echo '<br><br><br>';
                foreach($array as $item) {
                    echo '<pre>'; var_dump($item);
                }  */
            }catch(Exception $e){
                echo $e->getMessage();
            }
    }



    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function Carga_TXTDTE($ArchivoTXT,$TipoArchivo)
    {
        try{
            // Configuración del cliente SOAP
            $options = array(
                'trace' => 1,
                'connection_timeout' => 30, // tiempo de espera de conexión en segundos
                // Aumentar el tiempo de timeout
                'timeout' => 120 // tiempo de espera total en segundos
            );
            $soapclient = new SoapClient(env('APP_URLSII'), $options);
            $response = $soapclient->Carga_TXTDTE([
                "ArchivoTXT" => $ArchivoTXT,
                "TipoArchivo" => $TipoArchivo
            ]);

            //return ($response->Carga_TXTDTEResult);

            // Verificar el status de la solicitud HTTP
            $http_status = $soapclient->__getLastResponseHeaders();

            // Extraer el status HTTP
            preg_match('/^HTTP\/1\.\d (\d+)/', $http_status, $matches);
            $status_code = isset($matches[1]) ? intval($matches[1]) : null;

            // Verificar el status 200 de la solicitud HTTP
            if ($status_code === 200) {
                // Si el status es 200, la solicitud se completó exitosamente
                // Procesar la respuesta del servidor aquí
                return $response->Carga_TXTDTEResult;
            } else {
                // Si el status no es 200, hubo un error en la solicitud
                return "Hubo un error en la solicitud HTTP. Status: $http_status";
            }

        }catch(Exception $e){
            return $e->getMessage();
        }
    }

    public function Solicitar_Folio($RutEmpresa,$TipoDocto){
        try{
            // Configuración del cliente SOAP
            $options = array(
                'trace' => 1,
                'connection_timeout' => 30, // tiempo de espera de conexión en segundos
                // Aumentar el tiempo de timeout
                'timeout' => 120 // tiempo de espera total en segundos
            );
            
            $soapFolio = new SoapClient(env('APP_URLSII'), $options);
            //$soapFolio = new SoapClient('http://bes-cert.bestechnology.cl/wsfactlocal/dtelocal.asmx?wsdl'); //AMBIENTE PRUEBA
            //$soapFolio = new SoapClient('http://bes-dte.bestechnology.cl/wsfactlocal/dtelocal.asmx?wsdl'); //AMBIENTE PRODUCTIVO
            $response = $soapFolio->Solicitar_Folio([
                "RutEmpresa" => $RutEmpresa,
                "TipoDocto" => $TipoDocto
            ]);
            //return $response->Solicitar_FolioResult;

            // Verificar el status de la solicitud HTTP
            $http_status = $soapFolio->__getLastResponseHeaders();

            // Extraer el status HTTP
            preg_match('/^HTTP\/1\.\d (\d+)/', $http_status, $matches);
            $status_code = isset($matches[1]) ? intval($matches[1]) : null;

            // Verificar el status 200 de la solicitud HTTP
            if ($status_code === 200) {
                // Si el status es 200, la solicitud se completó exitosamente
                // Procesar la respuesta del servidor aquí
                return $response->Solicitar_FolioResult;
            } else {
                // Si el status no es 200, hubo un error en la solicitud
                return "Hubo un error en la solicitud HTTP. Status: $http_status";
            }
            
        }catch(Exception $e){
            return $e->getMessage();
        }
    }

    public function Estado_DTE($RutEmpresa,$TipoDocto,$NroDocto){
        try{
            // Configuración del cliente SOAP
            $options = array(
                'trace' => 1,
                'connection_timeout' => 30, // tiempo de espera de conexión en segundos
                // Aumentar el tiempo de timeout
                'timeout' => 120 // tiempo de espera total en segundos
            );
            $soapFolio = new SoapClient(env('APP_URLSII'), $options);
            //$soapFolio = new SoapClient('http://bes-cert.bestechnology.cl/wsfactlocal/dtelocal.asmx?wsdl'); //AMBIENTE PRUEBA
            //$soapFolio = new SoapClient('http://bes-dte.bestechnology.cl/wsfactlocal/dtelocal.asmx?wsdl'); //AMBIENTE PRODUCTIVO
            $response = $soapFolio->Estado_DTE([
                "RUTEmpresa" => $RutEmpresa,
                "TipoDocto" => $TipoDocto,
                "NroDocto" => $NroDocto
            ]);
            //return $response->Estado_DTEResult;
            // Verificar el status de la solicitud HTTP
            $http_status = $soapFolio->__getLastResponseHeaders();

            // Extraer el status HTTP
            preg_match('/^HTTP\/1\.\d (\d+)/', $http_status, $matches);
            $status_code = isset($matches[1]) ? intval($matches[1]) : null;

            // Verificar el status 200 de la solicitud HTTP
            if ($status_code === 200) {
                // Si el status es 200, la solicitud se completó exitosamente
                // Procesar la respuesta del servidor aquí
                return $response->Estado_DTEResult;
            } else {
                // Si el status no es 200, hubo un error en la solicitud
                return "Hubo un error en la solicitud HTTP. Status: $http_status";
            }            
        }catch(Exception $e){
            return $e->getMessage();
        }
    }

    public function Reimprimir_DoctoDTE($RutEmpresa,$TipoDocto,$NroDocto){
        try{
            // Configuración del cliente SOAP
            $options = array(
                'trace' => 1,
                'connection_timeout' => 30, // tiempo de espera de conexión en segundos
                // Aumentar el tiempo de timeout
                'timeout' => 120 // tiempo de espera total en segundos
            );
            $soapFolio = new SoapClient(env('APP_URLSII'), $options);
            //$soapFolio = new SoapClient('http://bes-cert.bestechnology.cl/wsfactlocal/dtelocal.asmx?wsdl'); //AMBIENTE PRUEBA
            //$soapFolio = new SoapClient('http://bes-dte.bestechnology.cl/wsfactlocal/dtelocal.asmx?wsdl'); //AMBIENTE PRODUCTIVO
            $response = $soapFolio->Reimprimir_DoctoDTE([
                "RUTEmpresa" => $RutEmpresa,
                "TipoDocto" => $TipoDocto,
                "NroDocto" => $NroDocto
            ]);
            //return $response->Reimprimir_DoctoDTEResult;
            // Verificar el status de la solicitud HTTP
            $http_status = $soapFolio->__getLastResponseHeaders();

            // Extraer el status HTTP
            preg_match('/^HTTP\/1\.\d (\d+)/', $http_status, $matches);
            $status_code = isset($matches[1]) ? intval($matches[1]) : null;

            // Verificar el status 200 de la solicitud HTTP
            if ($status_code === 200) {
                // Si el status es 200, la solicitud se completó exitosamente
                // Procesar la respuesta del servidor aquí
                return $response->Reimprimir_DoctoDTEResult;
            } else {
                // Si el status no es 200, hubo un error en la solicitud
                return "Hubo un error en la solicitud HTTP. Status: $http_status";
            }

        }catch(Exception $e){
            return $e->getMessage();
        }
    }

    public function Comando01CargaDocumentos($cadenaxml){

        // URL del servicio SOAP
        $soapUrl = env('APP_URLMANAGER');

        // Cuerpo de la solicitud SOAP
        $soapRequest = '<?xml version="1.0" encoding="utf-8"?>
                            <soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
                            <soap:Body>
                                <Comando01CargaDocumentos xmlns="https://aplicaciones.sigetec.cl/WSPlastiServi/">
                                <Codigo>0mpPWcXj8AC312v</Codigo>
                                <XMlIngreso><![CDATA[' . $cadenaxml . ']]></XMlIngreso>
                                </Comando01CargaDocumentos>
                            </soap:Body>
                            </soap:Envelope>';
        //dd($soapRequest);
        try {
            // Configurar la solicitud cURL
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $soapUrl);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $soapRequest);
            curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                'Content-Type: text/xml',
                'Content-Length: ' . strlen($soapRequest)
            ));

            // Establecer tiempo máximo de espera para la conexión (en segundos)
            curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10); // Por ejemplo, 10 segundos
            
            // Establecer tiempo máximo de espera para la ejecución total (en segundos)
            curl_setopt($curl, CURLOPT_TIMEOUT, 30); // Por ejemplo, 30 segundos
            
            // Activar el trace de la solicitud SOAP (opcional)
            //curl_setopt($curl, CURLOPT_VERBOSE, true);
            //$verbose = fopen('php://temp', 'w+');
            //curl_setopt($curl, CURLOPT_STDERR, $verbose);

            // Realizar la solicitud cURL
            $response = curl_exec($curl);

            // Verificar si hubo errores
            if(curl_errno($curl)) {
                throw new Exception('Error cURL: ' . curl_error($curl));
            }

            // Cerrar la sesión cURL
            curl_close($curl);

            // Procesar la respuesta SOAP
            // Aquí debes manejar la respuesta según la estructura que esperas recibir del servicio
            // Por ejemplo, podrías devolver los datos o hacer cualquier otra operación necesaria
            //dd($response);
            return [
                "id" => 1,
                "response" => $response,
                "titulo" => "Procesado",
                "mensaje" => "Procesado con exito.",
                "tipo_alert" => "success"
            ];

        } catch (Exception $e) {
            // Capturar errores
            return [
                "id" => 0,
                "error" => 1,
                "titulo" => "Error",
                "mensaje" => "No hay comunicacion con Sistema de Cobranza. " . $e->getMessage(),
                "bloqueo" => "No hay comunicacion con Sistema de Cobranza. " . $e->getMessage(),
                "tipo_alert" => "info"
            ];
            //dd("Error: " . $e->getMessage());
        }
    }


    public function Comando03CreaClientes($cadenaxml)
    {
        /* try {
            $soapClient = new SoapClient(env('APP_URLMANAGER'));
            $response = $soapClient->Comando03CreaClientes([
                "Codigo" => "0mpPWcXj8AC312v",
                "XMlIngreso" => $cadenaxml
            ]);
            dd($response->Comando03CreaClientesResult);
            return $response->Comando03CreaClientesResult;
        } catch (Exception $e) {
            dd("Error: " . $e->getMessage());
        } */

        // URL del servicio SOAP
        $soapUrl = env('APP_URLMANAGER');

        // Cuerpo de la solicitud SOAP
        $soapRequest = '<?xml version="1.0" encoding="utf-8"?>
                        <soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
                        <soap:Body>
                            <Comando03CreaClientes xmlns="https://aplicaciones.sigetec.cl/WSPlastiServi/">
                            <Codigo>0mpPWcXj8AC312v</Codigo>
                            <XMlIngreso><![CDATA[' . $cadenaxml . ']]></XMlIngreso>
                            </Comando03CreaClientes>
                        </soap:Body>
                        </soap:Envelope>';

        try {
            // Configurar la solicitud cURL
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $soapUrl);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $soapRequest);
            curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                'Content-Type: text/xml',
                'Content-Length: ' . strlen($soapRequest)
            ));

            // Establecer tiempo máximo de espera para la conexión (en segundos)
            curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10); // Por ejemplo, 10 segundos

            // Establecer tiempo máximo de espera para la ejecución total (en segundos)
            curl_setopt($curl, CURLOPT_TIMEOUT, 30); // Por ejemplo, 30 segundos
            
            // Activar el trace de la solicitud SOAP (opcional)
            //curl_setopt($curl, CURLOPT_VERBOSE, true);
            //$verbose = fopen('php://temp', 'w+');
            //curl_setopt($curl, CURLOPT_STDERR, $verbose);
            

            // Realizar la solicitud cURL
            $response = curl_exec($curl);

            // Verificar si hubo errores
            if(curl_errno($curl)) {
                throw new Exception('Error cURL: ' . curl_error($curl));
            }

            // Cerrar la sesión cURL
            curl_close($curl);

            // Procesar la respuesta SOAP
            // Aquí debes manejar la respuesta según la estructura que esperas recibir del servicio
            // Por ejemplo, podrías devolver los datos o hacer cualquier otra operación necesaria
            //dd($response);
            return [
                "id" => 1,
                "response" => $response,
            ];
        } catch (Exception $e) {
            // Capturar errores
            return [
                "id" => 0,
                "error" => 1,
                "titulo" => "Error",
                "mensaje" => "No hay comunicacion con Sistema de Cobranza. " . $e->getMessage(),
                "bloqueo" => "No hay comunicacion con Sistema de Cobranza. " . $e->getMessage(),
                "tipo_alert" => "info"

            ];
            //dd("Error: " . $e->getMessage());
        }

    }

    public function Comando02ListaPendientes($RutCliente){
        /* try {
            $soapClient = new SoapClient(env('APP_URLMANAGER'));
            $response = $soapClient->Comando02ListaPendientes([
                "Codigo" => "0mpPWcXj8AC312v",
                "RutCliente" => $RutCliente
            ]);
            return $response->Comando02ListaPendientesResult->Contenido->any;
        } catch (SoapFault $e) {
            // Capturar errores específicos de SOAP
            dd("Error 01: " . $e->getMessage());
        } catch (Exception $e) {
            // Capturar otros errores
            dd("Error 02: " . $e->getMessage());
        } */

        // URL del servicio SOAP
        $soapUrl = env('APP_URLMANAGER');

        // Cuerpo de la solicitud SOAP
        $soapRequest = '<?xml version="1.0" encoding="utf-8"?>
                        <soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
                        <soap:Body>
                            <Comando02ListaPendientes xmlns="https://aplicaciones.sigetec.cl/WSPlastiServi/">
                            <Codigo>0mpPWcXj8AC312v</Codigo>
                            <RutCliente>' . $RutCliente . '</RutCliente>
                            </Comando02ListaPendientes>
                        </soap:Body>
                        </soap:Envelope>';

        try {
            // Configurar la solicitud cURL
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $soapUrl);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $soapRequest);
            curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                'Content-Type: text/xml',
                'Content-Length: ' . strlen($soapRequest)
            ));

            // Realizar la solicitud cURL
            $response = curl_exec($curl);

            // Verificar si hubo errores
            if(curl_errno($curl)) {
                throw new Exception('Error cURL: ' . curl_error($curl));
            }

            // Cerrar la sesión cURL
            curl_close($curl);

            // Procesar la respuesta SOAP
            // Aquí debes manejar la respuesta según la estructura que esperas recibir del servicio
            // Por ejemplo, podrías devolver los datos o hacer cualquier otra operación necesaria
            //dd($response);
            return $response;
        } catch (Exception $e) {
            // Capturar errores
            return [
                "id" => 0,
                "error" => 1,
                "titulo" => "Error",
                "mensaje" => "No hay comunicacion con Sistema de Cobranza. " . $e->getMessage(),
                "bloqueo" => "No hay comunicacion con Sistema de Cobranza. " . $e->getMessage(),
                "tipo_alert" => "info"

            ];
        }
    }

}
