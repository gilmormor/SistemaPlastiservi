<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Agrega 'scrap' a opdetregprod_origen (definitiva): mismo significado que en
 * opdetregprodtemp_origen, copiado al aprobar el registro temp.
 */
class AddScrapToOpdetregprodOrigenTable extends Migration
{
    public function up()
    {
        Schema::table('opdetregprod_origen', function (Blueprint $table) {
            $table->double('scrap', 18, 2)->default(0)->after('kg');
        });
    }

    public function down()
    {
        Schema::table('opdetregprod_origen', function (Blueprint $table) {
            $table->dropColumn('scrap');
        });
    }
}
