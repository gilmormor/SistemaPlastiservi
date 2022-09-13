<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableAprobstatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('aprobstatus', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->string('desc',50)->comment('Descripción')->nullable();
            $table->tinyInteger('ubistatus')->comment('Ubicacion status. 1=Aprobado pasa a siguiente fase (Cotizacion o Nota Venta), 2=Rechazado (Se devuelve a Bandeja Cotizacion o Nota de Venta), 3=Transito (Cae en Bandeja de documnentos en transito.)');
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
        Schema::dropIfExists('aprobstatus');
    }
}
