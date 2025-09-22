<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableOtoc extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('otoc', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('ot_id');
            $table->foreign('ot_id','fk_otoc_ot')->references('id')->on('ot')->onDelete('restrict')->onUpdate('restrict');
            $table->string('oc_id',18)->comment('Numero Orden de Compra')->nullable();
            $table->string('oc_file',20)->comment('Archivo o imagen de Orden de Compra')->nullable();
            $table->engine = 'InnoDB';
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
        Schema::dropIfExists('otoc');
    }
}
