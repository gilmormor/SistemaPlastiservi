<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMateriaprimaPe extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('materiaprima', function (Blueprint $table) {
            $table->double('pe',4,3)->comment('Peso especifico. Factor')->after('desc');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('materiaprima', function (Blueprint $table) {
            $table->dropColumn('pe');
        });
    }
}
