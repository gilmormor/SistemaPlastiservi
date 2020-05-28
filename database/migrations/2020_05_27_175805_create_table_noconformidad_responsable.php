<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableNoconformidadResponsable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('noconformidad_responsable', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('noconformidad_id');
            $table->foreign('noconformidad_id','fk_noconformidad_responsable_noconformidad')->references('id')->on('noconformidad')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('jefatura_sucursal_area_id');
            $table->foreign('jefatura_sucursal_area_id','fk_noconformidad_responsable_jefatura_sucursal_area')->references('id')->on('jefatura_sucursal_area')->onDelete('restrict')->onUpdate('restrict');
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
        Schema::dropIfExists('noconformidad_responsable');
    }
}
