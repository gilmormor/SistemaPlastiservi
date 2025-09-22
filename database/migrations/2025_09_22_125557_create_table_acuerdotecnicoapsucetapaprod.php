<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableAcuerdotecnicoapsucetapaprod extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('acuerdotecnicoapsucetapaprod', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('acuerdotecnico_id');
            $table->foreign('acuerdotecnico_id','fk_acuerdotecnicoapsucetapaprod_acuerdotecnico')->references('id')->on('acuerdotecnico')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('apsucetapaprod_id');
            $table->foreign('apsucetapaprod_id','fk_acuerdotecnicoapsucetapaprod_apsucetapaprod')->references('id')->on('areaproduccionsucetapaprod')->onDelete('restrict')->onUpdate('restrict');
            $table->string('obs',300)->comment('Observacion')->nullable();
            $table->engine = 'InnoDB';
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
        Schema::dropIfExists('acuerdotecnicoapsucetapaprod');
    }
}
