<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModificarNoconformidad extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('noconformidad', function (Blueprint $table) {
            $table->unsignedBigInteger('rechazoresmedtom_id')->comment('Id Rechazo No Conformidad.')->after('rechazonc_id')->nullable();;
            $table->foreign('rechazoresmedtom_id','fk_noconformidad_rechazoresmedtom')->references('id')->on('rechazoresmedtom')->onDelete('restrict')->onUpdate('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('noconformidad', function (Blueprint $table) {
            //
        });
    }
}
