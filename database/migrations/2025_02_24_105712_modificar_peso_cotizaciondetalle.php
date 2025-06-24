<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModificarPesoCotizaciondetalle extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cotizaciondetalle', function (Blueprint $table) {
            $table->float('peso', 15, 10)->change();
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
            $table->float('peso', 8, 3)->change(); // Revertir cambios
        });
    }
}
