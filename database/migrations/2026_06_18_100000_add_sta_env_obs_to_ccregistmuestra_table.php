<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStaEnvObsToCcregistmuestraTable extends Migration
{
    public function up()
    {
        Schema::table('ccregistmuestra', function (Blueprint $table) {
            // Observación del supervisor al rechazar (sta_env=3)
            $table->text('sta_env_obs')->nullable()->after('sta_env');
        });
    }

    public function down()
    {
        Schema::table('ccregistmuestra', function (Blueprint $table) {
            $table->dropColumn('sta_env_obs');
        });
    }
}
