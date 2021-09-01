<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableMateriaprima extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('materiaprima', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string("nombre",60)->comment("Nombre");
            $table->string("desc",100)->comment("Descripcion");
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
        Schema::dropIfExists('materiaprima');
    }
}
