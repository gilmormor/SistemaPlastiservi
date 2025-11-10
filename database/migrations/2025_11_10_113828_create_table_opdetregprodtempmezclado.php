<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableOpdetregprodtempmezclado extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('opdetregprodtempmezclado', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('opdetregprodtemp_id');
            $table->foreign('opdetregprodtemp_id','fk_opdetregprodtempmezclado_opdetregprodtemp')->references('id')->on('opdetregprodtemp')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('bin_id');
            $table->foreign('bin_id','fk_opdetregprodtempmezclado_bin')->references('id')->on('bin')->onDelete('restrict')->onUpdate('restrict');
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
        Schema::dropIfExists('opdetregprodtempmezclado');
    }
}
