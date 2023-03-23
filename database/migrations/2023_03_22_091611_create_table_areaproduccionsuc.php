<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableAreaproduccionsuc extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('areaproduccionsuc', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->unsignedBigInteger('areaproduccion_id');
            $table->foreign('areaproduccion_id','fk_areaproduccionsuc_areaproduccion')->references('id')->on('areaproduccion')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('sucursal_id');
            $table->foreign('sucursal_id','fk_areaproduccionsuc_sucursal')->references('id')->on('sucursal')->onDelete('restrict')->onUpdate('restrict');
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
        Schema::dropIfExists('areaproduccionsuc');
    }
}
