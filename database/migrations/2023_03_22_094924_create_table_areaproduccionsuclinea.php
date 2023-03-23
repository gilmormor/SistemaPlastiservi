<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableAreaproduccionsuclinea extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('areaproduccionsuclinea', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->unsignedBigInteger('areaproduccionsuc_id')->nullable();
            $table->foreign('areaproduccionsuc_id','fk_areaproduccionsuclinea_areaproduccionsuc')->references('id')->on('areaproduccionsuc')->onDelete('restrict')->onUpdate('restrict');
            $table->string('nombre',20)->comment('Nombre');
            $table->string('desc',50)->comment('Descripcion')->nullable();
            $table->string('obs',50)->comment('Observacion')->nullable();
            $table->tinyInteger('activo')->default(1)->comment('Estatus activo o inactivo');
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
        Schema::dropIfExists('areaproduccionsuclinea');
    }
}
