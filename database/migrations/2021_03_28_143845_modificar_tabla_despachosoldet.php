<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModificarTablaDespachosoldet extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('despachosoldet', function (Blueprint $table) {
            $table->float('cantsoldespdev',10,2)->comment('Cantidad Devuelta.')->default(0)->nullable()->after('cantsoldesp');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('despachosoldet', function (Blueprint $table) {
            //
        });
    }
}
