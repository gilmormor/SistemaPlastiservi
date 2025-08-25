<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableAcuerdotecnicocvalatdet extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('acuerdotecnicocvalatdet', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->unsignedBigInteger('acuerdotecnico_id');
            $table->foreign('acuerdotecnico_id','fk_acuerdotecnicocvalatdet_acuerdotecnico')->references('id')->on('acuerdotecnico')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('cvalatdet_id');
            $table->foreign('cvalatdet_id','fk_acuerdotecnicocvalatdet_cvalatdet')->references('id')->on('cvalatdet')->onDelete('restrict')->onUpdate('restrict');
            $table->string('valor')->comment('No=0, Si=1. Cuarquier valor string.');
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
        Schema::dropIfExists('acuerdotecnicocvalatdet');
    }
}
