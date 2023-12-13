<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ModificarTablePrcitemDtedet extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dtedet', function (Blueprint $table) {
            DB::statement("ALTER TABLE dtedet MODIFY COLUMN prcitem DOUBLE(20,4)");
            DB::statement("ALTER TABLE dtedet MODIFY COLUMN itemkg DOUBLE(12,4)");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dtedet', function (Blueprint $table) {
            DB::statement("ALTER TABLE dtedet MODIFY COLUMN prcitem DOUBLE(18,2)");
            DB::statement("ALTER TABLE dtedet MODIFY COLUMN itemkg DOUBLE(10,2)");
        });
    }
}
