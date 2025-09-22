<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableOperarioAreaproduccionsuc extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('operario_areaproduccionsuc', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('operario_id');
            $table->foreign('operario_id','fk_operario_areaproduccionsuc_areaproduccion')->references('id')->on('operario')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('areaproduccionsuc_id');
            $table->foreign('areaproduccionsuc_id','fk_operario_areaproduccionsuc_areaproduccionsuc')->references('id')->on('areaproduccionsuc')->onDelete('restrict')->onUpdate('restrict');
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
        Schema::dropIfExists('operario_areaproduccionsuc');
    }
}
