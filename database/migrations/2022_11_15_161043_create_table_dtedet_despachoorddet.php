<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableDtedetDespachoorddet extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dtedet_despachoorddet', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('dtedet_id');
            $table->foreign('dtedet_id','fk_dtedet_despachoorddet_dtedet')->references('id')->on('dtedet')->onDelete('CASCADE')->onUpdate('restrict');
            $table->unsignedBigInteger('despachoorddet_id')->nullable();
            $table->foreign('despachoorddet_id','fk_dtedet_despachoorddet_despachoorddet')->references('id')->on('despachoorddet')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('notaventadetalle_id')->nullable();
            $table->foreign('notaventadetalle_id','fk_dtedet_despachoorddet_notaventadetalle')->references('id')->on('notaventadetalle')->onDelete('restrict')->onUpdate('restrict');
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
        Schema::dropIfExists('dtedet_despachoorddet');
    }
}
