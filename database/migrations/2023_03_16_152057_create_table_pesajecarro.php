<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTablePesajecarro extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pesajecarro', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->unsignedBigInteger('sucursal_id');
            $table->foreign('sucursal_id','fk_pesajecarro_sucursal')->references('id')->on('sucursal')->onDelete('restrict')->onUpdate('restrict');
            $table->string('nombre',50)->comment('Nombre');
            $table->string('obs',50)->comment('Observacion')->nullable();
            $table->float('tara',18,2)->comment('Tara, peso del carro');
            $table->tinyInteger('activo')->default(1)->comment('Estatus activo o inactivo.');
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
        Schema::dropIfExists('pesajecarro');
    }
}
