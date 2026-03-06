<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTipodocDatacobranzadet extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('datacobranzadet', function (Blueprint $table) {
            $table->string('tipodoc')->comment('Tipo Documento FAV=Factura,CHF=Cheque')->after('dte_id')->default('FAV')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('datacobranzadet', function (Blueprint $table) {
            $table->dropColumn('tipodoc');
        });
    }
}
