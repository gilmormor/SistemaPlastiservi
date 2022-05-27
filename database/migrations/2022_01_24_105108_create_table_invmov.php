<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableInvmov extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invmov', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->dateTime('fechahora')->comment('Fecha y hora.');
            $table->char('annomes',6)->comment('Año y mes en formato AAAAMM');
            $table->string('desc',300)->comment('Descripción');
            $table->string('obs',300)->comment('Observación');
            $table->dateTime('staanul')->comment('Fecha de anulación')->nullable();
            $table->unsignedBigInteger('invmovmodulo_id');
            $table->foreign('invmovmodulo_id','fk_invmov_invmovmodulo')->references('id')->on('invmovmodulo')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('idmovmod')->comment('Id origen de Movimiento. Ej: Id de tabla despachoord o moventsal.')->nullable();
            $table->unsignedBigInteger('invmovtipo_id');
            $table->foreign('invmovtipo_id','fk_invmov_invmovtipo')->references('id')->on('invmovtipo')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('sucursal_id');
            $table->foreign('sucursal_id','fk_invmov_sucursal')->references('id')->on('sucursal')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('usuario_id')->comment('Usuario quien creo el registro');
            $table->foreign('usuario_id','fk_invmov_usuario')->references('id')->on('usuario')->onDelete('restrict')->onUpdate('restrict');
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
        Schema::dropIfExists('invmov');
    }
}
