<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableAcuerdotecnicotempcvalatdet extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('acuerdotecnicotempcvalatdet', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->unsignedBigInteger('acuerdotecnicotemp_id');
            $table->foreign('acuerdotecnicotemp_id','fk_acuerdotecnicotempcvalatdet_acuerdotecnico')->references('id')->on('acuerdotecnicotemp')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('cvalatdet_id');
            $table->foreign('cvalatdet_id','fk_acuerdotecnicotempcvalatdet_cvalatdet')->references('id')->on('cvalatdet')->onDelete('restrict')->onUpdate('restrict');
            $table->tinyInteger('valor')->comment('No=0, Si=1')->default(0);
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
        Schema::dropIfExists('acuerdotecnicotempcvalatdet');
    }
}
