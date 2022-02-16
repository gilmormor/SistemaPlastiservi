<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableInventsaldet extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inventsaldet', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->unsignedBigInteger('inventsal_id');
            $table->foreign('inventsal_id','fk_inventsaldet_inventsal')->references('id')->on('inventsal')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('invmov_id')->nullable();
            $table->foreign('invmov_id','fk_inventsaldet_invmov')->references('id')->on('invmov')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('invstock_id')->nullable();
            $table->foreign('invstock_id','fk_inventsaldet_invstock')->references('id')->on('invstock')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('producto_id');
            $table->foreign('producto_id','fk_inventsaldet_producto')->references('id')->on('producto')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('invbodega_id');
            $table->foreign('invbodega_id','fk_inventsaldet_invbodega')->references('id')->on('invbodega')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('unidadmedida_id');
            $table->foreign('unidadmedida_id','fk_inventsaldet_unidadmedida')->references('id')->on('unidadmedida')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('invmovtipo_id');
            $table->foreign('invmovtipo_id','fk_inventsaldet_invmovtipo')->references('id')->on('invmovtipo')->onDelete('restrict')->onUpdate('restrict');
            $table->float('cant',18,2)->comment('Cantidad');
            $table->float('cantkg',18,2)->comment('Cantidad en kg');
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
        Schema::dropIfExists('inventsaldet');
    }
}
