<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableAtfirmeli extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('atfirmeli', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('acuerdotecnico_id');
            $table->foreign('acuerdotecnico_id','fk_atfirmeli_acuerdotecnico')->references('id')->on('acuerdotecnico')->onDelete('restrict')->onUpdate('restrict');
            $table->string('at_firmado')->comment('Nombre de archivo de acuerdotecnico firmado eliminado.')->nullable();
            $table->unsignedBigInteger('usuario_id')->comment('Usuario que hizo el cambio');
            $table->foreign('usuario_id','fk_atfirmeli_usuario')->references('id')->on('usuario')->onDelete('restrict')->onUpdate('restrict');
            $table->timestamps();
            $table->engine = 'InnoDB';
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
        Schema::dropIfExists('atfirmeli');
    }
}
