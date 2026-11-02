<?php
namespace App\Services;

use Carbon\Carbon;
use App\Models\DteTrans;
use App\Models\Comuna;
use App\Models\TransEmp;
use App\Models\TransChofer;
use App\Models\TransVeh;

class DteTransService
{
    public function crearDesdeRequest($request, $dteId)
    {
        /* Los datos del chofer, la patente y el RUT del transportista solo se
           validaban en el navegador (datostrans/crear.js). Si el JS no carga o el
           POST llega por otra via, llegaban vacios: el servicio los convertia en
           null y las columnas de dtetrans son NOT NULL, de modo que la emision
           moria con un error SQL en vez de un mensaje entendible.
           Son exigencias del resolutivo 1° de la Resolucion Ex. SII N°144 (puntos
           2 y 3), asi que la regla vive aqui, en el servidor. Cuando la 144
           habilite emitir con "Diferido" o "Sin Dato", se relaja en este unico
           lugar y no repartido entre la vista y el JS.
           ruttrans sigue la misma regla que el JS: obligatorio solo si el
           transporte es EXTERNO. */
        /* Los datos del transportista se exigen solo cuando el despacho corre por
           cuenta de la empresa (tipo de despacho 2 o 3). Con el tipo 1 el comprador
           retira los bienes en el establecimiento del vendedor y la Resolucion
           Ex. SII N°144, resolutivo 15°, no exige esos datos; el Formato DTE 2.5 lo
           dice igual en los campos Patente, PatenteCarro y RUTTrans: "Relevante si
           Indicador Tipo de Despacho 2 o 3".
           Las fechas y el destino se exigen siempre, porque su condicion en el
           formato es "Obligatorio para documentos que acompañan traslado de bienes"
           y no depende del tipo de despacho: una guia con retiro del cliente
           tambien ampara un traslado. */
        $aux_contransportista = ($request->tipodespacho == 2 or $request->tipodespacho == 3);

        $reglas = [
            'fchsalida' => 'required|date_format:d/m/Y',
            'hrasalida' => 'required|date_format:H:i',
            'fchllegada' => 'required|date_format:d/m/Y',
        ];
        if ($aux_contransportista) {
            $reglas['tipo_transporte'] = 'required|in:INTERNO,EXTERNO';
            $reglas['rutchofer1'] = 'required';
            $reglas['nombrechofer'] = 'required';
            $reglas['patente'] = 'required';
            $reglas['ruttrans'] = 'required_if:tipo_transporte,EXTERNO';
        }

        $data = $request->validate($reglas, [
            'tipo_transporte.required' => 'Debe seleccionar el tipo de transporte.',
            'tipo_transporte.in' => 'El tipo de transporte debe ser INTERNO o EXTERNO.',
            'rutchofer1.required' => 'Debe ingresar el RUT del chofer.',
            'nombrechofer.required' => 'Debe ingresar el nombre completo del chofer.',
            'patente.required' => 'Debe ingresar la patente del vehiculo.',
            'ruttrans.required_if' => 'Debe ingresar el RUT del transportista para transporte externo.',
        ]);

        $fechaHoraSalida = Carbon::createFromFormat(
            'd/m/Y H:i',
            $data['fchsalida'] . ' ' . $data['hrasalida']
        );

        $fechaLlegada = Carbon::createFromFormat(
            'd/m/Y',
            $data['fchllegada']
        );

        $aux_comuna = Comuna::findOrFail($request->comunaentrega_id);
        $usuarioId = auth()->id();

        /* Los datos del transportista se ignoran cuando el tipo de despacho no los
           admite. Ocultar un campo en el formulario no impide que se envie: un input
           oculto viaja igual en el POST, de modo que si el usuario cargaba patente y
           chofer con tipo 2 y despues cambiaba a 1, esos datos se guardaban y salian
           en el XML, al reves de lo que corresponde cuando retira el cliente. El JS
           ademas los limpia, pero la garantia tiene que estar aqui: si el JS no carga
           o el POST llega por otra via, el resultado debe ser igual de correcto.
           Se reutiliza $aux_contransportista, la misma condicion con que se validan
           mas arriba. */

        /* De los maestros ahora se guarda el id, no el texto: dtetrans se vincula con
           transemp, transchofer y transveh en vez de repetir el RUT, el nombre y la
           patente. El XML los lee desde esas relaciones, y el nombre de la comuna
           desde comuna_id, que ya estaba. */

        // Empresa transportista — solo si tipo_transporte es EXTERNO y viene el rut
        $aux_transemp = null;
        if ($aux_contransportista and $request->tipo_transporte === 'EXTERNO') {
            $aux_ruttrans = limpiarRut($request->ruttrans);
            if (!empty($aux_ruttrans)) {
                $aux_transemp = TransEmp::firstOrCreate(
                    ['rut' => $aux_ruttrans],
                    ['usuario_id' => $usuarioId]
                );
            }
        }

        // Chofer — si vienen rut y nombre
        $aux_transchofer = null;
        $aux_rutchofer = $aux_contransportista ? limpiarRut($request->rutchofer1) : null;
        $aux_nombrechofer = $aux_contransportista ? $request->nombrechofer : null;
        if (!empty($aux_rutchofer) && !empty($aux_nombrechofer)) {
            $aux_transchofer = TransChofer::firstOrCreate(
                ['rut' => $aux_rutchofer],
                ['nombre' => $aux_nombrechofer, 'usuario_id' => $usuarioId]
            );
        }

        // Vehículo principal — si viene la patente
        $aux_transveh = null;
        $aux_patente = $aux_contransportista ? strtoupper(trim($request->patente ?? '')) : '';
        if (!empty($aux_patente)) {
            $aux_transveh = TransVeh::firstOrCreate(
                ['patente' => $aux_patente],
                ['usuario_id' => $usuarioId]
            );
        }

        // Carro / remolque — opcional
        $aux_transvehcarro = null;
        $aux_patentecarro = $aux_contransportista ? strtoupper(trim($request->patentecarro ?? '')) : '';
        if (!empty($aux_patentecarro)) {
            $aux_transvehcarro = TransVeh::firstOrCreate(
                ['patente' => $aux_patentecarro],
                ['usuario_id' => $usuarioId]
            );
        }

        return DteTrans::create([
            "dte_id"        => $dteId,
            "fchsalida"     => $fechaHoraSalida,
            "fchllegada"    => $fechaLlegada,
            "comuna_id"        => $request->comunaentrega_id,
            "dirdest"          => $request->lugarentrega,
            "transemp_id"      => optional($aux_transemp)->id,
            "transchofer_id"   => optional($aux_transchofer)->id,
            "transveh_id"      => optional($aux_transveh)->id,
            "transvehcarro_id" => optional($aux_transvehcarro)->id,
        ]);
    }
}