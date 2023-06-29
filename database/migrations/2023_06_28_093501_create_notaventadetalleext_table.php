<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotaventadetalleextTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notaventadetalleext', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->unsignedBigInteger('notaventadetalle_id');
            $table->foreign('notaventadetalle_id','fk_notaventadetalleext_notaventadetalle')->references('id')->on('notaventadetalle')->onDelete('restrict')->onUpdate('restrict');
            $table->float('cantext',10,2)->comment('Cantidad extra que se va a despachar por sobrante de produccion o por sobre produccion.')->nullable();
            $table->timestamps();
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_spanish_ci';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notaventadetalleext');
    }
}
