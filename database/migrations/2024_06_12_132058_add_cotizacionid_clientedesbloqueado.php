<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCotizacionidClientedesbloqueado extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('clientedesbloqueado', function (Blueprint $table) {
            $table->unsignedBigInteger('cotizacion_id')->nullable()->after('notaventa_id');
            $table->foreign('cotizacion_id','fk_clientedesbloqueado_cotizacion')->references('id')->on('cotizacion')->onDelete('restrict')->onUpdate('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('clientedesbloqueado', function (Blueprint $table) {
            $table->dropForeign('fk_clientedesbloqueado_cotizacion');
            $table->dropColumn('cotizacion_id');
        });
    }
}
