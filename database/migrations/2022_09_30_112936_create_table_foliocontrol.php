<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableFoliocontrol extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('foliocontrol', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string("doc",4)->comment("Documento");
            $table->string("desc",50)->comment("Descripción");
            $table->unsignedBigInteger('ultfoliouti')->comment("Ultimo folio Utilizado");
            $table->unsignedBigInteger('ultfoliohab')->comment("Ultimo folio Habilitado");
            $table->integer('activo')->comment("Estatus Activo: 1=Activo 0=Inactivo");
            $table->tinyInteger('bloqueo')->comment('Estatus para bloquear el registro mientras es editado. No=0, Si=1')->default(0);
            $table->unsignedBigInteger('usuario_id')->comment('Usuario quien creo el registro');
            $table->foreign('usuario_id','fk_foliocontrol_usuario')->references('id')->on('usuario')->onDelete('restrict')->onUpdate('restrict');
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
        Schema::dropIfExists('foliocontrol');
    }
}
