<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateTableIndtrasladoAndAddToDtetrans extends Migration
{
    /**
     * Run the migrations.
     * Indicador Tipo de traslado de bienes (SII Chile)
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tipotraslado', function (Blueprint $table) {
            $table->increments('id');
            $table->tinyInteger('indtraslado')->unsigned()->unique()->comment('Indicador Tipo de traslado de bienes');
            $table->string('desc', 100)->comment('Descripción del indicador');
            $table->tinyInteger('activo')->unsigned()->default(0)->comment('1=Activo, 0=Inactivo');
            $table->timestamps();
        });

        DB::table('tipotraslado')->insert([
            ['indtraslado' => 1, 'desc' => 'Operación constituye venta',           'activo' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['indtraslado' => 2, 'desc' => 'Ventas por efectuar',                  'activo' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['indtraslado' => 3, 'desc' => 'Consignaciones',                        'activo' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['indtraslado' => 4, 'desc' => 'Entrega gratuita',                      'activo' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['indtraslado' => 5, 'desc' => 'Traslados internos',                    'activo' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['indtraslado' => 6, 'desc' => 'Otros traslados no venta',              'activo' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['indtraslado' => 7, 'desc' => 'Venta para exportación',                'activo' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['indtraslado' => 8, 'desc' => 'Traslado para exportación (No venta)', 'activo' => 0, 'created_at' => now(), 'updated_at' => now()],
        ]);

        Schema::table('dtetrans', function (Blueprint $table) {
            $table->tinyInteger('indtraslado')->unsigned()->nullable()
                ->comment('Indicador Tipo de traslado de bienes')
                ->after('patentecarro');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dtetrans', function (Blueprint $table) {
            $table->dropColumn('indtraslado');
        });

        Schema::dropIfExists('tipotraslado');
    }
}
