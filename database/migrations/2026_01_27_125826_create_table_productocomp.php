<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableProductocomp extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('productocomp', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('producto_id');
            $table->foreign('producto_id','fk_productocomp_producto')->references('id')->on('producto')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('productocomp_id')->nullable();
            $table->foreign('productocomp_id','fk_productocomp_productocomp')->references('id')->on('producto')->onDelete('restrict')->onUpdate('restrict');
            $table->float('cant',10,2)->comment('Cantidad de productos por componente');
            $table->string('obs',100)->comment('Observacion del componente');
            $table->unsignedBigInteger('usuario_id')->comment('Id Usuario');
            $table->foreign('usuario_id','fk_productocomp_usuario')->references('id')->on('usuario')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('usuariodel_id')->comment('ID Usuario que elimino el registro')->nullable();
            $table->engine = 'InnoDB';
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
        Schema::dropIfExists('productocomp');
    }
}
