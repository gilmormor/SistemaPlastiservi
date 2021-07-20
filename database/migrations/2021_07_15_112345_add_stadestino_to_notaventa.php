<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStadestinoToNotaventa extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('notaventa', function (Blueprint $table) {
            //
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
            $table->smallInteger('stadestino')->comment('Destino de la NV: 1=Venta directa despacho, 2=Produccion generar OT.')->nullable()->after('codbarra');
        });
    }
}
