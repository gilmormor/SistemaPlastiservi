<?php

namespace App\Http\Controllers;

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
            $soapclient = new SoapClient(env('APP_URLSII'));
            //$soapclient = new SoapClient('http://bes-cert.bestechnology.cl/wsfactlocal/dtelocal.asmx?wsdl'); //AMBIENTE PRUEBA
            //$soapclient = new SoapClient('http://bes-dte.bestechnology.cl/wsfactlocal/dtelocal.asmx?wsdl'); //AMBIENTE PRODUCTIVO
            $response = $soapclient->Carga_TXTDTE([
                "ArchivoTXT" => $ArchivoTXT,
                "TipoArchivo" => $TipoArchivo
            ]);

            return ($response->Carga_TXTDTEResult);
        }catch(Exception $e){
            return $e->getMessage();
        }
    }

    public function Solicitar_Folio($RutEmpresa,$TipoDocto){
        try{
            $soapFolio = new SoapClient(env('APP_URLSII'));
            //$soapFolio = new SoapClient('http://bes-cert.bestechnology.cl/wsfactlocal/dtelocal.asmx?wsdl'); //AMBIENTE PRUEBA
            //$soapFolio = new SoapClient('http://bes-dte.bestechnology.cl/wsfactlocal/dtelocal.asmx?wsdl'); //AMBIENTE PRODUCTIVO
            $response = $soapFolio->Solicitar_Folio([
                "RutEmpresa" => $RutEmpresa,
                "TipoDocto" => $TipoDocto
            ]);
            return $response->Solicitar_FolioResult;
        }catch(Exception $e){
            return $e->getMessage();
        }
    }

    public function Estado_DTE($RutEmpresa,$TipoDocto,$NroDocto){
        try{
            $soapFolio = new SoapClient(env('APP_URLSII'));
            //$soapFolio = new SoapClient('http://bes-cert.bestechnology.cl/wsfactlocal/dtelocal.asmx?wsdl'); //AMBIENTE PRUEBA
            //$soapFolio = new SoapClient('http://bes-dte.bestechnology.cl/wsfactlocal/dtelocal.asmx?wsdl'); //AMBIENTE PRODUCTIVO
            $response = $soapFolio->Estado_DTE([
                "RUTEmpresa" => $RutEmpresa,
                "TipoDocto" => $TipoDocto,
                "NroDocto" => $NroDocto
            ]);
            return $response->Estado_DTEResult;
            /*
            if($response->Estado_DTEResult->Estatus == 3){ // 3=Documento identificado no existe.
                dd($response);
            }
            */
        }catch(Exception $e){
            return $e->getMessage();
        }
    }

    public function Reimprimir_DoctoDTE($RutEmpresa,$TipoDocto,$NroDocto){
        try{
            $soapFolio = new SoapClient(env('APP_URLSII'));
            //$soapFolio = new SoapClient('http://bes-cert.bestechnology.cl/wsfactlocal/dtelocal.asmx?wsdl'); //AMBIENTE PRUEBA
            //$soapFolio = new SoapClient('http://bes-dte.bestechnology.cl/wsfactlocal/dtelocal.asmx?wsdl'); //AMBIENTE PRODUCTIVO
            $response = $soapFolio->Reimprimir_DoctoDTE([
                "RUTEmpresa" => $RutEmpresa,
                "TipoDocto" => $TipoDocto,
                "NroDocto" => $NroDocto
            ]);
            return $response->Reimprimir_DoctoDTEResult;
            /*
            if($response->Estado_DTEResult->Estatus == 3){ // 3=Documento identificado no existe.
                dd($response);
            }
            */
        }catch(Exception $e){
            return $e->getMessage();
        }
    }

    public function Comando01CargaDocumentos($cadenaxml){
        try {
            $soapClient = new SoapClient(env('APP_URLMANAGER'));
            $response = $soapClient->Comando01CargaDocumentos([
                "Codigo" => "0mpPWcXj8AC312v",
                "XMlIngreso" => $cadenaxml
            ]);
            return $response->Comando01CargaDocumentosResult;
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    public function Comando03CreaClientes($cadenaxml)
    {
        try {
            $soapClient = new SoapClient(env('APP_URLMANAGER'));
            $response = $soapClient->Comando03CreaClientes([
                "Codigo" => "0mpPWcXj8AC312v",
                "XMlIngreso" => $cadenaxml
            ]);
            return $response->Comando03CreaClientesResult;
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    public function Comando02ListaPendientes($RutCliente)
    {
        try {
            //$soapClient = new SoapClient("https://ws.manager.cl/Manager/ws/SigetecPlastiservi/wscomando.asmx?wsdl");
            $soapClient = new SoapClient(env('APP_URLMANAGER'));
            $response = $soapClient->Comando02ListaPendientes([
                "Codigo" => "0mpPWcXj8AC312v",
                "RutCliente" => $RutCliente
            ]);
            //dd($response->Comando02ListaPendientesResult->Contenido->any);
            return $response->Comando02ListaPendientesResult->Contenido->any;
        } catch (Exception $e) {
            echo "Error 01: " . $e->getMessage();
        }
        /*
        try{
            $soapclient = new SoapClient('https://ws.manager.cl/Manager/ws/SigetecPlastiservi/wscomando.asmx?op=Comando02ListaPendientes');
            $response = $soapclient->Comando02ListaPendientes([
                "Codigo" => "0mpPWcXj8AC312v",
                "RutCliente" => $RutCliente
            ]);
            dd($RutCliente);
            return ($response->Comando02ListaPendientesResponse);
        }catch(Exception $e){
            return $e->getMessage();
        }
        */
    }

}
