<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNotaventaidClientedesbloqueado extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('clientedesbloqueado', function (Blueprint $table) {
            $table->unsignedBigInteger('notaventa_id')->nullable()->after('cliente_id');
            $table->foreign('notaventa_id','fk_clientedesbloqueado_notaventa')->references('id')->on('notaventa')->onDelete('restrict')->onUpdate('restrict');
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
            $table->dropForeign('fk_clientedesbloqueado_notaventa');
            $table->dropColumn('notaventa_id');
        });
    }
}
