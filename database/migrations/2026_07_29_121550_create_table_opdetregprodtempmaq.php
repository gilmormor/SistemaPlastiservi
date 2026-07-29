<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableOpdetregprodtempmaq extends Migration
{
    /**
     * Tabla usada por el modelo OpDetRegProdTempMaq (máquina(s) asignadas a un
     * registro temporal de producción, ej. opdetregprodtempaprobsup/index01),
     * pero cuya migración nunca se había creado — solo existía en la BD de
     * desarrollo `biblioteca`, no en el repo. Espejo de create_table_opdetregprodmaq
     * (versión "temp" del registro de producción).
     *
     * @return void
     */
    public function up()
    {
        Schema::create('opdetregprodtempmaq', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('opdetregprodtemp_id');
            $table->foreign('opdetregprodtemp_id', 'fk_opdetregprodtempmaq_opdetregprodtemp')->references('id')->on('opdetregprodtemp')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('maquina_id');
            $table->foreign('maquina_id', 'fk_opdetregprodtempmaq_maquina')->references('id')->on('maquina')->onDelete('restrict')->onUpdate('restrict');
            $table->engine = 'InnoDB';
            $table->timestamps();
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_spanish_ci';
        });
    }

    /**
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('opdetregprodtempmaq');
    }
}
