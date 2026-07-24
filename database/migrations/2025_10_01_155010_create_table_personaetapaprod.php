<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTablePersonaetapaprod extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('personaetapaprod', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('persona_id');
            $table->foreign('persona_id','fk_personaetapaprod_persona')->references('id')->on('persona')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('areaproduccionsucetapaprod_id')->comment('Id areaproduccionsucetapaprod.');
            $table->foreign('areaproduccionsucetapaprod_id','fk_personaetapaprod_areaproduccionsucetapaprod')->references('id')->on('areaproduccionsucetapaprod')->onDelete('restrict')->onUpdate('restrict');
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
        Schema::dropIfExists('personaetapaprod');
    }
}
