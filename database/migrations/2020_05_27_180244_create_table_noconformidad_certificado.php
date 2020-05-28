<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableNoconformidadCertificado extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('noconformidad_certificado', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('noconformidad_id');
            $table->foreign('noconformidad_id','fk_noconformidad_certificado_noconformidad')->references('id')->on('noconformidad')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('certificado_id');
            $table->foreign('certificado_id','fk_noconformidad_certificado_certificado')->references('id')->on('certificado')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('usuariodel_id')->comment('ID Usuario que elimino el registro')->nullable();
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
        Schema::dropIfExists('noconformidad_certificado');
    }
}
