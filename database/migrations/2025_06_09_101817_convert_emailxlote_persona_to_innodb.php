<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ConvertEmailxlotePersonaToInnodb extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('emailxlote_persona', function (Blueprint $table) {
            DB::statement('ALTER TABLE emailxlote_persona ENGINE = InnoDB');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('emailxlote_persona', function (Blueprint $table) {
            DB::statement('ALTER TABLE emailxlote_persona ENGINE = MyISAM');
        });
    }
}
