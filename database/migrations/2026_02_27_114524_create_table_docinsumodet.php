<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableDocinsumodet extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('docinsumodet', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('origentipo', 4)->comment('Tipo de documento de origen');
            $table->unsignedBigInteger('origendet_id');
            $table->unsignedBigInteger('insumo_id');
            $table->foreign('insumo_id','fk_docinsumodet_insumo')->references('id')->on('insumo')->onDelete('restrict')->onUpdate('restrict');
            $table->float('cant',12,4)->comment('Cantidad total insumos');
            $table->float('costounitario',10,2)->comment('Costo unitario del insumo');
            $table->unsignedBigInteger('usuario_id')->comment('Id Usuario');
            $table->foreign('usuario_id','fk_docinsumodet_usuario')->references('id')->on('usuario')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('usuariodel_id')->comment('ID Usuario que elimino el registro')->nullable();
            $table->engine = 'InnoDB';
            $table->softDeletes();
            $table->timestamps();
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_spanish_ci';
            $table->index(['origentipo','origendet_id'], 'idx_docinsumodet_origen');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('docinsumodet');
    }
}
