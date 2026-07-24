<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStaprogOtdepTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('otdet', function (Blueprint $table) {
            $table->float('kgprog',18,2)->comment('Total Kg programados')->default(0)->after('obs');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('otdet', function (Blueprint $table) {
            $table->dropColumn('kgprog');
        });
    }
}
