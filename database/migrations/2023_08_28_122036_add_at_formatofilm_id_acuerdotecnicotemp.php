<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAtFormatofilmIdAcuerdotecnicotemp extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('acuerdotecnicotemp', function (Blueprint $table) {
            $table->float('at_formatofilm', 6, 2)->comment('Formato film Strech')->nullable()->after('at_impresocolor_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('acuerdotecnicotemp', function (Blueprint $table) {
            $table->dropColumn('at_formatofilm');
        });
    }
}
