<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNotaventadetalleCantgrupo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('notaventadetalle', function (Blueprint $table) {
            $table->float('cantgrupo',10,2)->comment('Cantidad agrupada ejemplo: 1 paquete, 1 rollo, 1 caja')->after('cant');
            $table->float('cantxgrupo',10,2)->comment('Cantidad por Grupo')->after('cantgrupo');
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
