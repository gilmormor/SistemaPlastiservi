<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableModulo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('modulo', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->string('nombre',50)->comment('Nombre');
            $table->string('desc',100)->comment('Descripcion');
            $table->tinyInteger('stamodapl')->comment('Estatus para saber en el modulo donde aplica. Ejemplo: 0 = Clientes, 1 = Nota de Venta, 2 = Cotizacion, asociado al cliente_id, notaventa_id o cotizacion_id. Esto para filtrar en el caso de desbloqueo de clientes por modulo');
            $table->unsignedBigInteger('usuario_id');
            $table->foreign('usuario_id','fk_modulo_usuario')->references('id')->on('usuario')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('usuariodel_id')->comment('ID Usuario que elimino el registro')->nullable();
            $table->timestamps();
            $table->softDeletes();
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
        Schema::dropIfExists('modulo');
    }
}
