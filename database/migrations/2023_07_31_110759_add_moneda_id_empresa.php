<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMonedaIdEmpresa extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('empresa', function (Blueprint $table) {
            $table->unsignedBigInteger('moneda_id')->nullable()->comment('Moneda ')->default(1)->after('sucursal_id');
            $table->foreign('moneda_id','fk_empresa_moneda')->references('id')->on('moneda')->onDelete('restrict')->onUpdate('restrict');
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
            $table->dropForeign('fk_empresa_moneda');
            $table->dropColumn('moneda_id');
        });
    }
}
