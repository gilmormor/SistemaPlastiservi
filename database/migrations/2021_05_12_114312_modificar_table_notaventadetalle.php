<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModificarTableNotaventadetalle extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('notaventadetalle', function (Blueprint $table) {
            $table->string('producto_nombre',100)->comment('Nombre producto.')->nullable()->after('subtotal');
            $table->float('ancho',10,2)->comment('Ancho')->nullable()->after('producto_nombre');
            $table->float('largo',10,2)->comment('Largo')->nullable()->after('ancho');
            $table->float('espesor',10,4)->comment('espesor')->nullable()->after('largo');
            $table->string('diametro',25)->comment('Diametro')->nullable()->after('espesor');
            $table->unsignedBigInteger('categoriaprod_id')->nullable()->after('diametro');
            $table->foreign('categoriaprod_id','fk_notaventadetalle_categoriaprod')->references('id')->on('categoriaprod')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('claseprod_id')->nullable()->after('categoriaprod_id');
            $table->foreign('claseprod_id','fk_notaventadetalle_claseprod')->references('id')->on('claseprod')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('grupoprod_id')->nullable()->after('claseprod_id');
            $table->foreign('grupoprod_id','fk_notaventadetalle_grupoprod')->references('id')->on('grupoprod')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('color_id')->comment("Color")->nullable()->after('grupoprod_id');
            $table->foreign('color_id','fk_notaventadetalle_color')->references('id')->on('color')->onDelete('restrict')->onUpdate('restrict');
            $table->string('obs')->comment('Observaciones')->nullable()->after('color_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('notaventadetalle', function (Blueprint $table) {
            //
        });
    }
}
