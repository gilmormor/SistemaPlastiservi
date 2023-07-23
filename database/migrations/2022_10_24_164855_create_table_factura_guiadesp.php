<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableFacturaGuiadesp extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('factura_guiadesp', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->unsignedBigInteger('factura_id');
            $table->foreign('factura_id','fk_facturaguiadesp_factura')->references('id')->on('factura')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('guiadesp_id');
            $table->foreign('guiadesp_id','fk_facturaguiadesp_guiadesp')->references('id')->on('guiadesp')->onDelete('restrict')->onUpdate('restrict');
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
        Schema::dropIfExists('factura_guiadesp');
    }
}
