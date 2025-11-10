<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableOpdetregprod extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('opdetregprod', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('opdetregprodtemp_id');
            $table->foreign('opdetregprodtemp_id','fk_opdetregprod_opdetregprodtemp')->references('id')->on('opdetregprodtemp')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('opdet_id');
            $table->foreign('opdet_id','fk_opdetregprod_opdet')->references('id')->on('opdet')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('etapaprod_id');
            $table->foreign('etapaprod_id','fk_opdetregprod_etapaprod')->references('id')->on('etapaprod')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('producto_id');
            $table->foreign('producto_id','fk_opdetregprod_producto')->references('id')->on('producto')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('sucursal_id');
            $table->foreign('sucursal_id','fk_opdetregprod_sucursal')->references('id')->on('sucursal')->onDelete('restrict')->onUpdate('restrict');
            $table->float('cant',10,2)->comment('Cantidad producida')->nullable();
            $table->float('kg',18,2)->comment('Total Kg producidos')->nullable();
            $table->float('kgscrap',18,2)->comment('Kilos scrap.')->default(0);
            $table->float('mtslineal',18,2)->comment('Metros lineales.')->default(0);
            $table->string('obs',100)->comment('Observacion')->nullable();
            $table->unsignedBigInteger('operario_id');
            $table->foreign('operario_id','fk_opdetregprod_operario')->references('id')->on('operario')->onDelete('restrict')->onUpdate('restrict');

            $table->boolean('aprobstatus')->comment('Status de aprobacion (null o 0)=Sin aprobar, 1=Aprobado')->nullable();
            $table->unsignedBigInteger('aprobusu_id')->comment('Usuario quien aprobo')->nullable();
            $table->foreign('aprobusu_id','fk_opdetregprod_aprobusu')->references('id')->on('usuario')->onDelete('restrict')->onUpdate('restrict');
            $table->dateTime('aprobfechahora')->comment('fecha y hora cuando fue aprobada.')->nullable();
            $table->string('aprobobs',300)->comment('Observación aprobacion')->nullable();
            $table->unsignedBigInteger('usuario_id');
            $table->foreign('usuario_id','fk_opdetregprod_usuario')->references('id')->on('usuario')->onDelete('restrict')->onUpdate('restrict');
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
        Schema::dropIfExists('opdetregprod');
    }
}
