<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTablaJefaturaSucursalAreaPersona extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jefatura_sucursal_area_persona', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('jefatura_sucursal_area_id');
            $table->foreign('jefatura_sucursal_area_id','fk_jefaturaSucursalAreaPersona_jefatura_sucursal_area')->references('id')->on('jefatura_sucursal_area')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('persona_id');
            $table->foreign('persona_id','fk_jefaturaSucursalAreaPersona_persona')->references('id')->on('persona')->onDelete('restrict')->onUpdate('restrict');
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
        Schema::dropIfExists('jefatura_sucursal_area_persona');
    }
}
