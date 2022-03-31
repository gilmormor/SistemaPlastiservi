<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableInventsal extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inventsal', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->unsignedBigInteger('invmov_id')->nullable();
            $table->foreign('invmov_id','fk_inventsal_invmov')->references('id')->on('invmov')->onDelete('restrict')->onUpdate('restrict');
            $table->dateTime('fechahora')->comment('Fecha y hora.');
            $table->char('annomes',6)->comment('Año y mes en formato AAAAMM');
            $table->string('desc',300)->comment('Descripción');
            $table->string('obs',300)->comment('Observación');
            $table->dateTime('staanul')->comment('Fecha de anulación')->nullable();
            $table->unsignedBigInteger('invmovmodulo_id');
            $table->foreign('invmovmodulo_id','fk_inventsal_invmovmodulo')->references('id')->on('invmovmodulo')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('invmovtipo_id');
            $table->foreign('invmovtipo_id','fk_inventsal_invmovtipo')->references('id')->on('invmovtipo')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('sucursal_id');
            $table->foreign('sucursal_id','fk_inventsal_sucursal')->references('id')->on('sucursal')->onDelete('restrict')->onUpdate('restrict');
            $table->tinyInteger('staaprob')->comment('Status aprobacion. null o 0 = Sin aprobar, 1 = Enviado para aprobacion, 2 = Aprobado Los registros pasaron a tabla invmov invmovdet.')->nullable();
            $table->dateTime('fechahoraaprob')->comment('Fecha y hora de aprobacion.')->nullable();
            $table->unsignedBigInteger('usuario_id')->comment('Usuario quien creo el registro');
            $table->foreign('usuario_id','fk_inventsal_usuario')->references('id')->on('usuario')->onDelete('restrict')->onUpdate('restrict');
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
        Schema::dropIfExists('inventsal');
    }
}
