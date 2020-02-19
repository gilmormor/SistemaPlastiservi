<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CrearTablaJefaturaSucursalArea extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jefatura_sucursal_area', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->unsignedBigInteger('sucursal_area_id');
            $table->foreign('sucursal_area_id','fk_jefaturasucursalarea_sucursalarea')->references('id')->on('sucursal_area')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('jefatura_id');
            $table->foreign('jefatura_id','fk_jefaturasucursalarea_jefatura')->references('id')->on('jefatura')->onDelete('restrict')->onUpdate('restrict');
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
        Schema::dropIfExists('jefatura_sucursal_area');
    }
}
