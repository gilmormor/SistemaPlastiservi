<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableCvalatdet extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cvalatdet', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->unsignedBigInteger('cvalat_id');
            $table->foreign('cvalat_id','fk_cvalatdet_cvalat')->references('id')->on('cvalat')->onDelete('restrict')->onUpdate('restrict');
            $table->string('nombre',50)->comment('Nombre');
            $table->string('desc',100)->comment('Descripcion');
            $table->tinyInteger('orden')->comment('Orden de registros.')->default(0);
            $table->unsignedBigInteger('usuario_id');
            $table->foreign('usuario_id','fk_cvalatdet_usuario')->references('id')->on('usuario')->onDelete('restrict')->onUpdate('restrict');
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
        Schema::dropIfExists('cvalatdet');
    }
}
