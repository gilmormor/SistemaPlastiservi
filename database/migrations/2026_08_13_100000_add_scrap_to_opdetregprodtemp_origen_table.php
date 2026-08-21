<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Agrega 'scrap' a opdetregprodtemp_origen: porción del 'kg' tomado de ese lote
 * que el operario declaró explícitamente como scrap (no se infiere automáticamente).
 */
class AddScrapToOpdetregprodtempOrigenTable extends Migration
{
    public function up()
    {
        Schema::table('opdetregprodtemp_origen', function (Blueprint $table) {
            $table->double('scrap', 18, 2)->default(0)->after('kg');
        });
    }

    public function down()
    {
        Schema::table('opdetregprodtemp_origen', function (Blueprint $table) {
            $table->dropColumn('scrap');
        });
    }
}
