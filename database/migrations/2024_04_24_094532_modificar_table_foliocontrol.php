<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModificarTableFoliocontrol extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('foliocontrol', function (Blueprint $table) {
            $table->tinyInteger('maxitemxdoc')->comment('Maximo items por documento')->after('signo');
            $table->tinyInteger('folmindisp')->comment('Numero de folios minimos disponibles para dar aviso que quedan pocos folios.')->after('maxitemxdoc');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('foliocontrol', function (Blueprint $table) {
            $table->dropColumn('maxitemxdoc');
            $table->dropColumn('folmindisp');
        });
    }
}
