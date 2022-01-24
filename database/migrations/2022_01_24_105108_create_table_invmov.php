<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableInvmov extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invmov', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->dateTime('fechahora')->comment('Fecha y hora.');
            $table->string('obs',100)->comment('Observación');
            $table->dateTime('staanul')->comment('Fecha de anulación');
            $table->unsignedBigInteger('invmovmodulo_id');
            $table->foreign('invmovmodulo_id','fk_invmov_invmovmodulo')->references('id')->on('invmovmodulo')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('usuariodel_id')->comment('ID Usuario que elimino el registro')->nullable();
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
        Schema::dropIfExists('invmov');
    }
}
