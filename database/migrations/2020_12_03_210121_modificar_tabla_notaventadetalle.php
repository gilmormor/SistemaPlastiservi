<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModificarTablaNotaventadetalle extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('notaventadetalle', function (Blueprint $table) {
            $table->float('peso',8,3)->comment('Peso Producto')->nullable()->after('preciounit');
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
