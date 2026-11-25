<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * La reasignacion se cumplio: el destinatario despacho lo que le entregaron.
 *
 * Mientras esta abierta, una reasignacion descuenta del stock libre igual que una
 * reserva -ese material tiene dueno-. Si no se cerrara al despachar, seguiria
 * comprometiendo material que ya salio de la bodega y el disponible quedaria
 * corto para siempre.
 *
 * Se cierra sola en DespachoSolController, cuando lo despachado por el
 * destinatario cubre lo entregado. No se borra nada: la reasignacion sigue
 * contando lo que paso, solo deja de pesar sobre el stock.
 */
class AddConsumidaAtToProdreasignacionTable extends Migration
{
    public function up()
    {
        Schema::table('prodreasignacion', function (Blueprint $table) {
            $table->timestamp('consumida_at')->nullable()->after('anulmotivo');
        });
    }

    public function down()
    {
        Schema::table('prodreasignacion', function (Blueprint $table) {
            $table->dropColumn('consumida_at');
        });
    }
}
