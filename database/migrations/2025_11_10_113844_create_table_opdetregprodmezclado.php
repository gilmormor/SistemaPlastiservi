<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableOpdetregprodmezclado extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('opdetregprodmezclado', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('opdetregprod_id');
            $table->foreign('opdetregprod_id','fk_opdetregprodmezclado_opdetregprod')->references('id')->on('opdetregprod')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('bin_id');
            $table->foreign('bin_id','fk_opdetregprodmezclado_bin')->references('id')->on('bin')->onDelete('restrict')->onUpdate('restrict');
            $table->float('cant',10,2)->comment('Cantidad producida')->nullable();
            $table->float('kg',18,2)->comment('Total Kg producidos')->nullable();
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
        Schema::dropIfExists('opdetregprodmezclado');
    }
}
