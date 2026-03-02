<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableProductoinsumo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('productoinsumo', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->unsignedBigInteger('producto_id');
            $table->foreign('producto_id','fk_productoinsumo_producto')->references('id')->on('producto')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('insumo_id');
            $table->foreign('insumo_id','fk_productoinsumo_insumo')->references('id')->on('insumo')->onDelete('restrict')->onUpdate('restrict');
            $table->decimal('cant',12,4)->comment('Cantidad del insumo en el producto');
            $table->decimal('unidadesproducto',12,4)->comment('Unidades del producto que se producen con la cantidad del insumo, Ejemplo 6 productos caben en una caja, es decir que 1 producto es igual a 1/6 de caja, entonces unidadesproducto es igual a 6');
            $table->tinyInteger('activo')->default(1)->comment('Estatus activo o inactivo.');
            $table->string('obs')->nullable()->comment('Observaciones');
            $table->unsignedBigInteger('usuario_id');
            $table->foreign('usuario_id','fk_productoinsumo_usuario')->references('id')->on('usuario')->onDelete('restrict')->onUpdate('restrict');
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
        Schema::dropIfExists('productoinsumo');
    }
}
