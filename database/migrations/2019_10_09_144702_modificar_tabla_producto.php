<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModificarTablaProducto extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('producto', function (Blueprint $table) {
            $table->float('ancho',8,2)->comment('Ancho centimetros')->after('codbarra');
            $table->float('largo',8,2)->comment('Largo centimetros')->after('ancho');
            $table->float('fuelle',8,2)->comment('Fuelle centimetros')->after('largo');
            $table->string('foto',100)->comment('Foto Producto')->after('precioneto')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('producto', function (Blueprint $table) {
            //
        });
    }
}
