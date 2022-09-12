<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableAcuerdotecnicoCertificado extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('acuerdotecnico_certificado', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('acuerdotecnico_id');
            $table->foreign('acuerdotecnico_id','fk_acuerdotecnicocertificado_acuerdotecnico')->references('id')->on('acuerdotecnico')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('certificado_id');
            $table->foreign('certificado_id','fk_acuerdotecnicocertificado_certificado')->references('id')->on('certificado')->onDelete('restrict')->onUpdate('restrict');
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
        Schema::dropIfExists('acuerdotecnico_certificado');
    }
}
