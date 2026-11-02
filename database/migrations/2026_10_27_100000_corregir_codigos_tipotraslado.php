<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CorregirCodigosTipotraslado extends Migration
{
    /**
     * Run the migrations.
     *
     * Corrige los codigos del indicador de tipo de traslado. La carga inicial se
     * tomo del anexo de la Resolucion Ex. SII N°154, que traia los numeros
     * cruzados: puso el 7 como "Venta para exportacion" y omitio el 9.
     *
     * Segun el Formato DTE version 2.5 (2026-02) del SII, los valores correctos son:
     *   7 = Devolucion de mercaderias  (antes se llamaba "Guia de devolucion")
     *   8 = Traslado para exportacion (No venta)   <- ya estaba bien
     *   9 = Venta para exportacion
     *
     * El cambio es seguro: en la tabla dte solo se usan los codigos 1 y 6, de modo
     * que ningun documento emitido queda con su tipo alterado. Ambos codigos estan
     * ademas inactivos (activo=0), asi que no se ofrecen en pantalla.
     *
     * @return void
     */
    public function up()
    {
        DB::table('tipotraslado')
            ->where('indtraslado', 7)
            ->update([
                'desc'       => 'Devolución de mercaderías',
                'updated_at' => now(),
            ]);

        // el 9 nunca se inserto; se valida antes por si se agrego a mano
        if (DB::table('tipotraslado')->where('indtraslado', 9)->count() == 0) {
            DB::table('tipotraslado')->insert([
                'indtraslado' => 9,
                'desc'        => 'Venta para exportación',
                'activo'      => 0,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * Devuelve la tabla al estado anterior, aunque ese estado sea el incorrecto.
     *
     * @return void
     */
    public function down()
    {
        DB::table('tipotraslado')->where('indtraslado', 9)->delete();

        DB::table('tipotraslado')
            ->where('indtraslado', 7)
            ->update([
                'desc'       => 'Venta para exportación',
                'updated_at' => now(),
            ]);
    }
}
