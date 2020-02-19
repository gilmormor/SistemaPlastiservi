<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\ClienteDirec;
use App\Models\ClienteSucursal;
use App\Models\ClienteVendedor;
use App\Models\Comuna;
use App\Models\ImportClienteSucursal;
use App\Models\ImportClienteVendedor;
use App\Models\ImportDirecciones;
use App\Models\SucursalClienteDirec;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ImportController extends Controller
{
    
    public function import(){
        $clientes = Cliente::orderBy('id')->get();
        $importclientevendedor = ImportClienteVendedor::orderBy('id')->get();
        //dd($clientevendedor);
        foreach($importclientevendedor as $icv)
        {
            //echo $cv->cliente_rut . "<br>";
            $cliente = DB::table('cliente')->where('rut', '=', $icv->cliente_rut)->get();
            if (!$cliente->isEmpty()) {
                //dd($cliente->first()->id);
                //dd($prueba->id);
                //echo $cliente->id . "<br>";
                $clientevendedor = new ClienteVendedor();

                $clientevendedor->cliente_id = $cliente->first()->id;
                $clientevendedor->vendedor_id = $icv->vendedor_id;

                $clientevendedor->save();
            };

        }
        $importdirecciones = ImportDirecciones::orderBy('id')->get();
    }

    public function importdirecciones(){
        $importdirecciones = ImportDirecciones::orderBy('id')->get();
        foreach($importdirecciones as $idirec)
        {
            //echo $cv->cliente_rut . "<br>";
            $cliente = DB::table('cliente')->where('rut', '=', $idirec->rut)->get();
            if (!$cliente->isEmpty()) {
                $clientedirec = new ClienteDirec();

                $clientedirec->cliente_id = $cliente->first()->id;
                $clientedirec->direccion = $idirec->direccion;
                $importcomuna = DB::table('importcomuna')->where('comuna_ideproplas', '=', $idirec->comuna_id)->get();
                $clientedirec->comuna_id = $importcomuna->first()->comuna_id;
                $comuna = Comuna::findOrFail($importcomuna->first()->comuna_id);
                $clientedirec->provincia_id = $comuna->provincia_id;
                $clientedirec->region_id = $comuna->provincia->region_id;
                $formapago = DB::table('formapago')->where('descripcion', '=', $idirec->formadepago)->get();
                $clientedirec->formapago_id = $formapago->first()->id;
                $plazopago = DB::table('plazopago')->where('descripcion', '=', $idirec->plazodepago)->get();
                $clientedirec->plazopago_id = $plazopago->first()->id;
                $clientedirec->contactonombre = $idirec->nombrecontacto;
                $clientedirec->contactoemail = $idirec->emailcontacto;
                $clientedirec->contactotelef = $idirec->telefonocontacto;
                $clientedirec->finanzascontacto = $idirec->nombrecontactofinanzas;
                $clientedirec->finanzanemail = $idirec->emailContactofinanzas;
                $clientedirec->finanzastelefono = $idirec->telefonocontactofinanzas;

                $clientedirec->nombrefantasia = $cliente->first()->nombrefantasiaprinc;
                $clientedirec->mostrarguiasfacturas = $idirec->mostrarguiasfacturas;
                $clientedirec->save();
            };

        }
    }

    public function importclientesucursal(){
        $importclientesucursal = ImportClienteSucursal::orderBy('id')->get();
        foreach($importclientesucursal as $ics)
        {
            //echo $cv->cliente_rut . "<br>";
            $cliente = DB::table('cliente')->where('rut', '=', $ics->rut)->get();
            if (!$cliente->isEmpty()) {
                $clientesucursal = new ClienteSucursal();

                $clientesucursal->cliente_id = $cliente->first()->id;
                $clientesucursal->sucursal_id = $ics->sucursal_id;

                $clientesucursal->save();

                $clientedirec = DB::table('clientedirec')->where('cliente_id', '=', $cliente->first()->id)->get();
                if (!$clientedirec->isEmpty()) {
                    $sucursalclientedirec = new SucursalClienteDirec();

                    $sucursalclientedirec->clientedirec_id = $clientedirec->first()->id;
                    $sucursalclientedirec->sucursal_id = $ics->sucursal_id;
    
                    $sucursalclientedirec->save();
                }

                

            };

        }
    }
    public function pasarDatosDeDirecAClientes(){
        $clientes = Cliente::orderBy('id')->get();
        //dd($clientevendedor);
        foreach($clientes as $cliente)
        {
            $data = Cliente::findOrFail($cliente->id);
            $direcciones = ClienteDirec::findOrFail($cliente->id);
            $sql = 'SELECT *
                FROM clientedirec 
                where cliente_id = ' . $cliente->id;

            $clientedirec = DB::select($sql);
            //dd($clientedirec[0]->formapago_id);
            $data->formapago_id = $clientedirec[0]->formapago_id;
            $data->plazopago_id = $clientedirec[0]->plazopago_id;
            $data->contactonombre = $clientedirec[0]->contactonombre;
            $data->contactoemail = $clientedirec[0]->contactoemail;
            $data->contactotelef = $clientedirec[0]->contactotelef;
            $data->mostrarguiasfacturas = $clientedirec[0]->mostrarguiasfacturas;
            $data->finanzascontacto = $clientedirec[0]->finanzascontacto;
            $data->finanzanemail = $clientedirec[0]->finanzanemail;
            $data->finanzastelefono = $clientedirec[0]->finanzastelefono;
            $data->observaciones = $clientedirec[0]->observaciones;
            $data->save();
        };
    }
}
