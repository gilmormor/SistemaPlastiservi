<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Etapa Externa — ETIQUETAS de paquete.
 *
 * El operario que procesa el lote arma N paquetes y le pega una etiqueta a
 * cada uno. El supervisor manda a imprimir la tirada completa antes de que el
 * trabajo empiece (las etiquetas son todas iguales).
 *
 * UNA FILA POR TIRADA, no una por etiqueta: si manda imprimir 50 etiquetas es
 * un registro con cantidad=50. Si despues necesita 30 mas, es otro registro.
 * Eso incluye las reimpresiones posteriores al registro de produccion de la
 * etapa siguiente, que se hacen desde la misma pantalla escribiendo el lote.
 *
 * etapaextplanilla_id es nullable a proposito: lo normal es imprimir primero
 * la planilla y despues las etiquetas desde ella (heredando maquina y
 * operario, que el supervisor igual puede cambiar), pero no se obliga. Si se
 * bloquearan las etiquetas hasta que exista planilla, un dia sin impresora
 * frenaria la produccion y volverian al Excel.
 *
 * kilos y unidades son POR PAQUETE, no el total del lote.
 */
class CreateEtapaextetiquetaTable extends Migration
{
    public function up()
    {
        Schema::create('etapaextetiqueta', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('etapaextplanilla_id')->nullable(); // planilla de la que salio, si la hubo
            $table->unsignedBigInteger('opdetregprod_id');                 // lote de origen
            $table->unsignedBigInteger('etapaprod_id');                    // etapa en la que se procesa
            $table->unsignedBigInteger('maquina_id');
            $table->unsignedBigInteger('operario_id');
            $table->date('fecha');                                         // fecha de produccion (la digita el supervisor)
            $table->string('turno', 20)->nullable();
            $table->double('kilos', 18, 2)->nullable();                    // kilos POR PAQUETE
            $table->double('unidades', 18, 2)->nullable();                 // unidades POR PAQUETE
            $table->string('obs', 300)->nullable();
            $table->integer('cantidad');                                   // copias de la etiqueta a imprimir
            $table->unsignedBigInteger('usuario_id');
            $table->unsignedBigInteger('usuariodel_id')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('etapaextplanilla_id', 'fk_etapaexteti_planilla')
                  ->references('id')->on('etapaextplanilla');
            $table->foreign('opdetregprod_id', 'fk_etapaexteti_lote')
                  ->references('id')->on('opdetregprod');
            $table->foreign('etapaprod_id', 'fk_etapaexteti_etapa')
                  ->references('id')->on('etapaprod');
            $table->foreign('maquina_id', 'fk_etapaexteti_maquina')
                  ->references('id')->on('maquina');
            $table->foreign('operario_id', 'fk_etapaexteti_operario')
                  ->references('id')->on('operario');
            $table->foreign('usuario_id', 'fk_etapaexteti_usuario')
                  ->references('id')->on('usuario');
        });
    }

    public function down()
    {
        Schema::dropIfExists('etapaextetiqueta');
    }
}
