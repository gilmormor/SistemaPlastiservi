<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNotpedDtefac extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dtefac', function (Blueprint $table) {
            $table->string('notped',12)->comment('Nota de Pedido CodRef 802')->nullable()->after('hep');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dtefac', function (Blueprint $table) {
            $table->dropColumn('notped');
        });
    }
}
