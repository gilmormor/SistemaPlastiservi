<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStacorregiratCotizaciondetalleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cotizaciondetalle', function (Blueprint $table) {
            $table->boolean('stacorregirat')->comment('0=no modificar, 1=Modificar At.')->nullable()->after('acuerdotecnicotemp_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cotizaciondetalle', function (Blueprint $table) {
            $table->dropColumn('stacorregirat');
        });
    }
}
