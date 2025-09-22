<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableProduccion extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('produccion', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('opdet_id');
            $table->foreign('opdet_id','fk_produccion_opdet')->references('id')->on('opdet')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('operarioapsuc_id')->nullable();
            $table->foreign('operarioapsuc_id','fk_produccion_operario_areaproduccionsuc')->references('id')->on('operario_areaproduccionsuc')->onDelete('restrict')->onUpdate('restrict');
            $table->float('cantprod',10,2)->comment('Cantidad producido')->nullable();
            $table->float('kgprod',18,2)->comment('Total Kg producidos')->nullable();
            $table->string('obs',100)->comment('Observacion')->nullable();
            $table->boolean('aprobstatus')->comment('Status de aprobacion (null o 0)=Sin aprobar, 1=Aprobado')->nullable();
            $table->unsignedBigInteger('aprobusu_id')->comment('Usuario quien aprobo')->nullable();
            $table->foreign('aprobusu_id','fk_produccion_aprobusu')->references('id')->on('usuario')->onDelete('restrict')->onUpdate('restrict');
            $table->dateTime('aprobfechahora')->comment('fecha y hora cuando fue aprobada.')->nullable();
            $table->string('aprobobs',300)->comment('Observación aprobacion')->nullable();
            $table->unsignedBigInteger('usuario_id');
            $table->foreign('usuario_id','fk_produccion_usuario')->references('id')->on('usuario')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('usuariodel_id')->comment('ID Usuario que elimino el registro')->nullable();
            $table->engine = 'InnoDB';
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
        Schema::dropIfExists('produccion');
    }
}
