<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDiasPlazopago extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('plazopago', function (Blueprint $table) {
            $table->tinyInteger('dias')->comment('Dias plazo de pago.')->default(0)->after('descripcion');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('plazopago', function (Blueprint $table) {
            $table->dropColumn('dias');
        });
    }
}
