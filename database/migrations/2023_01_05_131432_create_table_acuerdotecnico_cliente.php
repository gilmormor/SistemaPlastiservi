<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableAcuerdotecnicoCliente extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('acuerdotecnico_cliente', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('acuerdotecnico_id');
            $table->foreign('acuerdotecnico_id','fk_acuerdotecnico_cliente_acuerdotecnico')->references('id')->on('acuerdotecnico')->onDelete('CASCADE')->onUpdate('restrict');
            $table->unsignedBigInteger('cliente_id')->nullable();
            $table->foreign('cliente_id','fk_acuerdotecnico_cliente_cliente')->references('id')->on('cliente')->onDelete('restrict')->onUpdate('restrict');
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
        Schema::dropIfExists('acuerdotecnico_cliente');
    }
}
