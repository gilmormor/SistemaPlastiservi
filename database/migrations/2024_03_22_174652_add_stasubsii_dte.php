<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStasubsiiDte extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dte', function (Blueprint $table) {
            $table->boolean('stasubsii')->comment('Status registro fue subido a Sii')->default(0)->after('aprobfechahora');
            $table->boolean('stasubcob')->comment('Status registro fue subido a Sistema Contabilidad Cobranza')->default(0)->after('stasubsii');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dte', function (Blueprint $table) {
            $table->dropColumn('stasubsii');
            $table->dropColumn('stasubcob');
        });
    }
}
