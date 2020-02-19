<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CrearTablaSucursal extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sucursal', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->string('nombre', 100);
            $table->string('direccion', 250);
            $table->string('telefono1', 14);
            $table->string('telefono2', 14)->nullable();
            $table->string('telefono3', 14)->nullable();
            $table->string('email', 50)->nullable();
            $table->unsignedBigInteger('region_id')->after('nombre');
            $table->foreign('region_id','fk_sucursal_region')->references('id')->on('region')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('provincia_id')->after('region_id');
            $table->foreign('provincia_id','fk_sucursal_provincia')->references('id')->on('provincia')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('comuna_id')->after('provincia_id');
            $table->foreign('comuna_id','fk_sucursal_comuna')->references('id')->on('comuna')->onDelete('restrict')->onUpdate('restrict');
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
        Schema::dropIfExists('sucursal');
    }
}
