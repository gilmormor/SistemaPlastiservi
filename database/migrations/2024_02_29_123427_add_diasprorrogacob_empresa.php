<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDiasprorrogacobEmpresa extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('empresa', function (Blueprint $table) {
            $table->boolean('diasprorrogacob')->comment('Dias de prorroga para cobranza. Este campo si una factura vence el 01/02/2024 y hay 10 dias de porroga es quiere decir que la factura se vence el 11/02/2024.')->default(0)->after('stabloxdeusiscob');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('empresa', function (Blueprint $table) {
            $table->dropColumn('diasprorrogacob');
        });
    }
}
