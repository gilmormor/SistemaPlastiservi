<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddKgscrapTableOpdet extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('opdet', function (Blueprint $table) {
            $table->float('kgscrap',18,2)->comment('Kilos scrap.')->default(0)->after('kgprod');
            $table->float('mtslineal',18,2)->comment('Metros lineales.')->default(0)->after('kgscrap');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('opdet', function (Blueprint $table) {
            $table->dropColumn('kgscrap');
            $table->dropColumn('mtslineal');
        });
    }
}
