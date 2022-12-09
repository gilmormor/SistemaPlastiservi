<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableDtefac extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dtefac', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('dte_id');
            $table->foreign('dte_id','fk_dtefac_dte')->references('id')->on('dte')->onDelete('restrict')->onUpdate('restrict');
            $table->string('hep',12)->comment('Numero de atencion o Hep')->nullable();
            $table->unsignedBigInteger('formapago_id')->comment('Id Forma de pago y TermPagoGlosa valor que se incluye en el DTE PDF');
            $table->foreign('formapago_id','fk_dtefac_formapago')->references('id')->on('formapago')->onDelete('restrict')->onUpdate('restrict');
            $table->date('fchvenc')->comment('Vecha de vencimiento.')->nullable();
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
        Schema::dropIfExists('dtefac');
    }
}
