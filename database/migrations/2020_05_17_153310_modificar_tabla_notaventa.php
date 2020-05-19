<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModificarTablaNotaventa extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('notaventa', function (Blueprint $table) {
            $table->dateTime('inidespacho')->comment('Fecha Inicio de despacho')->nullable()->after('visto');
            $table->string('guiasdespacho',100)->comment('Guias de despacho ')->nullable()->after('inidespacho');
            $table->dateTime('findespacho')->comment('Fecha Fin de despacho')->nullable()->after('guiasdespacho');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('notaventa', function (Blueprint $table) {
            //
        });
    }
}
