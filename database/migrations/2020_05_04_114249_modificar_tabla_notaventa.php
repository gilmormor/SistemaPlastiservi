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
            $table->dateTime('visto')->comment('Estatus Visto por el jefe de Vendedores. Fecha hora')->nullable()->after('anulada');;
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