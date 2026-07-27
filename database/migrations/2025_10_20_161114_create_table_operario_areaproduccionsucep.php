<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableOperarioAreaproduccionsucep extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('operario_areaproduccionsucep', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('operario_id');
            $table->foreign('operario_id','fk_operario_areaproduccionsucep_areaproduccion')->references('id')->on('operario')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('areaproduccionsucep_id');
            $table->foreign('areaproduccionsucep_id','fk_operario_areaproduccionsucep_areaproduccionsucep')->references('id')->on('areaproduccionsucetapaprod')->onDelete('restrict')->onUpdate('restrict');
            $table->timestamps();
            $table->engine = 'InnoDB';
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
        Schema::dropIfExists('operario_areaproduccionsucep');
    }
}
