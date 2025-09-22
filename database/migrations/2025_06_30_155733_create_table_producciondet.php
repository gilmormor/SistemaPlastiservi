<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableProducciondet extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('producciondet', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('produccion_id');
            $table->foreign('produccion_id','fk_producciondet_produccion')->references('id')->on('produccion')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('opdet_id');
            $table->foreign('opdet_id','fk_producciondet_opdet')->references('id')->on('opdet')->onDelete('restrict')->onUpdate('restrict');
            $table->float('cant',10,2)->comment('Cantidad producido')->nullable();
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
        Schema::dropIfExists('producciondet');
    }
}
