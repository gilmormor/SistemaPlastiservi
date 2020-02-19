<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTablaMatfabr extends Migration
{
    /**
     * Run the migrations.
     * Material de Fabricacion, esta tabla se usa en el Acuerdo Técnico
     * @return void
     */
    public function up()
    {
        Schema::create('matfabr', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string("nombre",60)->comment("Nombre");
            $table->string("descripcion",100)->comment("Descripcion");
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
        Schema::dropIfExists('matfabr');
    }
}
