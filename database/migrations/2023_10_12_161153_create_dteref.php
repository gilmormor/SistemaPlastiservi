<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDteref extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dteref', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('dte_id');
            $table->foreign('dte_id','fk_dteref_dte')->references('id')->on('dte')->onDelete('restrict')->onUpdate('restrict');
            $table->date('fchref')->comment('Fecha del documento')->nullable();
            $table->unsignedBigInteger('ref_id');
            $table->foreign('ref_id','fk_dteref_ref')->references('id')->on('ref')->onDelete('restrict')->onUpdate('restrict');
            $table->string('folioref',18)->comment('Folio Referencia')->nullable();
            $table->string('razonref',90)->comment('Razon')->nullable();
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
        Schema::dropIfExists('dteref');
    }
}
