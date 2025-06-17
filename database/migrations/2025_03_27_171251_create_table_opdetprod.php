<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableOpdetprod extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('opdetprod', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('opdet_id');
            $table->foreign('opdet_id','fk_opdetprod_opdet')->references('id')->on('opdet')->onDelete('restrict')->onUpdate('restrict');
            $table->string('obs',100)->comment('Observacion')->nullable();
            $table->string('ip',20)->comment('Ip PC')->nullable();
            $table->float('cant',10,2)->comment('Cantidad o unidades producidas.')->nullable();
            $table->float('kg',18,2)->comment('Kg producidos.')->nullable();
            $table->unsignedBigInteger('usuariodel_id')->comment('ID Usuario que elimino el registro')->nullable();
            $table->engine = 'InnoDB';
            $table->softDeletes();
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
        Schema::dropIfExists('opdetprod');
    }
}
