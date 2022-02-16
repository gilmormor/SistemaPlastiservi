<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableInvstockmes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invstockmes', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->unsignedBigInteger('invstock_id');
            $table->foreign('invstock_id','fk_invstockmes_invstock')->references('id')->on('invstock')->onDelete('restrict')->onUpdate('restrict');
            $table->char('annomes',6)->comment('Año y mes en formato AAAAMM');
            $table->float('stockini',18,2)->comment('Stock inicial mes. Que debe ser igual al stockfin del mes anterior.');
            $table->float('stockfin',18,2)->comment('Stock final mes, pero tambien seria el stock a la fecha ya que se va ir actualizando con cada movimiento de inv.');
            $table->float('stockinikg',18,2)->comment('Stock inicial Kg mes. Que debe ser igual al stockfin del mes anterior.');
            $table->float('stockfinkg',18,2)->comment('Stock final Kg mes, pero tambien seria el stock a la fecha ya que se va ir actualizando con cada movimiento de inv.');
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
        Schema::dropIfExists('invstockmes');
    }
}
