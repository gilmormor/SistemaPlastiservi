<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSucursalIdDespachosol extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('despachosol', function (Blueprint $table) {
            $table->unsignedBigInteger('sucursal_id')->comment('Sucursal de despacho')->nullable()->after('notaventa_id');
            $table->foreign('sucursal_id','fk_despachosol_sucursal')->references('id')->on('sucursal')->onDelete('restrict')->onUpdate('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('despachosol', function (Blueprint $table) {
            $table->dropForeign('fk_despachosol_sucursal');
            $table->dropColumn('sucursal_id');
        });
    }
}
