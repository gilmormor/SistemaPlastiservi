<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSignoFoliocontrol extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('foliocontrol', function (Blueprint $table) {
            $table->tinyInteger('signo')->comment('Signo para saber si el documento suma o resta')->default(1)->after('bloqueo');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('foliocontrol', function (Blueprint $table) {
            $table->dropColumn('signo');
        });
    }
}
