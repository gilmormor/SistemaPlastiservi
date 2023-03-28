<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAreaproduccionsuclineaIdPesajedet extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pesajedet', function (Blueprint $table) {
            $table->unsignedBigInteger('areaproduccionsuclinea_id')->nullable()->after('pesajecarro_id');
            $table->foreign('areaproduccionsuclinea_id','fk_pesajedet_areaproduccionsuclinea')->references('id')->on('areaproduccionsuclinea')->onDelete('restrict')->onUpdate('restrict');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pesajedet', function (Blueprint $table) {
            $table->dropForeign('fk_pesajedet_areaproduccionsuclinea');
            $table->dropColumn('areaproduccionsuclinea_id');
        });
    }
}
