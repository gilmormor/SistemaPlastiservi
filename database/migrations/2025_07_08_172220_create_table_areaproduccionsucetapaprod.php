<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableAreaproduccionsucetapaprod extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('areaproduccionsucetapaprod', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('areaproduccionsuc_id');
            $table->foreign('areaproduccionsuc_id','fk_areaproduccionsucetapaprod_areaproduccion')->references('id')->on('areaproduccion')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('etapaprod_id');
            $table->foreign('etapaprod_id','fk_areaproduccionsucetapaprod_etapaprod')->references('id')->on('etapaprod')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedInteger('orden')->comment('Orden en que se ejecuta la etapa de produccion. 2 o mas Etapas se pueden ejecutar al mismo tiempo. es decir que van a llevar el mismo orden por ejemplo Impresion = 2, Sellado = 3.')->default(0);
            $table->engine = 'InnoDB';
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
        Schema::dropIfExists('areaproduccionsucetapaprod');
    }
}
