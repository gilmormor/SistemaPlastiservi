<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableOpdetregmprodmaq extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('opdetregmprodmaq', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('opdetregprod_id');
            $table->foreign('opdetregprod_id','fk_opdetregmprodmaq_opdetregprod')->references('id')->on('opdetregprod')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('maquina_id');
            $table->foreign('maquina_id','fk_opdetregmprodmaq_maquina')->references('id')->on('maquina')->onDelete('restrict')->onUpdate('restrict');
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
        Schema::dropIfExists('opdetregmprodmaq');
    }
}
