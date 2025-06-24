<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableOp extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('op', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('otdet_id');
            $table->foreign('otdet_id','fk_op_otdet')->references('id')->on('otdet')->onDelete('restrict')->onUpdate('restrict');
            $table->float('cantprod',10,2)->comment('Cantidad de producto produccion');
            $table->float('kgprod',18,2)->comment('Total Kg para produccion')->nullable();
            $table->string('obs',100)->comment('Observacion')->nullable();
            $table->tinyInteger('ordenaten')->comment('Orden de atencion')->unsigned();
            $table->unsignedBigInteger('usuario_id');
            $table->foreign('usuario_id','fk_op_usuario')->references('id')->on('usuario')->onDelete('restrict')->onUpdate('restrict');
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
        Schema::dropIfExists('op');
    }
}
