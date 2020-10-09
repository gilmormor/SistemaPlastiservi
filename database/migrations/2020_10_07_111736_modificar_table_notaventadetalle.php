<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModificarTableNotaventadetalle extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('notaventadetalle', function (Blueprint $table) {
            $table->float('cantsoldesp',10,2)->comment('Total Cantidad solicitada para despacho.')->nullable()->after('cant');
            $table->float('cantdesp',10,2)->comment('Total Cantidad Despachada.')->nullable()->after('cantsoldesp');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('notaventadetalle', function (Blueprint $table) {
            //
        });
    }
}
