<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSaldocantTableOpdet extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('opdet', function (Blueprint $table) {
            $table->float('cantrec',18,2)->comment('Cantidad recibida de la etapa de produccion anterior.')->default(0)->after('cant');
            $table->float('kgrec',18,2)->comment('Kilos recibidos de la etapa de produccion anterior.')->default(0)->after('cantrec');
            $table->float('cantprod',18,2)->comment('Cantidad producido.')->default(0)->after('kgrec');
            $table->float('kgprod',18,2)->comment('Kilos producidos.')->default(0)->after('cantprod');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('opdet', function (Blueprint $table) {
            $table->dropColumn('cantrec');
            $table->dropColumn('kgrec');
            $table->dropColumn('cantprod');
            $table->dropColumn('kgprod');
        });
    }
}
