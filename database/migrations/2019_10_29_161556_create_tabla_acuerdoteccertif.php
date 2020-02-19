<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTablaAcuerdoteccertif extends Migration
{
    /**
     * Run the migrations.
     * Tabla Intermadia entre acuerdo Tecnico y certificados
     * de muchos a muchos
     * @return void
     */
    public function up()
    {
        Schema::create('acuerdotectemp_certificado', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('acuerdotecnicotemp_id');
            $table->foreign('acuerdotecnicotemp_id','fk_acuerdotectemp_certificado_acuerdotectemp')->references('id')->on('acuerdotectemp')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('certificado_id');
            $table->foreign('certificado_id','fk_acuerdotectemp_certificado_certificado')->references('id')->on('certificado')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('usuariodel_id')->comment('ID Usuario que elimino el registro')->nullable();
            $table->softDeletes();
            $table->timestamps();
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_spanish_ci';
            $table->engine = 'InnoDB';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('acuerdotectemp_certificado');
    }
}
