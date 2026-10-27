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
        $data = $request->validate([
            'fchsalida' => 'required|date_format:d/m/Y',
            'hrasalida' => 'required|date_format:H:i',
            'fchllegada' => 'required|date_format:d/m/Y',
            'tipo_transporte' => 'required|in:INTERNO,EXTERNO',
            'rutchofer1' => 'required',
            'nombrechofer' => 'required',
            'patente' => 'required',
            'ruttrans' => 'required_if:tipo_transporte,EXTERNO',
        ], [
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

        // Empresa transportista — solo si tipo_transporte es EXTERNO y viene el rut
        $aux_ruttrans = null;
        if ($request->tipo_transporte === 'EXTERNO') {
            $aux_ruttrans = limpiarRut($request->ruttrans);
            if (!empty($aux_ruttrans)) {
                TransEmp::firstOrCreate(
                    ['rut' => $aux_ruttrans],
                    ['usuario_id' => $usuarioId]
                );
            }
        }

        // Chofer — si vienen rut y nombre
        $aux_rutchofer = limpiarRut($request->rutchofer1);
        if (!empty($aux_rutchofer) && !empty($request->nombrechofer)) {
            TransChofer::firstOrCreate(
                ['rut' => $aux_rutchofer],
                ['nombre' => $request->nombrechofer, 'usuario_id' => $usuarioId]
            );
        }

        // Vehículo principal — si viene la patente
        $aux_patente = strtoupper(trim($request->patente ?? ''));
        if (!empty($aux_patente)) {
            TransVeh::firstOrCreate(
                ['patente' => $aux_patente],
                ['usuario_id' => $usuarioId]
            );
        }

        // Carro / remolque — opcional
        $aux_patentecarro = strtoupper(trim($request->patentecarro ?? ''));
        if (!empty($aux_patentecarro)) {
            TransVeh::firstOrCreate(
                ['patente' => $aux_patentecarro],
                ['usuario_id' => $usuarioId]
            );
        }

        return DteTrans::create([
            "dte_id"        => $dteId,
            "fchsalida"     => $fechaHoraSalida,
            "fchllegada"    => $fechaLlegada,
            "comuna_id"     => $request->comunaentrega_id,
            "cmnadest"      => $aux_comuna->nombre,
            "dirdest"       => $request->lugarentrega,
            "ruttrans"      => $aux_ruttrans ?: null,
            "rutchofer"     => $aux_rutchofer ?: null,
            "nombrechofer"  => $request->nombrechofer,
            "patente"       => $aux_patente ?: null,
            "patentecarro"  => $aux_patentecarro ?: null,
        ]);
    }
}