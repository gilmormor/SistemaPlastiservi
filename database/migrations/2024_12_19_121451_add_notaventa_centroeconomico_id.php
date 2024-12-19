<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddNotaventaCentroeconomicoId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('notaventa', function (Blueprint $table) {
            $table->unsignedBigInteger('centroeconomico_id')->nullable()->after('sucursal_id');
            $table->foreign('centroeconomico_id','fk_notaventa_centroeconomico')->references('id')->on('centroeconomico')->onDelete('restrict')->onUpdate('restrict');
        });
        DB::table('notaventa')
        ->update([
            'centroeconomico_id' => DB::raw('sucursal_id'),
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('notaventa', function (Blueprint $table) {
            $table->dropForeign('fk_notaventa_centroeconomico');
            $table->dropColumn('centroeconomico_id');
        });
    }
}
