<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * REASIGNACION DE PRODUCCION — un lote fabricado para una nota de venta se
 * entrega a otro cliente o a otra nota de venta.
 *
 * Pasa en la practica: se fabrica para un pedido y, antes de despacharlo, otro
 * cliente lo necesita con urgencia. Hasta ahora el sistema no lo permitia y se
 * resolvia por fuera, con lo que el sistema terminaba diciendo algo que no era
 * verdad.
 *
 * LA REGLA DE FONDO: nada se borra, se suma un hecho nuevo.
 *
 * El lote se fabrico para la nota de venta que lo origino, y eso sigue siendo
 * cierto para siempre: la OT, la OP y el registro de produccion nacieron de ese
 * pedido, y su vinculo (invmovdet_opdetregprod.notaventadetalle_id) NO se toca.
 * Esta tabla agrega el segundo hecho -a quien se le entrego finalmente- y las
 * dos cosas conviven.
 *
 * Por eso no se reescribe el notaventadetalle_id original: hacerlo borraria el
 * motivo por el que esa orden de trabajo existio, y manana nadie podria explicar
 * por que se fabrico.
 *
 * Primera version, acotada a proposito mientras se define el resto en reunion:
 *   - El lote se reasigna COMPLETO (no una parte).
 *   - No se relanza la fabricacion de la nota de venta que queda descubierta;
 *     solo se avisa. Como se le consigue el producto de nuevo (reabrir el item
 *     o generar otra OT) es decision de planta, documentada en
 *     docs/reasignacion-produccion.md.
 */
class CreateProdreasignacionTable extends Migration
{
    public function up()
    {
        Schema::create('prodreasignacion', function (Blueprint $table) {
            $table->bigIncrements('id');

            /* El lote que cambia de dueno. */
            $table->unsignedBigInteger('opdetregprod_id');

            /* De quien era. Se guarda aunque tambien este en
               invmovdet_opdetregprod: si manana ese vinculo cambia por otra via,
               esta fila tiene que seguir contando la historia completa por si
               sola. */
            $table->unsignedBigInteger('nvdet_origen_id')->nullable();

            /* A quien se le entrega. Al menos uno de los dos, igual que en las
               reservas: puede ir a una nota de venta concreta, o quedar apartado
               para un cliente que todavia no tiene pedido cargado. */
            $table->unsignedBigInteger('nvdet_destino_id')->nullable();
            $table->unsignedBigInteger('cliente_destino_id')->nullable();

            /* Cantidad del lote al momento de reasignar. En esta version es el
               lote completo; la columna queda para cuando se permita parcial. */
            $table->double('cant', 18, 2)->nullable();

            /* Obligatorio: alguien va a preguntar por que su pedido se atraso. */
            $table->string('motivo', 300);

            $table->unsignedBigInteger('usuario_id');

            /* Deshacer la reasignacion: el lote vuelve a su nota de venta de
               origen. No se borra la fila, para que quede el ida y vuelta. */
            $table->timestamp('anulada_at')->nullable();
            $table->unsignedBigInteger('anulusuario_id')->nullable();
            $table->string('anulmotivo', 300)->nullable();

            $table->unsignedBigInteger('usuariodel_id')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('opdetregprod_id')->references('id')->on('opdetregprod');
            $table->foreign('nvdet_origen_id')->references('id')->on('notaventadetalle');
            $table->foreign('nvdet_destino_id')->references('id')->on('notaventadetalle');
            $table->foreign('cliente_destino_id')->references('id')->on('cliente');
            $table->foreign('usuario_id')->references('id')->on('usuario');

            /* Las dos preguntas que se hacen todo el tiempo: "este lote, a quien
               se entrego" y "a esta nota de venta, le quitaron algo". */
            $table->index('opdetregprod_id', 'prodreasig_lote_idx');
            $table->index('nvdet_origen_id', 'prodreasig_origen_idx');
            $table->index('nvdet_destino_id', 'prodreasig_destino_idx');
        });
    }

    public function down()
    {
        Schema::dropIfExists('prodreasignacion');
    }
}
