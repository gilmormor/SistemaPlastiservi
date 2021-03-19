<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableNotificaciones extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notificaciones', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->unsignedBigInteger('usuarioorigen_id')->comment('Usuario quien creó la notificacion');
            $table->foreign('usuarioorigen_id','fk_notificacion_usuarioorigen')->references('id')->on('usuario')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('usuariodestino_id')->comment('Usuario quien creó la notificacion');
            $table->foreign('usuariodestino_id','fk_notificacion_usuariodestino')->references('id')->on('usuario')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('vendedor_id')->comment('Vendedor a quien va dirigida la Notificacion')->nullable();
            $table->foreign('vendedor_id','fk_notificacion_vendedor')->references('id')->on('vendedor')->onDelete('restrict')->onUpdate('restrict');
            $table->integer('status')->comment('Estatus de notificacion. 1=Activa, 2=Vista.');
            $table->string('nombretabla',30)->comment('Nombre de tabla donde se origino la accion');
            $table->string('mensaje',45)->comment('Mensaje al usuario.');
            $table->string('nombrepantalla',30)->comment('Nombre de pantalla donde se genero la Accion.');
            $table->string('rutaorigen',40)->comment('Ruta origen donde se genero la accion.');
            $table->string('rutadestino',40)->comment('Ruta destino donde debe ir cuando se seleccione la notificacion.');
            $table->unsignedBigInteger('tabla_id')->comment('Valor del campo id donde se genero la accion.');
            $table->string('accion',40)->comment('Es la accion ejecutada por ejemplo: Nota Venta Devuelta a Vendedor, Eliminado, Modificado etc.');
            $table->string('icono',40)->comment('Icono de la notificación.');
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
        Schema::dropIfExists('notificaciones');
    }
}
