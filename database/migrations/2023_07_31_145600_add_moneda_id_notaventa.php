<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMonedaIdNotaventa extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('notaventa', function (Blueprint $table) {
            $table->unsignedBigInteger('moneda_id')->nullable()->comment('Moneda ')->default(1)->after('total');
            $table->foreign('moneda_id','fk_notaventa_moneda')->references('id')->on('moneda')->onDelete('restrict')->onUpdate('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('notaventa', function (Blueprint $table) {
            $table->dropForeign('fk_notaventa_moneda');
            $table->dropColumn('moneda_id');
        });
    }
}
