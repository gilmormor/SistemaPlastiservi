<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use PhpParser\Comment;

class CrearTablaProducto extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('producto', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->string('nombre',100);
            $table->string('descripcion',200)->nullable();
            $table->string('codintprod',12)->nullable()->comment('Código interno de producto usado por la empresa');
            $table->string('codbarra',45)->nullable()->comment('Código de barras');
            $table->float('diamextmm',8,2)->comment('Diametro Ext milimetros')->nullable();
            $table->string('diamextpg')->comment('Diametro Ext pulgadas')->nullable();
            $table->float('espesor',8,2)->comment('Espesor milimetros')->nullable();
            $table->float('long',8,2)->comment('Longitud Mts')->nullable();
            $table->float('peso',8,2)->comment('Peso en Kilos')->nullable();
            $table->string('tipounion',5)->Comment('Tipo de Union')->nullable();
            $table->double('precioneto',10,2)->comment('Precio Neto');
            $table->unsignedBigInteger('categoriaprod_id');
            $table->foreign('categoriaprod_id','fk_producto_categoriaprod')->references('id')->on('categoriaprod')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('claseprod_id');
            $table->foreign('claseprod_id','fk_producto_claseprod')->references('id')->on('claseprod')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('grupoprod_id');
            $table->foreign('grupoprod_id','fk_producto_grupoprod')->references('id')->on('grupoprod')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('usuariodel_id')->comment('ID Usuario que elimino el registro')->nullable();
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
        Schema::dropIfExists('producto');
    }
}
