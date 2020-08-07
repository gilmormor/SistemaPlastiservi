<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModificarTableNoconformidad extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('noconformidad', function (Blueprint $table) {
            $table->boolean('notirecep')->comment('Estatus notificacion Nueva Recepcion No Conformidad.')->after('feccierreaccorr')->nullable();
            $table->boolean('notivalai')->comment('Estatus notificacion Nueva Validacion accion inmediata.')->after('notirecep')->nullable();
            $table->boolean('noticumpl')->comment('Estatus notificacion Nueva Cumplimiento No Conformidad.')->after('notivalai')->nullable();
            $table->boolean('notiresgi')->comment('Estatus notificacion Nueva Revision SGI No Conformidad.')->after('noticumpl')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('noconformidad', function (Blueprint $table) {
            //
        });
    }
}
