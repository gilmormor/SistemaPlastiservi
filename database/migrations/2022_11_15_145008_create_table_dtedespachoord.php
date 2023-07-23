<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableDtedespachoord extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dtedespachoord', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('dte_id')->nullable();
            $table->foreign('dte_id','fk_dtedespachoord_dte')->references('id')->on('dte')->onDelete('restrict')->onUpdate('restrict')->nullable();
            $table->unsignedBigInteger('despachoord_id')->nullable();
            $table->foreign('despachoord_id','fk_dtedespachoord_despachoord')->references('id')->on('despachoord')->onDelete('restrict')->onUpdate('restrict')->nullable();
            $table->unsignedBigInteger('notaventa_id')->nullable();
            $table->foreign('notaventa_id','fk_dtedespachoord_notaventa')->references('id')->on('notaventa')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('tipoentrega_id');
            $table->foreign('tipoentrega_id','fk_dtedespachoord_tipoentrega')->references('id')->on('tipoentrega')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('comunaentrega_id')->comment('Comuna de entrega');
            $table->foreign('comunaentrega_id','fk_dtedespachoord_comunaentrega')->references('id')->on('comuna')->onDelete('restrict')->onUpdate('restrict');
            $table->string('lugarentrega',100)->comment('Lugar de entrega');

            $table->string('ot',5)->comment('Orden de trabajo')->nullable();
            $table->engine = 'InnoDB';
            $table->timestamps();
            $table->softDeletes();
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
        Schema::dropIfExists('dtedespachoord');
    }
}
