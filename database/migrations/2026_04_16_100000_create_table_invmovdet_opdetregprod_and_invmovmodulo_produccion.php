<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateTableInvmovdetOpdetregprodAndInvmovmoduloProduccion extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // 1. Insertar módulo de Producción en invmovmodulo (id=9)
        $usuarioId = DB::table('usuario')->orderBy('id')->value('id') ?? 1;
        DB::table('invmovmodulo')->insert([
            'id'         => 9,
            'nombre'     => 'Producción',
            'desc'       => 'Movimientos de inventario generados por el módulo de Producción',
            'cod'        => 'PROD',
            'usuario_id' => $usuarioId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2. Crear tabla de trazabilidad invmovdet_opdetregprod
        //    Permite llegar desde un movimiento de inventario hasta el registro
        //    de producción exacto (opdetregprod) y desde ahí a toda la cadena.
        Schema::create('invmovdet_opdetregprod', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('invmovdet_id');
            $table->unsignedBigInteger('opdetregprod_id');
            $table->timestamps();

            $table->foreign('invmovdet_id')
                  ->references('id')->on('invmovdet')
                  ->onDelete('cascade');

            // No FK hacia opdetregprod porque esa tabla puede no existir aún
            // en todas las bases de datos del proyecto.
            $table->index('opdetregprod_id');
            $table->engine = 'InnoDB';
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('invmovdet_opdetregprod');

        DB::table('invmovmodulo')->where('id', 9)->delete();
    }
}
