<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableDoccompdet extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('doccompdet', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('origentipo', 4)->comment('Tipo de documento de origen');
            $table->unsignedBigInteger('origendet_id');
            $table->unsignedBigInteger('productocomp_id');
            $table->foreign('productocomp_id','fk_doccompdet_productocomp')->references('id')->on('productocomp')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('productopadre_id');
            $table->foreign('productopadre_id','fk_doccompdet_productopadre')->references('id')->on('producto')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('producto_id');
            $table->foreign('producto_id','fk_doccompdet_producto')->references('id')->on('producto')->onDelete('restrict')->onUpdate('restrict');
            $table->float('cant',12,4)->comment('Cantidad total componentes');
            $table->float('cantxcomp',12,4)->comment('Cantidad por componente o valor en cant de un componente.');
            $table->float('precio',10,2)->comment('Precio unitario del componente');
            $table->float('costo',10,2)->comment('Costo unitario del componente');
            $table->unsignedBigInteger('usuario_id')->comment('Id Usuario');
            $table->foreign('usuario_id','fk_doccompdet_usuario')->references('id')->on('usuario')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('usuariodel_id')->comment('ID Usuario que elimino el registro')->nullable();
            $table->engine = 'InnoDB';
            $table->softDeletes();
            $table->timestamps();
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_spanish_ci';
            $table->index(['origentipo','origendet_id'], 'idx_doccompdet_origen');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('doccompdet');
    }
}
