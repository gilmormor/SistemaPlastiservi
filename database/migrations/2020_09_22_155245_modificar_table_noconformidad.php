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
            $table->dateTime('fecharesmedtom')->comment('Fecha que fueron registrados los Resultados de las medidas tomadas')->nullable()->after('resmedtom');
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
