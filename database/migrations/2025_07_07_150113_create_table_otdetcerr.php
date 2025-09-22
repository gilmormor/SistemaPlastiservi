<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableOtdetcerr extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('otdetcerr', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('otdet_id');
            $table->foreign('otdet_id','fk_otdetcerr_otdet')->references('id')->on('otdet')->onDelete('restrict')->onUpdate('restrict');
            $table->string('obs',100)->comment('Observacion')->nullable();
            $table->tinyInteger('tipo')->comment('1=Cerrado al programar o al enviar a OP, 2=Cerrado directamente sin hacer programacion o envio a OP.')->default(0);
            $table->unsignedBigInteger('usuario_id')->comment('Usuario que creo el registro');
            $table->foreign('usuario_id','fk_otdetcerr_usuario')->references('id')->on('usuario')->onDelete('restrict')->onUpdate('restrict');
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
        Schema::drotdetIfExists('otdetcerr');
    }
}
