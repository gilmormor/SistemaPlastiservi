<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCcregistmuestraTable extends Migration
{
    public function up()
    {
        Schema::create('ccregistmuestra', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->unsignedBigInteger('opdetregprod_id')->comment('Registro de producción aprobado muestreado');
            $table->foreign('opdetregprod_id', 'fk_ccregistmuestra_opdetregprod')
                  ->references('id')->on('opdetregprod')
                  ->onDelete('restrict')->onUpdate('restrict');
            $table->dateTime('fechahora')->comment('Fecha y hora del registro de la muestra');
            $table->unsignedBigInteger('usuario_id')->comment('Usuario que hizo el registro de la muestra');
            $table->foreign('usuario_id', 'fk_ccregistmuestra_usuario')
                  ->references('id')->on('usuario')
                  ->onDelete('restrict')->onUpdate('restrict');
            $table->tinyInteger('status')->default(1)->comment('1=Aprobado (verde), 2=Aprobado con observaciones (amarillo), 3=Rechazado (rojo). Calculado automáticamente.');
            $table->text('observacion')->nullable()->comment('Observación general libre');
            $table->boolean('sta_env')->default(0)->comment('0 o Null=Sin enviar, 1=Enviado al siguiente módulo, 2=Aprobado por supervisor, 3=Rechazado por supervisor');
            $table->dateTime('fechahora_env')->nullable()->comment('Fecha y hora cuando fue liberado/enviado al siguiente módulo');
            $table->unsignedBigInteger('usuario_env_id')->nullable()->comment('Usuario que liberó/envió al siguiente módulo');
            $table->foreign('usuario_env_id', 'fk_ccregistmuestra_usuarioenv')
                  ->references('id')->on('usuario')
                  ->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('usuariodel_id')->nullable()->comment('ID Usuario que eliminó el registro');
            $table->timestamps();
            $table->softDeletes();
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_spanish_ci';
        });
    }

    public function down()
    {
        Schema::dropIfExists('ccregistmuestra');
    }
}
