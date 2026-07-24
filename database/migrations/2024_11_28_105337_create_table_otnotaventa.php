<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableOtnotaventa extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('otnotaventa', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('ot_id');
            $table->foreign('ot_id','fk_otnotaventa_ot')->references('id')->on('ot')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('notaventa_id');
            $table->foreign('notaventa_id','fk_otnotaventa_notaventa')->references('id')->on('notaventa')->onDelete('restrict')->onUpdate('restrict');
            $table->engine = 'InnoDB';
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
        Schema::dropIfExists('otnotaventa');
    }
}
