<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableAcuerdotecnicotempCertificado extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('acuerdotecnicotemp_certificado', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('acuerdotecnicotemp_id');
            $table->foreign('acuerdotecnicotemp_id','fk_acuerdotecnicotempcertificado_acuerdotecnicotemp')->references('id')->on('acuerdotecnicotemp')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('certificado_id');
            $table->foreign('certificado_id','fk_acuerdotecnicotempcertificado_certificado')->references('id')->on('certificado')->onDelete('restrict')->onUpdate('restrict');
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
        Schema::dropIfExists('acuerdotecnicotemp_certificado');
    }
}
