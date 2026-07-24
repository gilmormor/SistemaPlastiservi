<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddApetapaprodIdTableOpdet extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('opdet', function (Blueprint $table) {
            $table->float('kg',18,2)->comment('Kg programados para producir.')->default(0)->after('obs');
            $table->float('cant',18,2)->comment('Cantidad programados, si corresponde.')->default(0)->after('kg');
            $table->float('saldokg',18,2)->comment('Saldo Kg por producir.')->default(0)->after('cant');
            $table->dateTime('fechafin')->comment('Estatus y Fecha hora fin de proceso de la orden de produccion.')->nullable()->after('saldokg');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('opdet', function (Blueprint $table) {
            $table->dropColumn('kg');
            $table->dropColumn('cant');
            $table->dropColumn('saldokg');
            $table->dropColumn('fechafin');
        });
    }
}
