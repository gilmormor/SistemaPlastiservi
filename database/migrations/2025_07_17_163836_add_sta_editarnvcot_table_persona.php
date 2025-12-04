<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStaEditarnvcotTablePersona extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('persona', function (Blueprint $table) {
            $table->boolean('sta_editarnvcot')->comment('Status para permitir editar todas las Notas de Venta y cotizaciones sin importar por quien fue creada 0=No, 1=Si')->default(0)->after('cargo_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('persona', function (Blueprint $table) {
             $table->dropColumn('sta_editarnvcot');
        });
    }
}
