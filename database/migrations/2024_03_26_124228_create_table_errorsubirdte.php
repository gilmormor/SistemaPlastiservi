<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableErrorsubirdte extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('errorsubirdte', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->unsignedBigInteger('dte_id');
            $table->foreign('dte_id','fk_errorsubirdte_dte')->references('id')->on('dte')->onDelete('restrict')->onUpdate('restrict');
            $table->boolean('stasubsii')->comment('Status registro fue subido a Sii')->default(0);
            $table->string('errorsii')->comment('Error generado Sii.');
            $table->string('xmlsii')->comment('XML Sii.');
            $table->boolean('stasubcob')->comment('Status registro fue subido a Sistema Contabilidad Cobranza')->default(0);
            $table->string('errorcob')->comment('Error generado cob.');
            $table->string('xmlcob')->comment('XML Sistema cobranza.');
            $table->unsignedBigInteger('usuario_id')->comment('Usuario creó el registro');
            $table->foreign('usuario_id','fk_errorsubirdte_usuario')->references('id')->on('usuario')->onDelete('restrict')->onUpdate('restrict');
            $table->timestamps();
            $table->softDeletes();
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
        Schema::dropIfExists('errorsubirdte');
    }
}
