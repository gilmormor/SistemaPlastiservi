<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * RESERVA DE STOCK — apartar sin mover.
 *
 * Hoy el unico modo de apartar stock es el PICKING, que es una reserva dura: el
 * stock sale de la bodega de venta (tipo 2) y entra a la de picking (tipo 1), y
 * por eso deja de estar disponible. Funciona bien, pero exige que exista una
 * nota de venta con el item ya cargado.
 *
 * Eso deja tres casos sin cubrir:
 *   - Stock que entro por una OT libre (produccion para stock, sin NV).
 *   - Stock que entro por una entrada de inventario y se quiere apartar para un
 *     cliente, antes de que haya nota de venta.
 *   - Un item de NV marcado "requiere fabricacion" que se quiere cubrir con
 *     stock que ya esta en bodega, sin esperar a que la OT termine.
 *
 * Esta tabla es el paso ANTERIOR al picking: una reserva blanda. No mueve
 * inventario; solo declara que ese stock tiene dueno. El flujo completo queda:
 *
 *     Stock libre -> RESERVA (esta tabla) -> Picking (dura) -> Despacho -> Guia
 *
 * NO reemplaza al picking ni compite con el. Cuando el stock pasa a picking, la
 * reserva se cierra (consumida_at) y el compromiso vuelve a ser fisico: asi
 * nunca se cuenta dos veces.
 */
class CreateStockreservaTable extends Migration
{
    public function up()
    {
        Schema::create('stockreserva', function (Blueprint $table) {
            $table->bigIncrements('id');

            /* DONDE esta el stock. invbodegaproducto es bodega + producto, que es
               la unidad con la que trabaja todo el inventario. Es obligatorio
               incluso cuando se reserva un lote: un lote vive en una bodega. */
            $table->unsignedBigInteger('invbodegaproducto_id');

            /* QUE lote, cuando lo tiene. El stock de una OT libre o de produccion
               esta identificado por lote; el que entra por carga manual de
               inventario no, y por eso esto es opcional. Con lote se aparta ESE
               rollo; sin lote, una cantidad del producto en esa bodega. */
            $table->unsignedBigInteger('opdetregprod_id')->nullable();

            /* PARA QUIEN. Al menos uno de los dos: cliente para apartar antes de
               que exista la nota de venta, notaventadetalle cuando ya existe el
               item. Pueden ir los dos a la vez. */
            $table->unsignedBigInteger('cliente_id')->nullable();
            $table->unsignedBigInteger('notaventadetalle_id')->nullable();

            /* CUANTO. cant en la unidad del producto; cantkg informativo, para
               cuadrar con los reportes que totalizan en kilos. */
            $table->double('cant', 18, 2);
            $table->double('cantkg', 18, 2)->nullable();

            /* HASTA CUANDO. Obligatorio por decision del usuario: sin fecha tope,
               en un ano la mitad del inventario queda "reservado" por notas de
               venta que nadie recuerda. Vencida, la reserva deja de descontar del
               stock libre, pero la fila se conserva. */
            $table->date('fechavence');

            $table->string('motivo', 300)->nullable();

            $table->unsignedBigInteger('usuario_id');               // quien reservo

            /* Liberacion manual. No se borra la fila: si manana alguien pregunta
               por que el martes no habia stock, tiene que poder responderse. */
            $table->timestamp('anulada_at')->nullable();
            $table->unsignedBigInteger('anulusuario_id')->nullable();
            $table->string('anulmotivo', 300)->nullable();

            /* La reserva se cumplio: el stock paso a picking o se despacho. Se
               deja preparado desde ahora aunque el consumo automatico sea una
               segunda etapa, para no migrar la tabla despues. */
            $table->timestamp('consumida_at')->nullable();

            $table->unsignedBigInteger('usuariodel_id')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('invbodegaproducto_id')->references('id')->on('invbodegaproducto');
            $table->foreign('opdetregprod_id')->references('id')->on('opdetregprod');
            $table->foreign('cliente_id')->references('id')->on('cliente');
            $table->foreign('notaventadetalle_id')->references('id')->on('notaventadetalle');
            $table->foreign('usuario_id')->references('id')->on('usuario');

            /* El indice que manda: el calculo de stock libre pregunta siempre
               "que reservas vigentes tiene esta bodega-producto". */
            $table->index(['invbodegaproducto_id', 'fechavence'], 'stockreserva_ibp_vence_idx');
            $table->index('opdetregprod_id', 'stockreserva_lote_idx');
            $table->index('cliente_id', 'stockreserva_cliente_idx');
            $table->index('notaventadetalle_id', 'stockreserva_nvdet_idx');
        });
    }

    public function down()
    {
        Schema::dropIfExists('stockreserva');
    }
}
