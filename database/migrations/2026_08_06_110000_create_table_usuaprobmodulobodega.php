<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableUsuaprobmodulobodega extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('usuaprobmodulobodega', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->unsignedBigInteger('usuaprobmodulo_id');
            $table->foreign('usuaprobmodulo_id','fk_usuaprobmodulobodega_usuaprobmodulo')->references('id')->on('usuaprobmodulo')->onDelete('cascade')->onUpdate('restrict');
            $table->unsignedBigInteger('invbodega_id')->comment('Bodega que el usuario restringido puede aprobar (solo aplica a inventsalaprobar)');
            $table->foreign('invbodega_id','fk_usuaprobmodulobodega_invbodega')->references('id')->on('invbodega')->onDelete('restrict')->onUpdate('restrict');
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
        Schema::dropIfExists('usuaprobmodulobodega');
    }
}
