<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableEmailxlotePersona extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('emailxlote_persona', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('emailxlote_id');
            $table->foreign('emailxlote_id','fk_emailxlote_persona_emailxlote')->references('id')->on('dte')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('persona_id')->nullable();
            $table->foreign('persona_id','fk_emailxlote_persona_persona')->references('id')->on('persona')->onDelete('restrict')->onUpdate('restrict');
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
        Schema::dropIfExists('emailxlote_persona');
    }
}
