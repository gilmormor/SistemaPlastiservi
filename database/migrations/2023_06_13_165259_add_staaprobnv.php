<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStaaprobnv extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sucursal', function (Blueprint $table) {
            $table->string('staaprobnv',10)->nullable()->comment('Estatus aprobar Nota de Venta. 0 blanco o nulo no necesita aprobacion, 1=Todas la NV deben ser probadas antes de hacer solicitud de despacho, 2=Solo deben ser aprobadas las que esten por debajo de precio en tabla. Se puede dar el caso que tenga 2 estatus ejemplo: 1,2 que debe ser validado tanto precio como la NV en general.')->default("0")->after('email');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sucursal', function (Blueprint $table) {
            $table->dropColumn('staaprobnv');
        });
    }
}
