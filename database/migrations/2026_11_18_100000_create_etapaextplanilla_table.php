<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Etapa Externa — PLANILLA entregada al operario.
 *
 * Hay un proceso que hoy se hace fuera del sistema: entre una etapa y la
 * siguiente (tipicamente extrusion/impresion -> sellado) el operario toma un
 * lote ya aprobado y lo procesa a mano, sin computador donde registrar nada.
 * El supervisor le entrega una planilla impresa (formato DP REG.06) con los
 * datos de lo que va a procesar y la etiqueta del lote de origen pegada.
 *
 * Esta tabla guarda cada planilla impresa, para que ese proceso -que hoy se
 * lleva en Excel y sin control alguno- quede trazable.
 *
 * El ancla es el LOTE (opdetregprod_id), no la etapa ni el registro de
 * produccion siguiente: el modulo no toca el flujo de produccion y el dia que
 * este proceso se convierta en una etapa real del sistema se puede apagar sin
 * desenredar nada.
 *
 * La fecha de la planilla es created_at. No lleva turno (ese dato solo aplica
 * a las etiquetas de paquete).
 */
class CreateEtapaextplanillaTable extends Migration
{
    public function up()
    {
        Schema::create('etapaextplanilla', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('opdetregprod_id');          // lote de origen que se va a procesar
            $table->unsignedBigInteger('etapaprod_id');             // etapa en la que se procesa (la siguiente del lote)
            $table->unsignedBigInteger('maquina_id');               // maquina asignada por el supervisor para procesar
            $table->unsignedBigInteger('operario_id');              // responsable del equipo
            $table->double('nrobolsas', 18, 2)->nullable();         // N Bolsas: lo escribe el supervisor
            $table->string('obs', 300)->nullable();
            $table->unsignedBigInteger('usuario_id');               // quien imprimio
            $table->unsignedBigInteger('usuariodel_id')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('opdetregprod_id', 'fk_etapaextpla_lote')
                  ->references('id')->on('opdetregprod');
            $table->foreign('etapaprod_id', 'fk_etapaextpla_etapa')
                  ->references('id')->on('etapaprod');
            $table->foreign('maquina_id', 'fk_etapaextpla_maquina')
                  ->references('id')->on('maquina');
            $table->foreign('operario_id', 'fk_etapaextpla_operario')
                  ->references('id')->on('operario');
            $table->foreign('usuario_id', 'fk_etapaextpla_usuario')
                  ->references('id')->on('usuario');
        });
    }

    public function down()
    {
        Schema::dropIfExists('etapaextplanilla');
    }
}
