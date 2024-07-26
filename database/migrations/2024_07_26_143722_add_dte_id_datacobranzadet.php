<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDteIdDatacobranzadet extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('datacobranzadet', function (Blueprint $table) {
            $table->unsignedBigInteger('dte_id')->nullable()->after('datacobranza_id');
            $table->foreign('dte_id','fk_datacobranzadet_dte')->references('id')->on('dte')->onDelete('restrict')->onUpdate('restrict');
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
            $table->dropForeign('fk_datacobranzadet_dte');
            $table->dropColumn('dte_id');
        });
    }
}
