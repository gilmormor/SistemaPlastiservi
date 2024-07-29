<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableDatacobranza extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('datacobranza', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->unsignedBigInteger('cliente_id');
            $table->foreign('cliente_id','fk_datacobranza_cliente')->references('id')->on('cliente')->onDelete('restrict')->onUpdate('restrict');
            $table->double('tfac',18,2)->comment('Monto Total Facturas por pagar');
            $table->double('tdeuda',18,2)->comment('Monto Total Facturas Deuda');
            $table->double('tdeudafec',18,2)->comment('Monto Total Deuda a la fecha.');
            $table->string('nrofacdeu')->comment('Nro y fecha de facturas vencidas.');
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
        Schema::dropIfExists('datacobranza');
    }
}
