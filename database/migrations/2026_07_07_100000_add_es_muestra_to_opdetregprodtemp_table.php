<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEsMuestraToOpdetregprodtempTable extends Migration
{
    public function up()
    {
        Schema::table('opdetregprodtemp', function (Blueprint $table) {
            // R1: marcar registro como muestra física — no suma a producción ni genera bodega
            $table->tinyInteger('es_muestra')->default(0)->after('mtslineal');
        });
    }

    public function down()
    {
        Schema::table('opdetregprodtemp', function (Blueprint $table) {
            $table->dropColumn('es_muestra');
        });
    }
}
