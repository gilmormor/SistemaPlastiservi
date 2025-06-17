<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableOpdet extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('opdet', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('op_id');
            $table->foreign('op_id','fk_opdet_op')->references('id')->on('op')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('otdet_id');
            $table->foreign('otdet_id','fk_opdet_otdet')->references('id')->on('otdet')->onDelete('restrict')->onUpdate('restrict');
            $table->string('obs',100)->comment('Observacion')->nullable();
            $table->unsignedBigInteger('maquina_id');
            $table->foreign('maquina_id','fk_opdet_maquina')->references('id')->on('maquina')->onDelete('restrict')->onUpdate('restrict');
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
        Schema::dropIfExists('opdet');
    }
}
