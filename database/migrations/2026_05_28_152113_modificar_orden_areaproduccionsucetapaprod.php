<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModificarOrdenAreaproduccionsucetapaprod extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('areaproduccionsucetapaprod', function (Blueprint $table) {
            $table->float('orden', 10, 3)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('areaproduccionsucetapaprod', function (Blueprint $table) {
            $table->integer('orden')->change();
        });
    }
}
